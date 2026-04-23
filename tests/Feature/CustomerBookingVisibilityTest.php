<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\RentalCompany;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerBookingVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_own_booking_but_not_others(): void
    {
        $admin = User::factory()->create(['role' => 'admin_rental']);
        $customerOne = User::factory()->create(['role' => 'customer']);
        $customerTwo = User::factory()->create(['role' => 'customer']);

        $rental = RentalCompany::create([
            'user_id' => $admin->id,
            'company_name' => 'Demo Rental',
            'company_slug' => 'demo-rental-booking',
            'description' => 'Demo',
            'address' => 'Alamat demo',
            'city' => 'Jakarta',
            'phone' => '081200000060',
            'email' => 'booking-demo@example.com',
            'status_verification' => RentalCompany::STATUS_APPROVED,
        ]);

        $vehicle = Vehicle::create([
            'rental_company_id' => $rental->id,
            'name' => 'Demo Vehicle Booking',
            'slug' => 'demo-vehicle-booking',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'category' => 'family',
            'year' => 2024,
            'transmission' => 'Automatic',
            'fuel_type' => 'Bensin',
            'seat_capacity' => 7,
            'luggage_capacity' => '2 koper',
            'color' => 'Hitam',
            'price_per_day' => 400000,
            'status' => Vehicle::STATUS_ACTIVE,
        ]);

        $ownBooking = Booking::create([
            'booking_code' => 'BK-VIS-OWN',
            'customer_id' => $customerOne->id,
            'rental_company_id' => $rental->id,
            'vehicle_id' => $vehicle->id,
            'pickup_date' => now()->addDay()->toDateString(),
            'return_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 1,
            'customer_name' => $customerOne->name,
            'customer_email' => $customerOne->email,
            'customer_phone' => $customerOne->phone ?? '081200000061',
            'pickup_location' => 'Lokasi own',
            'subtotal' => 400000,
            'discount_amount' => 0,
            'additional_cost' => 0,
            'total_amount' => 400000,
            'booking_status' => Booking::BOOKING_WAITING_PAYMENT,
            'payment_status' => Booking::PAYMENT_UNPAID,
        ]);

        Booking::create([
            'booking_code' => 'BK-VIS-OTHER',
            'customer_id' => $customerTwo->id,
            'rental_company_id' => $rental->id,
            'vehicle_id' => $vehicle->id,
            'pickup_date' => now()->addDay()->toDateString(),
            'return_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 1,
            'customer_name' => $customerTwo->name,
            'customer_email' => $customerTwo->email,
            'customer_phone' => $customerTwo->phone ?? '081200000062',
            'pickup_location' => 'Lokasi other',
            'subtotal' => 400000,
            'discount_amount' => 0,
            'additional_cost' => 0,
            'total_amount' => 400000,
            'booking_status' => Booking::BOOKING_WAITING_PAYMENT,
            'payment_status' => Booking::PAYMENT_UNPAID,
        ]);

        $this->actingAs($customerOne)
            ->get(route('customer.bookings.show', $ownBooking))
            ->assertOk();

        $this->actingAs($customerOne)
            ->get(route('customer.bookings.show', 'BK-VIS-OTHER'))
            ->assertForbidden();
    }
}