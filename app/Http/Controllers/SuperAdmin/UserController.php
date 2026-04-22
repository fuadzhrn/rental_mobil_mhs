<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'role' => ['nullable', 'in:customer,admin_rental,super_admin'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $users = User::query()
            ->when($request->filled('role'), function ($query) use ($request): void {
                $query->where('role', $request->string('role')->toString());
            })
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('super-admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $detail = [];

        if ($user->role === 'customer') {
            $bookingQuery = Booking::query()->where('customer_id', $user->id);

            $detail = [
                'booking_count' => (clone $bookingQuery)->count(),
                'completed_booking_count' => (clone $bookingQuery)->where('booking_status', Booking::BOOKING_COMPLETED)->count(),
                'verified_transaction_total' => (float) ((clone $bookingQuery)->where('payment_status', Booking::PAYMENT_VERIFIED)->sum('total_amount') ?? 0),
                'review_count' => $user->reviews()->count(),
            ];
        }

        if ($user->role === 'admin_rental') {
            $rentalCompany = $user->rentalCompany;
            $detail = [
                'rental_company' => $rentalCompany,
                'vehicle_count' => $rentalCompany?->vehicles()->count() ?? 0,
                'booking_count' => $rentalCompany?->bookings()->count() ?? 0,
            ];
        }

        return view('super-admin.users.show', compact('user', 'detail'));
    }
}
