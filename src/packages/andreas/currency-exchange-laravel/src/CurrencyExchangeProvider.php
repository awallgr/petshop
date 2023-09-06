<?php

namespace Andreas\CurrencyExchange;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class CurrencyExchangeProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerMacros();
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->publishes([
            __DIR__ . '/config/currency_exchange.php' => config_path('currency_exchange.php'),
        ], 'config');
    }

    public function registerMacros(): void
    {
        Response::macro('success', function (array $data = [], $code = 200): JsonResponse {
            return response()->json([
                'success' => 1,
                'data' => $data,
                'error' => null,
                'errors' => [],
                'trace' => [],
            ], $code);
        });
        Response::macro('fail', function (string $message = "", $code = 422): JsonResponse {
            return response()->json([
                'success' => 0,
                'data' => [],
                'error' => $message,
                'errors' => [],
                'trace' => [],
            ], $code);
        });
    }
}
