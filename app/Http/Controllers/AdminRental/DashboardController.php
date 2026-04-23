<?php

namespace App\Http\Controllers\AdminRental;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $rentalId = (int) Auth::user()->rentalCompany->id;

        $totalVehicles = Vehicle::query()
            ->where('rental_company_id', $rentalId)
            ->count();

        $activeBookings = Booking::query()
            ->where('rental_company_id', $rentalId)
            ->whereIn('booking_status', [
                Booking::BOOKING_CONFIRMED,
                Booking::BOOKING_ONGOING,
            ])
            ->count();

        $totalCustomers = Booking::query()
            ->where('rental_company_id', $rentalId)
            ->distinct('customer_id')
            ->count('customer_id');

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $verifiedRevenueThisMonth = (float) Booking::query()
            ->where('rental_company_id', $rentalId)
            ->where('payment_status', Booking::PAYMENT_VERIFIED)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        $paymentsWaitingVerification = Booking::query()
            ->where('rental_company_id', $rentalId)
            ->where('payment_status', Booking::PAYMENT_UPLOADED)
            ->count();

        $bookingsToday = Booking::query()
            ->where('rental_company_id', $rentalId)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $summary = [
            ['label' => 'Total Kendaraan', 'value' => number_format($totalVehicles, 0, ',', '.')],
            ['label' => 'Booking Aktif', 'value' => number_format($activeBookings, 0, ',', '.')],
            ['label' => 'Customer', 'value' => number_format($totalCustomers, 0, ',', '.')],
            ['label' => 'Pendapatan Verified Bulan Ini', 'value' => 'Rp ' . number_format($verifiedRevenueThisMonth, 0, ',', '.')],
            ['label' => 'Payment Menunggu Verifikasi', 'value' => number_format($paymentsWaitingVerification, 0, ',', '.')],
            ['label' => 'Booking Hari Ini', 'value' => number_format($bookingsToday, 0, ',', '.')],
        ];

        return view('admin-rental.dashboard', compact('summary'));
    }
}
