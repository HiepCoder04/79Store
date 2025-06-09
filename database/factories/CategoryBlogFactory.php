<?php

namespace Database\Factories;

use App\Models\CategoryBlog;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryBlogFactory extends Factory
{
    protected $model = CategoryBlog::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}

