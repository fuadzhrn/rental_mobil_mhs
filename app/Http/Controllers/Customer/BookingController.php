<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\RentalCompany;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use App\Services\PromoService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly PromoService $promoService,
        private readonly NotificationService $notificationService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function create(Vehicle $vehicle): View
    {
        $this->authorize('create', Booking::class);

        $vehicle->load(['rentalCompany', 'images', 'primaryImage']);
        $this->ensureVehicleCanBeBooked($vehicle);

        $customer = Auth::user();
        $pickupDate = now()->addDay()->toDateString();
        $returnDate = now()->addDays(2)->toDateString();
        $durationDays = Carbon::parse($pickupDate)->diffInDays(Carbon::parse($returnDate)) + 1;
        $subtotal = $durationDays * (float) $vehicle->price_per_day;
        $availablePromos = $this->promoService->getVisiblePromosForBooking($vehicle->rental_company_id, (int) Auth::id(), $subtotal);

        return view('booking.index', compact('vehicle', 'customer', 'pickupDate', 'returnDate', 'durationDays', 'subtotal', 'availablePromos'));
    }

    public function store(StoreBookingRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('create', Booking::class);

        $vehicle->load('rentalCompany');
        $this->ensureVehicleCanBeBooked($vehicle);

        $validated = $request->validated();

        $pickupDate = Carbon::parse($validated['pickup_date'])->startOfDay();
        $returnDate = Carbon::parse($validated['return_date'])->startOfDay();
        $durationDays = $pickupDate->diffInDays($returnDate) + 1;
        $subtotal = $durationDays * (float) $vehicle->price_per_day;
        $withDriver = $request->boolean('with_driver');
        $additionalCost = 0;
        $promoCode = $validated['promo_code'] ?? null;

        if ($this->isVehicleBookedForDateRange($vehicle->id, $pickupDate, $returnDate)) {
            return back()
                ->withInput()
                ->withErrors([
                    'pickup_date' => 'Kendaraan tidak tersedia pada rentang tanggal tersebut.',
                ]);
        }

        try {
            $booking = DB::transaction(function () use ($validated, $vehicle, $pickupDate, $returnDate, $durationDays, $subtotal, $additionalCost, $withDriver, $promoCode): Booking {
                $promoResult = $this->promoService->resolvePromoForBooking(
                    $promoCode,
                    (int) $vehicle->rental_company_id,
                    (int) Auth::id(),
                    $subtotal,
                    true
                );

                $promo = $promoResult['promo'];
                $discountAmount = (float) $promoResult['discount_amount'];
                $totalAmount = max(0, $subtotal - $discountAmount + $additionalCost);

            $booking = Booking::create([
                'booking_code' => $this->generateBookingCode(),
                'customer_id' => Auth::id(),
                'rental_company_id' => $vehicle->rental_company_id,
                'vehicle_id' => $vehicle->id,
                'promo_id' => $promo?->id,
                'pickup_date' => $pickupDate->toDateString(),
                'return_date' => $returnDate->toDateString(),
                'pickup_time' => $validated['pickup_time'] ?? null,
                'pickup_location' => $validated['pickup_location'],
                'return_location' => $validated['return_location'] ?? null,
                'duration_days' => $durationDays,
                'with_driver' => $withDriver,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'identity_number' => $validated['identity_number'],
                'driver_license_number' => $validated['driver_license_number'] ?? null,
                'note' => $validated['note'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'additional_cost' => $additionalCost,
                'total_amount' => $totalAmount,
                'booking_status' => Booking::BOOKING_WAITING_PAYMENT,
                'payment_status' => Booking::PAYMENT_UNPAID,
            ]);

                if ($promo) {
                    $promo->increment('used_count');
                }

            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => 'manual_transfer',
                'amount' => $totalAmount,
                'proof_payment' => null,
                'paid_at' => null,
                'verified_by' => null,
                'verified_at' => null,
                'payment_status' => Payment::STATUS_UNPAID,
                'rejection_note' => null,
            ]);

            return $booking;
            });
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        $this->notificationService->notifyUser(
            userId: (int) $booking->customer_id,
            title: 'Booking Berhasil Dibuat',
            message: 'Booking ' . $booking->booking_code . ' berhasil dibuat. Silakan lanjutkan pembayaran.',
            type: 'success',
            url: route('customer.bookings.show', $booking),
            referenceType: 'booking',
            referenceId: $booking->id,
        );

        $rentalAdminId = $booking->rentalCompany?->user_id;
        if ($rentalAdminId) {
            $this->notificationService->notifyUser(
                userId: (int) $rentalAdminId,
                title: 'Booking Baru Masuk',
                message: 'Ada booking baru ' . $booking->booking_code . ' dari customer.',
                type: 'info',
                url: route('admin-rental.bookings.show', $booking),
                referenceType: 'booking',
                referenceId: $booking->id,
            );
        }

        $this->activityLogService->log(
            action: 'booking.created',
            description: 'Customer membuat booking baru: ' . $booking->booking_code,
            targetType: 'booking',
            targetId: $booking->id,
            meta: ['vehicle_id' => $booking->vehicle_id, 'total_amount' => $booking->total_amount]
        );

        return redirect()
            ->route('pembayaran.show', $booking)
            ->with('success', 'Booking berhasil dibuat. Silakan lanjutkan ke langkah pembayaran.');
    }

    private function ensureVehicleCanBeBooked(Vehicle $vehicle): void
    {
        if (
            $vehicle->status !== Vehicle::STATUS_ACTIVE ||
            !$vehicle->rentalCompany ||
            $vehicle->rentalCompany->status_verification !== RentalCompany::STATUS_APPROVED
        ) {
            abort(404);
        }
    }

    private function generateBookingCode(): string
    {
        do {
            $bookingCode = 'BK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Booking::where('booking_code', $bookingCode)->exists());

        return $bookingCode;
    }

    private function isVehicleBookedForDateRange(int $vehicleId, Carbon $pickupDate, Carbon $returnDate): bool
    {
        return Booking::query()
            ->where('vehicle_id', $vehicleId)
            ->whereIn('booking_status', [
                Booking::BOOKING_WAITING_PAYMENT,
                Booking::BOOKING_WAITING_VERIFICATION,
                Booking::BOOKING_CONFIRMED,
                Booking::BOOKING_ONGOING,
                Booking::BOOKING_PENDING,
            ])
            ->where(function ($query) use ($pickupDate, $returnDate): void {
                $query->whereBetween('pickup_date', [$pickupDate->toDateString(), $returnDate->toDateString()])
                    ->orWhereBetween('return_date', [$pickupDate->toDateString(), $returnDate->toDateString()])
                    ->orWhere(function ($nestedQuery) use ($pickupDate, $returnDate): void {
                        $nestedQuery->where('pickup_date', '<=', $pickupDate->toDateString())
                            ->where('return_date', '>=', $returnDate->toDateString());
                    });
            })
            ->exists();
    }
}
