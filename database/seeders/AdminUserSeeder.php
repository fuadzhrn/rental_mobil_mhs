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
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'adminrental@example.com'],
            [
                'name' => 'Admin Rental',
                'password' => Hash::make('password123'),
                'role' => 'admin_rental',
            ]
        );
    }
}
