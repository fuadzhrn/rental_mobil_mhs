<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = Vehicle::all();

        foreach ($vehicles as $vehicle) {
            Inventory::firstOrCreate(
                [
                    'rental_company_id' => $vehicle->rental_company_id,
                    'vehicle_id' => $vehicle->id,
                ],
                [
                    'total' => 1,
                    'reserved' => 0,
                    'available' => 1,
                ]
            );
        }
    }
}
