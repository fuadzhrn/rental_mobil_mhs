<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rental_company_id')->constrained('rental_companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->string('promo_code', 50)->unique();
            $table->text('description')->nullable();
            $table->string('discount_type', 20);
            $table->decimal('discount_value', 12, 2);
            $table->decimal('min_transaction', 12, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('quota')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->boolean('loyal_only')->default(false);
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['rental_company_id', 'status']);
            $table->index(['rental_company_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
