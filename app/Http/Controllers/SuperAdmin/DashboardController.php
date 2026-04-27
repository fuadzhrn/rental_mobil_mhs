<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\RentalCompany;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        $totalRevenue = (float) ((clone $validCommissionBase)->sum('total_amount') ?? 0);
        $totalCommission = round($totalRevenue * ($commissionRate / 100), 2);

        $totalRental = (int) RentalCompany::count();
        $mitraRental = (int) RentalCompany::approved()->count();
        $totalCustomers = (int) User::where('role', 'customer')->count();
        $totalVehicles = (int) Vehicle::count();
        $verifiedPayments = (int) Booking::where('payment_status', Booking::PAYMENT_VERIFIED)->count();
        $pendingCount = (int) RentalCompany::where('status_verification', RentalCompany::STATUS_PENDING)->count();

        $monthlyBaseQuery = Booking::query();
        $driverName = DB::connection()->getDriverName();

        $monthKeyExpression = $driverName === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthlyBookingCounts = $monthlyBaseQuery
            ->selectRaw($monthKeyExpression . ' as month_key, COUNT(*) as total')
            ->groupByRaw($monthKeyExpression)
            ->orderByRaw($monthKeyExpression)
            ->pluck('total', 'month_key');

        $monthlyBookings = collect(range(11, 0))
            ->map(function (int $offset) use ($monthlyBookingCounts): array {
                $month = Carbon::now()->startOfMonth()->subMonths($offset);
                $monthKey = $month->format('Y-m');

                return [
                    'label' => $month->format('M Y'),
                    'value' => (int) ($monthlyBookingCounts[$monthKey] ?? 0),
                ];
            })
            ->values();

        $summary = [
            ['label' => 'Mitra Rental', 'value' => $mitraRental, 'hint' => 'Rental approved / aktif', 'icon' => 'bi-buildings'],
            ['label' => 'Customer', 'value' => $totalCustomers, 'hint' => 'Akun customer terdaftar', 'icon' => 'bi-people'],
            ['label' => 'Kendaraan', 'value' => $totalVehicles, 'hint' => 'Total armada aktif', 'icon' => 'bi-car-front'],
            ['label' => 'Total Rental', 'value' => $totalRental, 'hint' => 'Seluruh rental terdaftar', 'icon' => 'bi-shop'],
            ['label' => 'Revenue', 'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'), 'hint' => 'Transaksi verified', 'icon' => 'bi-cash-stack'],
            ['label' => 'Payment Verified', 'value' => $verifiedPayments, 'hint' => 'Pembayaran terverifikasi', 'icon' => 'bi-patch-check'],
            ['label' => 'Pending', 'value' => $pendingCount, 'hint' => 'Rental menunggu verifikasi', 'icon' => 'bi-hourglass-split'],
            ['label' => 'Komisi', 'value' => 'Rp ' . number_format($totalCommission, 0, ',', '.'), 'hint' => 'Estimasi komisi platform', 'icon' => 'bi-percent'],
        ];

        $quickLinks = [
            ['label' => 'Verifikasi Rental', 'route' => route('super-admin.rentals.index'), 'icon' => 'bi-shield-check', 'hint' => 'Tinjau pengajuan partner'],
            ['label' => 'Semua User', 'route' => route('super-admin.users.index'), 'icon' => 'bi-people', 'hint' => 'Kelola akun sistem'],
            ['label' => 'Laporan', 'route' => route('super-admin.reports.index'), 'icon' => 'bi-graph-up-arrow', 'hint' => 'Monitoring performa'],
            ['label' => 'Komisi', 'route' => route('super-admin.reports.commissions'), 'icon' => 'bi-receipt', 'hint' => 'Detail komisi platform'],
        ];

        return view('super-admin.dashboard', compact(
            'summary',
            'quickLinks',
            'commissionRate',
            'totalRevenue',
            'totalCommission',
            'totalRental',
            'mitraRental',
            'totalCustomers',
            'totalVehicles',
            'verifiedPayments',
            'pendingCount',
            'monthlyBookings'
        ));
    }
}
