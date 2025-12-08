<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics = Category::where('name', 'Electronics')->first();
        
        // 20 products
        for ($i = 1; $i <= 20; $i++) {
            Product::create([
                'name' => "Product {$i}",
                'sku' => "SKU-00{$i}",
                'description' => "Description for product {$i}",
                'price' => rand(100, 1000),
                'cost' => rand(50, 90),
                'stock' => rand(10, 100),
                'category_id' => $electronics ? $electronics->id : null,
            ]);
        }
    }
}
