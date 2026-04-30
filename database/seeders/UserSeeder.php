<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Admin
         User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456789'),
            'address' => 'admin address',
            'city' => 'admin city',
            'phone' => '123456789',
            'role' => 'admin',
        ]);

        // Driver
        User::create([
            'name' => 'Delivery Driver',
            'email' => 'driver@example.com',
            'password' => Hash::make('password'),
            'address' => 'Driver Street 2',
            'city' => 'Driver City',
            'phone' => '0511111111',
            'role' => 'driver',
        ]);

        // Normal User
        User::create([
            'name' => 'Normal Customer',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'address' => 'User Street 3',
            'city' => 'User City',
            'phone' => '0522222222',
            'role' => 'user',
        ]);
    }
}
