<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'cashier', 'inventory'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@gunrange.local'],
            [
                'name'       => 'Admin',
                'citizen_id' => '1100000000001',
                'password'   => Hash::make('password'),
            ]
        );
        $admin->syncRoles(['admin']);

        // Cashier user
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@gunrange.local'],
            [
                'name'       => 'Cashier Staff',
                'citizen_id' => '1100000000002',
                'password'   => Hash::make('password'),
            ]
        );
        $cashier->syncRoles(['cashier']);

        // Inventory user
        $inventory = User::firstOrCreate(
            ['email' => 'inventory@gunrange.local'],
            [
                'name'       => 'Inventory Staff',
                'citizen_id' => '1100000000003',
                'password'   => Hash::make('password'),
            ]
        );
        $inventory->syncRoles(['inventory']);
    }
}
