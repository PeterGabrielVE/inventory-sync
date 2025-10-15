<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryLog;
use App\Services\InventoryService;

class InventoryLogController extends Controller
{
    protected $service;

    public function __construct(InventoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['from', 'to', 'product_id']);
        return response()->json($this->service->getInventoryLogs($filters));
    }
}
