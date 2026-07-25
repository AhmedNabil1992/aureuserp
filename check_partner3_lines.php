<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Webkul\Account\Models\MoveLine;
use Webkul\Account\Enums\MoveState;

$partnerId = 3;

echo "Checking MoveLines for Partner #3:\n";
$lines = MoveLine::query()
    ->with('move')
    ->where('partner_id', $partnerId)
    ->where('parent_state', MoveState::POSTED)
    ->where('reconciled', false)
    ->where('amount_residual', '<', 0)
    ->whereHas('account', fn ($query) => $query->where('account_type', 'asset_receivable'))
    ->get();

foreach ($lines as $line) {
    echo "  Line ID: {$line->id} | Move ID: {$line->move_id} | Residual: {$line->amount_residual} | Origin Payment ID: " . ($line->move?->origin_payment_id ?? 'NULL') . "\n";
}
