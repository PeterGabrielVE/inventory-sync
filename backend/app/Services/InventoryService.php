<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class InventoryService
{
    protected $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function updateStock(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $product = $this->productRepo->findAndLock($id);
            if (!$product) {
                throw new Exception('Producto no encontrado', 404);
            }

            $quantity = $data['new_stock'];
            if ($quantity <= 0) {
                throw new Exception('Cantidad debe ser positiva', 422);
            }

            $oldStock = $product->stock;
            $newStock = $oldStock;

            if ($data['operation'] === 'add') {
                $newStock += $quantity;
            } else {
                if ($oldStock < $quantity) {
                    throw new Exception('Stock insuficiente', 422);
                }
                $newStock -= $quantity;
            }

            $product->stock = $newStock;
            $this->productRepo->save($product);

            InventoryLog::create([
                'product_id' => $product->id,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'delta' => $newStock - $oldStock,
                'source' => $data['source'] ?? 'API',
                'note' => $data['note'] ?? null,
            ]);

            Log::info(" Stock actualizado para producto {$id}: {$oldStock} → {$newStock}");

            return $product;
        });
    }
}
