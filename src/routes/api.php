<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Controllers\Api\V1\Order\OrderController;

Route::prefix('v1')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::post('/login', 'login')->name('login');
            Route::get('/user-listing', 'userListing')->name('userLising')->middleware('validate-jwt-token');
        });
    });
    Route::prefix('user')->name('user.')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::post('/login', 'login')->name('login');
        });
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index')->name('orders')->middleware('validate-jwt-token');
    });

    Route::prefix('order')->middleware('validate-jwt-token')->name('order.')->group(function () {
        Route::controller(OrderController::class)->group(function () {
            Route::post('/', 'store')->name('store');
            Route::get('/{uuid}', 'show')->name('show');
            Route::put('/{uuid}', 'update')->name('update');
            Route::delete('/{uuid}', 'delete')->name('delete');
        });
    });
});
