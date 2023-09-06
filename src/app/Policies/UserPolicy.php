<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function userListing(User $user): bool
    {
        return $user->is_admin === true;
    }
}
