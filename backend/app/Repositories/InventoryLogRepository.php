<?php

namespace App\Repositories;

use App\Models\InventoryLog;
use Illuminate\Support\Collection;

class InventoryLogRepository
{
    protected $model;

    public function __construct(InventoryLog $model)
    {
        $this->model = $model;
    }

    /**
     * Obtener logs con filtros opcionales.
     */
    public function getLogs(array $filters = []): Collection
    {
        return $this->model->with('product:id,name')
            ->when(!empty($filters['from']), fn($q) => $q->where('created_at', '>=', $filters['from'] . ' 00:00:00'))
            ->when(!empty($filters['to']), fn($q) => $q->where('created_at', '<=', $filters['to'] . ' 23:59:59'))
            ->when(!empty($filters['product_id']), fn($q) => $q->where('product_id', $filters['product_id']))
            ->orderByDesc('created_at')
            ->get();
    }
}
