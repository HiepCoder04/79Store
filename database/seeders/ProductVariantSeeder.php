<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductVariant;
use App\Models\Product;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            ProductVariant::create([
                'product_id' => $product->id,
                'variant_name' => 'Mẫu A',
                'price' => $product->price,
                'quantity' => 5
            ]);
        }
    }
}
