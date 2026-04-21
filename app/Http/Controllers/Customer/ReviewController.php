<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function create(Booking $booking): View|RedirectResponse
    {
        if ((int) $booking->customer_id !== (int) Auth::id()) {
            abort(404);
        }

        if ($booking->booking_status !== Booking::BOOKING_COMPLETED) {
            return redirect()
                ->route('customer.bookings.show', $booking)
                ->with('error', 'Ulasan hanya bisa dibuat setelah booking selesai.');
        }

        if ($booking->review()->exists()) {
            return redirect()
                ->route('customer.bookings.show', $booking)
                ->with('error', 'Booking ini sudah memiliki ulasan.');
        }

        $booking->load(['vehicle.rentalCompany']);

        return view('customer.reviews.create', compact('booking'));
    }

    public function store(StoreReviewRequest $request, Booking $booking): RedirectResponse
    {
        if ((int) $booking->customer_id !== (int) Auth::id()) {
            abort(404);
        }

        if ($booking->booking_status !== Booking::BOOKING_COMPLETED) {
            return redirect()
                ->route('customer.bookings.show', $booking)
                ->with('error', 'Ulasan hanya bisa dibuat setelah booking selesai.');
        }

        if ($booking->review()->exists()) {
            return redirect()
                ->route('customer.bookings.show', $booking)
                ->with('error', 'Booking ini sudah memiliki ulasan.');
        }

        $validated = $request->validated();

        Review::create([
            'booking_id' => $booking->id,
            'customer_id' => Auth::id(),
            'vehicle_id' => $booking->vehicle_id,
            'rental_company_id' => $booking->rental_company_id,
            'rating' => $validated['rating'],
            'review' => $validated['review'] ?? null,
        ]);

        return redirect()
            ->route('customer.bookings.show', $booking)
            ->with('success', 'Ulasan berhasil dikirim. Terima kasih atas feedback Anda.');
    }
}
