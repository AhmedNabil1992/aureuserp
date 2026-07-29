<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Models\Partner;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Models\Invoices;
use Webkul\Psmonitor\Models\MarketHistory;
use Webkul\Psmonitor\Models\PlayHistory;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class InvoiceDetails extends Page
{
    use HasPsLicenseAccess;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected string $view = 'psmonitor::filament.customer.pages.invoice-details';

    protected static ?int $navigationSort = 47;

    protected static ?string $slug = 'invoice-details';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/invoice-details.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/invoice-details.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('psmonitor::filament/customer/navigation.group');
    }

    public ?string $invoiceNo = null;

    public ?Invoices $invoice = null;

    public Collection $playHistoryRows;

    public Collection $marketHistoryRows;

    protected function getHeaderWidgets(): array
    {
        return [
            LicenseSelectorWidget::class,
        ];
    }

    public function mount(): void
    {
        $this->invoiceNo = trim((string) request()->query('invoice_no', ''));
        $this->playHistoryRows = collect();
        $this->marketHistoryRows = collect();

        if ($this->invoiceNo === '') {
            return;
        }

        $customer = Auth::guard('customer')->user();

        if (! $customer instanceof Partner) {
            return;
        }

        try {
            $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

            if (! $license || ! RemoteModel::canConnectToHost($license->server_ip)) {
                return;
            }

            $this->invoice = Invoices::forLicense($license)
                ->where('Invoice_No', $this->invoiceNo)
                ->first();

            $this->playHistoryRows = PlayHistory::forLicense($license)
                ->where('Invoice_No', $this->invoiceNo)
                ->orderByDesc('ID')
                ->limit(500)
                ->get();

            $this->marketHistoryRows = MarketHistory::forLicense($license)
                ->where('Invoice_No', $this->invoiceNo)
                ->orderByDesc('ID')
                ->limit(500)
                ->get();
        } catch (\Throwable) {
            //
        }
    }

    public function getFormattedInvoiceTime(): string
    {
        $rawTime = trim((string) ($this->invoice?->Time ?? ''));

        if ($rawTime === '') {
            return '-';
        }

        if (preg_match('/(\d{2}:\d{2}(?::\d{2})?)/', $rawTime, $matches) === 1) {
            $timePart = $matches[1];

            return strlen($timePart) === 5 ? $timePart . ':00' : $timePart;
        }

        try {
            return \Carbon\Carbon::parse($rawTime)->format('H:i:s');
        } catch (\Throwable) {
            return $rawTime;
        }
    }

    public function getPlayHistoryTotal(): float
    {
        return (float) $this->playHistoryRows->sum('Cost');
    }

    public function getMarketHistoryTotal(): float
    {
        return (float) $this->marketHistoryRows->sum('Amount');
    }
}
