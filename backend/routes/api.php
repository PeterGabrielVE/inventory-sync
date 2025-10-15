<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InventoryLogController;

Route::patch('/products/{id}/stock', [ProductController::class, 'updateStock']);
Route::get('/inventory-logs', [InventoryLogController::class, 'index']);
