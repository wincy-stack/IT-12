<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name'        => '500mL Bottle',
                'size_key'    => '500ml',
                'category'    => 'Water Refill',
                'price'       => 5.00,
                'cost_price'  => 1.50,
                'stock'       => 250,
                'unit'        => 'bottle',
                'icon'        => 'fa-bottle-water',
                'description' => 'Compact personal drinking bottle filled with purified alkaline water',
                'is_active'   => true,
            ],
            [
                'name'        => '1-Gallon Bottle',
                'size_key'    => '1_gallon',
                'category'    => 'Water Refill',
                'price'       => 15.00,
                'cost_price'  => 4.50,
                'stock'       => 180,
                'unit'        => 'bottle',
                'icon'        => 'fa-jug-detergent',
                'description' => 'Ideal for personal & small family hydration needs',
                'is_active'   => true,
            ],
            [
                'name'        => '5-Gallon Container',
                'size_key'    => '5_gallon',
                'category'    => 'Water Refill',
                'price'       => 30.00,
                'cost_price'  => 8.00,
                'stock'       => 200,
                'unit'        => 'container',
                'icon'        => 'fa-bucket',
                'description' => 'Standard dispenser-ready purified water refill',
                'is_active'   => true,
            ],
            [
                'name'        => 'Brand New 5-Gallon Slim Container',
                'size_key'    => null,
                'category'    => 'New Container',
                'price'       => 220.00,
                'cost_price'  => 140.00,
                'stock'       => 45,
                'unit'        => 'container',
                'icon'        => 'fa-box-open',
                'description' => 'BPA-free slim blue container complete with cap and handle',
                'is_active'   => true,
            ],
            [
                'name'        => 'Brand New 5-Gallon Round Container',
                'size_key'    => null,
                'category'    => 'New Container',
                'price'       => 200.00,
                'cost_price'  => 130.00,
                'stock'       => 50,
                'unit'        => 'container',
                'icon'        => 'fa-box-open',
                'description' => 'Heavy duty round water jug suitable for top-load dispensers',
                'is_active'   => true,
            ],
            [
                'name'        => 'Manual Hand Pump Dispenser',
                'size_key'    => null,
                'category'    => 'Accessories & Equipment',
                'price'       => 180.00,
                'cost_price'  => 95.00,
                'stock'       => 30,
                'unit'        => 'pc',
                'icon'        => 'fa-faucet',
                'description' => 'Easy press manual water pump for 5-gallon containers',
                'is_active'   => true,
            ],
            [
                'name'        => 'Automatic USB Rechargeable Pump',
                'size_key'    => null,
                'category'    => 'Accessories & Equipment',
                'price'       => 350.00,
                'cost_price'  => 210.00,
                'stock'       => 25,
                'unit'        => 'pc',
                'icon'        => 'fa-plug-circle-bolt',
                'description' => 'Touch button electric drinking water pump with long battery life',
                'is_active'   => true,
            ],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['name' => $p['name']],
                $p
            );
        }
    }
}
