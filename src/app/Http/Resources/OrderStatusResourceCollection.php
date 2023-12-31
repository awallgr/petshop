<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderStatusResourceCollection extends ResourceCollection
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($orderStatus) {
            return [
                'uuid' => $orderStatus->uuid,
                'title' => $orderStatus->title,
                'created_at' => $orderStatus->created_at,
                'updated_at' => $orderStatus->updated_at,
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
