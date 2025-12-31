<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;

// Rutas Principales
Route::post('/sales', [SaleController::class, 'store']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products/{id}/restock', [ProductController::class, 'restock']);

/**
 * Reiniciar Simulación
 * 
 * ⚠️ **ZONA DE PELIGRO** ⚠️
 * 
 * Este endpoint borra todo el historial de ventas en MySQL, restablece el stock del producto
 * con ID 1 a 50 unidades, elimina la predicción de ventas y borra la memoria caché de Redis.
 * 
 * **Se usa únicamente para reiniciar la demo desde cero.** No usar en producción.
 * 
 * Acciones realizadas:
 * - Elimina todas las ventas registradas (truncate en tabla `sales`)
 * - Restablece el stock del producto ID 1 a 50 unidades
 * - Elimina la predicción de ventas del producto
 * - Borra la caché de Redis para la predicción del producto
 * 
 * @group Sistema
 * 
 * @response scenario="Simulación reiniciada exitosamente" {
 *   "message": "Simulación reiniciada"
 * }
 */
Route::post('/reset', function () {
    DB::table('sales')->truncate();
    
    DB::table('products')->where('id', 1)->update([
        'stock' => 50, 
        'sales_prediction' => null 
    ]);

    Redis::del('product_1_prediction');

    return response()->json(['message' => 'Simulación reiniciada']);
});