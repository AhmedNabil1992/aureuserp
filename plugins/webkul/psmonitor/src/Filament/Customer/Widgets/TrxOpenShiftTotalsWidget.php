<?php

namespace Webkul\Psmonitor\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Models\Shifts;
use Webkul\Psmonitor\Models\TRXHistory;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class TrxOpenShiftTotalsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    public ?int $licenseId = null;

    protected function getHeading(): ?string
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.heading_default');
        }

        $license = $this->resolveLicense($customer);

        if (! $license) {
            return __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.heading_default');
        }

        $branchName = trim((string) ($license->company_name ?? ''));

        return $branchName !== ''
            ? __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.heading_branch', ['branch' => $branchName])
            : __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.heading_default');
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
                    __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.status_connection'),
                    __('psmonitor::filament/customer/widgets/widgets.active_devices.connection_error')
                )->color('danger'),
            ];
        }

        try {
            $openShiftNo = Shifts::forLicense($license)
                ->where('Status', 'Open')
                ->orderByDesc('Shift_No')
                ->value('Shift_No');
        } catch (Throwable $e) {
            Log::warning('TrxOpenShiftTotalsWidget: failed to load open shift for selected license', [
                'license_id' => $license->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return [
                Stat::make(
                    __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.status_connection'),
                    __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.connection_error')
                )->color('danger'),
            ];
        }

        if ($openShiftNo === null || $openShiftNo === '') {
            return [
                Stat::make(
                    __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.status_shift'),
                    __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.no_open_shift')
                )
                    ->description(__('psmonitor::filament/customer/widgets/widgets.open_shift_totals.shift_closed_desc'))
                    ->color('warning'),
            ];
        }

        try {
            $rows = TRXHistory::forLicense($license)
                ->selectRaw('RTRIM(TRX_Type) as trx_type, RTRIM(TRX_Name) as trx_name, SUM(Amount) as total_amount')
                ->where('Shift', (string) $openShiftNo)
                ->groupByRaw('RTRIM(TRX_Type), RTRIM(TRX_Name)')
                ->havingRaw('SUM(Amount) != 0')
                ->get();
        } catch (Throwable $e) {
            Log::warning('TrxOpenShiftTotalsWidget: failed to load shift totals for selected license', [
                'license_id' => $license->id ?? null,
                'open_shift' => $openShiftNo,
                'error' => $e->getMessage(),
            ]);

            return [
                Stat::make(
                    __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.status_connection'),
                    __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.connection_error')
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

        if (empty($totalsByLabel)) {
            return [
                Stat::make(
                    __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.status_current'),
                    __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.no_transactions')
                )
                    ->description(__('psmonitor::filament/customer/widgets/widgets.open_shift_totals.shift_desc', ['shift' => $openShiftNo]))
                    ->color('warning'),
            ];
        }

        $stats = [];

        foreach ($totalsByLabel as $displayLabel => $total) {
            if ((float) $total === 0.0) {
                continue;
            }

            $stats[] = Stat::make($displayLabel, number_format($total, 2) . ' EGP')
                ->description(__('psmonitor::filament/customer/widgets/widgets.open_shift_totals.shift_desc', ['shift' => $openShiftNo]))
                ->color($total < 0 ? 'danger' : 'success');
        }

        if (empty($stats)) {
            return [
                Stat::make(
                    __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.status_current'),
                    __('psmonitor::filament/customer/widgets/widgets.open_shift_totals.no_transactions')
                )
                    ->description(__('psmonitor::filament/customer/widgets/widgets.open_shift_totals.shift_desc', ['shift' => $openShiftNo]))
                    ->color('warning'),
            ];
        }

        return $stats;
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
