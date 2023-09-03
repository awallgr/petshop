<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Contracts\UserRepositoryContract;
use App\Models\User;

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
