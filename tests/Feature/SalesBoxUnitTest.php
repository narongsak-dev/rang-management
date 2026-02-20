<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\User;
use App\Services\POSService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesBoxUnitTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashier;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'cashier']);
        Role::create(['name' => 'inventory']);
        $this->cashier = User::factory()->create();
        $this->cashier->assignRole('cashier');
    }

    public function test_unit_sale_reduces_stock_by_qty(): void
    {
        $product = Product::create([
            'barcode' => 'UNIT001',
            'name' => 'Ammo Unit',
            'type' => 'sale',
            'price' => 50.00,
            'price_per_unit' => 50.00,
            'stock_qty' => 100,
            'available_qty' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->cashier);

        // Use POSService directly
        $service = app(POSService::class);
        $sale = $service->checkout([
            'items' => [[
                'product_id' => $product->id,
                'qty' => 3,
                'unit_type' => 'unit',
                'unit_price' => 50.00,
                'line_total' => 150.00,
                'is_rental' => false,
                'serials' => [],
            ]],
            'payment_method' => 'cash',
            'discount' => 0,
        ]);

        $product->refresh();
        $this->assertEquals(97, $product->stock_qty);
    }

    public function test_box_sale_reduces_stock_by_qty_times_units_per_box(): void
    {
        $product = Product::create([
            'barcode' => 'BOX001',
            'name' => 'Ammo Box',
            'type' => 'sale',
            'price' => 50.00,
            'price_per_unit' => 50.00,
            'price_per_box' => 500.00,
            'units_per_box' => 20,
            'stock_qty' => 200,
            'available_qty' => 0,
            'is_active' => true,
        ]);

        $service = app(POSService::class);
        $this->actingAs($this->cashier);
        $sale = $service->checkout([
            'items' => [[
                'product_id' => $product->id,
                'qty' => 2,
                'unit_type' => 'box',
                'unit_price' => 500.00,
                'line_total' => 1000.00,
                'is_rental' => false,
                'serials' => [],
            ]],
            'payment_method' => 'cash',
            'discount' => 0,
        ]);

        $product->refresh();
        // 2 boxes × 20 units = 40 units deducted from stock_qty 200
        $this->assertEquals(160, $product->stock_qty);
    }

    public function test_rental_serial_required_for_gun(): void
    {
        $customer = Customer::create([
            'citizen_id' => '1234512345001',
            'name' => 'Test Renter',
        ]);

        $product = Product::create([
            'barcode' => 'GUN_SN_001',
            'name' => 'Serial Gun',
            'type' => 'rent',
            'price' => 300.00,
            'deposit' => 2000.00,
            'stock_qty' => 5,
            'available_qty' => 5,
            'requires_serial' => true,
            'is_active' => true,
        ]);

        // Create a serial
        $serial = ProductSerial::create([
            'product_id' => $product->id,
            'serial_no' => 'SN-001',
            'status' => 'available',
        ]);

        $service = app(POSService::class);
        $this->actingAs($this->cashier);

        $sale = $service->checkout([
            'items' => [[
                'product_id' => $product->id,
                'qty' => 1,
                'unit_type' => 'unit',
                'unit_price' => 300.00,
                'line_total' => 300.00,
                'is_rental' => true,
                'serials' => ['SN-001'],
            ]],
            'payment_method' => 'cash',
            'discount' => 0,
        ], $customer);

        // Serial should be marked rented
        $serial->refresh();
        $this->assertEquals('rented', $serial->status);

        // Available qty decreased
        $product->refresh();
        $this->assertEquals(4, $product->available_qty);
        $this->assertEquals(5, $product->stock_qty);
    }

    public function test_return_serial_sets_available(): void
    {
        $customer = Customer::create([
            'citizen_id' => '1234512345002',
            'name' => 'Test Renter 2',
        ]);

        $product = Product::create([
            'barcode' => 'GUN_SN_002',
            'name' => 'Serial Gun 2',
            'type' => 'rent',
            'price' => 300.00,
            'deposit' => 2000.00,
            'stock_qty' => 5,
            'available_qty' => 5,
            'requires_serial' => true,
            'is_active' => true,
        ]);

        $serial = ProductSerial::create([
            'product_id' => $product->id,
            'serial_no' => 'SN-002',
            'status' => 'available',
        ]);

        $service = app(POSService::class);
        $this->actingAs($this->cashier);

        $sale = $service->checkout([
            'items' => [[
                'product_id' => $product->id,
                'qty' => 1,
                'unit_type' => 'unit',
                'unit_price' => 300.00,
                'line_total' => 300.00,
                'is_rental' => true,
                'serials' => ['SN-002'],
            ]],
            'payment_method' => 'cash',
            'discount' => 0,
        ], $customer);

        $serial->refresh();
        $this->assertEquals('rented', $serial->status);

        // Now return
        $rental = $sale->rental;
        $rentalItem = $rental->items()->first();

        $rentalService = app(\App\Services\RentalService::class);
        $rentalService->returnItem($rentalItem);

        $serial->refresh();
        $this->assertEquals('available', $serial->status);
    }

    public function test_dashboard_stats_returns_json(): void
    {
        $this->actingAs($this->cashier);
        $response = $this->getJson(route('dashboard.stats'));
        $response->assertOk()
            ->assertJsonStructure([
                'sales_7days',
                'monthly_by_type',
                'cards' => ['today_revenue', 'open_rentals', 'low_stock'],
            ]);
    }

    public function test_customer_search_by_citizen_id(): void
    {
        Customer::create([
            'citizen_id' => '9876543210001',
            'name' => 'Search Test',
            'phone' => '0811111111',
        ]);

        $this->actingAs($this->cashier);
        $response = $this->getJson(route('customers.search') . '?citizen_id=9876543210001');
        $response->assertOk()
            ->assertJson(['found' => true])
            ->assertJsonPath('customer.citizen_id', '9876543210001');
    }
}
