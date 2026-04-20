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
        Schema::create('rental_companies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('company_name');
            $table->string('company_slug')->unique();
            $table->text('description')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('phone', 20);
            $table->string('email');
            $table->string('logo')->nullable();
            $table->string('status_verification')->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('status_verification');
            $table->index(['city', 'status_verification']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_companies');
    }
};
