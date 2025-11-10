import pandas as pd
from sqlalchemy import create_engine, text
from sklearn.preprocessing import StandardScaler
from sklearn.pipeline import Pipeline
from sklearn.neural_network import MLPRegressor
from sklearn.metrics import mean_absolute_error, mean_squared_error
from datetime import datetime, timedelta
import numpy as np

# --- Configuración ---
DB_USER = "root"
DB_PASS = "hgVvISLFXzOyIALjHttvbipzQmSCPMAl"
DB_HOST = "gondola.proxy.rlwy.net"
DB_PORT = 59369
DB_NAME = "constructora"

DB_URI = f"mysql+pymysql://{DB_USER}:{DB_PASS}@{DB_HOST}:{DB_PORT}/{DB_NAME}"
engine = create_engine(DB_URI)
MESES_A_PREDECIR = 3  # Número de meses futuros a pronosticar

# --- Consulta de datos históricos ---
QUERY = """
SELECT fecha_pedido AS fecha, costo
FROM pedidos
WHERE fecha_pedido IS NOT NULL AND costo IS NOT NULL;
"""
df = pd.read_sql(text(QUERY), engine)

# --- Preparación de datos ---
df['fecha'] = pd.to_datetime(df['fecha'])
df['mes'] = df['fecha'].dt.month
df['anio'] = df['fecha'].dt.year

X = df[['anio', 'mes']]
y = df['costo']

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

for i in range(1, MESES_A_PREDECIR + 1):
    # Calcular mes y año del próximo mes
    nuevo_mes = (ultimo_mes + pd.DateOffset(months=i))
    X_pred = pd.DataFrame({
        'anio': [nuevo_mes.year],
        'mes': [nuevo_mes.month]
    })
    
    pred = pipe.predict(X_pred)[0]
    
    predicciones.append({
        'fecha': nuevo_mes.date(),
        'periodo': nuevo_mes.strftime('%Y-%m'),
        'costo': float(pred),
        'modelo': 'MLPRegressor',
        'mae': None,   # No aplicable para predicción futura
        'rmse': None,
        'mape': None
    })

# --- Inserción de predicciones ---
insert_query = text("""
INSERT INTO pronosticos_ingresos 
(fecha, periodo, costo, modelo, mae, rmse, mape)
VALUES (:fecha, :periodo, :costo, :modelo, :mae, :rmse, :mape)
""")

with engine.begin() as conn:
    for registro in predicciones:
        conn.execute(insert_query, **registro)

print(f"{MESES_A_PREDECIR} predicciones futuras insertadas correctamente.")
