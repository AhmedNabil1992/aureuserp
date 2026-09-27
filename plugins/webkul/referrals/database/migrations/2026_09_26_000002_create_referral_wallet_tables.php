<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\Referral\Services\ReferralAccountingDefaults;
use Webkul\Referral\Services\ReferralWalletService;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_accounting_defaults', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('journal_id')->constrained('accounts_journals')->restrictOnDelete();
            $table->foreignId('expense_account_id')->constrained('accounts_accounts')->restrictOnDelete();
            $table->foreignId('liability_account_id')->constrained('accounts_accounts')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::table('referral_campaigns', function (Blueprint $table): void {
            $table->foreignId('liability_account_id')->nullable()->after('expense_account_id')
                ->constrained('accounts_accounts')->restrictOnDelete();
        });

        Schema::create('referral_wallet_balances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->foreignId('partner_id')->constrained('partners_partners')->restrictOnDelete();
            $table->decimal('available_amount', 16, 4)->default(0);
            $table->decimal('lifetime_earned', 16, 4)->default(0);
            $table->decimal('lifetime_redeemed', 16, 4)->default(0);
            $table->timestamps();

            $table->unique(['company_id', 'currency_id', 'partner_id'], 'ref_wallet_balance_owner_unique');
        });

        Schema::create('referral_wallet_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->foreignId('partner_id')->constrained('partners_partners')->restrictOnDelete();
            $table->foreignId('referral_redemption_id')->nullable()->constrained('referral_redemptions')->restrictOnDelete();
            $table->foreignId('accounting_move_id')->nullable()->constrained('accounts_account_moves')->nullOnDelete();
            $table->foreignId('invoice_move_id')->nullable()->constrained('accounts_account_moves')->nullOnDelete();
            $table->string('type', 30);
            $table->decimal('amount', 16, 4);
            $table->string('idempotency_key')->unique();
            $table->json('metadata')->nullable();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'partner_id', 'created_at'], 'ref_wallet_partner_history_idx');
        });

        Schema::table('accounts_account_moves', function (Blueprint $table): void {
            $table->decimal('referral_wallet_amount', 16, 4)->default(0)->after('referral_code');
        });

        app(ReferralAccountingDefaults::class)->ensureForAllCompanies();
        app(ReferralWalletService::class)->importLegacyEarnedRewards();
    }

    public function down(): void
    {
        Schema::table('accounts_account_moves', fn (Blueprint $table) => $table->dropColumn('referral_wallet_amount'));
        Schema::dropIfExists('referral_wallet_transactions');
        Schema::dropIfExists('referral_wallet_balances');
        Schema::table('referral_campaigns', fn (Blueprint $table) => $table->dropConstrainedForeignId('liability_account_id'));
        Schema::dropIfExists('referral_accounting_defaults');
    }
};
