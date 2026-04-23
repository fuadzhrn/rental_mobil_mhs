<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'phone' => '081100000001',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'adminrental1@example.com'],
            [
                'name' => 'Admin Rental Satu',
                'phone' => '081100000002',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'admin_rental',
            ]
        );

        User::updateOrCreate(
            ['email' => 'adminrental2@example.com'],
            [
                'name' => 'Admin Rental Dua',
                'phone' => '081100000003',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'admin_rental',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer1@example.com'],
            [
                'name' => 'Customer Satu',
                'phone' => '081100000101',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer2@example.com'],
            [
                'name' => 'Customer Dua',
                'phone' => '081100000102',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer3@example.com'],
            [
                'name' => 'Customer Tiga',
                'phone' => '081100000103',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer4@example.com'],
            [
                'name' => 'Customer Empat',
                'phone' => '081100000104',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer5@example.com'],
            [
                'name' => 'Customer Lima',
                'phone' => '081100000105',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Customer Demo',
                'phone' => '081100000100',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        );
    }
}
