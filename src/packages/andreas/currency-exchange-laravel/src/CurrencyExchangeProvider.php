<?php

namespace Andreas\CurrencyExchange;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class CurrencyExchangeProvider extends ServiceProvider
{
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
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->publishes([
            __DIR__.'/config/currency_exchange.php' => config_path('currency_exchange.php'),
        ], 'config');
    }
}
