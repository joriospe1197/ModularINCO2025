import pandas as pd
from sqlalchemy import create_engine, text
from sqlalchemy.engine import URL
from sklearn.preprocessing import StandardScaler
from sklearn.pipeline import Pipeline
from sklearn.impute import SimpleImputer
from sklearn.neural_network import MLPRegressor
from datetime import datetime
import numpy as np

# --- Configuración ---
DB_USER = "root"
DB_PASS = "hgVvISLFXzOyIALjHttvbipzQmSCPMAl"
DB_HOST = "gondola.proxy.rlwy.net"
DB_PORT = 59369
DB_NAME = "constructora"

url = URL.create(
    "mysql+pymysql",
    username=DB_USER,
    password=DB_PASS,
    host=DB_HOST,
    port=DB_PORT,
    database=DB_NAME,
    query={"charset": "utf8mb4"}
)
engine = create_engine(
    url,
    pool_pre_ping=True,
    pool_recycle=180,
    connect_args={"connect_timeout": 5, "read_timeout": 30, "write_timeout": 30}
)

MESES_A_PREDECIR = 3  # Número de meses futuros a pronosticar

# --- Consulta de datos históricos ---
QUERY = """
SELECT 
  id   AS id,
  fecha_pedido   AS fecha_pedido,
  servicio AS servicio,
  domicilio_cliente AS domicilio_cliente,
  nombre_cliente AS nombre_cliente,
  gastos  AS gastos,
  costo   AS costo,
  NULL AS cantidad
FROM pedidos
WHERE fecha_pedido IS NOT NULL AND servicio IS NOT NULL;
"""
df = pd.read_sql(text(QUERY), engine)
if df.empty:
    raise ValueError("No hay filas en 'pedidos' con fecha_pedido/costo. Revisa la consulta o la tabla.")

df['fecha_pedido'] = pd.to_datetime(df['fecha_pedido'], errors='coerce')
df = df.dropna(subset=['fecha_pedido', 'costo'])

df['year_month'] = df['fecha_pedido'].dt.to_period('M').dt.to_timestamp()
monthly = (df.groupby('year_month')['costo']
           .sum()
           .rename('y')  
           .reset_index())

# --- Pipeline ---
pipe = Pipeline([
    ('scaler', StandardScaler()),
    ('mlp', MLPRegressor(
        hidden_layer_sizes=(32, 16),
        activation='relu',
        solver='adam',
        max_iter=800,
        random_state=42,
        early_stopping=False,
        learning_rate_init=1e-3
    ))
])

# Entrenamiento completo con todos los datos
pipe.fit(X, y)

# --- Predicciones futuras ---
ultimo_mes = df['fecha'].max()
predicciones = []

mae  = mean_absolute_error(y_test, pred)
try:
    rmse = mean_squared_error(y_test, pred, squared=False)
except TypeError:
    rmse = float(np.sqrt(mean_squared_error(y_test, pred)))

def mape(y_true, y_pred):
    y_true = np.array(y_true, dtype=float)
    y_pred = np.array(y_pred, dtype=float)
    mask = y_true != 0
    return np.mean(np.abs((y_true[mask] - y_pred[mask]) / y_true[mask]))*100 if mask.sum() else np.nan

mape_val = mape(y_test, pred)

print("=== Evaluación (ingresos mensuales) ===")
print(monthly[['year_month','y']].tail(6))
ultimo_mes = monthly['y'].iloc[-1]
prom3 = monthly['y'].iloc[-3:].mean() if len(monthly)>=3 else np.nan
mediana6 = monthly['y'].iloc[-6:].median() if len(monthly)>=6 else np.nan

print(f"\nBaseline ingenuo (último mes): ${ultimo_mes:,.2f}")
print(f"Promedio 3 meses:              ${prom3:,.2f}")
print(f"Mediana 6 meses:               ${mediana6:,.2f}")
print(f"MAE : {mae:,.2f}")
print(f"RMSE: {rmse:,.2f}")
print(f"MAPE: {mape_val:.2f}% (ignora meses con 0 real)")



last_month = monthly['year_month'].max()
target_month = (last_month + pd.offsets.MonthBegin(1))

def build_feature_row(y_hist_df, target_month):
   
    hist = y_hist_df.sort_values('year_month')
    lag1 = hist['y'].iloc[-1] if len(hist)>=1 else np.nan
    lag2 = hist['y'].iloc[-2] if len(hist)>=2 else np.nan
    lag3 = hist['y'].iloc[-3] if len(hist)>=3 else np.nan
    roll3 = hist['y'].iloc[-3:].mean() if len(hist)>=3 else np.nan

    m = target_month.month
    month_sin = np.sin(2*np.pi * m/12)
    month_cos = np.cos(2*np.pi * m/12)

    row = pd.DataFrame([[lag1, lag2, lag3, roll3, month_sin, month_cos]],
                       columns=['lag1','lag2','lag3','roll3_mean','month_sin','month_cos'])
    return row

X_next = build_feature_row(monthly[['year_month','y']], target_month)
y_next_pred = float(max(0.0, pipe.predict(X_next)[0])) 

y_mlp = y_next_pred
y_bl = prom3 if not np.isnan(prom3) else ultimo_mes

y_blend = 0.6 * y_bl + 0.4 * y_mlp

min6 = monthly['y'].iloc[-6:].min() if len(monthly)>=6 else None
max6 = monthly['y'].iloc[-6:].max() if len(monthly)>=6 else None
if min6 is not None and max6 is not None:
    y_blend = float(np.clip(y_blend, 0.8*min6, 1.2*max6)) 

print(f"\nPredicción MLP: ${y_mlp:,.2f}")
print(f"Predicción blend (recomendada): ${y_blend:,.2f}")

print("\n=== Pronóstico de INGRESOS para el próximo mes ===")
print(f"{target_month.strftime('%Y-%m')}: ${y_blend:,.2f}")


periodo = target_month.strftime('%Y-%m')
fecha_pedido   = pd.to_datetime(target_month).date()

with engine.begin() as conn:
    # evita duplicados del mismo periodo
    conn.execute(text("DELETE FROM pronosticos_ingresos WHERE periodo = :p"), {"p": periodo})

    conn.execute(text("""
        INSERT INTO pronosticos_ingresos
        (fecha, periodo, ingreso_pronosticado, modelo, mae, rmse, mape)
        VALUES
        (:fecha_pedido, :periodo, :ingreso_pronosticado, :modelo, :mae, :rmse, :mape)
    """), {
        "fecha_pedido": fecha_pedido,
        "periodo": periodo,
        "ingreso_pronosticado": y_blend,
        "modelo": "MLPRegressor",
        "mae": float(mae),
        "rmse": float(rmse),
        "mape": float(mape_val) if not np.isnan(mape_val) else None
    })

# --- Inserción de predicciones ---
insert_query = text("""
INSERT INTO pronosticos_ingresos 
(fecha, periodo, ingreso_pronosticado, modelo, mae, rmse, mape)
VALUES (:fecha, :periodo, :ingreso_pronosticado, :modelo, :mae, :rmse, :mape)
""")

with engine.begin() as conn:
    for registro in predicciones:
        conn.execute(insert_query, registro)  # pasar diccionario directamente

print(f"{MESES_A_PREDECIR} predicciones futuras insertadas correctamente.")
