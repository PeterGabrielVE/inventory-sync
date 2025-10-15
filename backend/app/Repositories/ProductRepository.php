<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function findAndLock(int $id): ?Product
    {
        return Product::lockForUpdate()->find($id);
    }

    public function save(Product $product): void
    {
        $product->save();
    }
}
