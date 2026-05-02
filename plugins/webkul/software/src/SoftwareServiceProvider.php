<?php

namespace Webkul\Software;

use Filament\Panel;
use Livewire\Livewire;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Software\Livewire\OpenTicketsSidebar;
use Webkul\Software\Livewire\TicketConversationPanel;
use Webkul\Software\Services\LicenseInvoiceManager;
use Webkul\Software\Services\LicenseManager;
use Webkul\Software\Services\SubscriptionManager;
use Webkul\Software\Services\TicketService;

class SoftwareServiceProvider extends PackageServiceProvider
{
    public static string $name = 'software';

    public static string $viewNamespace = 'software';

    public function configureCustomPackage(Package $package): void
    {
        $this->registerServices();

        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasRoute('api')
            ->hasMigrations([
                '2026_04_09_000001_create_software_programs_table',
                '2026_04_09_000002_create_software_program_editions_table',
                '2026_04_09_000003_create_software_program_features_table',
                '2026_04_09_000004_create_software_program_releases_table',
                '2026_04_09_000005_create_software_licenses_table',
                '2026_04_09_000006_create_software_license_devices_table',
                '2026_04_09_000007_create_software_license_subscriptions_table',
                '2026_04_09_000008_create_software_remote_profiles_table',
                '2026_04_09_000009_create_software_license_activities_table',
                '2026_04_09_000010_create_software_error_logs_table',
                '2026_04_09_000011_create_software_tags_table',
                '2026_04_09_000012_create_software_tickets_table',
                '2026_04_09_000013_create_software_ticket_events_table',
                '2026_04_09_000014_create_software_ticket_tag_table',
                '2026_04_09_000015_create_software_ticket_assignees_table',
                '2026_04_10_000016_alter_software_licenses_add_workflow_columns',
                '2026_04_11_000018_alter_software_licenses_add_city_id',
                '2026_04_11_000019_alter_software_licenses_make_license_plan_nullable',
                '2026_04_11_000020_create_software_license_invoices_table',
                '2026_04_12_000021_alter_software_program_editions_add_product_id',
                '2026_04_12_000022_alter_software_license_invoices_add_account_move_id',
                '2026_04_12_000023_alter_software_program_features_add_service_type',
                '2026_04_12_000024_alter_software_license_subscriptions_add_feature_id',
                '2026_04_13_000025_alter_software_programs_add_product_id',
                '2026_04_13_000026_alter_software_program_editions_add_variant_product_id',
                '2026_04_13_000027_alter_software_program_features_add_product_id',
                '2026_04_15_000028_alter_software_licenses_make_edition_nullable',
                '2026_04_19_000029_create_software_ticket_attachments_table',
                '2026_05_02_000031_create_fcm_tokens_table',
                '2026_05_02_000032_create_software_customer_notifications_table',
            ])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {})
            ->icon('software');
    }

    public function packageBooted(): void
    {
        Livewire::component('software-ticket-conversation-panel', TicketConversationPanel::class);
        Livewire::component('software-open-tickets-sidebar', OpenTicketsSidebar::class);
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(SoftwarePlugin::make());
        });
    }

    private function registerServices(): void
    {
        $this->app->singleton(LicenseInvoiceManager::class);
        $this->app->singleton(SubscriptionManager::class);
        $this->app->singleton(LicenseManager::class);
        $this->app->singleton(TicketService::class);
    }
}
