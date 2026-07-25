<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Models\Partner;
use Webkul\Account\Filament\Customer\Clusters\Account\Resources\InvoiceResource;
use Webkul\Account\Filament\Customer\Clusters\Account\Resources\PaymentRequestResource;

$partner = Partner::find(3);
Auth::guard('customer')->setUser($partner);

echo "Accounting translation: " . __('admin.navigation.accounting') . "\n";
echo "InvoiceResource nav label: " . InvoiceResource::getNavigationLabel() . "\n";
echo "InvoiceResource shouldRegisterNavigation: " . (InvoiceResource::shouldRegisterNavigation() ? 'YES' : 'NO') . "\n";
echo "InvoiceResource canViewAny: " . (InvoiceResource::canViewAny() ? 'YES' : 'NO') . "\n";

echo "PaymentRequestResource nav label: " . PaymentRequestResource::getNavigationLabel() . "\n";
echo "PaymentRequestResource shouldRegisterNavigation: " . (PaymentRequestResource::shouldRegisterNavigation() ? 'YES' : 'NO') . "\n";
echo "PaymentRequestResource canViewAny: " . (PaymentRequestResource::canViewAny() ? 'YES' : 'NO') . "\n";

$panel = \Filament\Facades\Filament::getPanel('customer');
$navItems = $panel->getNavigation();

echo "\nCustomer Navigation Items (" . count($navItems) . " Groups):\n";
foreach ($navItems as $group) {
    $label = is_object($group) && method_exists($group, 'getLabel') ? $group->getLabel() : (string) $group;
    echo "Group: " . ($label ?: 'Ungrouped') . "\n";
    if (is_object($group) && method_exists($group, 'getItems')) {
        foreach ($group->getItems() as $item) {
            echo "  - Item: " . $item->getLabel() . " | URL: " . $item->getUrl() . "\n";
        }
    }
}
