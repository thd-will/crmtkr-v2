<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // MOU Products
            ['type' => 'MOU', 'duration' => '3_months', 'duration_display' => '3 เดือน', 'base_price' => 590.00],
            ['type' => 'MOU', 'duration' => '6_months', 'duration_display' => '6 เดือน', 'base_price' => 990.00],
            ['type' => 'MOU', 'duration' => '12_months', 'duration_display' => '1 ปี', 'base_price' => 1790.00],
            ['type' => 'MOU', 'duration' => '15_months', 'duration_display' => '15 เดือน', 'base_price' => 2475.00],
            
            // มติ24 Products
            ['type' => 'มติ24', 'duration' => '3_months', 'duration_display' => '3 เดือน', 'base_price' => 590.00],
            ['type' => 'มติ24', 'duration' => '6_months', 'duration_display' => '6 เดือน', 'base_price' => 990.00],
            ['type' => 'มติ24', 'duration' => '12_months', 'duration_display' => '1 ปี', 'base_price' => 1790.00],
            ['type' => 'มติ24', 'duration' => '15_months', 'duration_display' => '15 เดือน', 'base_price' => 2475.00],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
