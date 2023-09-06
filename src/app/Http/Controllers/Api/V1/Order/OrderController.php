<?php

namespace App\Http\Controllers\Api\V1\Order;

use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;

class OrderController extends Controller
{
    private OrderService $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/orders",
     *      operationId="getOrders",
     *      tags={"Orders"},
     *      summary="List all orders",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        return $this->service->getAllOrders($request);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/order",
     *      operationId="createOrder",
     *      tags={"Orders"},
     *      summary="Create a new order",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"order_status_uuid"},
     *                 @OA\Property(
     *                     property="order_status_uuid",
     *                     type="string",
     *                     description="Order status UUID"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        return $this->service->createOrder($request);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/order/{uuid}",
     *      operationId="getOrder",
     *      tags={"Orders"},
     *      summary="Fetch a order",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function show(string $uuid): JsonResponse
    {
        return $this->service->getOrder($uuid);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/order/{uuid}",
     *      operationId="updateOrder",
     *      tags={"Orders"},
     *      summary="Update an existing order",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"order_status_uuid"},
     *                 @OA\Property(
     *                     property="order_status_uuid",
     *                     type="string",
     *                     description="Order status UUID"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function update(UpdateOrderRequest $request, string $uuid): JsonResponse
    {
        return $this->service->updateOrder($uuid, $request);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/order/{uuid}",
     *      operationId="deleteOrder",
     *      tags={"Orders"},
     *      summary="Delete an existing order",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function delete(string $uuid): JsonResponse
    {
        return $this->service->deleteOrder($uuid);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/order/current-state/{uuid}",
     *      operationId="getOrderCurrentState",
     *      tags={"Orders"},
     *      summary="Fetch current state of Order",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function currentState(string $uuid): JsonResponse
    {
        return $this->service->getCurrentStateOfOrder($uuid);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/order/available-states/{uuid}",
     *      operationId="getOrderAvailableStates",
     *      tags={"Orders"},
     *      summary="Fetch available states of Order",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function availableStates(string $uuid): JsonResponse
    {
        return $this->service->getAvailableStatesOfOrder($uuid);
    }
}
