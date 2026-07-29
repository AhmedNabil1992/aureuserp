<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Pages\Page;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Webkul\Partner\Models\Partner;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Models\OrderWait;
use Webkul\Psmonitor\Models\PlayWait;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class DeviceCurrent extends Page
{
    use HasPsLicenseAccess;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected string $view = 'psmonitor::filament.customer.pages.device-current';

    protected static ?int $navigationSort = 43;

    protected static ?string $slug = 'device-current';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/device-current.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/device-current.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('psmonitor::filament/customer/navigation.group');
    }

    public ?string $orderNo = null;

    public Collection $playWaitRows;

    public Collection $orderWaitRows;

    public ?string $error = null;

    protected function getHeaderWidgets(): array
    {
        return [
            LicenseSelectorWidget::class,
        ];
    }

    public function mount(): void
    {
        $this->orderNo = trim((string) request()->query('order_no', ''));
        $this->playWaitRows = collect();
        $this->orderWaitRows = collect();

        if ($this->orderNo === '') {
            return;
        }

        $customer = Auth::guard('customer')->user();

        if (! $customer instanceof Partner) {
            return;
        }

        try {
            $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

            if (! $license || ! RemoteModel::canConnectToHost($license->server_ip)) {
                $this->error = __('psmonitor::filament/customer/pages/common.connection_failed.body');

                return;
            }

            $this->playWaitRows = PlayWait::forLicense($license)
                ->where('Order_No', $this->orderNo)
                ->orderByDesc('ID')
                ->limit(200)
                ->get();

            $this->orderWaitRows = OrderWait::forLicense($license)
                ->with('item')
                ->where('Order_No', $this->orderNo)
                ->orderByDesc('ID')
                ->limit(200)
                ->get();
        } catch (QueryException $exception) {
            Log::warning('DeviceCurrent: failed to load remote device data', [
                'order_no' => $this->orderNo,
                'error' => $exception->getMessage(),
            ]);

            $this->error = __('psmonitor::filament/customer/pages/common.connection_failed.body');
        }
    }
}
