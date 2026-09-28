<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_codes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained('partners_partners')->cascadeOnDelete();
            $table->string('code', 32)->unique();
            $table->boolean('is_active')->default(true);
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'partner_id']);
        });

        Schema::create('referral_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->foreignId('journal_id')->constrained('accounts_journals')->restrictOnDelete();
            $table->foreignId('expense_account_id')->constrained('accounts_accounts')->restrictOnDelete();
            $table->string('name');
            $table->json('contexts');
            $table->string('discount_type')->default('fixed');
            $table->decimal('customer_discount', 16, 4)->default(0);
            $table->decimal('referrer_reward', 16, 4)->default(0);
            $table->decimal('minimum_eligible_amount', 16, 4)->default(0);
            $table->boolean('first_purchase_only')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('max_redemptions')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'is_active', 'starts_at', 'ends_at'], 'ref_campaign_active_idx');
        });

        Schema::create('referral_campaign_products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('referral_campaigns')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products_products')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['campaign_id', 'product_id']);
        });

        Schema::create('referral_redemptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('referral_campaigns')->restrictOnDelete();
            $table->foreignId('referral_code_id')->constrained('referral_codes')->restrictOnDelete();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->foreignId('referrer_partner_id')->constrained('partners_partners')->restrictOnDelete();
            $table->foreignId('referred_partner_id')->constrained('partners_partners')->restrictOnDelete();
            $table->foreignId('move_id')->constrained('accounts_account_moves')->cascadeOnDelete();
            $table->foreignId('reward_move_id')->nullable()->constrained('accounts_account_moves')->nullOnDelete();
            $table->foreignId('reward_reversal_move_id')->nullable()->constrained('accounts_account_moves')->nullOnDelete();
            $table->string('first_purchase_key')->nullable()->unique();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('context', 40);
            $table->string('status', 20)->default('pending');
            $table->decimal('eligible_amount', 16, 4);
            $table->decimal('customer_discount', 16, 4);
            $table->decimal('referrer_reward', 16, 4);
            $table->json('snapshot')->nullable();
            $table->timestamp('earned_at')->nullable();
            $table->timestamp('reversed_at')->nullable();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('move_id');
            $table->index(['campaign_id', 'referred_partner_id', 'status'], 'ref_redemption_first_purchase_idx');
            $table->index(['referrer_partner_id', 'status'], 'ref_redemption_rewards_idx');
        });

        Schema::table('accounts_account_moves', function (Blueprint $table): void {
            $table->string('referral_code', 32)->nullable()->after('invoice_origin');
        });
    }

    public function down(): void
    {
        Schema::table('accounts_account_moves', function (Blueprint $table): void {
            $table->dropColumn('referral_code');
        });

        Schema::dropIfExists('referral_redemptions');
        Schema::dropIfExists('referral_campaign_products');
        Schema::dropIfExists('referral_campaigns');
        Schema::dropIfExists('referral_codes');
    }
};
