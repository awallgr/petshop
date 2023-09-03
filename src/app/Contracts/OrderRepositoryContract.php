<?php

namespace App\Contracts;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Order;

interface OrderRepositoryContract
{
    /**
     * @return LengthAwarePaginator<Order>
     */
    public function getAllOrders(Authenticatable $user): LengthAwarePaginator;

    /**
     * @param string $uuid
     *
     * @return Order
     */
    public function find(string $uuid): Order;

    /**
     * @param Authenticatable $user
     * @param array<string, string> $data
     *
     * @return Order
     */
    public function create(Authenticatable $user, array $data): Order;

    /**
     * @param string $uuid
     * @param array<string, string> $data
     *
     * @return bool
     */
    public function update(string $uuid, array $data): bool;

    /**
     * @param string $uuid
     *
     * @return bool
     */
    public function delete(string $uuid): bool;
}
