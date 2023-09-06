<?php

namespace App\Services;

use Auth;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Repositories\OrderRepository;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResourceCollection;
use Illuminate\Foundation\Auth\User as Authenticatable;
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
        try {
            /** @var Authenticatable $user */
            $user = Auth::User();

            Gate::authorize('store', Order::class);

            $order = $this->repository->create($user, $request->all());
            $this->sendOrderUpdateEvent($order);
            return response()->success($order->toArray());
        } catch (\Exception $e) {
            return response()->fail("Can't create order", 422);
        }
    }

    public function getOrder(string $uuid): JsonResponse
    {
        try {
            $order = $this->repository->find($uuid);

            Gate::authorize('show', $order);

            return response()->success($order->toArray());
        } catch (\Exception $e) {
            return response()->fail('Order not found', 422);
        }
    }

    public function updateOrder(string $uuid, UpdateOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->repository->find($uuid);

            Gate::authorize('update', $order);

            if ($this->isValidStateChange($order, $request)) {
                $this->updateAndProcessEvent($order, $request);
                return response()->success(['message' => 'Order updated']);
            }
            return response()->fail('Order failed to update', 422);
        } catch (\Exception $e) {
            return response()->fail('Order failed to update', 422);
        }
    }

    private function isValidStateChange(Order $order, UpdateOrderRequest $request): bool
    {
        $order->changeCurrentStateFromName($this->getOrderStatusTitle($order->status));
        $nextOrderStatus = OrderStatus::where('uuid', '=', $request['order_status_uuid'])->first();
        $nextState = $order->getStateFromName($this->getOrderStatusTitle($nextOrderStatus));

        return $nextState && $order->isValidNextState($nextState);
    }

    private function updateAndProcessEvent(Order $order, UpdateOrderRequest $request): void
    {
        $nextOrderStatus = OrderStatus::where('uuid', '=', $request['order_status_uuid'])->first();
        $nextState = $order->getStateFromName($this->getOrderStatusTitle($nextOrderStatus));
        if ($nextState) {
            $transition = $order->getTransitionToState($nextState);

            if ($transition) {
                $this->repository->update($order->uuid, $request->all());
                $order->process($transition->name);
                $this->sendOrderUpdateEvent($order);
            }
        }
    }

    public function deleteOrder(string $uuid): JsonResponse
    {
        try {
            $order = $this->repository->find($uuid);

            Gate::authorize('delete', $order);

            $this->repository->delete($uuid);
            return response()->success(['message' => 'Order deleted']);
        } catch (\Exception $e) {
            return response()->fail('Order could not be deleted');
        }
    }

    public function getCurrentStateOfOrder(string $uuid): JsonResponse
    {
        try {
            $order = $this->repository->find($uuid);
            $order->changeCurrentStateFromName($this->getOrderStatusTitle($order->status));

            Gate::authorize('show', $order);

            return response()->success(['current_state' => $order->getCurrentState()->name]);
        } catch (\Exception $e) {
            return response()->fail('Failed getting current state');
        }
    }

    public function getAvailableStatesOfOrder(string $uuid): JsonResponse
    {
        try {
            $order = $this->repository->find($uuid);
            $order->changeCurrentStateFromName($this->getOrderStatusTitle($order->status));

            Gate::authorize('show', $order);

            $nextTransitions = $order->getNextTransitions();
            $availableStates = [];
            foreach ($nextTransitions as $transition) {
                $availableStates[] = $transition->to;
            }

            return response()->success(['available_states' => $availableStates]);
        } catch (\Exception $e) {
            return response()->fail('Failed getting current state');
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

    private function getOrderStatusTitle(?OrderStatus $orderStatus): string
    {
        return $orderStatus->title ?? "";
    }
}
