<?php

namespace App\Http\Controllers\AdminRental;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectPaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentalCompany;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function index(): View|RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company.');
        }

        $bookings = Booking::query()
            ->with(['customer', 'vehicle', 'payment'])
            ->where('rental_company_id', $rentalCompany->id)
            ->whereHas('payment')
            ->latest('id')
            ->paginate(10);

        return view('admin-rental.payments.index', compact('bookings', 'rentalCompany'));
    }

    public function show(Booking $booking): View|RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company.');
        }

        $this->ensureBookingBelongsToRental($booking, $rentalCompany->id);
        $booking->load(['customer', 'vehicle.rentalCompany', 'vehicle.primaryImage', 'payment']);

        return view('admin-rental.payments.show', compact('booking', 'rentalCompany'));
    }

    public function verify(Booking $booking): RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company.');
        }

        $this->ensureBookingBelongsToRental($booking, $rentalCompany->id);
        $booking->load('payment');

        if (!$booking->payment || $booking->payment->payment_status !== Payment::STATUS_UPLOADED) {
            return back()->with('error', 'Pembayaran harus berstatus uploaded sebelum diverifikasi.');
        }

        DB::transaction(function () use ($booking): void {
            $booking->payment->update([
                'payment_status' => Payment::STATUS_VERIFIED,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'rejection_note' => null,
            ]);

            $booking->update([
                'payment_status' => Booking::PAYMENT_VERIFIED,
                'booking_status' => Booking::BOOKING_CONFIRMED,
            ]);

            $this->notificationService->notifyUser(
                userId: (int) $booking->customer_id,
                title: 'Pembayaran Diverifikasi',
                message: 'Bukti pembayaran untuk booking ' . $booking->booking_code . ' telah diverifikasi. Booking Anda dikonfirmasi.',
                type: 'success',
                url: route('customer.bookings.show', $booking),
                referenceType: 'booking',
                referenceId: $booking->id,
            );

            $this->activityLogService->log(
                action: 'payment.verified',
                description: 'Admin rental memverifikasi pembayaran booking: ' . $booking->booking_code,
                targetType: 'payment',
                targetId: $booking->payment->id,
                meta: ['booking_id' => $booking->id, 'amount' => $booking->payment->amount]
            );
        });

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function reject(RejectPaymentRequest $request, Booking $booking): RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()
                ->route('admin-rental.dashboard')
                ->with('error', 'Akun admin rental ini belum memiliki rental company.');
        }

        $this->ensureBookingBelongsToRental($booking, $rentalCompany->id);
        $booking->load('payment');

        if (!$booking->payment || $booking->payment->payment_status !== Payment::STATUS_UPLOADED) {
            return back()->with('error', 'Pembayaran harus berstatus uploaded sebelum ditolak.');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($booking, $validated): void {
            $booking->payment->update([
                'payment_status' => Payment::STATUS_REJECTED,
                'rejection_note' => $validated['rejection_note'],
            ]);

            $booking->update([
                'payment_status' => Booking::PAYMENT_REJECTED,
                'booking_status' => Booking::BOOKING_WAITING_PAYMENT,
            ]);

            $this->notificationService->notifyUser(
                userId: (int) $booking->customer_id,
                title: 'Pembayaran Ditolak',
                message: 'Bukti pembayaran untuk booking ' . $booking->booking_code . ' ditolak. Alasan: ' . $validated['rejection_note'],
                type: 'error',
                url: route('customer.bookings.show', $booking),
                referenceType: 'booking',
                referenceId: $booking->id,
            );

            $this->activityLogService->log(
                action: 'payment.rejected',
                description: 'Admin rental menolak pembayaran booking: ' . $booking->booking_code,
                targetType: 'payment',
                targetId: $booking->payment->id,
                meta: ['booking_id' => $booking->id, 'rejection_note' => $validated['rejection_note']]
            );
        });

        return back()->with('success', 'Pembayaran berhasil ditolak. Customer dapat upload ulang bukti pembayaran.');
    }

    private function getRentalCompany(): ?RentalCompany
    {
        return Auth::user()?->rentalCompany;
    }

    private function ensureBookingBelongsToRental(Booking $booking, int $rentalCompanyId): void
    {
        if ((int) $booking->rental_company_id !== $rentalCompanyId) {
            abort(404);
        }
    }
}
