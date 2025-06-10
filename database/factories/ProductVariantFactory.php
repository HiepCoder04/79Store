<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition()
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'pot' => $this->faker->optional()->word,
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'stock_quantity' => $this->faker->numberBetween(1, 50),
        ];
    }
}
