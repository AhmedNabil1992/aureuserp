<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('accounts_balance_requests')) {
            return;
        }

        if (! Schema::hasTable('payments_payment_transactions')) {
            return;
        }

        if (! Schema::hasColumn('accounts_balance_requests', 'payment_transaction_id')) {
            return;
        }

        Schema::table('accounts_balance_requests', function (Blueprint $table): void {
            $table->foreign('payment_transaction_id')
                ->references('id')
                ->on('payments_payment_transactions')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('accounts_balance_requests')) {
            return;
        }

        if (! Schema::hasColumn('accounts_balance_requests', 'payment_transaction_id')) {
            return;
        }

        Schema::table('accounts_balance_requests', function (Blueprint $table): void {
            $table->dropForeign(['payment_transaction_id']);
        });
    }
};
