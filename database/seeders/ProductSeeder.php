<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Laptop', 'price' => 1500.00, 'stock' => 5000],
            ['name' => 'Smartphone', 'price' => 800.00, 'stock' => 5000],
            ['name' => 'Headphones', 'price' => 150.00, 'stock' => 5000],
            ['name' => 'Keyboard', 'price' => 50.00, 'stock' => 5000],
            ['name' => 'Mouse', 'price' => 25.00, 'stock' => 5000],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
