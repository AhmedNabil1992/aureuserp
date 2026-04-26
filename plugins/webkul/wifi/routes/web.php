<?php

use Illuminate\Support\Facades\Route;
use Webkul\Wifi\Http\Controllers\VoucherBatchPdfController;

Route::get('wifi/voucher-batches/{batchCode}/download', [VoucherBatchPdfController::class, 'download'])
    ->where('batchCode', '.*')
    ->middleware(['web'])
    ->name('wifi.voucher-batches.download');

Route::get('wifi/voucher-batches/{batchCode}/signed-download', [VoucherBatchPdfController::class, 'signedDownload'])
    ->where('batchCode', '.*')
    ->middleware(['signed'])
    ->name('wifi.voucher-batches.signed-download');
