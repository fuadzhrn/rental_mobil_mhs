<?php

namespace App\Http\Controllers\AdminRental;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    private float $commissionRate = 10.0; // 10% commission

    /**
     * Dashboard ringkas laporan rental
     */
    public function index(Request $request): View
    {
        $rentalId = Auth::user()->rentalCompany->id;
        $dateFilter = $this->getDateFilter($request);

        // Total Booking untuk rental ini
        $totalBookings = $this->getBaseBookingsQuery($rentalId, $dateFilter)->count();

        // Total Payment Verified
        $totalPaymentsVerified = $this->getBaseBookingsQuery($rentalId, $dateFilter)
            ->where('payment_status', Booking::PAYMENT_VERIFIED)
            ->count();

        // Total Revenue (dari payment verified)
        $totalRevenue = (float) ($this->getBaseBookingsQuery($rentalId, $dateFilter)
            ->where('payment_status', Booking::PAYMENT_VERIFIED)
            ->sum('total_amount') ?? 0);

        // Total Commission (10% dari revenue)
        $totalCommission = round($totalRevenue * ($this->commissionRate / 100), 2);

        // Total Vehicles (milik rental)
        $totalVehicles = Vehicle::where('rental_company_id', $rentalId)->count();

        // Total Active Customers (yang pernah booking ke rental ini)
        $totalActiveCustomers = User::query()
            ->where('role', 'customer')
            ->whereHas('bookings', fn($q) => $q->where('rental_company_id', $rentalId))
            ->count();

        return view('admin-rental.reports.index', compact(
            'totalBookings',
            'totalPaymentsVerified',
            'totalRevenue',
            'totalCommission',
            'totalVehicles',
            'totalActiveCustomers'
        ));
    }

    /**
     * Laporan Booking Rental
     */
    public function bookings(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'booking_status' => ['nullable', 'in:waiting_payment,waiting_verification,confirmed,ongoing,completed,cancelled'],
        ]);

        $rentalId = Auth::user()->rentalCompany->id;
        $dateFilter = $this->getDateFilter($request);

        $baseQuery = Booking::query()
            ->where('rental_company_id', $rentalId)
            ->with(['customer', 'vehicle', 'payment'])
            ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
            ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']))
            ->when($request->filled('booking_status'), fn($q) => $q->where('booking_status', $request->string('booking_status')));

        // Summary statistics
        $summary = [
            'total_bookings' => (clone $baseQuery)->count(),
            'total_completed' => (clone $baseQuery)->where('booking_status', Booking::BOOKING_COMPLETED)->count(),
            'total_cancelled' => (clone $baseQuery)->where('booking_status', Booking::BOOKING_CANCELLED)->count(),
            'total_ongoing' => (clone $baseQuery)->where('booking_status', Booking::BOOKING_ONGOING)->count(),
        ];

        $bookings = (clone $baseQuery)
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $bookingStatuses = Booking::statusOptions();

        return view('admin-rental.reports.bookings', compact(
            'bookings',
            'summary',
            'bookingStatuses'
        ));
    }

    /**
     * Laporan Pembayaran Rental
     */
    public function payments(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'payment_status' => ['nullable', 'in:unpaid,uploaded,verified,rejected'],
        ]);

        $rentalId = Auth::user()->rentalCompany->id;
        $dateFilter = $this->getDateFilter($request);

        $baseQuery = Booking::query()
            ->where('rental_company_id', $rentalId)
            ->with(['customer', 'vehicle', 'payment'])
            ->whereHas('payment')
            ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
            ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']))
            ->when($request->filled('payment_status'), fn($q) => $q->where('payment_status', $request->string('payment_status')));

        // Summary statistics
        $summary = [
            'total_payments_verified' => (clone $baseQuery)->where('payment_status', Booking::PAYMENT_VERIFIED)->count(),
            'total_payments_uploaded' => (clone $baseQuery)->where('payment_status', Booking::PAYMENT_UPLOADED)->count(),
            'total_payments_rejected' => (clone $baseQuery)->where('payment_status', Booking::PAYMENT_REJECTED)->count(),
            'total_nominal_verified' => (float) ((clone $baseQuery)->where('payment_status', Booking::PAYMENT_VERIFIED)->sum('total_amount') ?? 0),
        ];

        $bookings = (clone $baseQuery)
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $paymentStatuses = Booking::paymentStatusOptions();

        return view('admin-rental.reports.payments', compact(
            'bookings',
            'summary',
            'paymentStatuses'
        ));
    }

    /**
     * Laporan Kendaraan Terlaris Rental
     */
    public function topVehicles(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'limit' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $limit = $request->integer('limit', 20);
        $rentalId = Auth::user()->rentalCompany->id;
        $dateFilter = $this->getDateFilter($request);

        // Kendaraan terlaris berdasarkan jumlah booking dengan payment verified
        $topVehicles = Vehicle::query()
            ->where('rental_company_id', $rentalId)
            ->withCount([
                'bookings as verified_booking_count' => function (Builder $query) use ($dateFilter): void {
                    $query->where('payment_status', Booking::PAYMENT_VERIFIED)
                        ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                        ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
                },
            ])
            ->withSum([
                'bookings as total_revenue' => function (Builder $query) use ($dateFilter): void {
                    $query->where('payment_status', Booking::PAYMENT_VERIFIED)
                        ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                        ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
                },
            ], 'total_amount')
            ->having('verified_booking_count', '>', 0)
            ->orderByDesc('verified_booking_count')
            ->limit($limit)
            ->get();

        return view('admin-rental.reports.top-vehicles', compact('topVehicles'));
    }

    /**
     * Laporan Customer Aktif Rental
     */
    public function activeCustomers(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'limit' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $limit = $request->integer('limit', 20);
        $rentalId = Auth::user()->rentalCompany->id;
        $dateFilter = $this->getDateFilter($request);

        // Customer aktif berdasarkan completed bookings
        $activeCustomers = User::query()
            ->where('role', 'customer')
            ->withCount([
                'bookings as total_booking_count' => function (Builder $query) use ($rentalId, $dateFilter): void {
                    $query->where('rental_company_id', $rentalId)
                        ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                        ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
                },
                'bookings as completed_booking_count' => function (Builder $query) use ($rentalId, $dateFilter): void {
                    $query->where('rental_company_id', $rentalId)
                        ->where('booking_status', Booking::BOOKING_COMPLETED)
                        ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                        ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
                },
            ])
            ->withMax([
                'bookings as last_booking_date' => function (Builder $query) use ($rentalId, $dateFilter): void {
                    $query->where('rental_company_id', $rentalId)
                        ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                        ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
                },
            ], 'created_at')
            ->withSum([
                'bookings as total_transaction' => function (Builder $query) use ($rentalId, $dateFilter): void {
                    $query->where('rental_company_id', $rentalId)
                        ->where('payment_status', Booking::PAYMENT_VERIFIED)
                        ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                        ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
                },
            ], 'total_amount')
            ->having('completed_booking_count', '>', 0)
            ->orderByDesc('completed_booking_count')
            ->limit($limit)
            ->get();

        // Check loyal threshold (3 completed bookings)
        $loyalThreshold = 3;

        return view('admin-rental.reports.active-customers', compact('activeCustomers', 'loyalThreshold'));
    }

    /**
     * Laporan Pendapatan Rental
     */
    public function revenue(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $rentalId = Auth::user()->rentalCompany->id;
        $dateFilter = $this->getDateFilter($request);

        // Revenue untuk rental ini
        $baseQuery = Booking::query()
            ->where('rental_company_id', $rentalId)
            ->where('payment_status', Booking::PAYMENT_VERIFIED)
            ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
            ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));

        $verifiedBookingCount = (clone $baseQuery)->count();
        $grossRevenue = (float) ((clone $baseQuery)->sum('total_amount') ?? 0);

        $commission = round($grossRevenue * ($this->commissionRate / 100), 2);
        $netRevenue = round($grossRevenue - $commission, 2);

        $rentalCompany = Auth::user()->rentalCompany;

        return view('admin-rental.reports.revenue', compact(
            'rentalCompany',
            'verifiedBookingCount',
            'grossRevenue',
            'commission',
            'netRevenue'
        ));
    }

    /**
     * Helper: Get date filter array
     */
    private function getDateFilter(Request $request): array
    {
        return [
            'start' => $request->filled('start_date') ? $request->date('start_date')->startOfDay() : null,
            'end' => $request->filled('end_date') ? $request->date('end_date')->endOfDay() : null,
        ];
    }

    /**
     * Helper: Get base bookings query with filters
     */
    private function getBaseBookingsQuery(int $rentalId, array $dateFilter): Builder
    {
        return Booking::query()
            ->where('rental_company_id', $rentalId)
            ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
            ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
    }
}
