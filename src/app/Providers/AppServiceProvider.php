<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', function (array $data = [], $code = 200): JsonResponse {
            return response()->json([
                'success' => 1,
                'data' => $data,
                'error' => null,
                'errors' => [],
                'trace' => []
            ], $code);
        });
        Response::macro('fail', function (string $message = null, $code = 422): JsonResponse {
            return response()->json([
                'success' => 0,
                'data' => [],
                'error' => $message,
                'errors' => [],
                'trace' => []
            ], $code);
        });
    }
}
