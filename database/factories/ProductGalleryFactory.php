<?php

namespace Database\Factories;

use App\Models\ProductGallery;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductGalleryFactory extends Factory
{
    protected $model = ProductGallery::class;

    public function definition()
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'image' => $this->faker->imageUrl(),
        ];
    }
}

