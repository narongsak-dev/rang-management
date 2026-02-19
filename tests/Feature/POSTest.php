<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Services\POSService;
use App\Services\ReceiptService;
use App\Services\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class POSTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashier;
    protected User $admin;
    protected User $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'cashier']);
        Role::create(['name' => 'inventory']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->cashier = User::factory()->create();
        $this->cashier->assignRole('cashier');

        $this->inventory = User::factory()->create();
        $this->inventory->assignRole('inventory');
    }

    public function test_cashier_can_access_pos(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('pos.index'));
        $response->assertOk();
    }

    public function test_inventory_cannot_access_pos(): void
    {
        $response = $this->actingAs($this->inventory)->get(route('pos.index'));
        $response->assertStatus(403);
    }

    public function test_pos_can_scan_product(): void
    {
        $product = Product::create([
            'barcode' => 'TEST001',
            'name' => 'Test Product',
            'type' => 'sale',
            'price' => 100.00,
            'stock_qty' => 10,
            'available_qty' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->cashier)
            ->postJson(route('pos.scan'), ['barcode' => 'TEST001']);

        $response->assertOk()
            ->assertJson(['found' => true])
            ->assertJsonPath('product.barcode', 'TEST001');
    }

    public function test_pos_returns_404_for_unknown_barcode(): void
    {
        $response = $this->actingAs($this->cashier)
            ->postJson(route('pos.scan'), ['barcode' => 'UNKNOWN']);

        $response->assertStatus(404)
            ->assertJson(['found' => false]);
    }

    public function test_checkout_sale_item_reduces_stock(): void
    {
        $product = Product::create([
            'barcode' => 'AMMO001',
            'name' => 'Ammo',
            'type' => 'sale',
            'price' => 450.00,
            'stock_qty' => 50,
            'available_qty' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->cashier)
            ->postJson(route('pos.checkout'), [
                'items' => [[
                    'product_id' => $product->id,
                    'qty' => 2,
                    'unit_price' => 450.00,
                    'is_rental' => false,
                ]],
                'payment_method' => 'cash',
                'discount' => 0,
            ]);

        $response->assertOk()->assertJsonPath('success', true);
        $product->refresh();
        $this->assertEquals(48, $product->stock_qty);
    }

    public function test_checkout_rental_reduces_available_qty(): void
    {
        $customer = Customer::create([
            'citizen_id' => '1234567890001',
            'name' => 'Test Customer',
            'phone' => '0800000000',
        ]);

        $product = Product::create([
            'barcode' => 'GUN001',
            'name' => 'Glock',
            'type' => 'rent',
            'price' => 200.00,
            'deposit' => 1000.00,
            'stock_qty' => 5,
            'available_qty' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->cashier)
            ->postJson(route('pos.checkout'), [
                'items' => [[
                    'product_id' => $product->id,
                    'qty' => 1,
                    'unit_price' => 200.00,
                    'is_rental' => true,
                ]],
                'payment_method' => 'cash',
                'discount' => 0,
                'customer_id' => $customer->id,
            ]);

        $response->assertOk()->assertJsonPath('success', true);
        $product->refresh();
        $this->assertEquals(4, $product->available_qty);
        $this->assertEquals(5, $product->stock_qty); // stock unchanged
    }

    public function test_cannot_rent_if_insufficient_available(): void
    {
        $customer = Customer::create([
            'citizen_id' => '1234567890002',
            'name' => 'Test Customer 2',
        ]);

        $product = Product::create([
            'barcode' => 'GUN002',
            'name' => 'AR-15',
            'type' => 'rent',
            'price' => 350.00,
            'deposit' => 2000.00,
            'stock_qty' => 1,
            'available_qty' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->cashier)
            ->postJson(route('pos.checkout'), [
                'items' => [[
                    'product_id' => $product->id,
                    'qty' => 1,
                    'unit_price' => 350.00,
                    'is_rental' => true,
                ]],
                'payment_method' => 'cash',
                'discount' => 0,
                'customer_id' => $customer->id,
            ]);

        $response->assertStatus(422)->assertJsonStructure(['error']);
    }
}
