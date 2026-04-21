<?php

namespace App\Http\Controllers\AdminRental;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\RentalCompany;
use App\Models\Review;
use App\Models\User;
use App\Services\PromoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            return redirect()->route('admin-rental.dashboard')->with('error', 'Akun admin rental ini belum memiliki rental company.');
        }

        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'loyal' => ['nullable', 'in:loyal,non_loyal'],
        ]);

        $threshold = PromoService::LOYAL_COMPLETED_THRESHOLD;
        $loyalCustomerIdSubQuery = Booking::query()
            ->select('customer_id')
            ->where('rental_company_id', $rentalCompany->id)
            ->where('booking_status', Booking::BOOKING_COMPLETED)
            ->groupBy('customer_id')
            ->havingRaw('COUNT(*) >= ?', [$threshold]);

        $customers = User::query()
            ->whereHas('bookings', function (Builder $query) use ($rentalCompany): void {
                $query->where('rental_company_id', $rentalCompany->id);
            })
            ->withCount([
                'bookings as booking_count' => function (Builder $query) use ($rentalCompany): void {
                    $query->where('rental_company_id', $rentalCompany->id);
                },
                'bookings as completed_booking_count' => function (Builder $query) use ($rentalCompany): void {
                    $query->where('rental_company_id', $rentalCompany->id)
                        ->where('booking_status', Booking::BOOKING_COMPLETED);
                },
            ])
            ->withSum([
                'bookings as total_transaction_amount' => function (Builder $query) use ($rentalCompany): void {
                    $query->where('rental_company_id', $rentalCompany->id);
                },
            ], 'total_amount')
            ->withMax([
                'bookings as last_booking_at' => function (Builder $query) use ($rentalCompany): void {
                    $query->where('rental_company_id', $rentalCompany->id);
                },
            ], 'created_at')
            ->withAvg([
                'reviews as average_rating_given' => function (Builder $query) use ($rentalCompany): void {
                    $query->where('rental_company_id', $rentalCompany->id);
                },
            ], 'rating')
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function (Builder $subQuery) use ($search): void {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($request->string('loyal')->toString() === 'loyal', function (Builder $query) use ($loyalCustomerIdSubQuery): void {
                $query->whereIn('id', $loyalCustomerIdSubQuery);
            })
            ->when($request->string('loyal')->toString() === 'non_loyal', function (Builder $query) use ($loyalCustomerIdSubQuery): void {
                $query->whereNotIn('id', $loyalCustomerIdSubQuery);
            })
            ->orderByDesc('last_booking_at')
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total_customers' => Booking::query()
                ->where('rental_company_id', $rentalCompany->id)
                ->distinct('customer_id')
                ->count('customer_id'),
            'loyal_customers' => DB::query()->fromSub($loyalCustomerIdSubQuery, 'loyal_customers')->count(),
            'completed_bookings' => Booking::query()
                ->where('rental_company_id', $rentalCompany->id)
                ->where('booking_status', Booking::BOOKING_COMPLETED)
                ->count(),
        ];

        return view('admin-rental.customers.index', compact('customers', 'rentalCompany', 'summary', 'threshold'));
    }

    public function show(User $customer): View
    {
        $rentalCompany = $this->getRentalCompanyOrAbort();

        $bookingsQuery = Booking::query()
            ->with(['vehicle', 'payment', 'review', 'promo'])
            ->where('rental_company_id', $rentalCompany->id)
            ->where('customer_id', $customer->id)
            ->latest('id');

        if (!(clone $bookingsQuery)->exists()) {
            abort(404);
        }

        $threshold = PromoService::LOYAL_COMPLETED_THRESHOLD;
        $bookingCount = (clone $bookingsQuery)->count();
        $completedCount = (clone $bookingsQuery)->where('booking_status', Booking::BOOKING_COMPLETED)->count();
        $totalTransactionAmount = (float) ((clone $bookingsQuery)->sum('total_amount') ?? 0);
        $lastBooking = (clone $bookingsQuery)->first();
        $lastBookingDate = $lastBooking?->created_at;

        $vehicles = (clone $bookingsQuery)
            ->with('vehicle')
            ->distinct('vehicle_id')
            ->get()
            ->pluck('vehicle')
            ->unique('id')
            ->sortBy('name');

        $reviews = Review::query()
            ->with(['vehicle', 'booking'])
            ->where('rental_company_id', $rentalCompany->id)
            ->where('customer_id', $customer->id)
            ->latest('id')
            ->get();

        $bookings = $bookingsQuery->paginate(8);
        $isLoyal = $completedCount >= $threshold;

        return view('admin-rental.customers.show', compact(
            'customer',
            'rentalCompany',
            'bookings',
            'bookingCount',
            'completedCount',
            'totalTransactionAmount',
            'lastBookingDate',
            'vehicles',
            'reviews',
            'threshold',
            'isLoyal'
        ));
    }

    private function getRentalCompany(): ?RentalCompany
    {
        return Auth::user()?->rentalCompany;
    }

    private function getRentalCompanyOrAbort(): RentalCompany
    {
        $rentalCompany = $this->getRentalCompany();

        if (!$rentalCompany) {
            abort(404);
        }

        return $rentalCompany;
    }
}
