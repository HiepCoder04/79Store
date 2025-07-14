<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       User::factory()->count(10)->create();

       User::updateOrCreate(
        ['email' => 'admin@example.com'], // điều kiện: nếu trùng email thì update
        [
            'name' => 'Admin',
            'password' => Hash::make('admin123'), // mã hoá
            'role' => 'admin',
        ]
    );

    }
}
