from pandas.tseries.offsets import MonthBegin
import pandas as pd
import numpy as np
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import StandardScaler
from sklearn.impute import SimpleImputer
from sklearn.neural_network import MLPRegressor
from sklearn.metrics import mean_absolute_error, mean_squared_error
from sqlalchemy import create_engine, text
from sqlalchemy.engine import URL

# --- Conexión a la DB ---
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
engine = create_engine(url, pool_pre_ping=True, pool_recycle=180)

# --- Traer datos históricos ---
QUERY = """
SELECT 
  id,
  fecha_pedido,
  costo
FROM pedidos
WHERE fecha_pedido IS NOT NULL AND costo IS NOT NULL;
"""
df = pd.read_sql(text(QUERY), engine)
df['fecha_pedido'] = pd.to_datetime(df['fecha_pedido'], errors='coerce')
df = df.dropna(subset=['fecha_pedido'])

# --- Agrupar por mes ---
df['year_month'] = df['fecha_pedido'].dt.to_period('M').dt.to_timestamp()
monthly = df.groupby('year_month')['costo'].sum().reset_index().rename(columns={'costo':'y'})

# --- Crear features de series de tiempo ---
monthly = monthly.sort_values('year_month')
monthly['lag1'] = monthly['y'].shift(1)
monthly['lag2'] = monthly['y'].shift(2)
monthly['lag3'] = monthly['y'].shift(3)
monthly['roll3_mean'] = monthly['y'].shift(1).rolling(3).mean().reset_index(drop=True)
monthly['month'] = monthly['year_month'].dt.month
monthly['month_sin'] = np.sin(2*np.pi*monthly['month']/12)
monthly['month_cos'] = np.cos(2*np.pi*monthly['month']/12)
monthly = monthly.dropna(subset=['lag1','lag2','lag3','roll3_mean'])

feature_cols = ['lag1','lag2','lag3','roll3_mean','month_sin','month_cos']
X = monthly[feature_cols].values
y = monthly['y'].values

# --- Entrenar modelo ---
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
pipe.fit(X, y)

# --- Forecast dinámico ---
last_month = monthly['year_month'].max()
horizon = 2  # próximos 2 meses
future_months = [last_month + MonthBegin(i) for i in range(1, horizon+1)]

def build_feature_row(y_hist_df, target_month):
    hist = y_hist_df.sort_values('year_month')
    lag1 = hist['y'].iloc[-1]
    lag2 = hist['y'].iloc[-2] if len(hist) >= 2 else np.nan
    lag3 = hist['y'].iloc[-3] if len(hist) >= 3 else np.nan
    roll3 = hist['y'].iloc[-3:].mean() if len(hist) >= 3 else np.nan

    m = target_month.month
    month_sin = np.sin(2*np.pi*m/12)
    month_cos = np.cos(2*np.pi*m/12)

    row = pd.DataFrame([[lag1, lag2, lag3, roll3, month_sin, month_cos]],
                       columns=feature_cols)
    return row

forecasts = []
hist_for_forecast = monthly[['year_month','y']].copy()
for tm in future_months:
    X_row = build_feature_row(hist_for_forecast, tm)
    y_hat = max(0, pipe.predict(X_row)[0])
    forecasts.append({'year_month': tm, 'y_pred': float(y_hat)})

forecast_df = pd.DataFrame(forecasts)
forecast_df['periodo'] = forecast_df['year_month'].dt.strftime('%Y-%m')
forecast_df['modelo'] = 'MLPRegressor'

# --- Insertar pronósticos evitando duplicados ---
with engine.begin() as conn:
    periods = tuple(forecast_df['periodo'])
    if len(periods) == 1:
        conn.execute(text("DELETE FROM pronosticos_ingresos WHERE periodo = :p"), {"p": periods[0]})
    else:
        conn.execute(text(f"DELETE FROM pronosticos_ingresos WHERE periodo IN ({','.join([':p'+str(i) for i in range(len(periods))])})"),
                     {('p'+str(i)): periods[i] for i in range(len(periods))})

    rows = []
    for _, r in forecast_df.iterrows():
        rows.append({
            "fecha": pd.to_datetime(r["year_month"]).date(),
            "periodo": r["periodo"],
            "pronostico_mes": r["y_pred"],
            "modelo": r["modelo"],
        })
    conn.execute(text("""
        INSERT INTO pronosticos_ingresos
        (fecha, periodo, pronostico_mes, modelo)
        VALUES
        (:fecha, :periodo, :pronostico_mes, :modelo)
    """), rows)

print("Pronósticos insertados correctamente para los próximos meses.")
