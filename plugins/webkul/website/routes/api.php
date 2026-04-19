<?php

use Illuminate\Support\Facades\Route;
use Webkul\Website\Http\Controllers\API\V1\CustomerAuthController;

Route::name('customer.api.v1.website.auth.')
    ->prefix('customer/api/v1/auth')
    ->group(function (): void {
        Route::post('register', [CustomerAuthController::class, 'register'])->name('register');
        Route::post('login', [CustomerAuthController::class, 'login'])->name('login');
    });

Route::name('customer.api.v1.website.auth.')
    ->prefix('customer/api/v1/auth')
    ->middleware(['auth:sanctum'])
    ->group(function (): void {
        Route::get('me', [CustomerAuthController::class, 'me'])->name('me');
        Route::post('logout', [CustomerAuthController::class, 'logout'])->name('logout');
    });
