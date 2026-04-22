<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPaymentProofRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\ActivityLogService;
use App\Services\FileUploadService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService,
        private readonly NotificationService $notificationService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);
        $this->ensureCustomerOwnsBooking($booking);

        $booking->load(['customer', 'vehicle.rentalCompany', 'vehicle.primaryImage', 'payment']);
        $payment = $this->ensurePaymentExists($booking);
        $paymentMethods = config('payment_methods');
        $selectedMethod = old('payment_method', $payment->payment_method);

        return view('pembayaran.index', compact('booking', 'payment', 'paymentMethods', 'selectedMethod'));
    }

    public function uploadProof(UploadPaymentProofRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);
        $this->ensureCustomerOwnsBooking($booking);
        $booking->load(['payment', 'rentalCompany']);
        $payment = $this->ensurePaymentExists($booking);

        if ($payment->payment_status === Payment::STATUS_VERIFIED || $booking->payment_status === Booking::PAYMENT_VERIFIED) {
            return back()->with('error', 'Pembayaran sudah diverifikasi dan tidak dapat diubah lagi.');
        }

        $validated = $request->validated();
        $paymentMethod = $validated['payment_method'];
        $oldProofPath = $payment->proof_payment;

        try {
            $proofPath = $this->fileUploadService->storePublic($request->file('proof_payment'), 'payments/proofs');
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'Upload bukti pembayaran gagal. Pastikan file valid dan coba lagi.');
        }

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
            $this->fileUploadService->deletePublic($oldProofPath);
        }

        $rentalAdminId = $booking->rentalCompany?->user_id;
        if ($rentalAdminId) {
            $this->notificationService->notifyUser(
                userId: (int) $rentalAdminId,
                title: 'Bukti Pembayaran Baru',
                message: 'Booking ' . $booking->booking_code . ' mengunggah bukti pembayaran baru.',
                type: 'info',
                url: route('admin-rental.payments.show', $booking),
                referenceType: 'payment',
                referenceId: (int) $payment->id,
            );
        }

        $this->activityLogService->log(
            action: 'payment.uploaded',
            description: 'Customer upload bukti pembayaran untuk booking: ' . $booking->booking_code,
            targetType: 'payment',
            targetId: (int) $payment->id,
            meta: ['booking_id' => $booking->id, 'payment_method' => $paymentMethod]
        );

        return redirect()
            ->route('pembayaran.show', $booking)
            ->with('success', 'Bukti pembayaran berhasil diupload dan menunggu verifikasi admin rental.');
    }

    public function invoice(Booking $booking): View
    {
        $this->authorize('view', $booking);
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
        $this->authorize('view', $booking);
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
