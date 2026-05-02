<?php

use Illuminate\Support\Facades\Route;
use Webkul\Software\Http\Controllers\API\Legacy\ClientLegacyController;
use Webkul\Software\Http\Controllers\API\Legacy\LicenseLegacyController;
use Webkul\Software\Http\Controllers\API\Legacy\LocationLegacyController;
use Webkul\Software\Http\Controllers\API\Legacy\ProductLegacyController;
use Webkul\Software\Http\Controllers\API\V1\CustomerNotificationController;
use Webkul\Software\Http\Controllers\API\V1\FcmTokenController;
use Webkul\Software\Http\Controllers\API\V1\TicketController;

// ─── Admin / staff routes (Sanctum token required) ───────────────────────────
Route::name('admin.api.v1.software.')
    ->prefix('admin/api/v1/software')
    ->middleware(['auth:sanctum'])
    ->group(function (): void {
        Route::apiResource('tickets', TicketController::class);
        Route::post('tickets/{ticket}/replies', [TicketController::class, 'reply'])->name('tickets.replies.store');
        Route::get('tickets/{ticket}/replies', [TicketController::class, 'replies'])->name('tickets.replies.index');

        // FCM token registration for admin web / desktop clients
        Route::post('fcm-tokens', [FcmTokenController::class, 'store'])->name('fcm-tokens.store');
        Route::delete('fcm-tokens', [FcmTokenController::class, 'destroy'])->name('fcm-tokens.destroy');
    });

// ─── Customer (Flutter app) routes ────────────────────────────────────────────
Route::name('customer.api.v1.software.')
    ->prefix('customer/api/v1/software')
    ->middleware(['auth:customer'])
    ->group(function (): void {
        // FCM token registration from Flutter
        Route::post('fcm-tokens', [FcmTokenController::class, 'store'])->name('fcm-tokens.store');
        Route::delete('fcm-tokens', [FcmTokenController::class, 'destroy'])->name('fcm-tokens.destroy');

        // Persistent notification inbox for Flutter
        Route::get('notifications', [CustomerNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/read', [CustomerNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('notifications/read-all', [CustomerNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
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
