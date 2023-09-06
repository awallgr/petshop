<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryContract
{
    /**
     * @param array<string, string> $query
     *
     * @return LengthAwarePaginator<User>
     */
    public function getAllUsers(array $query = []): LengthAwarePaginator;
}
