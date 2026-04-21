<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyBookingController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'booking_status' => ['nullable', 'in:waiting_payment,waiting_verification,confirmed,ongoing,completed,cancelled'],
            'payment_status' => ['nullable', 'in:unpaid,uploaded,verified,rejected'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $bookings = Booking::query()
            ->with(['vehicle.rentalCompany', 'vehicle.primaryImage', 'payment', 'review'])
            ->where('customer_id', Auth::id())
            ->when($request->filled('booking_status'), function ($query) use ($request): void {
                $query->where('booking_status', $request->string('booking_status')->toString());
            })
            ->when($request->filled('payment_status'), function ($query) use ($request): void {
                $query->where('payment_status', $request->string('payment_status')->toString());
            })
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('booking_code', 'like', '%' . $search . '%')
                        ->orWhereHas('vehicle', function ($vehicleQuery) use ($search): void {
                            $vehicleQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $bookingStatusOptions = Booking::statusOptions();
        $paymentStatusOptions = Booking::paymentStatusOptions();

        return view('customer.bookings.index', compact('bookings', 'bookingStatusOptions', 'paymentStatusOptions'));
    }

    public function show(Booking $booking): View
    {
        $this->ensureBookingOwnership($booking);

        $booking->load(['vehicle.rentalCompany', 'vehicle.primaryImage', 'customer', 'payment', 'review']);

        return view('customer.bookings.show', compact('booking'));
    }

    private function ensureBookingOwnership(Booking $booking): void
    {
        if ((int) $booking->customer_id !== (int) Auth::id()) {
            abort(404);
        }
    }
}
