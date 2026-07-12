<?php

namespace Webkul\Purchase\Filament\Customer\Pages;

use Webkul\Account\Filament\Customer\Widgets\AvailableBalanceWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = 'dashboard';

    protected static ?int $navigationSort = -10;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.dashboard');
    }

    public function getWidgets(): array
    {
        $widgets = parent::getWidgets();

        if (class_exists(AvailableBalanceWidget::class) && ! in_array(AvailableBalanceWidget::class, $widgets, true)) {
            $widgets[] = AvailableBalanceWidget::class;
        }

        return $widgets;
    }
}
