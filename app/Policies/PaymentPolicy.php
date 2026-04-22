<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'customer') {
            return (int) $payment->booking?->customer_id === (int) $user->id;
        }

        if ($user->role === 'admin_rental') {
            return (int) $payment->booking?->rental_company_id === (int) $user->rentalCompany?->id;
        }

        return false;
    }

    public function update(User $user, Payment $payment): bool
    {
        return $this->view($user, $payment);
    }
}
