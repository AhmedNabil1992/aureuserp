<?php
use Illuminate\Support\Facades\DB;

DB::transaction(function () {
    // 1. Remove full_reconcile id=1 (only referenced by orphan line 5)
    DB::table('accounts_full_reconciles')->where('id', 1)->delete();
    echo "Deleted full_reconcile id=1" . PHP_EOL;

    // 2. Delete orphan move lines 4 and 5
    $deleted = DB::table('accounts_account_move_lines')->whereIn('id', [4, 5])->delete();
    echo "Deleted move lines: {$deleted}" . PHP_EOL;

    // 3. Delete orphan move id=5
    DB::table('accounts_account_moves')->where('id', 5)->delete();
    echo "Deleted move id=5 (PBANK/2026/5)" . PHP_EOL;

    // Verify nothing remains
    $remaining = DB::table('accounts_account_moves')->where('id', 5)->count();
    echo "Remaining move 5 records: {$remaining}" . PHP_EOL;
});
