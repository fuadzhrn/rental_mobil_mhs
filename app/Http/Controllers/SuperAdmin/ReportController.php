<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentalCompany;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $baseBookings = Booking::query()
            ->when($request->filled('start_date'), function (Builder $query) use ($request): void {
                $query->whereDate('created_at', '>=', $request->string('start_date')->toString());
            })
            ->when($request->filled('end_date'), function (Builder $query) use ($request): void {
                $query->whereDate('created_at', '<=', $request->string('end_date')->toString());
            });

        $revenueQuery = (clone $baseBookings)
            ->where('payment_status', Booking::PAYMENT_VERIFIED)
            ->whereIn('booking_status', [
                Booking::BOOKING_CONFIRMED,
                Booking::BOOKING_ONGOING,
                Booking::BOOKING_COMPLETED,
            ]);

        $transactionRevenue = (float) ((clone $revenueQuery)->sum('total_amount') ?? 0);
        $commissionRate = (float) config('platform.commission_percentage', 10);
        $totalCommission = round($transactionRevenue * ($commissionRate / 100), 2);

        $stats = [
            'total_rentals' => RentalCompany::count(),
            'total_rentals_approved' => RentalCompany::where('status_verification', RentalCompany::STATUS_APPROVED)->count(),
            'total_rentals_pending' => RentalCompany::where('status_verification', RentalCompany::STATUS_PENDING)->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_admin_rental' => User::where('role', 'admin_rental')->count(),
            'total_vehicles' => Vehicle::count(),
            'total_bookings' => (clone $baseBookings)->count(),
            'total_bookings_completed' => (clone $baseBookings)->where('booking_status', Booking::BOOKING_COMPLETED)->count(),
            'total_payment_verified' => Payment::where('payment_status', Payment::STATUS_VERIFIED)->count(),
            'total_transaction_revenue' => $transactionRevenue,
            'total_commission' => $totalCommission,
            'commission_rate' => $commissionRate,
        ];

        $latestTransactions = (clone $revenueQuery)
            ->with(['rentalCompany', 'vehicle', 'customer'])
            ->latest('id')
            ->take(10)
            ->get();

        $topRentals = RentalCompany::query()
            ->leftJoin('bookings', 'rental_companies.id', '=', 'bookings.rental_company_id')
            ->select('rental_companies.id', 'rental_companies.company_name', DB::raw('COUNT(bookings.id) as total_bookings'))
            ->groupBy('rental_companies.id', 'rental_companies.company_name')
            ->orderByDesc('total_bookings')
            ->take(10)
            ->get();

        $topVehicles = Vehicle::query()
            ->leftJoin('bookings', 'vehicles.id', '=', 'bookings.vehicle_id')
            ->select('vehicles.id', 'vehicles.name', DB::raw('COUNT(bookings.id) as total_bookings'))
            ->groupBy('vehicles.id', 'vehicles.name')
            ->orderByDesc('total_bookings')
            ->take(10)
            ->get();

        return view('super-admin.reports.index', compact('stats', 'latestTransactions', 'topRentals', 'topVehicles'));
    }
}
