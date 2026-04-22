<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\RentalCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'rental_company_id' => ['nullable', 'integer', 'exists:rental_companies,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $commissionRate = (float) config('platform.commission_percentage', 10);

        $commissionBaseQuery = Booking::query()
            ->with(['rentalCompany', 'customer'])
            ->where('payment_status', Booking::PAYMENT_VERIFIED)
            ->whereIn('booking_status', [
                Booking::BOOKING_CONFIRMED,
                Booking::BOOKING_ONGOING,
                Booking::BOOKING_COMPLETED,
            ])
            ->when($request->filled('rental_company_id'), function (Builder $query) use ($request): void {
                $query->where('rental_company_id', (int) $request->input('rental_company_id'));
            })
            ->when($request->filled('start_date'), function (Builder $query) use ($request): void {
                $query->whereDate('created_at', '>=', $request->string('start_date')->toString());
            })
            ->when($request->filled('end_date'), function (Builder $query) use ($request): void {
                $query->whereDate('created_at', '<=', $request->string('end_date')->toString());
            });

        $totalTransaction = (float) ((clone $commissionBaseQuery)->sum('total_amount') ?? 0);
        $totalCommission = round($totalTransaction * ($commissionRate / 100), 2);

        $bookings = (clone $commissionBaseQuery)
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $rentalOptions = RentalCompany::query()
            ->orderBy('company_name')
            ->get(['id', 'company_name']);

        return view('super-admin.commissions.index', compact(
            'bookings',
            'commissionRate',
            'totalTransaction',
            'totalCommission',
            'rentalOptions'
        ));
    }
}
