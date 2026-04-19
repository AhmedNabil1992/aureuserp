<?php

namespace Webkul\Article;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class ArticleServiceProvider extends PackageServiceProvider
{
    public static string $name = 'articles';

    public static string $viewNamespace = 'articles';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2026_04_19_000001_create_articles_categories_table',
                '2026_04_19_000002_create_articles_tags_table',
                '2026_04_19_000003_create_articles_articles_table',
                '2026_04_19_000004_create_articles_article_tags_table',
                '2026_04_19_000005_create_articles_article_programs_table',
            ])
            ->runsMigrations()
            ->hasSettings([])
            ->runsSettings()
            ->hasDependencies([
                'software',
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('heroicon-o-book-open');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(ArticlePlugin::make());
        });
    }
}
