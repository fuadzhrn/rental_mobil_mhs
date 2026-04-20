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
        Schema::create('vehicles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rental_company_id')->constrained('rental_companies')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand');
            $table->string('type');
            $table->string('category');
            $table->unsignedSmallInteger('year');
            $table->string('transmission');
            $table->string('fuel_type');
            $table->unsignedSmallInteger('seat_capacity');
            $table->string('luggage_capacity')->nullable();
            $table->string('color')->nullable();
            $table->decimal('price_per_day', 12, 2);
            $table->text('description')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->string('status')->default('active');
            $table->string('main_image')->nullable();
            $table->timestamps();

            $table->index(['rental_company_id', 'status']);
            $table->index(['category', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
