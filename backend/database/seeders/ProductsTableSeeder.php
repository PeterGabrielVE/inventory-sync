<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop HP Pavilion',
                'sku' => 'HP-'.Str::upper(Str::random(6)),
                'stock' => 20,
                'price' => 850.00,
            ],
            [
                'name' => 'Mouse Logitech',
                'sku' => 'MOU-'.Str::upper(Str::random(6)),
                'stock' => 50,
                'price' => 25.99,
            ],
            [
                'name' => 'Teclado Mecánico Redragon',
                'sku' => 'KEY-'.Str::upper(Str::random(6)),
                'stock' => 30,
                'price' => 59.90,
            ],
            [
                'name' => 'Monitor Samsung 24"',
                'sku' => 'MON-'.Str::upper(Str::random(6)),
                'stock' => 15,
                'price' => 199.99,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
