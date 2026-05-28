<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('inventories')) {
            return;
        }

        Schema::table('inventories', function (Blueprint $table): void {
            if (! Schema::hasColumn('inventories', 'rental_company_id')) {
                $table->unsignedBigInteger('rental_company_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('inventories', 'vehicle_id')) {
                $table->unsignedBigInteger('vehicle_id')->nullable()->after('rental_company_id');
            }
            if (! Schema::hasColumn('inventories', 'total')) {
                $table->integer('total')->default(0)->after('vehicle_id');
            }
            if (! Schema::hasColumn('inventories', 'reserved')) {
                $table->integer('reserved')->default(0)->after('total');
            }
            if (! Schema::hasColumn('inventories', 'available')) {
                $table->integer('available')->default(0)->after('reserved');
            }
            if (! Schema::hasColumn('inventories', 'last_checked_by')) {
                $table->unsignedBigInteger('last_checked_by')->nullable()->after('available');
            }
            if (! Schema::hasColumn('inventories', 'notes')) {
                $table->text('notes')->nullable()->after('last_checked_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('inventories')) {
            return;
        }

        Schema::table('inventories', function (Blueprint $table): void {
            foreach (['notes', 'last_checked_by', 'available', 'reserved', 'total', 'vehicle_id', 'rental_company_id'] as $col) {
                if (Schema::hasColumn('inventories', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
