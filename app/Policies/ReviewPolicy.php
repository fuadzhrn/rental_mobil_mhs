<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin_rental'], true);
    }

    public function view(User $user, Review $review): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'customer') {
            return (int) $review->customer_id === (int) $user->id;
        }

        if ($user->role === 'admin_rental') {
            return (int) $review->rental_company_id === (int) $user->rentalCompany?->id;
        }

        return false;
    }

    public function create(User $user, Booking $booking): bool
    {
        return $user->role === 'customer'
            && (int) $booking->customer_id === (int) $user->id
            && $booking->booking_status === Booking::BOOKING_COMPLETED
            && !$booking->review()->exists();
    }
}
