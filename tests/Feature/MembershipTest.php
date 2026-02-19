<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Membership;
use App\Models\User;
use App\Services\MembershipService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MembershipTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashier;
    protected User $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'cashier']);
        Role::create(['name' => 'inventory']);

        $this->cashier = User::factory()->create();
        $this->cashier->assignRole('cashier');

        $this->inventory = User::factory()->create();
        $this->inventory->assignRole('inventory');
    }

    public function test_cashier_can_view_memberships(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('memberships.index'));
        $response->assertOk();
    }

    public function test_inventory_cannot_view_memberships(): void
    {
        $response = $this->actingAs($this->inventory)->get(route('memberships.index'));
        $response->assertStatus(403);
    }

    public function test_membership_service_creates_one_year_membership(): void
    {
        $this->actingAs($this->cashier);
        $customer = Customer::create(['citizen_id' => '1111111111', 'name' => 'Test']);
        $service = app(MembershipService::class);

        $membership = $service->create($customer);

        $this->assertEquals('active', $membership->status);
        $this->assertTrue($membership->expires_at->isNextYear() || $membership->expires_at->greaterThan(Carbon::now()));
        $this->assertEquals(
            Carbon::now()->addYear()->toDateString(),
            $membership->expires_at->toDateString()
        );
    }

    public function test_membership_renewal_extends_expires_at(): void
    {
        $this->actingAs($this->cashier);
        $customer = Customer::create(['citizen_id' => '2222222222', 'name' => 'Test 2']);
        $service = app(MembershipService::class);

        $membership = $service->create($customer);
        $originalExpiry = $membership->expires_at->copy();

        $renewed = $service->renew($membership);
        $this->assertEquals(
            $originalExpiry->addYear()->toDateString(),
            $renewed->expires_at->toDateString()
        );
    }

    public function test_cannot_create_second_membership_for_same_customer(): void
    {
        $this->actingAs($this->cashier);
        $customer = Customer::create(['citizen_id' => '3333333333', 'name' => 'Test 3']);
        $service = app(MembershipService::class);
        $service->create($customer);

        $response = $this->actingAs($this->cashier)->post(route('memberships.store'), [
            'customer_id' => $customer->id,
        ]);

        $response->assertSessionHasErrors('customer_id');
    }
}
