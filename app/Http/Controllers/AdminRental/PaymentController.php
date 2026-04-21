<?php

namespace App\Http\Controllers\AdminRental;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectPaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentalCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
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
