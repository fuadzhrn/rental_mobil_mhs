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
        Schema::create('bookings', function (Blueprint $table): void {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('rental_company_id')->constrained('rental_companies')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('vehicle_id')->constrained('vehicles')->restrictOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('promo_id')->nullable();
            $table->date('pickup_date');
            $table->date('return_date');
            $table->time('pickup_time')->nullable();
            $table->text('pickup_location');
            $table->text('return_location')->nullable();
            $table->unsignedSmallInteger('duration_days');
            $table->boolean('with_driver')->default(false);
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20);
            $table->text('customer_address')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('driver_license_number')->nullable();
            $table->text('note')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('additional_cost', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('booking_status')->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->timestamps();

            $table->index(['customer_id', 'booking_status']);
            $table->index(['vehicle_id', 'pickup_date']);
            $table->index(['rental_company_id', 'booking_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
