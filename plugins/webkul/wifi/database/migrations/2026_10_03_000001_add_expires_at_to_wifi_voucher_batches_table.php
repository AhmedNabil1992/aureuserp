<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wifi_voucher_batches', function (Blueprint $table): void {
            $table->date('expires_at')->nullable()->after('never_expire');
        });

        DB::table('wifi_voucher_batches as batches')
            ->leftJoin('wifi_purchases as purchases', 'purchases.id', '=', 'batches.wifi_purchase_id')
            ->leftJoin('accounts_account_move_lines as lines', 'lines.id', '=', 'purchases.move_line_id')
            ->leftJoin('accounts_account_moves as moves', 'moves.id', '=', 'lines.move_id')
            ->where('batches.never_expire', false)
            ->select([
                'batches.id',
                'batches.created_at',
                'moves.invoice_date',
            ])
            ->orderBy('batches.id')
            ->get()
            ->each(function (object $batch): void {
                $validityStartDate = $batch->invoice_date ?? $batch->created_at;

                if (! $validityStartDate) {
                    return;
                }

                DB::table('wifi_voucher_batches')
                    ->where('id', $batch->id)
                    ->update([
                        'expires_at' => CarbonImmutable::parse($validityStartDate)
                            ->addMonthNoOverflow()
                            ->toDateString(),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('wifi_voucher_batches', function (Blueprint $table): void {
            $table->dropColumn('expires_at');
        });
    }
};
