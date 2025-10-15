<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\InventoryLog;
use App\Services\InventoryService;
use App\Http\Requests\UpdateStockRequest;
use Exception;

class ProductController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function updateStock(UpdateStockRequest $request, $id)
    {
        try {

            $validated = $request->validated();

            $product = $this->inventoryService->updateStock($id, $validated);

            return response()->json([
                'message' => 'Stock actualizado correctamente',
                'product' => $product
            ], 200);
        } catch (Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            Log::error('Error en updateStock: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], $status);
        }
    }
}
