#calculo de ingresos mensuales
import os
import pandas as pd
import numpy as np
from sqlalchemy import create_engine, text
from sklearn.neural_network import MLPRegressor
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import StandardScaler
from sklearn.impute import SimpleImputer
from sklearn.metrics import mean_absolute_error, mean_squared_error
from sqlalchemy.engine import URL
import warnings
from sklearn.preprocessing import RobustScaler

warnings.filterwarnings("ignore")

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

QUERY = """
SELECT 
  fecha   AS fecha,
  costo   AS costo
FROM pedidos
WHERE fecha IS NOT NULL AND costo IS NOT NULL;
"""

df = pd.read_sql(text(QUERY), engine)
if df.empty:
    raise ValueError("No hay filas en 'pedidos' con fecha/costo. Revisa la consulta o la tabla.")

df['fecha'] = pd.to_datetime(df['fecha'], errors='coerce')
df = df.dropna(subset=['fecha', 'costo'])

df['year_month'] = df['fecha'].dt.to_period('M').dt.to_timestamp()
monthly = (df.groupby('year_month')['costo']
           .sum()
           .rename('y')  
           .reset_index())


all_months = pd.DataFrame({
    'year_month': pd.date_range(monthly['year_month'].min(), monthly['year_month'].max(), freq='MS')
})
monthly = all_months.merge(monthly, how='left', on='year_month').fillna({'y': 0.0})

monthly = monthly.sort_values('year_month')
monthly['lag1'] = monthly['y'].shift(1)
monthly['lag2'] = monthly['y'].shift(2)
monthly['lag3'] = monthly['y'].shift(3)
monthly['roll3_mean'] = monthly['y'].shift(1).rolling(3).mean()

monthly['month'] = monthly['year_month'].dt.month
monthly['month_sin'] = np.sin(2*np.pi * monthly['month']/12)
monthly['month_cos'] = np.cos(2*np.pi * monthly['month']/12)

data = monthly.dropna(subset=['lag1','lag2','lag3','roll3_mean']).copy()

N_TEST_MONTHS = 6
unique_months = np.sort(data['year_month'].unique())
if len(unique_months) <= N_TEST_MONTHS + 6:
    N_TEST_MONTHS = min(3, max(1, len(unique_months)//4))

cutoff = unique_months[-N_TEST_MONTHS]
train_df = data[data['year_month'] < cutoff].copy()
test_df  = data[data['year_month'] >= cutoff].copy()

if train_df.empty or test_df.empty:
    raise ValueError("No hay suficientes meses para train/test. Se requieren más datos históricos.")

feature_cols = ['lag1','lag2','lag3','roll3_mean','month_sin','month_cos']
X_train = train_df[feature_cols].values
y_train = train_df['y'].values
X_test  = test_df[feature_cols].values
y_test  = test_df['y'].values

n_train = X_train.shape[0]
MIN_VAL_SAMPLES = 5  
use_early_stopping = n_train >= 40  

val_frac = 0.2
if use_early_stopping:
    if int(n_train * val_frac) < MIN_VAL_SAMPLES:
        val_frac = min(0.5, max(0.25, MIN_VAL_SAMPLES / max(1, n_train)))

pipe = Pipeline(steps=[
    ('imputer', SimpleImputer(strategy='median')),
    ('scaler', StandardScaler()),
    ('mlp', MLPRegressor(
        hidden_layer_sizes=(32,16),
        activation='relu',
        solver='adam',
        max_iter=800,
        random_state=42,
        early_stopping=use_early_stopping,
        validation_fraction=val_frac if use_early_stopping else 0.1,
        n_iter_no_change=20,
        learning_rate_init=1e-3,
        
    ))
])
pipe.fit(X_train, y_train)

pred = pipe.predict(X_test)


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
fecha   = pd.to_datetime(target_month).date()

with engine.begin() as conn:
    # evita duplicados del mismo periodo
    conn.execute(text("DELETE FROM pronosticos_ingresos WHERE periodo = :p"), {"p": periodo})

    conn.execute(text("""
        INSERT INTO pronosticos_ingresos
        (fecha, periodo, ingreso_pronosticado, modelo, mae, rmse, mape)
        VALUES
        (:fecha, :periodo, :ingreso_pronosticado, :modelo, :mae, :rmse, :mape)
    """), {
        "fecha": fecha,
        "periodo": periodo,
        "ingreso_pronosticado": y_blend,
        "modelo": "MLPRegressor",
        "mae": float(mae),
        "rmse": float(rmse),
        "mape": float(mape_val) if not np.isnan(mape_val) else None
    })

print("\nPronóstico guardado en 'pronosticos_ingresos'.")
