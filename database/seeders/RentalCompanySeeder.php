<?php

namespace Database\Seeders;

use App\Models\RentalCompany;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RentalCompanySeeder extends Seeder
{
    public function run(): void
    {
        $adminOne = User::where('email', 'adminrental1@example.com')->firstOrFail();
        $adminTwo = User::where('email', 'adminrental2@example.com')->firstOrFail();

        RentalCompany::updateOrCreate(
            ['user_id' => $adminOne->id],
            [
                'company_name' => 'Velora Trans',
                'company_slug' => Str::slug('Velora Trans'),
                'description' => 'Rental kendaraan premium untuk kebutuhan harian, bisnis, dan perjalanan keluarga.',
                'address' => 'Jl. Merdeka No. 10, Jakarta',
                'city' => 'Jakarta',
                'phone' => '081200000001',
                'email' => 'velora@example.com',
                'status_verification' => RentalCompany::STATUS_APPROVED,
                'verified_by' => User::where('role', 'super_admin')->value('id'),
                'verified_at' => now()->subDays(3),
            ]
        );

        RentalCompany::updateOrCreate(
            ['user_id' => $adminTwo->id],
            [
                'company_name' => 'Nusantara Mobility',
                'company_slug' => Str::slug('Nusantara Mobility'),
                'description' => 'Rental kendaraan untuk area kota dan antar kota dengan layanan fleksibel.',
                'address' => 'Jl. Diponegoro No. 21, Bandung',
                'city' => 'Bandung',
                'phone' => '081200000002',
                'email' => 'nusantara@example.com',
                'status_verification' => RentalCompany::STATUS_PENDING,
                'verified_by' => null,
                'verified_at' => null,
                'rejection_note' => null,
            ]
        );
    }
}