<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['barcode' => 'P001', 'name' => 'Shooting Lane Fee (30 min)', 'type' => 'fee',     'price' => 150, 'stock_qty' => 0,  'available_qty' => 0],
            ['barcode' => 'P002', 'name' => '9mm Ammunition (50 rounds)', 'type' => 'sale',    'price' => 450, 'stock_qty' => 500,'available_qty' => 0],
            ['barcode' => 'P003', 'name' => 'Glock 17 Pistol Rental',     'type' => 'rent',    'price' => 200, 'deposit' => 1000, 'stock_qty' => 5,  'available_qty' => 5],
            ['barcode' => 'P004', 'name' => 'AR-15 Rifle Rental',         'type' => 'rent',    'price' => 350, 'deposit' => 2000, 'stock_qty' => 3,  'available_qty' => 3],
            ['barcode' => 'P005', 'name' => 'Safety Briefing Service',    'type' => 'service', 'price' => 100, 'stock_qty' => 0,  'available_qty' => 0],
            ['barcode' => 'P006', 'name' => 'Eye Protection',             'type' => 'rent',    'price' => 50,  'deposit' => 200,  'stock_qty' => 20, 'available_qty' => 20],
            ['barcode' => 'P007', 'name' => 'Ear Protection',             'type' => 'rent',    'price' => 50,  'deposit' => 200,  'stock_qty' => 20, 'available_qty' => 20],
            ['barcode' => 'P008', 'name' => '.45 ACP Ammunition (50 rnd)','type' => 'sale',    'price' => 550, 'stock_qty' => 300,'available_qty' => 0],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['barcode' => $product['barcode']], array_merge($product, ['is_active' => true]));
        }
    }
}
