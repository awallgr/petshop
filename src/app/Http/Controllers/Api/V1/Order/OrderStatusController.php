<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderStatusResourceCollection;

class OrderStatusController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/order-statuses",
     *      operationId="getOrderStatuses",
     *      tags={"OrderStatuses"},
     *      summary="List all order statuses",
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
        return (new OrderStatusResourceCollection(OrderStatus::paginate()))->toResponse($request);
    }
}
