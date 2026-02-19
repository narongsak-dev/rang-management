<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Services\MembershipService;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $membershipService = app(MembershipService::class);

        $customers = [
            ['citizen_id' => '3100000000001', 'name' => 'John Doe',     'phone' => '0811234567'],
            ['citizen_id' => '3100000000002', 'name' => 'Jane Smith',   'phone' => '0829876543'],
            ['citizen_id' => '3100000000003', 'name' => 'Bob Johnson',  'phone' => '0851112222'],
        ];

        foreach ($customers as $data) {
            $customer = Customer::firstOrCreate(['citizen_id' => $data['citizen_id']], $data);
            if (!$customer->activeMembership) {
                $membershipService->create($customer);
            }
        }
    }
}
