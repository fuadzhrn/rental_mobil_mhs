<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Promo;
use App\Models\RentalCompany;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $approvedRental = RentalCompany::where('company_name', 'Velora Trans')->firstOrFail();
        $pendingRental = RentalCompany::where('company_name', 'Nusantara Mobility')->firstOrFail();

        $vehicles = Vehicle::query()->orderBy('id')->get()->keyBy('name');
        $customers = User::query()
            ->where('role', 'customer')
            ->orderBy('email')
            ->get()
            ->keyBy('email');

        $loyalPromo = Promo::where('promo_code', 'LOYAL10')->first();
        $weekendPromo = Promo::where('promo_code', 'WEEKEND50')->first();

        $bookings = [
            $this->makeBooking(
                bookingCode: 'BK-20260423-0001',
                customer: $customers['customer1@example.com'],
                rentalCompanyId: $approvedRental->id,
                vehicle: $vehicles['Toyota Avanza 2023'],
                promoId: $loyalPromo?->id,
                pickupDate: now()->subDays(15),
                durationDays: 3,
                status: Booking::BOOKING_COMPLETED,
                paymentStatus: Booking::PAYMENT_VERIFIED,
                paymentMethod: 'transfer_bank',
                note: 'Loyal customer demo booking 1',
            ),
            $this->makeBooking(
                bookingCode: 'BK-20260423-0002',
                customer: $customers['customer1@example.com'],
                rentalCompanyId: $approvedRental->id,
                vehicle: $vehicles['Honda Brio 2024'],
                promoId: $loyalPromo?->id,
                pickupDate: now()->subDays(12),
                durationDays: 2,
                status: Booking::BOOKING_COMPLETED,
                paymentStatus: Booking::PAYMENT_VERIFIED,
                paymentMethod: 'qris',
                note: 'Loyal customer demo booking 2',
            ),
            $this->makeBooking(
                bookingCode: 'BK-20260423-0003',
                customer: $customers['customer1@example.com'],
                rentalCompanyId: $approvedRental->id,
                vehicle: $vehicles['Mitsubishi Pajero Sport 2022'],
                promoId: $weekendPromo?->id,
                pickupDate: now()->subDays(8),
                durationDays: 4,
                status: Booking::BOOKING_COMPLETED,
                paymentStatus: Booking::PAYMENT_VERIFIED,
                paymentMethod: 'transfer_bank',
                note: 'Loyal customer demo booking 3',
            ),
            $this->makeBooking(
                bookingCode: 'BK-20260423-0004',
                customer: $customers['customer2@example.com'],
                rentalCompanyId: $approvedRental->id,
                vehicle: $vehicles['Toyota Avanza 2023'],
                promoId: null,
                pickupDate: now()->addDays(2),
                durationDays: 2,
                status: Booking::BOOKING_CONFIRMED,
                paymentStatus: Booking::PAYMENT_VERIFIED,
                paymentMethod: 'transfer_bank',
                note: 'Confirmed booking demo',
            ),
            $this->makeBooking(
                bookingCode: 'BK-20260423-0005',
                customer: $customers['customer3@example.com'],
                rentalCompanyId: $approvedRental->id,
                vehicle: $vehicles['Honda Brio 2024'],
                promoId: null,
                pickupDate: now()->addDays(1),
                durationDays: 1,
                status: Booking::BOOKING_ONGOING,
                paymentStatus: Booking::PAYMENT_VERIFIED,
                paymentMethod: 'cash',
                note: 'Ongoing booking demo',
            ),
            $this->makeBooking(
                bookingCode: 'BK-20260423-0006',
                customer: $customers['customer4@example.com'],
                rentalCompanyId: $approvedRental->id,
                vehicle: $vehicles['Mitsubishi Pajero Sport 2022'],
                promoId: null,
                pickupDate: now()->addDays(5),
                durationDays: 3,
                status: Booking::BOOKING_WAITING_VERIFICATION,
                paymentStatus: Booking::PAYMENT_UPLOADED,
                paymentMethod: 'transfer_bank',
                note: 'Waiting verification demo',
            ),
            $this->makeBooking(
                bookingCode: 'BK-20260423-0007',
                customer: $customers['customer5@example.com'],
                rentalCompanyId: $approvedRental->id,
                vehicle: $vehicles['Toyota Avanza 2023'],
                promoId: null,
                pickupDate: now()->addDays(7),
                durationDays: 2,
                status: Booking::BOOKING_WAITING_PAYMENT,
                paymentStatus: Booking::PAYMENT_UNPAID,
                paymentMethod: null,
                note: 'Waiting payment demo',
            ),
            $this->makeBooking(
                bookingCode: 'BK-20260423-0008',
                customer: $customers['customer2@example.com'],
                rentalCompanyId: $approvedRental->id,
                vehicle: $vehicles['Honda Brio 2024'],
                promoId: null,
                pickupDate: now()->subDays(3),
                durationDays: 1,
                status: Booking::BOOKING_CANCELLED,
                paymentStatus: Booking::PAYMENT_REJECTED,
                paymentMethod: 'transfer_bank',
                note: 'Cancelled after rejection demo',
            ),
            $this->makeBooking(
                bookingCode: 'BK-20260423-0009',
                customer: $customers['customer3@example.com'],
                rentalCompanyId: $pendingRental->id,
                vehicle: $vehicles['Suzuki XL7 2023'],
                promoId: null,
                pickupDate: now()->addDays(10),
                durationDays: 2,
                status: Booking::BOOKING_CONFIRMED,
                paymentStatus: Booking::PAYMENT_VERIFIED,
                paymentMethod: 'transfer_bank',
                note: 'Pending rental demo booking',
            ),
        ];

        foreach ($bookings as $bookingData) {
            Booking::updateOrCreate(
                ['booking_code' => $bookingData['booking_code']],
                $bookingData
            );
        }
    }

    private function makeBooking(
        string $bookingCode,
        User $customer,
        int $rentalCompanyId,
        Vehicle $vehicle,
        ?int $promoId,
        Carbon $pickupDate,
        int $durationDays,
        string $status,
        string $paymentStatus,
        ?string $paymentMethod,
        string $note
    ): array {
        $subtotal = (float) $vehicle->price_per_day * $durationDays;
        $discountAmount = $promoId ? round($subtotal * 0.10, 2) : 0;
        $totalAmount = max($subtotal - $discountAmount, 0);

        return [
            'booking_code' => $bookingCode,
            'customer_id' => $customer->id,
            'rental_company_id' => $rentalCompanyId,
            'vehicle_id' => $vehicle->id,
            'promo_id' => $promoId,
            'pickup_date' => $pickupDate->toDateString(),
            'return_date' => $pickupDate->copy()->addDays($durationDays)->toDateString(),
            'pickup_time' => '09:00:00',
            'pickup_location' => 'Lokasi pickup demo',
            'return_location' => 'Lokasi return demo',
            'duration_days' => $durationDays,
            'with_driver' => false,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'customer_address' => 'Alamat demo customer',
            'identity_number' => 'ID-' . Str::upper(Str::random(8)),
            'driver_license_number' => 'SIM-' . Str::upper(Str::random(8)),
            'note' => $note,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'additional_cost' => 0,
            'total_amount' => $totalAmount,
            'booking_status' => $status,
            'payment_status' => $paymentStatus,
        ];
    }
}