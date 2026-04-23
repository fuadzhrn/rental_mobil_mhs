<?php

namespace Database\Seeders;

use App\Models\RentalCompany;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $approvedRental = RentalCompany::where('company_name', 'Velora Trans')->firstOrFail();
        $pendingRental = RentalCompany::where('company_name', 'Nusantara Mobility')->firstOrFail();

        $vehicles = [
            [
                'rental_company_id' => $approvedRental->id,
                'name' => 'Toyota Avanza 2023',
                'slug' => Str::slug('Toyota Avanza 2023'),
                'brand' => 'Toyota',
                'type' => 'MPV',
                'category' => 'family',
                'year' => 2023,
                'transmission' => 'Automatic',
                'fuel_type' => 'Bensin',
                'seat_capacity' => 7,
                'luggage_capacity' => '2 koper',
                'color' => 'Silver',
                'price_per_day' => 450000,
                'description' => 'MPV favorit untuk keluarga dan perjalanan bisnis ringan.',
                'terms_conditions' => 'Penggunaan wajar, tidak merokok di dalam kendaraan.',
                'status' => Vehicle::STATUS_ACTIVE,
            ],
            [
                'rental_company_id' => $approvedRental->id,
                'name' => 'Honda Brio 2024',
                'slug' => Str::slug('Honda Brio 2024'),
                'brand' => 'Honda',
                'type' => 'City Car',
                'category' => 'city',
                'year' => 2024,
                'transmission' => 'Automatic',
                'fuel_type' => 'Bensin',
                'seat_capacity' => 5,
                'luggage_capacity' => '1 koper',
                'color' => 'Merah',
                'price_per_day' => 350000,
                'description' => 'City car lincah dan hemat bahan bakar.',
                'terms_conditions' => 'Wajib pengisian bahan bakar sesuai awal serah terima.',
                'status' => Vehicle::STATUS_ACTIVE,
            ],
            [
                'rental_company_id' => $approvedRental->id,
                'name' => 'Mitsubishi Pajero Sport 2022',
                'slug' => Str::slug('Mitsubishi Pajero Sport 2022'),
                'brand' => 'Mitsubishi',
                'type' => 'SUV',
                'category' => 'premium',
                'year' => 2022,
                'transmission' => 'Automatic',
                'fuel_type' => 'Solar',
                'seat_capacity' => 7,
                'luggage_capacity' => '3 koper',
                'color' => 'Hitam',
                'price_per_day' => 950000,
                'description' => 'SUV premium untuk perjalanan jauh dan medan berat.',
                'terms_conditions' => 'Tidak untuk penggunaan off-road berat tanpa izin.',
                'status' => Vehicle::STATUS_ACTIVE,
            ],
            [
                'rental_company_id' => $pendingRental->id,
                'name' => 'Suzuki XL7 2023',
                'slug' => Str::slug('Suzuki XL7 2023'),
                'brand' => 'Suzuki',
                'type' => 'SUV',
                'category' => 'family',
                'year' => 2023,
                'transmission' => 'Automatic',
                'fuel_type' => 'Bensin',
                'seat_capacity' => 7,
                'luggage_capacity' => '2 koper',
                'color' => 'Putih',
                'price_per_day' => 500000,
                'description' => 'SUV keluarga dengan interior lega.',
                'terms_conditions' => 'Tersedia setelah verifikasi rental selesai.',
                'status' => Vehicle::STATUS_ACTIVE,
            ],
            [
                'rental_company_id' => $pendingRental->id,
                'name' => 'Daihatsu Sigra 2024',
                'slug' => Str::slug('Daihatsu Sigra 2024'),
                'brand' => 'Daihatsu',
                'type' => 'MPV',
                'category' => 'city',
                'year' => 2024,
                'transmission' => 'Manual',
                'fuel_type' => 'Bensin',
                'seat_capacity' => 7,
                'luggage_capacity' => '2 koper',
                'color' => 'Abu-abu',
                'price_per_day' => 300000,
                'description' => 'Pilihan ekonomis untuk mobilitas harian.',
                'terms_conditions' => 'Harga dapat berubah saat musim liburan.',
                'status' => Vehicle::STATUS_ACTIVE,
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            Vehicle::updateOrCreate(
                ['name' => $vehicleData['name']],
                $vehicleData
            );
        }
    }
}