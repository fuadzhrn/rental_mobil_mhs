<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPaymentProofRequest;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Booking $booking): View
    {
        $this->ensureCustomerOwnsBooking($booking);

        $booking->load(['customer', 'vehicle.rentalCompany', 'vehicle.primaryImage', 'payment']);
        $payment = $this->ensurePaymentExists($booking);
        $paymentMethods = config('payment_methods');
        $selectedMethod = old('payment_method', $payment->payment_method);

        return view('pembayaran.index', compact('booking', 'payment', 'paymentMethods', 'selectedMethod'));
    }

    public function uploadProof(UploadPaymentProofRequest $request, Booking $booking): RedirectResponse
    {
        $this->ensureCustomerOwnsBooking($booking);
        $booking->load('payment');
        $payment = $this->ensurePaymentExists($booking);

        if ($payment->payment_status === Payment::STATUS_VERIFIED || $booking->payment_status === Booking::PAYMENT_VERIFIED) {
            return back()->with('error', 'Pembayaran sudah diverifikasi dan tidak dapat diubah lagi.');
        }

        $validated = $request->validated();
        $paymentMethod = $validated['payment_method'];
        $proofPath = $request->file('proof_payment')->store('payments/proofs', 'public');
        $oldProofPath = $payment->proof_payment;

        DB::transaction(function () use ($booking, $payment, $paymentMethod, $proofPath): void {
            $payment->update([
                'payment_method' => $paymentMethod,
                'proof_payment' => $proofPath,
                'paid_at' => now(),
                'payment_status' => Payment::STATUS_UPLOADED,
                'rejection_note' => null,
            ]);

            $booking->update([
                'payment_status' => Booking::PAYMENT_UPLOADED,
                'booking_status' => Booking::BOOKING_WAITING_VERIFICATION,
            ]);
        });

        if ($oldProofPath && $oldProofPath !== $proofPath) {
            Storage::disk('public')->delete($oldProofPath);
        }

        return redirect()
            ->route('pembayaran.show', $booking)
            ->with('success', 'Bukti pembayaran berhasil diupload dan menunggu verifikasi admin rental.');
    }

    public function invoice(Booking $booking): View
    {
        $this->ensureCustomerOwnsBooking($booking);
        $booking->load(['customer', 'vehicle.rentalCompany', 'payment']);
        $payment = $this->ensurePaymentExists($booking);

        return view('pembayaran.print', [
            'booking' => $booking,
            'payment' => $payment,
            'paymentMethods' => config('payment_methods'),
            'selectedMethod' => $payment->payment_method,
            'documentTitle' => 'Invoice',
        ]);
    }

    public function receipt(Booking $booking): View
    {
        $this->ensureCustomerOwnsBooking($booking);
        $booking->load(['customer', 'vehicle.rentalCompany', 'payment']);
        $payment = $this->ensurePaymentExists($booking);

        return view('pembayaran.print', [
            'booking' => $booking,
            'payment' => $payment,
            'paymentMethods' => config('payment_methods'),
            'selectedMethod' => $payment->payment_method,
            'documentTitle' => 'Bukti Transaksi',
        ]);
    }

    private function ensureCustomerOwnsBooking(Booking $booking): void
    {
        if ((int) $booking->customer_id !== (int) Auth::id()) {
            abort(404);
        }
    }

    private function ensurePaymentExists(Booking $booking): Payment
    {
        if ($booking->payment) {
            return $booking->payment;
        }

        return $booking->payment()->create([
            'payment_method' => 'manual_transfer',
            'amount' => $booking->total_amount,
            'payment_status' => Payment::STATUS_UNPAID,
        ]);
    }
}
