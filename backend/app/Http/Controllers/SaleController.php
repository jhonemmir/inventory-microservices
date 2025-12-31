<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Services\RabbitMQService;
use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * @group Gestión de Ventas
 * 
 * Endpoints para procesar transacciones de ventas y eventos asíncronos.
 * 
 * Cuando se registra una venta, el sistema:
 * 1. Guarda la transacción en MySQL
 * 2. Actualiza el stock del producto
 * 3. Envía un evento a RabbitMQ para procesamiento asíncrono por el AI Worker
 * 4. El AI Worker calcula y actualiza la predicción de agotamiento de stock
 */
class SaleController extends Controller
{
    /**
     * Registrar Nueva Venta
     * 
     * Este endpoint recibe una orden de compra, la guarda en MySQL y envía un evento asíncrono
     * a RabbitMQ ('sales_queue') para que el AI Worker procese la predicción de stock.
     * 
     * El evento en RabbitMQ contiene información sobre la venta y permite al worker de IA
     * recalcular las predicciones de cuándo se agotará el inventario basándose en la velocidad de ventas.
     * 
     * @bodyParam product_id integer required ID del producto a vender. Debe existir en la base de datos. Example: 1
     * @bodyParam quantity integer required Cantidad de unidades a vender. Debe ser mayor a 0. Example: 5
     * 
     * @response 201 scenario="Venta registrada exitosamente" {
     *   "message": "Venta registrada y notificada a la IA",
     *   "data": {
     *     "id": 15,
     *     "product_id": 1,
     *     "quantity": 5,
     *     "created_at": "2024-01-15T10:30:00.000000Z",
     *     "updated_at": "2024-01-15T10:30:00.000000Z"
     *   }
     * }
     * 
     * @response 422 scenario="Validación fallida" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "product_id": ["The product id field is required."],
     *     "quantity": ["The quantity must be at least 1."]
     *   }
     * }
     * 
     * @response 404 scenario="Producto no encontrado" {
     *   "message": "No query results for model [App\\Models\\Product] 999"
     * }
     */
    public function store(Request $request, RabbitMQService $mqService)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $sale = Sale::create([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity
        ]);

        $product = Product::find($request->product_id);
        $product->decrement('stock', $request->quantity);

        $mqService->publish([
            'event' => 'sale_created',
            'product_id' => $sale->product_id,
            'quantity' => $sale->quantity,
            'timestamp' => now()->toIso8601String()
        ]);

        return response()->json([
            'message' => 'Venta registrada y notificada a la IA',
            'data' => $sale
        ], 201);
    }
}