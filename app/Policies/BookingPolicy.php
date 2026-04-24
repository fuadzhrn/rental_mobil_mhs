<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'customer';
    }

    public function view(User $user, Booking $booking): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'customer') {
            return (int) $booking->customer_id === (int) $user->id;
        }

        if ($user->role === 'admin_rental') {
            return (int) $booking->rental_company_id === (int) $user->rentalCompany?->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->role === 'customer';
    }

    public function update(User $user, Booking $booking): bool
    {
        return $this->view($user, $booking);
    }
}
