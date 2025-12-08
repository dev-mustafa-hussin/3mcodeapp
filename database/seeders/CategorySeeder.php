<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics = Category::create([
            'name' => 'Electronics',
            'description' => 'Gadgets and devices',
        ]);

        $phones = Category::create([
            'name' => 'Phones',
            'description' => 'Smartphones',
            'parent_id' => $electronics->id,
        ]);

        $laptops = Category::create([
            'name' => 'Laptops',
            'description' => 'Computers',
            'parent_id' => $electronics->id,
        ]);

        Category::create([
            'name' => 'Furniture',
            'description' => 'Home and Office',
        ]);
    }
}
