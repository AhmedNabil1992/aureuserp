<?php

use Illuminate\Support\Facades\Route;
use Webkul\Wifi\Http\Controllers\API\V1\CustomerWifiController;

Route::name('customer.api.v1.wifi.')
    ->prefix('customer/api/v1/wifi')
    ->middleware(['auth:sanctum'])
    ->group(function (): void {
        Route::post('clouds', [CustomerWifiController::class, 'clouds'])->name('clouds.index');
        Route::post('dynamic-clients', [CustomerWifiController::class, 'dynamicClients'])->name('dynamic-clients.index');
        Route::get('clouds/{cloud_id}/realms', [CustomerWifiController::class, 'cloudRealms'])->name('cloud-realms.index');
        Route::get('dashboard', [CustomerWifiController::class, 'dashboard'])->name('dashboard');
        Route::get('voucher-batches', [CustomerWifiController::class, 'voucherBatches'])->name('voucher-batches.index');
        Route::get('voucher-batches/{batch_id}/download-url', [CustomerWifiController::class, 'voucherBatchDownloadUrl'])->name('voucher-batches.download-url');
        Route::get('sales', [CustomerWifiController::class, 'sales'])->name('sales.index');
        Route::get('sales-summary', [CustomerWifiController::class, 'salesSummary'])->name('sales-summary');
    });
