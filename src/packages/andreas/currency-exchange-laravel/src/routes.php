<?php

use Illuminate\Support\Facades\Route;
use Andreas\CurrencyExchange\Controllers\CurrencyExchangeController;

Route::prefix('api/v1')->group(function () {
    Route::prefix('currency-exchange')->name('currency-exchange.')->group(function () {
        Route::controller(CurrencyExchangeController::class)->group(function () {
            Route::post('/exchange', 'exchange')->name('exchange');
        });
    });
});
