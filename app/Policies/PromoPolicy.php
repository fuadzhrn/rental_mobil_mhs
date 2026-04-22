<?php

namespace App\Policies;

use App\Models\Promo;
use App\Models\User;

class PromoPolicy
{
    public function view(User $user, Promo $promo): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->role === 'admin_rental' && (int) $promo->rental_company_id === (int) $user->rentalCompany?->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin_rental' && (bool) $user->rentalCompany;
    }

    public function update(User $user, Promo $promo): bool
    {
        return $this->view($user, $promo);
    }

    public function delete(User $user, Promo $promo): bool
    {
        return $this->view($user, $promo);
    }
}
