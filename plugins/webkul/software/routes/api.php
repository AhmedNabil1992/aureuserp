<?php

use Illuminate\Support\Facades\Route;
use Webkul\Software\Http\Controllers\API\Legacy\ClientLegacyController;
use Webkul\Software\Http\Controllers\API\Legacy\LicenseLegacyController;
use Webkul\Software\Http\Controllers\API\Legacy\LocationLegacyController;
use Webkul\Software\Http\Controllers\API\Legacy\ProductLegacyController;

Route::name('admin.api.v1.software.')
    ->prefix('admin/api/v1/software')
    ->middleware(['auth:sanctum'])
    ->group(function (): void {
        // Modern API endpoints will be added here.
    });

Route::prefix('api')->group(function (): void {
    Route::post('/insert-licenses', [LicenseLegacyController::class, 'insertLicenses']);
    Route::post('/insert-keys', [LicenseLegacyController::class, 'insertKeys']);
    Route::post('/license-info', [LicenseLegacyController::class, 'licenseInfo']);
    Route::post('/LicGen/info', [LicenseLegacyController::class, 'licenseInfo']);
    Route::match(['get', 'post'], '/client-id', [ClientLegacyController::class, 'getClientId']);
    Route::get('/product', [ProductLegacyController::class, 'getProduct']);
    Route::get('/governorates', [LocationLegacyController::class, 'index']);
    Route::get('/city', [LocationLegacyController::class, 'getCity']);
});
