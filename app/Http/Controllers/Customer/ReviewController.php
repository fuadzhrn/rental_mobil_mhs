<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Booking;
use App\Models\Review;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function create(Booking $booking): View|RedirectResponse
    {
        if (!$this->can('create', [Review::class, $booking])) {
            abort(403);
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
        if (!$this->can('create', [Review::class, $booking])) {
            abort(403);
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

        $review = Review::create([
            'booking_id' => $booking->id,
            'customer_id' => Auth::id(),
            'vehicle_id' => $booking->vehicle_id,
            'rental_company_id' => $booking->rental_company_id,
            'rating' => $validated['rating'],
            'review' => $validated['review'] ?? null,
        ]);

        $rentalAdminId = $booking->rentalCompany?->user_id;
        if ($rentalAdminId) {
            $this->notificationService->notifyUser(
                userId: (int) $rentalAdminId,
                title: 'Ulasan Baru Customer',
                message: 'Booking ' . $booking->booking_code . ' menerima ulasan baru.',
                type: 'info',
                url: route('admin-rental.reviews.index'),
                referenceType: 'review',
                referenceId: $review->id,
            );
        }

        $this->activityLogService->log(
            action: 'review.created',
            description: 'Customer mengirim review untuk booking: ' . $booking->booking_code,
            targetType: 'review',
            targetId: $review->id,
            meta: ['rating' => $review->rating]
        );

        return redirect()
            ->route('customer.bookings.show', $booking)
            ->with('success', 'Ulasan berhasil dikirim. Terima kasih atas feedback Anda.');
    }
}
