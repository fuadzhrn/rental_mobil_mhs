<?php

namespace Database\Seeders;

use App\Models\Promo;
use App\Models\RentalCompany;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
{
    public function run(): void
    {
        $approvedRental = RentalCompany::where('company_name', 'Velora Trans')->firstOrFail();
        $pendingRental = RentalCompany::where('company_name', 'Nusantara Mobility')->firstOrFail();

        $promos = [
            [
                'rental_company_id' => $approvedRental->id,
                'title' => 'Loyal Customer 10%',
                'promo_code' => 'LOYAL10',
                'description' => 'Promo khusus customer loyal dengan minimal transaksi tertentu.',
                'discount_type' => Promo::DISCOUNT_PERCENT,
                'discount_value' => 10,
                'min_transaction' => 400000,
                'start_date' => now()->subMonth()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'quota' => null,
                'used_count' => 0,
                'loyal_only' => true,
                'status' => Promo::STATUS_ACTIVE,
            ],
            [
                'rental_company_id' => $approvedRental->id,
                'title' => 'Diskon Akhir Pekan',
                'promo_code' => 'WEEKEND50',
                'description' => 'Potongan tetap untuk transaksi di atas nominal tertentu.',
                'discount_type' => Promo::DISCOUNT_FIXED,
                'discount_value' => 50000,
                'min_transaction' => 500000,
                'start_date' => now()->subMonth()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'quota' => 20,
                'used_count' => 4,
                'loyal_only' => false,
                'status' => Promo::STATUS_ACTIVE,
            ],
            [
                'rental_company_id' => $pendingRental->id,
                'title' => 'Promo Pending Rental',
                'promo_code' => 'PENDING20',
                'description' => 'Promo contoh untuk rental pending.',
                'discount_type' => Promo::DISCOUNT_PERCENT,
                'discount_value' => 20,
                'min_transaction' => 300000,
                'start_date' => now()->subMonth()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'quota' => 10,
                'used_count' => 1,
                'loyal_only' => false,
                'status' => Promo::STATUS_INACTIVE,
            ],
        ];

        foreach ($promos as $promoData) {
            Promo::updateOrCreate(
                ['promo_code' => $promoData['promo_code']],
                $promoData
            );
        }
    }
}