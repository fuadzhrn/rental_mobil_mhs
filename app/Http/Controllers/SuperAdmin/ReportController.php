<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentalCompany;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    private float $commissionRate = 10.0; // 10% commission

    /**
     * Dashboard ringkas laporan
     */
    public function index(Request $request): View
    {
        $dateFilter = $this->getDateFilter($request);

        // Total Booking
        $totalBookings = $this->getBaseBookingsQuery($request, $dateFilter)->count();

        // Total Payment Verified
        $totalPaymentsVerified = $this->getBaseBookingsQuery($request, $dateFilter)
            ->where('payment_status', Booking::PAYMENT_VERIFIED)
            ->count();

        // Total Revenue (dari payment verified)
        $totalRevenue = (float) ($this->getBaseBookingsQuery($request, $dateFilter)
            ->where('payment_status', Booking::PAYMENT_VERIFIED)
            ->sum('total_amount') ?? 0);

        // Total Commission
        $totalCommission = round($totalRevenue * ($this->commissionRate / 100), 2);

        // Total Vehicles
        $totalVehicles = Vehicle::count();

        // Total Active Customers (yang pernah booking)
        $totalActiveCustomers = User::query()
            ->where('role', 'customer')
            ->whereHas('bookings')
            ->count();

        return view('super-admin.reports.index', compact(
            'totalBookings',
            'totalPaymentsVerified',
            'totalRevenue',
            'totalCommission',
            'totalVehicles',
            'totalActiveCustomers'
        ));
    }

    /**
     * Laporan Booking per Rental
     */
    public function bookings(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'rental_id' => ['nullable', 'exists:rental_companies,id'],
            'booking_status' => ['nullable', 'in:waiting_payment,waiting_verification,confirmed,ongoing,completed,cancelled'],
        ]);

        $dateFilter = $this->getDateFilter($request);

        $baseQuery = Booking::query()
            ->with(['customer', 'vehicle.rentalCompany', 'payment'])
            ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
            ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']))
            ->when($request->filled('rental_id'), fn($q) => $q->where('rental_company_id', $request->integer('rental_id')))
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

        $rentalCompanies = RentalCompany::orderBy('company_name')->get(['id', 'company_name']);
        $bookingStatuses = Booking::statusOptions();

        return view('super-admin.reports.bookings', compact(
            'bookings',
            'summary',
            'rentalCompanies',
            'bookingStatuses'
        ));
    }

    /**
     * Laporan Pembayaran
     */
    public function payments(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'rental_id' => ['nullable', 'exists:rental_companies,id'],
            'payment_status' => ['nullable', 'in:unpaid,uploaded,verified,rejected'],
        ]);

        $dateFilter = $this->getDateFilter($request);

        $baseQuery = Booking::query()
            ->with(['customer', 'vehicle.rentalCompany', 'payment'])
            ->whereHas('payment')
            ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
            ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']))
            ->when($request->filled('rental_id'), fn($q) => $q->where('rental_company_id', $request->integer('rental_id')))
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

        $rentalCompanies = RentalCompany::orderBy('company_name')->get(['id', 'company_name']);
        $paymentStatuses = Booking::paymentStatusOptions();

        return view('super-admin.reports.payments', compact(
            'bookings',
            'summary',
            'rentalCompanies',
            'paymentStatuses'
        ));
    }

    /**
     * Laporan Kendaraan Terlaris
     */
    public function topVehicles(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'limit' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $limit = $request->integer('limit', 20);
        $dateFilter = $this->getDateFilter($request);

        // Kendaraan terlaris berdasarkan jumlah booking dengan payment verified
        $topVehicles = Vehicle::query()
            ->with('rentalCompany')
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
            ->whereHas('bookings', function (Builder $query) use ($dateFilter): void {
                $query->where('payment_status', Booking::PAYMENT_VERIFIED)
                    ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                    ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
            })
            ->orderByDesc('verified_booking_count')
            ->limit($limit)
            ->get();

        return view('super-admin.reports.top-vehicles', compact('topVehicles'));
    }

    /**
     * Laporan Customer Aktif
     */
    public function activeCustomers(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'limit' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $limit = $request->integer('limit', 20);
        $dateFilter = $this->getDateFilter($request);

        // Customer aktif berdasarkan completed bookings
        $activeCustomers = User::query()
            ->where('role', 'customer')
            ->withCount([
                'bookings as total_booking_count' => function (Builder $query) use ($dateFilter): void {
                    $query->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                        ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
                },
                'bookings as completed_booking_count' => function (Builder $query) use ($dateFilter): void {
                    $query->where('booking_status', Booking::BOOKING_COMPLETED)
                        ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                        ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
                },
            ])
            ->withMax([
                'bookings as last_booking_date' => function (Builder $query) use ($dateFilter): void {
                    $query->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                        ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
                },
            ], 'created_at')
            ->withSum([
                'bookings as total_transaction' => function (Builder $query) use ($dateFilter): void {
                    $query->where('payment_status', Booking::PAYMENT_VERIFIED)
                        ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                        ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
                },
            ], 'total_amount')
            ->whereHas('bookings', function (Builder $query) use ($dateFilter): void {
                $query->where('booking_status', Booking::BOOKING_COMPLETED)
                    ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                    ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
            })
            ->orderByDesc('completed_booking_count')
            ->limit($limit)
            ->get();

        // Check loyal threshold (3 completed bookings)
        $loyalThreshold = 3;

        return view('super-admin.reports.active-customers', compact('activeCustomers', 'loyalThreshold'));
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

        $dateFilter = $this->getDateFilter($request);

        // Revenue per rental
        $revenues = RentalCompany::query()
            ->with(['user'])
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
            ->where('status_verification', RentalCompany::STATUS_APPROVED)
            ->whereHas('bookings', function (Builder $query) use ($dateFilter): void {
                $query->where('payment_status', Booking::PAYMENT_VERIFIED)
                    ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
                    ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
            })
            ->orderByDesc('total_revenue')
            ->get();

        // Calculate commission for each rental
        $revenues = $revenues->map(function ($rental) {
            $gross = (float) ($rental->total_revenue ?? 0);
            $commission = round($gross * ($this->commissionRate / 100), 2);
            $net = round($gross - $commission, 2);

            return [
                'rental' => $rental,
                'verified_booking_count' => $rental->verified_booking_count ?? 0,
                'gross_revenue' => $gross,
                'commission' => $commission,
                'net_revenue' => $net,
            ];
        });

        // Total summary
        $totalGross = $revenues->sum('gross_revenue');
        $totalCommission = $revenues->sum('commission');
        $totalNet = $revenues->sum('net_revenue');

        return view('super-admin.reports.revenue', compact(
            'revenues',
            'totalGross',
            'totalCommission',
            'totalNet'
        ));
    }

    /**
     * Laporan Komisi Platform
     */
    public function commissions(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'rental_id' => ['nullable', 'exists:rental_companies,id'],
            'rental_company_id' => ['nullable', 'exists:rental_companies,id'],
            'payment_status' => ['nullable', 'in:unpaid,uploaded,verified,rejected'],
            'booking_status' => ['nullable', 'in:waiting_payment,waiting_verification,confirmed,ongoing,completed,cancelled'],
        ]);

        $dateFilter = $this->getDateFilter($request);
        $selectedRentalId = $request->integer('rental_company_id') ?: $request->integer('rental_id');

        // Commission dari transaksi verified
        $baseQuery = Booking::query()
            ->with(['rentalCompany', 'customer'])
            ->where('payment_status', Booking::PAYMENT_VERIFIED)
            ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
            ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']))
            ->when($selectedRentalId, fn($q) => $q->where('rental_company_id', $selectedRentalId))
            ->when($request->filled('payment_status'), fn($q) => $q->where('payment_status', $request->string('payment_status')))
            ->when($request->filled('booking_status'), fn($q) => $q->where('booking_status', $request->string('booking_status')));

        // Summary
        $summary = [
            'total_transactions' => (clone $baseQuery)->count(),
            'total_gross_revenue' => (float) ((clone $baseQuery)->sum('total_amount') ?? 0),
        ];
        $summary['total_commission'] = round($summary['total_gross_revenue'] * ($this->commissionRate / 100), 2);
        $summary['avg_commission_per_booking'] = $summary['total_transactions'] > 0
            ? round($summary['total_commission'] / $summary['total_transactions'], 2)
            : 0;

        $commissions = (clone $baseQuery)
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        // Add commission calculation to each commission item
        $commissions->getCollection()->transform(function ($booking) {
            $booking->commission_amount = round($booking->total_amount * ($this->commissionRate / 100), 2);
            return $booking;
        });

        $rentalCompanies = RentalCompany::orderBy('company_name')->get(['id', 'company_name']);

        return view('super-admin.commissions.index', [
            'bookings' => $commissions,
            'commissionRate' => $this->commissionRate,
            'totalTransaction' => $summary['total_gross_revenue'],
            'totalCommission' => $summary['total_commission'],
            'rentalOptions' => $rentalCompanies,
            'bookingStatuses' => Booking::statusOptions(),
        ]);
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
    private function getBaseBookingsQuery(Request $request, array $dateFilter): Builder
    {
        return Booking::query()
            ->when($dateFilter['start'], fn($q) => $q->where('created_at', '>=', $dateFilter['start']))
            ->when($dateFilter['end'], fn($q) => $q->where('created_at', '<=', $dateFilter['end']));
    }
}
