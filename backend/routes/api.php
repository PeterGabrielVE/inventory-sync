<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::patch('/products/{id}/stock', [ProductController::class, 'updateStock']);
