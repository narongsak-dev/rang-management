<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\Sale;
use App\Models\User;
use App\Services\RentalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RentalReturnTest extends TestCase
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

    public function test_returning_item_increases_available_qty(): void
    {
        $this->actingAs($this->cashier);

        $product = Product::create([
            'barcode' => 'GUN001',
            'name' => 'Glock',
            'type' => 'rent',
            'price' => 200.00,
            'deposit' => 1000.00,
            'stock_qty' => 5,
            'available_qty' => 4,
            'is_active' => true,
        ]);

        $customer = Customer::create(['citizen_id' => '1111111111', 'name' => 'Test']);

        $sale = Sale::create([
            'order_no' => 'ORD-TEST-001',
            'customer_id' => $customer->id,
            'staff_id' => $this->cashier->id,
            'subtotal' => 200,
            'discount' => 0,
            'total' => 200,
            'payment_method' => 'cash',
            'paid_at' => now(),
        ]);

        $rental = Rental::create([
            'sale_id' => $sale->id,
            'customer_id' => $customer->id,
            'rented_at' => now(),
            'status' => 'open',
        ]);

        $rentalItem = RentalItem::create([
            'rental_id' => $rental->id,
            'product_id' => $product->id,
            'qty' => 1,
            'deposit_amount' => 1000,
        ]);

        $service = app(RentalService::class);
        $service->returnItem($rentalItem);

        $product->refresh();
        $this->assertEquals(5, $product->available_qty);

        $rentalItem->refresh();
        $this->assertNotNull($rentalItem->returned_at);

        $rental->refresh();
        $this->assertEquals('returned', $rental->status);
    }

    public function test_rental_status_remains_open_if_items_pending(): void
    {
        $this->actingAs($this->cashier);

        $product1 = Product::create(['barcode' => 'G1', 'name' => 'Glock', 'type' => 'rent', 'price' => 200, 'deposit' => 1000, 'stock_qty' => 5, 'available_qty' => 3, 'is_active' => true]);
        $product2 = Product::create(['barcode' => 'G2', 'name' => 'Rifle', 'type' => 'rent', 'price' => 350, 'deposit' => 2000, 'stock_qty' => 3, 'available_qty' => 2, 'is_active' => true]);
        $customer = Customer::create(['citizen_id' => '2222222222', 'name' => 'Test 2']);

        $sale = Sale::create(['order_no' => 'ORD-TEST-002', 'customer_id' => $customer->id, 'staff_id' => $this->cashier->id, 'subtotal' => 550, 'discount' => 0, 'total' => 550, 'payment_method' => 'cash', 'paid_at' => now()]);
        $rental = Rental::create(['sale_id' => $sale->id, 'customer_id' => $customer->id, 'rented_at' => now(), 'status' => 'open']);

        $item1 = RentalItem::create(['rental_id' => $rental->id, 'product_id' => $product1->id, 'qty' => 1, 'deposit_amount' => 1000]);
        $item2 = RentalItem::create(['rental_id' => $rental->id, 'product_id' => $product2->id, 'qty' => 1, 'deposit_amount' => 2000]);

        $service = app(RentalService::class);
        $service->returnItem($item1);

        $rental->refresh();
        $this->assertEquals('open', $rental->status); // still open
    }
}
