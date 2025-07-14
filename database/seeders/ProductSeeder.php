<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;


class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::factory(5)->create()->pluck('id')->toArray();
        for ($i = 1; $i <= 10; $i++) {
            Product::create([
                'name' => 'Sản phẩm ' . $i,
                'slug' => 'san-pham-' . $i,
                'price' => rand(100, 500) * 1000,
                'quantity' => 10,
                'category_id' => $categoryIds[array_rand($categoryIds)],
            ]);
        }
    }
}
