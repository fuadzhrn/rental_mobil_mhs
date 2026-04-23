<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminId = User::where('role', 'super_admin')->value('id');

        $map = [
            'BK-20260423-0001' => ['status' => Payment::STATUS_VERIFIED, 'method' => 'transfer_bank', 'note' => null],
            'BK-20260423-0002' => ['status' => Payment::STATUS_VERIFIED, 'method' => 'qris', 'note' => null],
            'BK-20260423-0003' => ['status' => Payment::STATUS_VERIFIED, 'method' => 'transfer_bank', 'note' => null],
            'BK-20260423-0004' => ['status' => Payment::STATUS_VERIFIED, 'method' => 'transfer_bank', 'note' => null],
            'BK-20260423-0005' => ['status' => Payment::STATUS_VERIFIED, 'method' => 'cash', 'note' => null],
            'BK-20260423-0006' => ['status' => Payment::STATUS_UPLOADED, 'method' => 'transfer_bank', 'note' => null],
            'BK-20260423-0008' => ['status' => Payment::STATUS_REJECTED, 'method' => 'transfer_bank', 'note' => 'Bukti transfer tidak jelas.'],
            'BK-20260423-0009' => ['status' => Payment::STATUS_VERIFIED, 'method' => 'transfer_bank', 'note' => null],
        ];

        foreach ($map as $bookingCode => $paymentData) {
            $booking = Booking::where('booking_code', $bookingCode)->first();

            if (!$booking) {
                continue;
            }

            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'payment_method' => $paymentData['method'],
                    'amount' => $booking->total_amount,
                    'proof_payment' => $paymentData['status'] === Payment::STATUS_UNPAID ? null : 'payments/proofs/demo-' . $booking->booking_code . '.jpg',
                    'paid_at' => in_array($paymentData['status'], [Payment::STATUS_UPLOADED, Payment::STATUS_VERIFIED, Payment::STATUS_REJECTED], true) ? now()->subDays(1) : null,
                    'verified_by' => $paymentData['status'] === Payment::STATUS_VERIFIED ? $superAdminId : null,
                    'verified_at' => $paymentData['status'] === Payment::STATUS_VERIFIED ? now()->subHours(5) : null,
                    'payment_status' => $paymentData['status'],
                    'rejection_note' => $paymentData['note'],
                ]
            );
        }
    }
}