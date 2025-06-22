<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       User::factory()->count(10)->create();

       User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('admin123'), // Mật khẩu đã mã hoá
        'role' => 'admin', // Nếu có cột role
    ]);
    }
}
