# mlp_demanda_materiales_mysql.py
import pandas as pd
import numpy as np
from sqlalchemy import create_engine, text
from sqlalchemy.engine import URL
from sklearn.neural_network import MLPRegressor
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import StandardScaler
from sklearn.impute import SimpleImputer
from sklearn.metrics import mean_absolute_error, mean_squared_error
from datetime import datetime
import warnings

warnings.filterwarnings("ignore")

# Configuración de la conexión
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

# Consulta de datos
QUERY = """
SELECT 
  id,
  fecha_pedido,
  servicio,
  domicilio_cliente,
  nombre_cliente,
  gastos,
  costo,
  NULL AS cantidad
FROM pedidos
WHERE fecha_pedido IS NOT NULL AND servicio IS NOT NULL;
"""

df = pd.read_sql(text(QUERY), engine)

if df.empty:
    raise ValueError("No se obtuvieron filas desde 'pedidos'. Verifica la tabla/consulta.")

df['fecha_pedido'] = pd.to_datetime(df['fecha_pedido'], errors='coerce')
df = df.dropna(subset=['fecha_pedido', 'servicio'])
df['servicio'] = df['servicio'].astype(str).str.strip()
df['year_month'] = df['fecha_pedido'].dt.to_period('M').dt.to_timestamp()

USE_CANTIDAD = 'cantidad' in df.columns and df['cantidad'].notna().any()

if USE_CANTIDAD:
    monthly = df.groupby(['year_month','servicio'])['cantidad'].sum().rename('y').reset_index()
else:
    monthly = df.groupby(['year_month','servicio'])['id'].count().rename('y').reset_index()

# Crear todas las combinaciones de meses y servicios
all_months = pd.DataFrame({'year_month': pd.date_range(monthly['year_month'].min(),
                                                       monthly['year_month'].max(),
                                                       freq='MS')})
materials = pd.DataFrame({'servicio': monthly['servicio'].unique()})
full = all_months.merge(materials, how='cross')
monthly = full.merge(monthly, how='left', on=['year_month','servicio']).fillna({'y':0})

# Características de lags y promedio móvil
monthly = monthly.sort_values(['servicio','year_month'])
monthly['lag1'] = monthly.groupby('servicio')['y'].shift(1)
monthly['lag2'] = monthly.groupby('servicio')['y'].shift(2)
monthly['lag3'] = monthly.groupby('servicio')['y'].shift(3)
monthly['roll3_mean'] = monthly.groupby('servicio')['y'].shift(1).rolling(3).mean().reset_index(level=0, drop=True)

monthly['month'] = monthly['year_month'].dt.month
monthly['month_sin'] = np.sin(2*np.pi * monthly['month']/12)
monthly['month_cos'] = np.cos(2*np.pi * monthly['month']/12)

material_dummies = pd.get_dummies(monthly['servicio'], prefix='mat', drop_first=True)

data = pd.concat([monthly[['year_month','servicio','y','lag1','lag2','lag3','roll3_mean','month_sin','month_cos']],
                  material_dummies], axis=1)
data = data.dropna(subset=['lag1','lag2','lag3','roll3_mean'])

# Entrenamiento y test
N_TEST_MONTHS = 6
unique_months = np.sort(data['year_month'].unique())
if len(unique_months) <= N_TEST_MONTHS + 6:
    N_TEST_MONTHS = min(3, max(1, len(unique_months)//4))

cutoff = unique_months[-N_TEST_MONTHS]
train_df = data[data['year_month'] < cutoff].copy()
test_df  = data[data['year_month'] >= cutoff].copy()

feature_cols = ['lag1','lag2','lag3','roll3_mean','month_sin','month_cos'] + list(material_dummies.columns)
X_train = train_df[feature_cols].values
y_train = train_df['y'].values
X_test  = test_df[feature_cols].values
y_test  = test_df['y'].values

pipe = Pipeline([
    ('imputer', SimpleImputer(strategy='median')),
    ('scaler', StandardScaler()),
    ('mlp', MLPRegressor(
        hidden_layer_sizes=(32,16),
        activation='relu',
        solver='adam',
        max_iter=800,
        random_state=42,
        early_stopping=True,
        n_iter_no_change=20,
        learning_rate_init=1e-3
    ))
])
pipe.fit(X_train, y_train)

pred = pipe.predict(X_test)

# Métricas
# Métricas
mae  = mean_absolute_error(y_test, pred)
rmse = np.sqrt(mean_squared_error(y_test, pred))

def mape(y_true, y_pred):
    y_true = np.array(y_true, dtype=float)
    y_pred = np.array(y_pred, dtype=float)
    mask = y_true != 0
    return np.mean(np.abs((y_true[mask] - y_pred[mask]) / y_true[mask]))*100 if mask.sum() else np.nan

mape_val = mape(y_test, pred)

print("=== Evaluación ===")
print(f"MAE : {mae:,.2f}")
print(f"RMSE: {rmse:,.2f}")
print(f"MAPE: {mape_val:.2f}% (ignora meses con 0 real)")


# Pronóstico siguiente mes
horizon = 1
last_month = monthly['year_month'].max()
future_months = [(last_month + pd.offsets.MonthBegin(i)) for i in range(1, horizon+1)]

def build_feature_row(servicio, y_hist_df, target_month):
    hist = y_hist_df[y_hist_df['servicio']==servicio].sort_values('year_month')
    lag1 = hist['y'].iloc[-1] if len(hist)>=1 else np.nan
    lag2 = hist['y'].iloc[-2] if len(hist)>=2 else np.nan
    lag3 = hist['y'].iloc[-3] if len(hist)>=3 else np.nan
    roll3 = hist['y'].iloc[-3:].mean() if len(hist)>=3 else np.nan

    m = target_month.month
    month_sin = np.sin(2*np.pi * m/12)
    month_cos = np.cos(2*np.pi * m/12)

    dummies = pd.Series(0, index=material_dummies.columns, dtype=int)
    if f"mat_{servicio}" in dummies.index:
        dummies[f"mat_{servicio}"] = 1

    row = pd.DataFrame([[lag1, lag2, lag3, roll3, month_sin, month_cos] + list(dummies.values)],
                       columns=['lag1','lag2','lag3','roll3_mean','month_sin','month_cos'] + list(material_dummies.columns))
    return row

hist_for_forecast = monthly[['year_month','servicio','y']].copy()

forecasts = []
for tm in future_months:
    for mat in materials['servicio']:
        X_row = build_feature_row(mat, hist_for_forecast, tm)
        y_hat = max(0, pipe.predict(X_row)[0])
        forecasts.append({'year_month': tm, 'servicio': mat, 'y_pred': float(y_hat)})

forecast_df = pd.DataFrame(forecasts).sort_values(['year_month','servicio'])
forecast_df['periodo'] = forecast_df['year_month'].dt.strftime('%Y-%m')
forecast_df['unidad']  = 'cantidad' if USE_CANTIDAD else 'pedidos'

print("\n=== Pronóstico próximos meses por material ===")
for ym, sub in forecast_df.groupby('year_month'):
    print(f"\n{ym.strftime('%Y-%m')}:")
    for _, r in sub.sort_values('y_pred', ascending=False).iterrows():
        print(f" - {r['servicio']}: {r['y_pred']:.1f} {forecast_df['unidad'].iloc[0]}/mes")

# Inserción en la base de datos
with engine.begin() as conn:
    fut_periods = tuple(sorted(forecast_df['periodo'].unique()))
    if len(fut_periods) == 1:
        conn.execute(text("DELETE FROM pronosticos_materiales WHERE periodo = :p"), {"p": fut_periods[0]})
    else:
        conn.execute(
            text(f"DELETE FROM pronosticos_materiales WHERE periodo IN ({','.join([':p'+str(i) for i in range(len(fut_periods))])})"),
            {('p'+str(i)): fut_periods[i] for i in range(len(fut_periods))}
        )

    rows = []
    for _, r in forecast_df.iterrows():
        rows.append({
            "servicio": r["servicio"],
            "year_month": pd.to_datetime(r["year_month"]).date(),
            "periodo": r["periodo"],
            "pronostico_mes": float(r["y_pred"]),
            "unidad": r["unidad"],
            "modelo": "MLPRegressor",
            "mae": float(mae),
            "rmse": float(rmse),
            "mape": float(mape_val) if not np.isnan(mape_val) else None
        })

    conn.execute(text("""
        INSERT INTO pronosticos_materiales
        (servicio, fecha, periodo, pronostico_mes, unidad, modelo, mae, rmse, mape)
        VALUES
        (:servicio, :year_month, :periodo, :pronostico_mes, :unidad, :modelo, :mae, :rmse, :mape)
    """), rows)

print("\nPronósticos guardados en la tabla 'pronosticos_materiales'.")
