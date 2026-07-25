<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$panel = \Filament\Facades\Filament::getPanel('customer');
$resources = $panel->getResources();

echo "Customer Panel Registered Resources (" . count($resources) . "):\n";
foreach ($resources as $r) {
    echo "  - {$r}\n";
}
