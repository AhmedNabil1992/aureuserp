<?php

namespace Webkul\Wifi\Filament\Customer\Pages;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Invoice;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\MoveLine;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Package;
use Webkul\Referral\Services\ReferralWalletService;
use Webkul\Wifi\Filament\Customer\Concerns\HasCustomerCloudAccess;
use Webkul\Wifi\Filament\Customer\Concerns\HasWifiAccess;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\WifiPackage;
use Webkul\Wifi\Models\WifiPurchase;

class VoucherInvoices extends Page implements HasTable
{
    use HasCustomerCloudAccess, HasWifiAccess, InteractsWithTable;

    protected string $view = 'wifi::filament.customer.pages.voucher-invoices';

    protected static ?int $navigationSort = 4;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public string $activeTab = 'incomplete';

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/customer/pages/voucher-invoices.title');
    }

    public static function getmodelLabel(): string
    {
        return __('wifi::filament/customer/pages/voucher-invoices.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function getTabCounts(): array
    {
        $cloudIds = $this->getCustomerCloudIds();
        $customer = $this->getCustomer();

        if (empty($cloudIds) || ! $customer) {
            return ['all' => 0, 'incomplete' => 0];
        }

        $base = WifiPurchase::query()
            ->forPartner($customer->id)
            ->whereIn('cloud_id', $cloudIds);

        return [
            'all'        => (clone $base)->count(),
            'incomplete' => (clone $base)->where('remaining_quantity', '>', 0)->count(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('عمل فاتورة جديدة')
                ->icon('heroicon-o-currency-dollar')
                ->model(WifiPurchase::class)
                ->form($this->getCreatePurchaseFormSchema())
                ->action(function (array $data): void {
                    $partner = Auth::guard('customer')->user();

                    if (! $partner instanceof Partner) {
                        throw ValidationException::withMessages([
                            'wifi_package_id' => 'جلسة تسجيل الدخول غير صالحة.',
                        ]);
                    }

                    $package = WifiPackage::query()->with(['product', 'currency'])->find($data['wifi_package_id'] ?? null);

                    if (! $package || ! $package->is_active) {
                        throw ValidationException::withMessages([
                            'wifi_package_id' => 'الباقة المختارة غير صالحة أو غير نشطة.',
                        ]);
                    }

                    $cloudId = (int) ($data['cloud_id'] ?? 0);
                    $customerCloudIds = $this->getCustomerCloudIds();

                    if (! in_array($cloudId, $customerCloudIds, true)) {
                        throw ValidationException::withMessages([
                            'cloud_id' => 'السحابة المختارة غير مسجلة لحسابك.',
                        ]);
                    }

                    $packagesCount = max(1, (int) ($data['quantity'] ?? 1));
                    $packageCards = max(1, (int) ($package->quantity ?? 1));

                    $priceUnit = (float) ($partner->is_dealer && $package->dealer_amount !== null
                        ? $package->dealer_amount
                        : $package->amount);

                    $totalCost = $packagesCount * $priceUnit;
                    $journal = $this->resolveSalesJournal($partner, (int) $package->currency_id);

                    if (! $journal) {
                        Notification::make()
                            ->title('دفتر مبيعات النظام غير مهيأ')
                            ->body('لا يوجد دفتر مبيعات بنفس شركة وعملة رصيدك.')
                            ->danger()
                            ->persistent()
                            ->send();

                        throw ValidationException::withMessages([
                            'wifi_package_id' => 'دفتر مبيعات النظام غير مهيأ.',
                        ]);
                    }

                    $availableCredit = $this->getPartnerAvailableCredit(
                        $partner->id,
                        (int) $journal->company_id,
                        (int) $package->currency_id,
                    );
                    $referralCredit = $this->getPartnerReferralCredit(
                        $partner->id,
                        (int) $journal->company_id,
                        (int) $package->currency_id,
                    );
                    $availableFunds = $availableCredit + $referralCredit;

                    if ($availableFunds + 0.0001 < $totalCost) {
                        Notification::make()
                            ->title('رصيد الحساب غير كافٍ')
                            ->body(sprintf('الرصيد الأساسي ورصيد الإحالات المتاحان: %s — تكلفة الفاتورة: %s', number_format($availableFunds, 2), number_format($totalCost, 2)))
                            ->danger()
                            ->persistent()
                            ->send();

                        throw ValidationException::withMessages([
                            'wifi_package_id' => sprintf('الرصيد المتاح غير كافٍ (%s متاح | %s مطلوب).', number_format($availableFunds, 2), number_format($totalCost, 2)),
                        ]);
                    }

                    DB::transaction(function () use ($journal, $package, $partner, $packagesCount, $packageCards, $priceUnit, $cloudId): void {
                        $invoice = Invoice::query()->create([
                            'journal_id'            => $journal->id,
                            'company_id'            => $journal->company_id,
                            'currency_id'           => $package->currency_id,
                            'partner_id'            => $partner->id,
                            'commercial_partner_id' => $partner->id,
                            'invoice_user_id'       => Auth::guard('web')->id(),
                            'creator_id'            => Auth::guard('web')->id(),
                            'state'                 => MoveState::DRAFT,
                            'move_type'             => MoveType::OUT_INVOICE,
                            'date'                  => now()->toDateString(),
                            'invoice_date'          => now()->toDateString(),
                            'invoice_date_due'      => now()->addDays(30)->toDateString(),
                        ]);

                        for ($i = 0; $i < $packagesCount; $i++) {
                            $moveLine = $invoice->invoiceLines()->create([
                                'product_id' => $package->product_id,
                                'uom_id'     => $package->product?->uom_id,
                                'quantity'   => 1,
                                'price_unit' => $priceUnit,
                            ]);

                            WifiPurchase::create([
                                'wifi_package_id'    => $package->id,
                                'move_line_id'       => $moveLine->id,
                                'cloud_id'           => $cloudId,
                                'quantity'           => $packageCards,
                                'remaining_quantity' => $packageCards,
                                'is_default'         => false,
                            ]);
                        }

                        AccountFacade::computeAccountMove($invoice);
                        $invoice = AccountFacade::confirmMove($invoice->refresh());

                        $this->applyOutstandingAdvancePayments($invoice->refresh());

                        $invoice->refresh();
                        $invoice->computePaymentState();
                        $invoice->save();

                        if ($invoice->payment_state !== PaymentState::PAID) {
                            throw ValidationException::withMessages([
                                'wifi_package_id' => 'تعذر تسوية قيمة الفاتورة بالكامل من رصيد الإحالات والرصيد الأساسي.',
                            ]);
                        }
                    });

                    $totalCards = $packagesCount * $packageCards;

                    Notification::make()
                        ->title('تم إنشاء الفاتورة وتخصيص رصيد الكروت بنجاح')
                        ->body(sprintf('تم تسوية %s من رصيد الإحالات و/أو الرصيد الأساسي وتفعيل %d كارت في السحابة (%d باقة).', number_format($totalCost, 2), $totalCards, $packagesCount))
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getCreatePurchaseFormSchema(): array
    {
        $cloudIds = $this->getCustomerCloudIds();
        $partner = Auth::guard('customer')->user();
        $availableCredit = $partner instanceof Partner ? $this->getPartnerAvailableCredit($partner->id) : 0.0;

        return [
            Select::make('cloud_id')
                ->label('إسم السحابة')
                ->options(fn (): array => Cloud::query()->whereIn('id', $cloudIds)->orderBy('name')->pluck('name', 'id')->all())
                ->default(fn (): ?int => count($cloudIds) === 1 ? $cloudIds[0] : null)
                ->required()
                ->searchable()
                ->preload(),

            Select::make('wifi_package_id')
                ->label('الباقة / عدد الكروت')
                ->options(function (): array {
                    $partner = Auth::guard('customer')->user();
                    $packages = WifiPackage::query()
                        ->where('is_active', true)
                        ->with(['product', 'currency'])
                        ->orderBy('id', 'desc')
                        ->get();

                    return $packages->mapWithKeys(function (WifiPackage $package) use ($partner): array {
                        $price = (float) ($partner?->is_dealer && $package->dealer_amount !== null ? $package->dealer_amount : $package->amount);
                        $currency = $package->currency?->name ?? 'EGP';

                        $label = sprintf(
                            '%s — %s (%s %s)',
                            $package->product?->name ?? 'Package',
                            $package->display_name,
                            number_format($price, 2),
                            $currency
                        );

                        return [$package->id => $label];
                    })->all();
                })
                ->helperText(sprintf('رصيدك الأساسي المتاح: %s — ويُضاف إليه رصيد الإحالات بالكامل عند شراء كروت Wi-Fi.', number_format($availableCredit, 2)))
                ->required()
                ->searchable()
                ->preload(),

            TextInput::make('quantity')
                ->label('عدد الباقات')
                ->numeric()
                ->integer()
                ->minValue(1)
                ->default(1)
                ->required(),
        ];
    }

    protected function getPartnerAvailableCredit(int $partnerId, ?int $companyId = null, ?int $currencyId = null): float
    {
        $creditRows = MoveLine::query()
            ->where('partner_id', $partnerId)
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->when($currencyId, fn ($query) => $query->where('currency_id', $currencyId))
            ->where('parent_state', MoveState::POSTED)
            ->where('reconciled', false)
            ->where('amount_residual', '<', 0)
            ->whereHas('account', fn ($query) => $query->where('account_type', 'asset_receivable'))
            ->selectRaw('SUM(CASE WHEN amount_residual_currency != 0 THEN amount_residual_currency ELSE amount_residual END) as residual_total')
            ->first();

        return abs((float) ($creditRows?->residual_total ?? 0.0));
    }

    private function resolveSalesJournal(Partner $partner, int $currencyId): ?Journal
    {
        $companyId = (int) $partner->company_id;

        if (! $companyId) {
            $companyId = (int) MoveLine::query()
                ->where('partner_id', $partner->id)
                ->where('currency_id', $currencyId)
                ->where('parent_state', MoveState::POSTED)
                ->where('reconciled', false)
                ->where('amount_residual', '<', 0)
                ->whereHas('account', fn ($query) => $query->where('account_type', 'asset_receivable'))
                ->orderBy('date')
                ->value('company_id');
        }

        $companyId = $companyId ?: (int) current_company_id();

        return Journal::query()
            ->where('type', JournalType::SALE)
            ->when(
                $companyId,
                fn ($query) => $query->where('company_id', $companyId),
                fn ($query) => $query->whereHas('company', fn ($companyQuery) => $companyQuery->where('currency_id', $currencyId)),
            )
            ->orderBy('id')
            ->first();
    }

    protected function getPartnerReferralCredit(int $partnerId, int $companyId, int $currencyId): float
    {
        if (
            ! class_exists(ReferralWalletService::class)
            || ! Package::isPluginInstalled('referrals')
            || ! DatabaseSchema::hasTable('referral_wallet_balances')
        ) {
            return 0.0;
        }

        return app(ReferralWalletService::class)->balance($companyId, $currencyId, $partnerId);
    }

    private function applyOutstandingAdvancePayments(Invoice $invoice): void
    {
        $invoice->loadMissing(['paymentTermLines.account']);

        $openTermLines = $invoice->paymentTermLines
            ->filter(fn (MoveLine $line): bool => ! $line->reconciled && (float) $line->amount_residual !== 0.0)
            ->values();

        foreach ($openTermLines as $termLine) {
            $operator = (float) $termLine->amount_residual > 0 ? '<' : '>';

            $outstandingLines = MoveLine::query()
                ->with('move')
                ->where('partner_id', $invoice->partner_id)
                ->where('account_id', $termLine->account_id)
                ->where('parent_state', MoveState::POSTED)
                ->where('reconciled', false)
                ->where('move_id', '!=', $invoice->id)
                ->where('amount_residual', $operator, 0)
                ->orderBy('date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($outstandingLines->isEmpty()) {
                continue;
            }

            try {
                AccountFacade::reconcile((new EloquentCollection([$termLine]))->merge($outstandingLines));

                $paymentIds = $outstandingLines
                    ->pluck('move.origin_payment_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                if (! empty($paymentIds)) {
                    $invoice->matchedPayments()->syncWithoutDetaching($paymentIds);
                }
            } catch (\Throwable $throwable) {
                Log::warning('Wi-Fi customer purchase auto reconciliation skipped.', [
                    'invoice_id' => $invoice->id,
                    'line_id'    => $termLine->id,
                    'error'      => $throwable->getMessage(),
                ]);
            }
        }
    }

    public function table(Table $table): Table
    {
        $cloudIds = $this->getCustomerCloudIds();
        $customer = $this->getCustomer();

        if (empty($cloudIds) || ! $customer) {
            return $table
                ->query(WifiPurchase::query()->whereRaw('1 = 0'))
                ->columns([])
                ->emptyStateHeading(__('wifi::filament/customer/pages/voucher-invoices.empty'))
                ->emptyStateIcon('heroicon-o-rectangle-stack');
        }

        $query = WifiPurchase::query()
            ->forPartner($customer->id)
            ->whereIn('cloud_id', $cloudIds)
            ->with(['package', 'cloud'])
            ->orderByDesc('created_at');

        if ($this->activeTab === 'incomplete') {
            $query->where('remaining_quantity', '>', 0);
        }

        return $table
            ->query($query)
            ->emptyStateHeading(__('wifi::filament/customer/pages/voucher-invoices.empty'))
            ->emptyStateIcon('heroicon-o-rectangle-stack')
            ->columns([
                TextColumn::make('package.display_name')
                    ->label(__('wifi::filament/customer/pages/voucher-invoices.columns.package_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cloud.name')
                    ->label(__('wifi::filament/customer/pages/voucher-invoices.columns.cloud_name'))
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label(__('wifi::filament/customer/pages/voucher-invoices.columns.quantity'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('generated_quantity')
                    ->label(__('wifi::filament/customer/pages/voucher-invoices.columns.generated_quantity'))
                    ->badge()
                    ->color('info')
                    ->getStateUsing(fn ($record) => $record->generated_quantity),

                TextColumn::make('remaining_quantity')
                    ->label(__('wifi::filament/customer/pages/voucher-invoices.columns.remaining_quantity'))
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('wifi::filament/customer/pages/voucher-invoices.columns.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(25);
    }
}
