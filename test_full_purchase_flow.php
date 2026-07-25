<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Models\Partner;
use Webkul\Wifi\Filament\Customer\Pages\VoucherInvoices;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Enums\MoveState;

$partner = Partner::find(3);
Auth::guard('customer')->setUser($partner);

echo "Testing Partner #{$partner->id} credit & purchase creation...\n\n";

$page = new VoucherInvoices();
$reflectionCredit = new \ReflectionMethod($page, 'getPartnerAvailableCredit');
$reflectionCredit->setAccessible(true);
$credit = $reflectionCredit->invoke($page, $partner->id);

echo "Available credit before test: {$credit}\n";
