<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_companies', function (Blueprint $table): void {
            $table->string('document')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('rental_companies', function (Blueprint $table): void {
            $table->dropColumn('document');
        });
    }
};
