<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_companies', function (Blueprint $table): void {
            $table->text('rejection_note')->nullable()->after('verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('rental_companies', function (Blueprint $table): void {
            $table->dropColumn('rejection_note');
        });
    }
};
