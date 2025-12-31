<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB; // Necesario para el reset


/**
 * @group Gestión de Inventario
 * 
 * APIs para consultar y gestionar productos, stock y predicciones de agotamiento.
 * 
 * Las predicciones de IA se almacenan tanto en Redis (para acceso rápido) como en MySQL
 * (para persistencia). El endpoint muestra de dónde proviene la predicción actual.
 */
class ProductController extends Controller
{

    /**
     * Consultar Producto
     * 
     * Obtiene la información actual del producto, incluyendo la predicción de agotamiento de stock
     * calculada por el AI Worker y la fuente de los datos (Redis o MySQL).
     * 
     * La respuesta incluye:
     * - Información básica del producto (nombre, precio, stock actual)
     * - Predicción de IA sobre cuándo se agotará el inventario
     * - Fuente de la predicción (Redis para acceso rápido o MySQL como respaldo)
     * 
     * @urlParam id integer required ID del producto a consultar. Example: 1
     * 
     * @response scenario="Producto con predicción en Redis" {
     *   "product": "Camisa Laravel",
     *   "price": "25.00",
     *   "stock": 35,
     *   "ai_prediction": "Se agota en 13.5 días",
     *   "source": "Redis (RAM)"
     * }
     * 
     * @response scenario="Producto con predicción en MySQL" {
     *   "product": "Pantalón Django",
     *   "price": "45.00",
     *   "stock": 20,
     *   "ai_prediction": "⚠️ ¡CRÍTICO! Se agota hoy",
     *   "source": "MySQL (Disk)"
     * }
     * 
     * @response scenario="Producto sin predicción aún" {
     *   "product": "Zapatos FastAPI",
     *   "price": "60.00",
     *   "stock": 50,
     *   "ai_prediction": "Calculando...",
     *   "source": "MySQL (Disk)"
     * }
     * 
     * @response 404 scenario="Producto no encontrado" {
     *   "message": "No query results for model [App\\Models\\Product] 999"
     * }
     */

    public function show($id)
    {
        $redisKey = "product_{$id}_prediction";

        $prediction = Redis::get($redisKey);

        $product = Product::findOrFail($id);

        if (!$prediction) {
            $prediction = $product->sales_prediction ?? 'Calculando...';
        }

        return response()->json([
            'product' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'ai_prediction' => $prediction, 
            'source' => Redis::exists($redisKey) ? 'Redis (RAM)' : 'MySQL (Disk)'
        ]);
    }

    /**
     * Reabastecer Stock
     * 
     * Agrega unidades al inventario del producto y dispara un evento a RabbitMQ para que
     * el AI Worker recalcule la predicción de agotamiento basándose en el nuevo stock.
     * 
     * Este endpoint es útil para simular reabastecimientos y ver cómo afectan a las
     * predicciones de la IA sobre el agotamiento del inventario.
     * 
     * @urlParam id integer required ID del producto a reabastecer. Example: 1
     * 
     * @bodyParam quantity integer required Cantidad de unidades a agregar al stock. Debe ser mayor a 0. Example: 20
     * 
     * @response scenario="Stock reabastecido exitosamente" {
     *   "message": "Stock actualizado",
     *   "new_stock": 70
     * }
     * 
     * @response 422 scenario="Validación fallida" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "quantity": ["The quantity field is required."]
     *   }
     * }
     * 
     * @response 404 scenario="Producto no encontrado" {
     *   "message": "No query results for model [App\\Models\\Product] 999"
     * }
     */

    public function restock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($id);
        
        $product->increment('stock', $request->quantity);

        $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('sales_queue', false, false, false, false);

        $msg = new \PhpAmqpLib\Message\AMQPMessage(json_encode([
            'event' => 'stock_added',
            'product_id' => $product->id,
            'timestamp' => now()->toIso8601String()
        ]));

        $channel->basic_publish($msg, '', 'sales_queue');
        $channel->close();
        $connection->close();

        return response()->json([
            'message' => 'Stock actualizado',
            'new_stock' => $product->stock
        ]);
    }
}