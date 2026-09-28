<?php

use Illuminate\Support\Facades\Route;
use Webkul\SoftwareOnline\Http\Controllers\TenantWebhookController;
use Webkul\SoftwareOnline\Http\Middleware\VerifyOnlineSystemWebhook;

Route::post(config('software-online.webhook.path', 'api/webhooks/ps-web/tenant-events'), TenantWebhookController::class)
    ->middleware([VerifyOnlineSystemWebhook::class, 'throttle:60,1'])
    ->name('api.webhooks.ps-web.tenant-events');
