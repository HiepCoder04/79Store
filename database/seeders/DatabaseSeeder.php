<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // \App\Models\Category::factory(10)->create();
        // \App\Models\Product::factory(10)->create();
        // \App\Models\ProductVariant::factory(10)->create();
        // \App\Models\ProductGallery::factory(10)->create();
        // \App\Models\CategoryBlog::factory(10)->create();


        $this->call([
            UsersTableSeeder::class,
            VoucherSeeder::class,
            UserAddressSeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            OrderSeeder::class,
        ]);

    }
}
