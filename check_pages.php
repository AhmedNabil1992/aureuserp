<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$panel = filament()->getPanel('customer');

// Check if Dashboard is in the pages (using getPages())
$pages = $panel->getPages();
echo "Dashboard in getPages: " . (in_array(\Webkul\Purchase\Filament\Customer\Pages\Dashboard::class, $pages) ? 'YES' : 'NO') . "\n";

// Try canAccess on dashboard
try {
    $canAccess = \Webkul\Purchase\Filament\Customer\Pages\Dashboard::canAccess();
    echo "Dashboard canAccess: " . ($canAccess ? 'YES' : 'NO') . "\n";
} catch (\Throwable $e) {
    echo "Dashboard canAccess ERROR: " . $e->getMessage() . "\n";
}

// Check parents
$parents = class_parents(\Webkul\Purchase\Filament\Customer\Pages\Dashboard::class);
echo "\nDashboard parent chain:\n";
foreach ($parents as $p) {
    echo "  - $p\n";
}

// Check if Filament auto-discovers Dashboard-type pages
// In Filament v3, Dashboard pages may need to be registered via ->pages() not discoverPages()
echo "\n--- Trying manual page registration check ---\n";
echo "Dashboard::getRoutePath: " . \Webkul\Purchase\Filament\Customer\Pages\Dashboard::getRoutePath() . "\n";
echo "Dashboard::getSlug: " . \Webkul\Purchase\Filament\Customer\Pages\Dashboard::getSlug() . "\n";
echo "Dashboard::getNavigationGroup: " . (\Webkul\Purchase\Filament\Customer\Pages\Dashboard::getNavigationGroup() ?? 'null') . "\n";
