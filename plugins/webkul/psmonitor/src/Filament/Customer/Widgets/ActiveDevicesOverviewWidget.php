<?php

namespace Webkul\Psmonitor\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Webkul\Psmonitor\Models\Device;
use Webkul\Psmonitor\Models\OrderWait;
use Webkul\Psmonitor\Models\PlayWait;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class ActiveDevicesOverviewWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    public ?int $licenseId = null;

    protected function getHeading(): ?string
    {
        return __('psmonitor::filament/customer/widgets/widgets.active_devices.heading');
    }

    public static function canView(): bool
    {
        $customer = Auth::guard('customer')->user();

        return $customer && app(CustomerLicenseResolver::class)->hasAccessibleRemoteLicense($customer);
    }

    protected function getStats(): array
    {
        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            return [];
        }

        try {
            $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer, $this->licenseId);
            if (! $license || ! RemoteModel::canConnectToHost($license->server_ip)) {
                return [
                    Stat::make(
                        __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.status_connection'),
                        __('psmonitor::filament/customer/widgets/widgets.active_devices.connection_error')
                    )->color('danger'),
                ];
            }

            $totalDevices = Device::forLicense($license)->where('IsActive', 1)->count();
            $busyDevices = Device::forLicense($license)->where('IsActive', 1)->where('Status', 'مشغول')->count();
            $availableDevices = Device::forLicense($license)->where('IsActive', 1)->where('Status', 'متاح')->count();
            $activePlaySessions = PlayWait::forLicense($license)->count();
            $pendingCafeOrders = OrderWait::forLicense($license)->count();

            return [
                Stat::make(__('psmonitor::filament/customer/widgets/widgets.active_devices.busy_devices'), $busyDevices . ' / ' . $totalDevices)
                    ->description(__('psmonitor::filament/customer/widgets/widgets.active_devices.busy_devices_desc'))
                    ->color($busyDevices > 0 ? 'warning' : 'gray')
                    ->icon('heroicon-o-computer-desktop'),

                Stat::make(__('psmonitor::filament/customer/widgets/widgets.active_devices.available_devices'), $availableDevices)
                    ->description(__('psmonitor::filament/customer/widgets/widgets.active_devices.available_devices_desc'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),

                Stat::make(__('psmonitor::filament/customer/widgets/widgets.active_devices.active_play_sessions'), $activePlaySessions)
                    ->description(__('psmonitor::filament/customer/widgets/widgets.active_devices.active_play_sessions_desc'))
                    ->color('info')
                    ->icon('heroicon-o-play'),

                Stat::make(__('psmonitor::filament/customer/widgets/widgets.active_devices.pending_cafe_orders'), $pendingCafeOrders)
                    ->description(__('psmonitor::filament/customer/widgets/widgets.active_devices.pending_cafe_orders_desc'))
                    ->color($pendingCafeOrders > 0 ? 'warning' : 'gray')
                    ->icon('heroicon-o-shopping-cart'),
            ];
        } catch (Throwable) {
            return [
                Stat::make(
                    __('psmonitor::filament/customer/widgets/widgets.trx_type_totals.status_data'),
                    __('psmonitor::filament/customer/widgets/widgets.active_devices.data_error')
                )->color('danger'),
            ];
        }
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
