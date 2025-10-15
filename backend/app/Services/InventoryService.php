<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\InventoryLogRepository;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class InventoryService
{
    protected $productRepo;
    protected $inventoryRepo;

    public function __construct(ProductRepository $productRepo, InventoryLogRepository $inventoryRepo)
    {
        $this->productRepo = $productRepo;
        $this->inventoryRepo = $inventoryRepo;
    }

    /**
     * Actualizar stock de un producto y registrar en logs.
     */
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("Stock actualizado para producto {$id}: {$oldStock} → {$newStock}");

            return $product;
        });
    }

    /**
     * Obtener logs de inventario filtrados y formateados.
     */
    public function getInventoryLogs(array $filters = []): array
    {
        $logs = $this->inventoryRepo->getLogs($filters);

        return $logs->map(fn($log) => $this->formatLog($log))->toArray();
    }

    /**
     * Formatear un log para la respuesta API.
     */
    private function formatLog(InventoryLog $log): array
    {
        $old = $log->old_stock ?? 0;
        $new = $log->new_stock ?? 0;
        $delta = $new - $old;

        return [
            'id' => $log->id,
            'product_id' => $log->product_id,
            'product_name' => $log->product->name ?? 'N/A',
            'old_stock' => $old,
            'new_stock' => $new,
            'delta' => $delta,
            'type' => $delta > 0 ? 'Entrada' : ($delta < 0 ? 'Salida' : 'Sin cambio'),
            'source' => $log->source ?? '',
            'note' => $log->note ?? '',
            'created_at' => Carbon::parse($log->created_at)->format('d-m-Y H:i:s'),
        ];
    }
}
