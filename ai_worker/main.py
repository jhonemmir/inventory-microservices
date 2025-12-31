import pika
import json
import time
import pandas as pd
import logging
import os
import redis
import sys
from sqlalchemy import create_engine, text

os.makedirs('logs', exist_ok=True)

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] %(message)s',
    handlers=[
        logging.FileHandler("logs/worker.log"),
        logging.StreamHandler(sys.stdout)
    ]
)
logger = logging.getLogger(__name__)

logger.info("⏳ Iniciando AI Worker... Esperando servicios...")
time.sleep(15) 

redis_client = redis.Redis(host='redis', port=6379, db=0)

DB_CONNECTION_STR = 'mysql+mysqlconnector://root:rootpassword@db/inventory_db'
db_engine = create_engine(DB_CONNECTION_STR)

RABBIT_HOST = 'rabbitmq'
QUEUE_NAME = 'sales_queue'

def get_db_connection():
    return db_engine.connect()

def predict_stock_depletion(product_id):
    logger.info(f"📊 Analizando producto ID {product_id}...")
    
    try:
        query = text("SELECT * FROM sales WHERE product_id = :pid ORDER BY created_at ASC")
        df = pd.read_sql(query, db_engine, params={"pid": product_id})
        
        if df.empty:
            logger.warning(f"⚠️ Producto {product_id}: Insuficientes datos para predecir.")
            return "Insuficientes datos"

        df['created_at'] = pd.to_datetime(df['created_at'])
        
        total_sold = df['quantity'].sum()
        start_time = df['created_at'].min()
        end_time = pd.Timestamp.now()
        
        hours_elapsed = (end_time - start_time).total_seconds() / 3600
        if hours_elapsed < 24:
            hours_elapsed = 24 
                    
            velocity_per_hour = total_sold / hours_elapsed
            velocity_per_hour = total_sold / hours_elapsed
        
        with get_db_connection() as conn:
            result = conn.execute(text("SELECT stock, name FROM products WHERE id = :pid"), {"pid": product_id}).fetchone()
            current_stock = result[0]
            product_name = result[1]

        logger.info(f"   Producto: {product_name} | Stock: {current_stock} | Velocidad: {velocity_per_hour:.2f} u/hora")

        if velocity_per_hour <= 0:
            prediction = "Ventas detenidas"
        else:
            hours_left = current_stock / velocity_per_hour
            days_left = hours_left / 24
            
            if days_left < 1:
                prediction = "⚠️ ¡CRÍTICO! Se agota hoy"
            else:
                prediction = f"Se agota en {days_left:.1f} días"

        return prediction

    except Exception as e:
        logger.error(f"❌ Error calculando predicción: {e}")
        return "Error en cálculo"

def callback(ch, method, properties, body):
    try:
        data = json.loads(body)
        logger.info(f"📩 Evento recibido: Venta de producto {data['product_id']}")
        
        prediction_text = predict_stock_depletion(data['product_id'])
        
        with get_db_connection() as conn:
            update_query = text("UPDATE products SET sales_prediction = :pred WHERE id = :pid")
            conn.execute(update_query, {"pred": prediction_text, "pid": data['product_id']})
            conn.commit()
            
            redis_key = f"product_{data['product_id']}_prediction"
            redis_client.setex(redis_key, 86400, prediction_text)

        logger.info(f"✅ BD Actualizada con predicción: {prediction_text}")

        ch.basic_ack(delivery_tag=method.delivery_tag)

    except Exception as e:
        logger.error(f"🔥 Error procesando mensaje: {e}")

def main():
    while True:
        try:
            connection = pika.BlockingConnection(pika.ConnectionParameters(host=RABBIT_HOST))
            channel = connection.channel()
            channel.queue_declare(queue=QUEUE_NAME, durable=False)
            channel.basic_consume(queue=QUEUE_NAME, on_message_callback=callback)
            
            logger.info(' [*] AI Worker escuchando ventas... Logs en: logs/worker.log')
            channel.start_consuming()
            
        except pika.exceptions.AMQPConnectionError:
            logger.warning("⚠️ RabbitMQ no está listo, reintentando en 5 segundos...")
            time.sleep(5)
        except Exception as e:
            logger.critical(f"❌ Error fatal inesperado: {e}")
            time.sleep(5)

if __name__ == '__main__':
    main()