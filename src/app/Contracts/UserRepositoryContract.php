<?php

namespace App\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;

interface UserRepositoryContract
{
    /**
     * @param array<string, string> $query
     *
     * @return LengthAwarePaginator<User>
     */
    public function getAllUsers(array $query = []): LengthAwarePaginator;
}
