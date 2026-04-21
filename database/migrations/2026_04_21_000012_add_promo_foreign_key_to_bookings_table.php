<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->foreign('promo_id')->references('id')->on('promos')->nullOnDelete()->cascadeOnUpdate();
            $table->index('promo_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropForeign(['promo_id']);
            $table->dropIndex(['promo_id']);
        });
    }
};
