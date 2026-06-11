<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use App\Models\Vehicle;
use Illuminate\Console\Command;

class SyncVehicleInventories extends Command
{
    protected $signature = 'inventory:sync';
    protected $description = 'Buat record inventoris untuk kendaraan yang belum memilikinya';

    public function handle(): void
    {
        $vehicles = Vehicle::whereDoesntHave('inventory')->get();

        if ($vehicles->isEmpty()) {
            $this->info('Semua kendaraan sudah memiliki inventoris.');
            return;
        }

        $this->info("Ditemukan {$vehicles->count()} kendaraan tanpa inventoris. Memproses...");

        foreach ($vehicles as $vehicle) {
            Inventory::create([
                'rental_company_id' => $vehicle->rental_company_id,
                'vehicle_id'        => $vehicle->id,
                'total'             => 0,
                'reserved'          => 0,
                'available'         => 0,
            ]);

            $this->line("  - {$vehicle->name}");
        }

        $this->info('Selesai! Inventoris berhasil dibuat untuk semua kendaraan.');
    }
}
