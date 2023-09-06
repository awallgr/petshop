<?php

namespace App\Repositories;

use App\Models\User;
use App\Contracts\UserRepositoryContract;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryContract
{
    /**
     * @param array<string, string> $query
     *
     * @return LengthAwarePaginator<User>
     */
    public function getAllUsers(array $query = []): LengthAwarePaginator
    {
        $sqlQuery = User::where('is_admin', '=', '0');
        if (isset($query['email'])) {
            $sqlQuery->where('email', '=', $query['email']);
        }
        return $sqlQuery->paginate(intval($query['limit'] ?? 15));
    }
}
