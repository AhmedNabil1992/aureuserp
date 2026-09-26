<?php

declare(strict_types=1);

namespace Webkul\Referral;

use Filament\Panel;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Webkul\Account\Events\MoveCancelled;
use Webkul\Account\Events\MoveConfirmed;
use Webkul\Account\Events\MovePaid;
use Webkul\Account\Events\MoveReversed;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Referral\Listeners\EarnReferralReward;
use Webkul\Referral\Listeners\ReverseReferralReward;
use Webkul\Referral\Models\ReferralCampaign;
use Webkul\Referral\Models\ReferralCode;
use Webkul\Referral\Models\ReferralRedemption;
use Webkul\Referral\Policies\ReferralCampaignPolicy;
use Webkul\Referral\Policies\ReferralCodePolicy;
use Webkul\Referral\Policies\ReferralRedemptionPolicy;

class ReferralServiceProvider extends PackageServiceProvider
{
    public static string $name = 'referrals';

    public static string $viewNamespace = 'referrals';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasDependencies(['partners', 'products', 'accounts'])
            ->hasMigrations(['2026_09_26_000001_create_referral_tables'])
            ->runsMigrations()
            ->hasInstallCommand(fn (InstallCommand $command) => $command->installDependencies()->runsMigrations())
            ->hasUninstallCommand(fn (UninstallCommand $command) => null)
            ->icon('heroicon-o-gift');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(ReferralPlugin::make()));
    }

    public function packageBooted(): void
    {
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        Gate::policy(ReferralCampaign::class, ReferralCampaignPolicy::class);
        Gate::policy(ReferralCode::class, ReferralCodePolicy::class);
        Gate::policy(ReferralRedemption::class, ReferralRedemptionPolicy::class);

        Event::listen([MoveConfirmed::class, MovePaid::class], EarnReferralReward::class);
        Event::listen([MoveCancelled::class, MoveReversed::class], ReverseReferralReward::class);
    }
}
