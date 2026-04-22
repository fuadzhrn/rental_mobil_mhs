<?php

namespace App\Policies;

use App\Models\RentalCompany;
use App\Models\User;

class RentalCompanyPolicy
{
    public function view(User $user, RentalCompany $rentalCompany): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->role === 'admin_rental' && (int) $rentalCompany->user_id === (int) $user->id;
    }

    public function verify(User $user, RentalCompany $rentalCompany): bool
    {
        return $user->role === 'super_admin';
    }

    public function update(User $user, RentalCompany $rentalCompany): bool
    {
        return $this->view($user, $rentalCompany);
    }
}
