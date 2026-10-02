<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wifi_purchases', function (Blueprint $table): void {
            $table->dropForeign(['move_line_id']);
            $table->dropUnique(['move_line_id']);
        });

        Schema::table('wifi_purchases', function (Blueprint $table): void {
            $table->unsignedBigInteger('move_line_id')->nullable()->change();
            $table->foreign('move_line_id')->references('id')->on('accounts_account_move_lines')->cascadeOnDelete();

            $table->foreignId('partner_id')
                ->nullable()
                ->after('wifi_package_id')
                ->constrained('partners_partners')
                ->nullOnDelete();
            $table->unsignedBigInteger('legacy_purchase_id')->nullable()->after('move_line_id')->unique();
            $table->unsignedInteger('legacy_consumed_quantity')->default(0)->after('remaining_quantity');
        });

        DB::table('wifi_purchases as purchases')
            ->join('accounts_account_move_lines as lines', 'lines.id', '=', 'purchases.move_line_id')
            ->join('accounts_account_moves as moves', 'moves.id', '=', 'lines.move_id')
            ->whereNull('purchases.partner_id')
            ->whereNotNull('moves.partner_id')
            ->select(['purchases.id', 'moves.partner_id'])
            ->orderBy('purchases.id')
            ->get()
            ->each(function (object $purchase): void {
                DB::table('wifi_purchases')
                    ->where('id', $purchase->id)
                    ->update(['partner_id' => $purchase->partner_id]);
            });
    }

    public function down(): void
    {
        if (DB::table('wifi_purchases')->whereNull('move_line_id')->exists()) {
            throw new RuntimeException('Legacy Wi-Fi purchases must be removed before rolling back this migration.');
        }

        Schema::table('wifi_purchases', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('partner_id');
            $table->dropUnique(['legacy_purchase_id']);
            $table->dropColumn(['legacy_purchase_id', 'legacy_consumed_quantity']);
            $table->dropForeign(['move_line_id']);
        });

        Schema::table('wifi_purchases', function (Blueprint $table): void {
            $table->unsignedBigInteger('move_line_id')->nullable(false)->change();
            $table->unique('move_line_id');
            $table->foreign('move_line_id')->references('id')->on('accounts_account_move_lines')->cascadeOnDelete();
        });
    }
};
