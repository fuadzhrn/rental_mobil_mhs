<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentalCompany;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_commission_report_shows_ten_percent_of_verified_transactions(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'email' => 'superadmin-test@example.com',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin_rental',
            'email' => 'admin-test@example.com',
        ]);

        $rental = RentalCompany::create([
            'user_id' => $admin->id,
            'company_name' => 'Demo Rental',
            'company_slug' => 'demo-rental',
            'description' => 'Demo',
            'address' => 'Alamat demo',
            'city' => 'Jakarta',
            'phone' => '081200000050',
            'email' => 'demo-rental@example.com',
            'status_verification' => RentalCompany::STATUS_APPROVED,
            'verified_by' => $superAdmin->id,
            'verified_at' => now(),
        ]);

        $vehicle = Vehicle::create([
            'rental_company_id' => $rental->id,
            'name' => 'Demo Vehicle',
            'slug' => 'demo-vehicle',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'category' => 'family',
            'year' => 2024,
            'transmission' => 'Automatic',
            'fuel_type' => 'Bensin',
            'seat_capacity' => 7,
            'luggage_capacity' => '2 koper',
            'color' => 'Putih',
            'price_per_day' => 500000,
            'status' => Vehicle::STATUS_ACTIVE,
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer-test@example.com',
        ]);

        $booking = Booking::create([
            'booking_code' => 'BK-TEST-0001',
            'customer_id' => $customer->id,
            'rental_company_id' => $rental->id,
            'vehicle_id' => $vehicle->id,
            'promo_id' => null,
            'pickup_date' => now()->addDays(1)->toDateString(),
            'return_date' => now()->addDays(3)->toDateString(),
            'pickup_time' => '09:00:00',
            'pickup_location' => 'Lokasi demo',
            'return_location' => 'Lokasi demo',
            'duration_days' => 2,
            'with_driver' => false,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone ?? '081200000099',
            'customer_address' => 'Alamat demo',
            'subtotal' => 1000000,
            'discount_amount' => 0,
            'additional_cost' => 0,
            'total_amount' => 1000000,
            'booking_status' => Booking::BOOKING_COMPLETED,
            'payment_status' => Booking::PAYMENT_VERIFIED,
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => 'transfer_bank',
            'amount' => 1000000,
            'paid_at' => now(),
            'verified_by' => $superAdmin->id,
            'verified_at' => now(),
            'payment_status' => Payment::STATUS_VERIFIED,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('super-admin.reports.commissions'));

        $response->assertOk();
        $response->assertSee('Rp 100.000');
        $response->assertSee('BK-TEST-0001');
    }
}