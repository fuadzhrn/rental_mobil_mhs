<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin_rental'], true);
    }

    public function view(User $user, User $model): bool
    {
        return in_array($user->role, ['super_admin', 'admin_rental'], true);
    }
}
