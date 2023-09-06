<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderResourceCollection extends ResourceCollection
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($order) {
            return [
                'user_id' => $order->user_id,
                'uuid' => $order->uuid,
                'order_status_id' => $order->order_status_id,
                'products' => $order->products,
                'address' => $order->address,
                'delivery_fee' => $order->delivery_fee,
                'amount' => $order->amount,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'shipped_at' => $order->shipped_at,
            ];
        })->toArray();
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'current_page' => $this->resource->currentPage(),
            'data' => $this->toArray($request),
            'first_page_url' => $this->resource->url(1),
            'from' => $this->resource->firstItem(),
            'last_page' => $this->resource->lastPage(),
            'last_page_url' => $this->resource->url($this->resource->lastPage()),
            'links' => $this->resource->toArray()['links'],
            'next_page_url' => $this->resource->nextPageUrl(),
            'path' => $this->resource->path(),
            'per_page' => $this->resource->perPage(),
            'prev_page_url' => $this->resource->previousPageUrl(),
            'to' => $this->resource->lastItem(),
            'total' => $this->resource->total(),
        ]);
    }
}
