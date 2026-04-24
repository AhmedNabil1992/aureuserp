<?php

use Illuminate\Support\Facades\Route;
use Webkul\Wifi\Http\Controllers\VoucherBatchPdfController;

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('wifi/voucher-batches/{batchCode}/download', [VoucherBatchPdfController::class, 'download'])
        ->where('batchCode', '.*')
        ->name('wifi.voucher-batches.download');
});
