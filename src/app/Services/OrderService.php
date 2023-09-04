<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use App\Http\Resources\OrderResourceCollection;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Andreas\NotificationService\Events\OrderStatusUpdatedEvent;

class OrderService
{
    private OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllOrders(Request $request): JsonResponse
    {
        /** @var Authenticatable $user */
        $user = Auth::User();
        return (new OrderResourceCollection($this->repository->getAllOrders($user)))->toResponse($request);
    }

    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        /** @var Authenticatable $user */
        $user = Auth::User();
        if ($user->cannot('store', Order::class)) {
            return response()->fail("Can't create order", 403);
        }
        $order = $this->repository->create($user, $request->all());
        $this->sendOrderUpdateEvent($order);
        return response()->success($order->toArray());
    }

    public function getOrder(string $uuid): JsonResponse
    {
        try {
            /** @var Authenticatable $user */
            $user = Auth::User();
            $order = $this->repository->find($uuid);
            return ($order != null && $user->can('show', $order)) ?
                response()->success($order->toArray()) :
                response()->fail('Order not found', 422);
        } catch (\Exception $e) {
            return response()->fail('Order not found', 422);
        }
    }

    public function updateOrder(string $uuid, UpdateOrderRequest $request): JsonResponse
    {
        try {
            /** @var Authenticatable $user */
            $user = Auth::User();
            $order = $this->repository->find($uuid);
            if ($user->can('update', $order) && $this->repository->update($uuid, $request->all())) {
                $this->sendOrderUpdateEvent($order);
                return response()->success(['message' => 'Order updated']);
            }
            return response()->fail('Order could not be updated');
        } catch (\Exception $e) {
            return response()->fail('Order not found', 422);
        }
    }

    public function deleteOrder(string $uuid): JsonResponse
    {
        try {
            /** @var Authenticatable $user */
            $user = Auth::User();
            $order = $this->repository->find($uuid);
            if ($user->can('delete', $order) && $this->repository->delete($uuid)) {
                return response()->success(['message' => 'Order deleted']);
            }
            return response()->fail('Order could not be deleted');
        } catch (\Exception $e) {
            return response()->fail('Order not found', 422);
        }
    }

    private function sendOrderUpdateEvent(Order $order): void
    {
        $order->refresh();
        $event = new OrderStatusUpdatedEvent(
            $order->uuid,
            optional($order->status)->title,
            optional($order->updated_at)->format("Y-m-d H:i:s")
        );
        event($event);
    }
}
