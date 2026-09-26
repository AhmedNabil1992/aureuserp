<?php

declare(strict_types=1);

namespace Webkul\Referral\Filament\Customer\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Webkul\Partner\Models\Partner;
use Webkul\Referral\Models\ReferralRedemption;
use Webkul\Referral\Services\ReferralService;
use Webkul\Support\Enums\NavigationGroup;

class MyReferralCode extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?int $navigationSort = 20;

    protected string $view = 'referrals::filament.customer.pages.my-referral-code';

    #[Locked]
    public string $referralCode = '';

    #[Locked]
    public int $referralCompanyId = 0;

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

        $code = $referrals->codeForPartner($partner);

        $this->referralCode = $code->code;
        $this->referralCompanyId = (int) $code->company_id;
    }

    public function getEarnedTotalProperty(): float
    {
        $partner = Auth::guard('customer')->user();

        abort_unless($partner instanceof Partner, 403);

        return (float) ReferralRedemption::query()
            ->where('company_id', $this->referralCompanyId)
            ->where('referrer_partner_id', $partner->id)
            ->where('status', 'earned')
            ->sum('referrer_reward');
    }

    public function getPendingTotalProperty(): float
    {
        $partner = Auth::guard('customer')->user();

        abort_unless($partner instanceof Partner, 403);

        return (float) ReferralRedemption::query()
            ->where('company_id', $this->referralCompanyId)
            ->where('referrer_partner_id', $partner->id)
            ->where('status', 'pending')
            ->sum('referrer_reward');
    }
}
