<?php

declare(strict_types=1);

namespace Webkul\Referral;

use Filament\Panel;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Webkul\Account\Events\MoveCancelled;
use Webkul\Account\Events\MoveConfirmed;
use Webkul\Account\Events\MovePaid;
use Webkul\Account\Events\MoveReversed;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Referral\Console\Commands\GenerateReferralCodesCommand;
use Webkul\Referral\Listeners\EarnReferralReward;
use Webkul\Referral\Listeners\ReverseReferralReward;
use Webkul\Referral\Models\ReferralCampaign;
use Webkul\Referral\Models\ReferralCode;
use Webkul\Referral\Models\ReferralRedemption;
use Webkul\Referral\Observers\PartnerObserver;
use Webkul\Referral\Observers\ReferralCampaignObserver;
use Webkul\Referral\Policies\ReferralCampaignPolicy;
use Webkul\Referral\Policies\ReferralCodePolicy;
use Webkul\Referral\Policies\ReferralRedemptionPolicy;
use Webkul\Referral\Services\ReferralCodeIssuer;

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
            ->hasCommand(GenerateReferralCodesCommand::class)
            ->hasInstallCommand(fn (InstallCommand $command) => $command
                ->installDependencies()
                ->runsMigrations()
                ->endWith(function (InstallCommand $command): void {
                    $created = app(ReferralCodeIssuer::class)->issueForActiveCampaigns();

                    $command->info("Generated {$created} missing referral code(s).");
                }))
            ->hasUninstallCommand(fn (UninstallCommand $command) => null)
            ->icon('heroicon-o-gift');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(ReferralPlugin::make()));
    }

    public function packageBooted(): void
    {
        if (! Package::isPluginInstalled(static::$name)
            || ! Schema::hasTable('referral_codes')
            || ! Schema::hasTable('referral_campaigns')) {
            return;
        }

        Gate::policy(ReferralCampaign::class, ReferralCampaignPolicy::class);
        Gate::policy(ReferralCode::class, ReferralCodePolicy::class);
        Gate::policy(ReferralRedemption::class, ReferralRedemptionPolicy::class);

        ReferralCampaign::observe(ReferralCampaignObserver::class);

        foreach ([
            Partner::class,
            \Webkul\Account\Models\Partner::class,
            \Webkul\Website\Models\Partner::class,
        ] as $partnerModel) {
            $partnerModel::observe(PartnerObserver::class);
        }

        Event::listen([MoveConfirmed::class, MovePaid::class], EarnReferralReward::class);
        Event::listen([MoveCancelled::class, MoveReversed::class], ReverseReferralReward::class);
    }
}
