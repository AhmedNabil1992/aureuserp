<?php

namespace Webkul\Wifi\Filament\Customer\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Webkul\Wifi\Enums\WifiPackageType;
use Webkul\Wifi\Filament\Customer\Concerns\HasCustomerCloudAccess;
use Webkul\Wifi\Filament\Customer\Concerns\HasWifiAccess;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\DynamicClient;
use Webkul\Wifi\Models\DynamicClientRealm;
use Webkul\Wifi\Models\Profile;
use Webkul\Wifi\Models\Realm;
use Webkul\Wifi\Models\Voucher;
use Webkul\Wifi\Models\WifiPurchase;
use Webkul\Wifi\Models\WifiVoucherBatch;
use Webkul\Wifi\Services\VoucherGenerationService;

class VoucherBatch extends Page implements HasTable
{
    use HasCustomerCloudAccess, HasWifiAccess, InteractsWithTable;

    protected string $view = 'wifi::filament.customer.pages.voucher-batch';

    protected static ?int $navigationSort = 3;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/customer/pages/voucher-batch.title');
    }

    public static function getmodelLabel(): string
    {
        return __('wifi::filament/customer/pages/voucher-batch.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.buttons.new_batch'))
                ->icon('heroicon-o-plus-circle')
                ->model(WifiVoucherBatch::class)
                ->form($this->getBatchFormSchema())
                ->after(function (WifiVoucherBatch $record): void {
                    try {
                        $result = app(VoucherGenerationService::class)->generateFromBatch($record);

                        $downloadUrl = $result['download_url'] ?? VoucherGenerationService::buildDownloadUrl($record->batch_code);

                        Notification::make()
                            ->title(__('wifi::filament/resources/wifi_voucher_batch.messages.generated_success'))
                            ->success()
                            ->actions([
                                Action::make('download')
                                    ->label(__('wifi::filament/customer/pages/voucher-batch.actions.download'))
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->button()
                                    ->url($downloadUrl, shouldOpenInNewTab: true),
                            ])
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title(__('wifi::filament/resources/wifi_voucher_batch.messages.generated_warning'))
                            ->body($e->getMessage())
                            ->warning()
                            ->send();
                    }
                }),
        ];
    }

    protected function getBatchFormSchema(): array
    {
        $cloudIds = $this->getCustomerCloudIds();

        return [
            Select::make('wifi_purchase_id')
                ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.wifi_purchase_id'))
                ->options(function () use ($cloudIds): array {
                    return WifiPurchase::query()
                        ->whereIn('cloud_id', $cloudIds)
                        ->where('remaining_quantity', '>', 0)
                        ->with(['package', 'cloud'])
                        ->orderByDesc('id')
                        ->get()
                        ->mapWithKeys(fn (WifiPurchase $p) => [
                            $p->id => sprintf(
                                '%s - %s (%s: %d)',
                                $p->package?->display_name ?? 'Package',
                                $p->cloud?->name ?? 'Cloud',
                                __('wifi::filament/customer/pages/voucher-invoices.columns.remaining_quantity'),
                                $p->remaining_quantity
                            ),
                        ])
                        ->all();
                })
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(function (Set $set, $state): void {
                    $purchase = WifiPurchase::query()->with('package')->find($state);

                    if ($purchase) {
                        $set('cloud_id', $purchase->cloud_id);
                        $set('realm_id', null);
                        $set('nasidentifier', null);

                        $defaultProfileId = Profile::where('cloud_id', $purchase->cloud_id)->value('id');
                        if ($defaultProfileId) {
                            $set('profile_id', $defaultProfileId);
                        }

                        $isUnlimitedPackage = ($purchase->package?->package_type?->value) === WifiPackageType::Unlimited->value;
                        $set('never_expire', $isUnlimitedPackage);
                    }
                })
                ->required(),

            Select::make('cloud_id')
                ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.cloud_id'))
                ->options(fn (): array => Cloud::query()->whereIn('id', $cloudIds)->orderBy('name')->pluck('name', 'id')->all())
                ->disabled()
                ->dehydrated()
                ->live(),

            Select::make('realm_id')
                ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.realm_id'))
                ->options(fn (Get $get): array => Realm::query()
                    ->whereIn('cloud_id', $cloudIds)
                    ->when($get('cloud_id'), fn ($query, $cloudId) => $query->where('cloud_id', $cloudId))
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('nasidentifier', null))
                ->required(),

            Select::make('nasidentifier')
                ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.nasidentifier'))
                ->options(function (Get $get): array {
                    $realmId = $get('realm_id');

                    if (blank($realmId)) {
                        return [];
                    }

                    return DynamicClient::query()
                        ->whereIn(
                            'id',
                            DynamicClientRealm::query()
                                ->where('realm_id', $realmId)
                                ->pluck('dynamic_client_id')
                        )
                        ->orderBy('name')
                        ->get()
                        ->mapWithKeys(fn (DynamicClient $client): array => [
                            $client->nasidentifier => ($client->name ? ($client->name . ' (' . $client->nasidentifier . ')') : $client->nasidentifier),
                        ])
                        ->all();
                })
                ->disabled(fn (Get $get): bool => blank($get('realm_id')))
                ->searchable()
                ->preload(),

            Select::make('profile_id')
                ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.profile_id'))
                ->options(function (Get $get) use ($cloudIds): array {
                    $cloudId = $get('cloud_id');

                    return Profile::query()
                        ->where(function ($query) use ($cloudId, $cloudIds) {
                            if ($cloudId) {
                                $query->where('cloud_id', $cloudId);
                            } else {
                                $query->whereIn('cloud_id', $cloudIds);
                            }

                            $query->orWhere('cloud_id', -1)
                                  ->orWhere('cloud_id', 0)
                                  ->orWhereNull('cloud_id');
                        })
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all();
                })
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('quantity')
                ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.quantity'))
                ->numeric()
                ->integer()
                ->minValue(1)
                ->maxValue(fn (Get $get): ?int => self::resolveAvailableQuantityForCustomer($get))
                ->helperText(function (Get $get): ?string {
                    $available = self::resolveAvailableQuantityForCustomer($get);

                    if ($available === null) {
                        return null;
                    }

                    return __('Max available: :quantity', ['quantity' => $available]);
                })
                ->required(),

            Fieldset::make(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.validity'))
                ->schema([
                    TextInput::make('days_valid')
                        ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.days_valid'))
                        ->numeric()
                        ->integer()
                        ->default(0)
                        ->minValue(0),

                    TextInput::make('hours_valid')
                        ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.hours_valid'))
                        ->numeric()
                        ->integer()
                        ->default(0)
                        ->minValue(0),

                    TextInput::make('minutes_valid')
                        ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.minutes_valid'))
                        ->numeric()
                        ->integer()
                        ->default(0)
                        ->minValue(0),
                ])
                ->columns(3)
                ->columnSpanFull(),

            Toggle::make('never_expire')
                ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.never_expire'))
                ->helperText(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.never_expire_helper_text'))
                ->disabled()
                ->dehydrated()
                ->default(false),

            TextInput::make('caption')
                ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.caption'))
                ->maxLength(255),
        ];
    }

    private static function resolveAvailableQuantityForCustomer(Get $get): ?int
    {
        $purchaseId = $get('wifi_purchase_id');

        if (blank($purchaseId)) {
            return null;
        }

        $purchase = WifiPurchase::query()->find($purchaseId);

        if (! $purchase) {
            return null;
        }

        return max(1, (int) $purchase->remaining_quantity);
    }

    public function table(Table $table): Table
    {
        $cloudIds = $this->getCustomerCloudIds();

        if (empty($cloudIds)) {
            return $table
                ->query(WifiVoucherBatch::query()->whereRaw('1 = 0'))
                ->columns([])
                ->emptyStateHeading(__('wifi::filament/customer/pages/voucher-batch.empty'))
                ->emptyStateIcon('heroicon-o-document-text');
        }

        $query = WifiVoucherBatch::query()
            ->whereIn('cloud_id', $cloudIds)
            ->orderByDesc('created_at');

        return $table
            ->query($query)
            ->emptyStateHeading(__('wifi::filament/customer/pages/voucher-batch.empty'))
            ->emptyStateIcon('heroicon-o-document-text')
            ->columns([
                TextColumn::make('nasidentifier')
                    ->label(__('wifi::filament/customer/pages/voucher-batch.columns.nasidentifier'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('Global'),

                TextColumn::make('quantity')
                    ->label(__('wifi::filament/customer/pages/voucher-batch.columns.qty'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('remaining_new_vouchers')
                    ->label(__('wifi::filament/customer/pages/voucher-batch.columns.remaining_vouchers'))
                    ->badge()
                    ->color('success')
                    ->getStateUsing(function ($record) {
                        return Voucher::where('batch', $record->batch_code)
                            ->where('status', 'new')
                            ->count();
                    }),

                TextColumn::make('batch_code')
                    ->label(__('wifi::filament/customer/pages/voucher-batch.columns.batch_code'))
                    ->badge()
                    ->copyable()
                    ->copyableState(fn ($record) => VoucherGenerationService::buildDownloadUrl($record->batch_code))
                    ->copyMessage(__('wifi::filament/customer/pages/voucher-batch.actions.copied_message'))
                    ->searchable(),

                TextColumn::make('caption')
                    ->label(__('wifi::filament/customer/pages/voucher-batch.columns.caption'))
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('never_expire')
                    ->label(__('wifi::filament/customer/pages/voucher-batch.columns.never_expire'))
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) {
                            return __('wifi::filament/customer/pages/voucher-batch.never_expire_options.yes');
                        }

                        return $record->updated_at ? $record->updated_at->format('Y-m-d') : '-';
                    }),

                TextColumn::make('created_at')
                    ->label(__('wifi::filament/customer/pages/voucher-batch.columns.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('download')
                    ->label(__('wifi::filament/customer/pages/voucher-batch.actions.download'))
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->color('primary')
                    ->url(fn ($record) => VoucherGenerationService::buildDownloadUrl($record->batch_code), shouldOpenInNewTab: true),

                EditAction::make()
                    ->label(__('wifi::filament/customer/pages/voucher-batch.actions.edit_caption'))
                    ->form([
                        TextInput::make('caption')
                            ->label(__('wifi::filament/customer/pages/voucher-batch.columns.caption'))
                            ->required()
                            ->maxLength(255)
                            ->rule('regex:/^[a-zA-Z0-9\s]+$/')
                            ->helperText('Allowed: English letters, numbers, and spaces.'),
                    ]),
            ])
            ->defaultPaginationPageOption(25);
    }
}
