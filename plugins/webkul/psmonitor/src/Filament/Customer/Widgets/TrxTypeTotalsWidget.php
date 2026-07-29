<?php

namespace Webkul\Psmonitor\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Models\TRXHistory;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class TrxTypeTotalsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    public ?int $licenseId = null;

    protected function getHeading(): ?string
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return __('psmonitor::filament/customer/widgets/widgets.trx_type_totals.heading_default');
        }

        $license = $this->resolveLicense($customer);

        if (! $license) {
            return __('psmonitor::filament/customer/widgets/widgets.trx_type_totals.heading_default');
        }

        $branchName = trim((string) ($license->company_name ?? ''));

        return $branchName !== ''
            ? __('psmonitor::filament/customer/widgets/widgets.trx_type_totals.heading_branch', ['branch' => $branchName])
            : __('psmonitor::filament/customer/widgets/widgets.trx_type_totals.heading_default');
    }

    public static function canView(): bool
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return false;
        }

        return app(CustomerLicenseResolver::class)->hasAccessibleRemoteLicense($customer);
    }

    protected function getStats(): array
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return [];
        }

        $license = $this->resolveLicense($customer);

        if (! $license || ! RemoteModel::canConnectToHost($license->server_ip)) {
            return [
                Stat::make(
                    __('psmonitor::filament/customer/widgets/widgets.trx_type_totals.status_connection'),
                    __('psmonitor::filament/customer/widgets/widgets.active_devices.connection_error')
                )->color('danger'),
            ];
        }

        $from = now()->startOfMonth()->toDateString();
        $until = now()->toDateString();
        $fromDateTime = $from . ' 00:00:00';
        $untilExclusive = now()->addDay()->startOfDay()->format('Y-m-d H:i:s');

        try {
            $rows = TRXHistory::forLicense($license)
                ->selectRaw('RTRIM(TRX_Type) as trx_type, RTRIM(TRX_Name) as trx_name, SUM(Amount) as total_amount')
                ->where('TRX_Date', '>=', $fromDateTime)
                ->where('TRX_Date', '<', $untilExclusive)
                ->groupByRaw('RTRIM(TRX_Type), RTRIM(TRX_Name)')
                ->havingRaw('SUM(Amount) != 0')
                ->get();
        } catch (Throwable $e) {
            Log::warning('TrxTypeTotalsWidget: failed to load totals for selected license', [
                'license_id' => $license->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return [
                Stat::make(
                    __('psmonitor::filament/customer/widgets/widgets.trx_type_totals.status_connection'),
                    __('psmonitor::filament/customer/widgets/widgets.trx_type_totals.connection_error')
                )->color('danger'),
            ];
        }

        $totalsByLabel = [];

        foreach ($rows as $row) {
            $type = trim((string) ($row->trx_type ?? ''));
            $name = trim((string) ($row->trx_name ?? ''));

            $label = $type === 'عملاء'
                ? trim($type . ' - ' . $name, ' -')
                : ($type !== '' ? $type : 'غير محدد');

            $totalsByLabel[$label] = ($totalsByLabel[$label] ?? 0.0) + (float) ($row->total_amount ?? 0);
        }

        $stats = [];

        foreach ($totalsByLabel as $displayLabel => $total) {
            if ((float) $total === 0.0) {
                continue;
            }

            $stats[] = Stat::make($displayLabel, number_format($total, 2) . ' EGP')
                ->description(__('psmonitor::filament/customer/widgets/widgets.trx_type_totals.date_range_desc', ['from' => $from, 'until' => $until]))
                ->color($total < 0 ? 'danger' : 'success');
        }

        if (! empty($stats)) {
            return $stats;
        }

        return [
            Stat::make(
                __('psmonitor::filament/customer/widgets/widgets.trx_type_totals.status_data'),
                __('psmonitor::filament/customer/widgets/widgets.trx_type_totals.no_data')
            )
                ->description(__('psmonitor::filament/customer/widgets/widgets.trx_type_totals.date_range_desc', ['from' => $from, 'until' => $until]))
                ->color('warning'),
        ];
    }

    protected function resolveLicense($customer)
    {
        try {
            return app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer, $this->licenseId);
        } catch (Throwable) {
            return null;
        }
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
