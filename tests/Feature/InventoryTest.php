<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $inventoryUser;
    protected User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'cashier']);
        Role::create(['name' => 'inventory']);

        $this->inventoryUser = User::factory()->create();
        $this->inventoryUser->assignRole('inventory');

        $this->cashier = User::factory()->create();
        $this->cashier->assignRole('cashier');
    }

    public function test_inventory_can_view_products(): void
    {
        $response = $this->actingAs($this->inventoryUser)->get(route('products.index'));
        $response->assertOk();
    }

    public function test_cashier_cannot_create_product(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('products.create'));
        $response->assertStatus(403);
    }

    public function test_inventory_can_create_product(): void
    {
        $response = $this->actingAs($this->inventoryUser)->post(route('products.store'), [
            'barcode'      => 'NEW001',
            'name'         => 'New Product',
            'type'         => 'sale',
            'price'        => 100,
            'stock_qty'    => 10,
            'available_qty'=> 0,
            'is_active'    => true,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['barcode' => 'NEW001']);
    }

    public function test_stock_in_increases_qty(): void
    {
        $this->actingAs($this->inventoryUser);

        $product = Product::create([
            'barcode' => 'AMMO001',
            'name' => 'Ammo',
            'type' => 'sale',
            'price' => 450,
            'stock_qty' => 10,
            'available_qty' => 0,
            'is_active' => true,
        ]);

        $service = app(InventoryService::class);
        $service->stockIn($product, 20, 'Restock');

        $product->refresh();
        $this->assertEquals(30, $product->stock_qty);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'qty' => 20,
        ]);
    }

    public function test_stock_in_also_increases_available_for_rent_type(): void
    {
        $this->actingAs($this->inventoryUser);

        $product = Product::create([
            'barcode' => 'GUN001',
            'name' => 'Glock',
            'type' => 'rent',
            'price' => 200,
            'deposit' => 1000,
            'stock_qty' => 3,
            'available_qty' => 3,
            'is_active' => true,
        ]);

        $service = app(InventoryService::class);
        $service->stockIn($product, 2, 'New guns');

        $product->refresh();
        $this->assertEquals(5, $product->stock_qty);
        $this->assertEquals(5, $product->available_qty);
    }

    public function test_adjust_stock(): void
    {
        $this->actingAs($this->inventoryUser);

        $product = Product::create([
            'barcode' => 'ADJ001',
            'name' => 'Test',
            'type' => 'sale',
            'price' => 50,
            'stock_qty' => 20,
            'available_qty' => 0,
            'is_active' => true,
        ]);

        $service = app(InventoryService::class);
        $service->adjust($product, 15, 'Stock count correction');

        $product->refresh();
        $this->assertEquals(15, $product->stock_qty);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'adjust',
            'qty' => -5,
        ]);
    }
}
