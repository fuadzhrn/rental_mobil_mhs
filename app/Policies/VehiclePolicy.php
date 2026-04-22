<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function view(User $user, Vehicle $vehicle): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->role === 'admin_rental' && (int) $vehicle->rental_company_id === (int) $user->rentalCompany?->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin_rental' && (bool) $user->rentalCompany;
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $this->view($user, $vehicle);
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $this->view($user, $vehicle);
    }
}
