<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\RentalCompany;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $commissionRate = (float) config('platform.commission_percentage', 10);

        $validCommissionBase = Booking::query()
            ->where('payment_status', Booking::PAYMENT_VERIFIED)
            ->whereIn('booking_status', [
                Booking::BOOKING_CONFIRMED,
                Booking::BOOKING_ONGOING,
                Booking::BOOKING_COMPLETED,
            ]);

        $totalTransaction = (float) ((clone $validCommissionBase)->sum('total_amount') ?? 0);
        $totalCommission = round($totalTransaction * ($commissionRate / 100), 2);

        $summary = [
            ['label' => 'Total Rental', 'value' => RentalCompany::count()],
            ['label' => 'Rental Pending', 'value' => RentalCompany::where('status_verification', RentalCompany::STATUS_PENDING)->count()],
            ['label' => 'Total Customer', 'value' => User::where('role', 'customer')->count()],
            ['label' => 'Total Kendaraan', 'value' => Vehicle::count()],
            ['label' => 'Total Booking', 'value' => Booking::count()],
            ['label' => 'Payment Verified', 'value' => Booking::where('payment_status', Booking::PAYMENT_VERIFIED)->count()],
            ['label' => 'Total Komisi', 'value' => 'Rp ' . number_format($totalCommission, 0, ',', '.')],
        ];

        $quickLinks = [
            ['label' => 'Verifikasi Rental', 'route' => route('super-admin.rentals.index')],
            ['label' => 'Semua User', 'route' => route('super-admin.users.index')],
            ['label' => 'Laporan', 'route' => route('super-admin.reports.index')],
            ['label' => 'Komisi', 'route' => route('super-admin.commissions.index')],
        ];

        return view('super-admin.dashboard', compact('summary', 'quickLinks', 'commissionRate'));
    }
}
