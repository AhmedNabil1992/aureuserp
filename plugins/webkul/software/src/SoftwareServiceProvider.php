<?php

namespace Webkul\Software;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class SoftwareServiceProvider extends PackageServiceProvider
{
    public static string $name = 'software';

    public static string $viewNamespace = 'software';

    public function configureCustomPackage(Package $package): void
    {
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
        //
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(SoftwarePlugin::make());
        });
    }
}
