<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Contracts\OrderRepositoryContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderStatus;

class OrderRepository implements OrderRepositoryContract
{
    public function getAllOrders(Authenticatable $user): LengthAwarePaginator
    {
        return $user->is_admin ?
            Order::paginate() :
            Order::where('user_id', '=', $user->id)->paginate();
    }

    public function find(string $uuid): Order
    {
        return Order::where('uuid', '=', $uuid)->firstOrFail();
    }

    public function create(Authenticatable $user, array $data): Order
    {
        $orderStatus = OrderStatus::where('uuid', '=', $data['order_status_uuid'])->firstOrFail();
        $orderData = [
            'user_id' => $user->id,
            'uuid' => Str::uuid(),
            'order_status_id' => $orderStatus->id,
        ];
        return Order::create($orderData);
    }

    public function update(string $uuid, array $data): bool
    {
        $order = Order::where('uuid', '=', $uuid)->firstOrFail();
        $orderStatus = OrderStatus::where('uuid', '=', $data['order_status_uuid'])->firstOrFail();
        return $order->update(['order_status_id' => $orderStatus->id]);
    }

    public function delete(string $uuid): bool
    {
        $order = Order::where('uuid', '=', $uuid)->firstOrFail();
        return (bool) $order->delete();
    }
}
