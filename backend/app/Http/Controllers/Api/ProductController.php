<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\InventoryLog;
use Exception;

class ProductController extends Controller
{
    public function updateStock(Request $request, $id)
    {
        try {
            Log::info('UpdateStock request', $request->all());

            $request->validate([
                'new_stock' => 'required|integer',
                'operation' => 'required|in:add,subtract',
                'source' => 'nullable|string|max:100',
                'note' => 'nullable|string|max:512',
            ]);

            return DB::transaction(function () use ($id, $request) {

                $product = Product::lockForUpdate()->find($id);
                if (!$product) {
                    Log::warning("Producto ID {$id} no encontrado");
                    return response()->json(['error' => 'Producto no encontrado'], 404);
                }

                $quantity = $request->input('new_stock');
                if ($quantity <= 0) {
                    Log::warning("Cantidad no válida: $quantity");
                    return response()->json(['error' => 'Cantidad debe ser positiva'], 422);
                }

                $oldStock = $product->stock;
                $newStock = $oldStock;

                if ($request->input('operation') === 'add') {
                    $newStock += $quantity;
                } else {
                    if ($oldStock < $quantity) {
                        Log::warning("Stock insuficiente para producto {$id}");
                        return response()->json(['error' => 'Stock insuficiente'], 422);
                    }
                    $newStock -= $quantity;
                }

                // Guardar producto
                $product->stock = $newStock;
                $product->save();

                // Registrar log
                InventoryLog::create([
                    'product_id' => $product->id,
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock,
                    'delta' => $newStock - $oldStock,
                    'source' => $request->input('source', 'API'),
                    'note' => $request->input('note'),
                ]);

                Log::info("✅ Stock actualizado para producto {$id}: {$oldStock} → {$newStock}");

                return response()->json([
                    'message' => 'Stock actualizado correctamente',
                    'product' => $product
                ], 200);
            });

        } catch (Exception $e) {
            Log::error('❌ Error en updateStock: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error interno del servidor',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
