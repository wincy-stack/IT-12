<?php

namespace Database\Seeders;

use App\Models\Supply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── System Users (admin & staff only – no demo customers) ──────
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin Manager', 'password' => Hash::make('admin123'), 'role' => 'admin']
        );

        User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            ['name' => 'Staff Member', 'password' => Hash::make('staff123'), 'role' => 'staff']
        );

        // ── Supplies ───────────────────────────────────────────────────
        $supplies = [
            ['name' => '5-Gallon Water Container',  'category' => 'Container',  'quantity' => 120, 'minimum_stock' => 20, 'unit_price' => 80.00,  'unit' => 'pcs',    'description' => 'Refillable 5-gallon round container'],
            ['name' => 'Purified Water (Bulk)',      'category' => 'Water',      'quantity' => 500, 'minimum_stock' => 100,'unit_price' => 10.00,  'unit' => 'liters', 'description' => 'Purified drinking water stock'],
            ['name' => 'Bottle Caps',                'category' => 'Packaging',  'quantity' => 300, 'minimum_stock' => 50, 'unit_price' => 2.00,   'unit' => 'pcs',    'description' => 'Screw-type caps for 5-gal bottles'],
            ['name' => 'Chlorine Tablets',           'category' => 'Chemical',   'quantity' => 8,   'minimum_stock' => 10, 'unit_price' => 150.00, 'unit' => 'pcs',    'description' => 'Water disinfection tablets'],
            ['name' => 'Delivery Crates',            'category' => 'Equipment',  'quantity' => 30,  'minimum_stock' => 10, 'unit_price' => 250.00, 'unit' => 'pcs',    'description' => 'Plastic crates for delivery transport'],
            ['name' => 'Pump / Dispenser',           'category' => 'Equipment',  'quantity' => 3,   'minimum_stock' => 5,  'unit_price' => 350.00, 'unit' => 'pcs',    'description' => 'Water dispenser pumps for containers'],
            ['name' => 'Sealing Labels',             'category' => 'Packaging',  'quantity' => 450, 'minimum_stock' => 100,'unit_price' => 1.50,   'unit' => 'pcs',    'description' => 'Aqua De Smiley branded seal labels'],
        ];

        foreach ($supplies as $supply) {
            Supply::firstOrCreate(['name' => $supply['name']], $supply);
        }
    }
}
