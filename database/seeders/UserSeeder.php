<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create customer user
        User::create([
            'username' => 'customer1',
            'email' => 'customer@mbjek.com',
            'handphone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_verified' => true,
        ]);

        // Create driver user
        User::create([
            'username' => 'driver1',
            'email' => 'driver@mbjek.com',
            'handphone' => '089876543210',
            'password' => Hash::make('password'),
            'role' => 'driver',
            'is_verified' => true,
        ]);
    }
}