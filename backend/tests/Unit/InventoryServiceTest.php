<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\InventoryLog;
use App\Repositories\ProductRepository;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Exception;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InventoryService $service;
    protected ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository();
        $this->service = new InventoryService($this->repository);
    }

    /** @test */
    public function it_adds_stock_successfully()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $updatedProduct = $this->service->updateStock($product->id, [
            'new_stock' => 5,
            'operation' => 'add',
            'source' => 'unit_test',
            'note' => 'Prueba agregar stock'
        ]);

        $this->assertEquals(15, $updatedProduct->stock);

        $this->assertDatabaseHas('inventory_logs', [
            'product_id' => $product->id,
            'old_stock' => 10,
            'new_stock' => 15,
            'delta' => 5
        ]);
    }

    /** @test */
    public function it_subtracts_stock_successfully()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $updatedProduct = $this->service->updateStock($product->id, [
            'new_stock' => 3,
            'operation' => 'subtract',
            'source' => 'unit_test',
            'note' => 'Prueba restar stock'
        ]);

        $this->assertEquals(7, $updatedProduct->stock);

        $this->assertDatabaseHas('inventory_logs', [
            'product_id' => $product->id,
            'old_stock' => 10,
            'new_stock' => 7,
            'delta' => -3
        ]);
    }

    /** @test */
    public function it_fails_when_stock_is_insufficient()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Stock insuficiente');

        $product = Product::factory()->create(['stock' => 2]);

        $this->service->updateStock($product->id, [
            'new_stock' => 5,
            'operation' => 'subtract',
        ]);
    }

    /** @test */
    public function it_fails_when_product_does_not_exist()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Producto no encontrado');

        $this->service->updateStock(9999, [
            'new_stock' => 1,
            'operation' => 'add',
        ]);
    }

    /** @test */
    public function transaction_rolls_back_on_exception()
    {
        $product = Product::factory()->create(['stock' => 5]);

        try {
            $this->service->updateStock($product->id, [
                'new_stock' => -1, // inválido
                'operation' => 'add'
            ]);
        } catch (Exception $e) {
            // Verificamos que el stock no se actualizó
            $freshProduct = Product::find($product->id);
            $this->assertEquals(5, $freshProduct->stock);
        }
    }
}
