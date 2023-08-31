<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\User\UserController;

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
});
