<?php

declare(strict_types=1);

namespace Webkul\Referral\Filament\Customer\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Models\Partner;
use Webkul\Referral\Models\ReferralCode;
use Webkul\Referral\Models\ReferralRedemption;
use Webkul\Referral\Services\ReferralService;
use Webkul\Support\Enums\NavigationGroup;

class MyReferralCode extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?int $navigationSort = 20;

    protected string $view = 'referrals::filament.customer.pages.my-referral-code';

    public ?ReferralCode $referralCode = null;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return NavigationGroup::Referrals;
    }

    public static function getNavigationLabel(): string
    {
        return __('referrals::app.customer.navigation');
    }

    public function getTitle(): string
    {
        return __('referrals::app.customer.title');
    }

    public function mount(ReferralService $referrals): void
    {
        $partner = Auth::guard('customer')->user();

        abort_unless($partner instanceof Partner, 403);

        $this->referralCode = $referrals->codeForPartner($partner);
    }

    public function getEarnedTotalProperty(): float
    {
        return (float) ReferralRedemption::query()
            ->where('referrer_partner_id', $this->referralCode?->partner_id)
            ->where('status', 'earned')
            ->sum('referrer_reward');
    }

    public function getPendingTotalProperty(): float
    {
        return (float) ReferralRedemption::query()
            ->where('referrer_partner_id', $this->referralCode?->partner_id)
            ->where('status', 'pending')
            ->sum('referrer_reward');
    }
}
