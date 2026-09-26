<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Referral\Enums\DiscountType;
use Webkul\Referral\Enums\RedemptionStatus;
use Webkul\Referral\Models\ReferralCampaign;
use Webkul\Referral\Models\ReferralCode;
use Webkul\Referral\Services\ReferralRewardService;
use Webkul\Referral\Services\ReferralService;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../accounts/tests/Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('referrals');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'referrals'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');
    URL::resolveMissingNamedRoutesUsing(fn () => '#');
    AccountHelper::actingAsAdmin();
});

function referralFixture(float $discount = 100, float $reward = 100): array
{
    $company = AccountHelper::company();
    $currency = AccountHelper::currency();
    $referrer = AccountHelper::partner();
    $referred = AccountHelper::partner();
    $income = AccountHelper::account('income');
    $expense = AccountHelper::account('expense');
    $journal = AccountHelper::generalJournal();
    $move = AccountHelper::invoice(MoveType::OUT_INVOICE, $referred);
    $line = AccountHelper::productLine($move, $income, qty: 2, priceUnit: 250);
    $code = ReferralCode::query()->create([
        'company_id' => $company->id,
        'partner_id' => $referrer->id,
        'code'       => 'REF-TEST100',
        'is_active'  => true,
    ]);
    $campaign = ReferralCampaign::query()->create([
        'company_id'              => $company->id,
        'currency_id'             => $currency->id,
        'journal_id'              => $journal->id,
        'expense_account_id'      => $expense->id,
        'name'                    => 'Test fixed referral',
        'contexts'                => [ReferralService::CONTEXT_SALES_INVOICE],
        'discount_type'           => DiscountType::Fixed,
        'customer_discount'       => $discount,
        'referrer_reward'         => $reward,
        'minimum_eligible_amount' => 0,
        'first_purchase_only'     => true,
        'is_active'               => true,
    ]);
    $campaign->products()->attach($line->product_id);

    return compact('move', 'line', 'code', 'campaign', 'referrer', 'referred', 'expense');
}

it('applies a fixed discount only to eligible invoice products', function () {
    $fixture = referralFixture();

    $redemption = app(ReferralService::class)->applyToDraftMove(
        $fixture['move'],
        $fixture['code']->code,
        ReferralService::CONTEXT_SALES_INVOICE,
    );

    expect((float) $fixture['line']->refresh()->discount)->toBe(20.0)
        ->and((float) $fixture['move']->refresh()->amount_total)->toBe(400.0)
        ->and((float) $redemption->customer_discount)->toBe(100.0)
        ->and($redemption->status)->toBe(RedemptionStatus::Pending)
        ->and($redemption->first_purchase_key)->toBe($fixture['campaign']->id.':'.$fixture['referred']->id);
});

it('rejects self referrals', function () {
    $fixture = referralFixture();
    $fixture['move']->update(['partner_id' => $fixture['referrer']->id]);

    app(ReferralService::class)->applyToDraftMove(
        $fixture['move'],
        $fixture['code']->code,
        ReferralService::CONTEXT_SALES_INVOICE,
    );
})->throws(ValidationException::class);

it('books an earned reward once as marketing expense and customer credit', function () {
    $fixture = referralFixture();
    $redemption = app(ReferralService::class)->applyToDraftMove(
        $fixture['move'],
        $fixture['code']->code,
        ReferralService::CONTEXT_SALES_INVOICE,
    );
    $fixture['move']->update(['payment_state' => PaymentState::PAID]);

    $earned = app(ReferralRewardService::class)->earnForPaidMove($fixture['move']);
    $again = app(ReferralRewardService::class)->earnForPaidMove($fixture['move']);

    expect($earned->status)->toBe(RedemptionStatus::Earned)
        ->and($again->reward_move_id)->toBe($earned->reward_move_id)
        ->and((float) $earned->rewardMove->lines()->where('account_id', $fixture['expense']->id)->sum('debit'))->toBe(100.0)
        ->and((float) $earned->rewardMove->lines()->where('partner_id', $fixture['referrer']->id)->sum('credit'))->toBe(100.0)
        ->and($redemption->refresh()->reward_move_id)->not->toBeNull();
});

it('reverses the customer credit when an earned referral is cancelled', function () {
    $fixture = referralFixture();
    app(ReferralService::class)->applyToDraftMove(
        $fixture['move'],
        $fixture['code']->code,
        ReferralService::CONTEXT_SALES_INVOICE,
    );
    $fixture['move']->update(['payment_state' => PaymentState::PAID]);
    $earned = app(ReferralRewardService::class)->earnForPaidMove($fixture['move']);

    $reversed = app(ReferralRewardService::class)->reverseForMove($fixture['move']);
    $originalCredit = $earned->rewardMove->lines()->where('partner_id', $fixture['referrer']->id)->firstOrFail();

    expect($reversed->status)->toBe(RedemptionStatus::Reversed)
        ->and($reversed->reward_reversal_move_id)->not->toBeNull()
        ->and($originalCredit->refresh()->reconciled)->toBeTrue();
});
