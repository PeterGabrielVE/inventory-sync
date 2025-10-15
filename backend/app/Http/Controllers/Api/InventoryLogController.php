<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryLog;
<<<<<<< Updated upstream
=======
use App\Services\InventoryService;
>>>>>>> Stashed changes

class InventoryLogController extends Controller
{
    protected $service;

    public function __construct(InventoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
<<<<<<< Updated upstream
        $query = InventoryLog::query();

        // Filtros opcionales
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('source')) {
            $query->where('source', $request->source);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        return response()->json($logs);
=======
        $filters = $request->only(['from', 'to', 'product_id']);
        return response()->json($this->service->getInventoryLogs($filters));
>>>>>>> Stashed changes
    }
}
