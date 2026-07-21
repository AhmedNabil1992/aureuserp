<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Webkul\Psmonitor\Filament\Customer\Concerns\HasRemoteTablePaginationForPage;
use Filament\Pages\Page;
use Webkul\Psmonitor\Models\DiscountHistory as DiscountHistoryModel;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Webkul\Software\Models\License;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;

class DiscountHistory extends Page implements HasTable
{
    use HasRemoteTablePaginationForPage , HasPsLicenseAccess;
    
    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.discount-history';

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/discount-history.title');
    }
    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.psmonitor');
    }
}
