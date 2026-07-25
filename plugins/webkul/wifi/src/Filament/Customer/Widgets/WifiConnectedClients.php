<?php

namespace Webkul\Wifi\Filament\Customer\Widgets;

use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Webkul\Wifi\Filament\Customer\Concerns\HasCustomerCloudAccess;
use Webkul\Wifi\Models\Radacct;

class WifiConnectedClients extends BaseWidget implements Tables\Contracts\HasTable
{
    use HasCustomerCloudAccess, Tables\Concerns\InteractsWithTable;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('wifi::filament/customer/widgets/wifi-connected-clients.heading');
    }

    public function table(Table $table): Table
    {
        $realmNames = $this->getCustomerRealmNames();

        if ($realmNames->isEmpty()) {
            return $table
                ->query(Radacct::query()->whereRaw('1 = 0'))
                ->columns([])
                ->emptyStateHeading(__('wifi::filament/customer/widgets/wifi-connected-clients.empty.no_subscription'))
                ->emptyStateDescription('')
                ->emptyStateIcon('heroicon-o-information-circle');
        }

        $query = Radacct::query()
            ->whereIn('realm', $realmNames)
            ->whereNull('acctstoptime')
            ->select([
                'radacctid',
                'username',
                'callingstationid',
                'acctstarttime',
                'acctinputoctets',
                'acctoutputoctets',
                'nasidentifier',
            ])
            ->orderByDesc('radacctid');

        $tableBuilder = $table
            ->query($query)
            ->heading(__('wifi::filament/customer/widgets/wifi-connected-clients.heading'))
            ->description(__('wifi::filament/customer/widgets/wifi-connected-clients.description'))
            ->emptyStateHeading(__('wifi::filament/customer/widgets/wifi-connected-clients.empty.no_clients'))
            ->emptyStateDescription('')
            ->emptyStateIcon('heroicon-o-wifi')
            ->columns([
                TextColumn::make('username')
                    ->label(__('wifi::filament/customer/widgets/wifi-connected-clients.columns.username'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('callingstationid')
                    ->label(__('wifi::filament/customer/widgets/wifi-connected-clients.columns.mac_address'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('wifi::filament/customer/widgets/wifi-connected-clients.columns.mac_copied')),

                TextColumn::make('acctstarttime')
                    ->label(__('wifi::filament/customer/widgets/wifi-connected-clients.columns.connected_since'))
                    ->since()
                    ->sortable(),

                TextColumn::make('acctinputoctets')
                    ->label(__('wifi::filament/customer/widgets/wifi-connected-clients.columns.download'))
                    ->formatStateUsing(fn ($state) => static::formatBytes((int) $state))
                    ->sortable(),

                TextColumn::make('acctoutputoctets')
                    ->label(__('wifi::filament/customer/widgets/wifi-connected-clients.columns.upload'))
                    ->formatStateUsing(fn ($state) => static::formatBytes((int) $state))
                    ->sortable(),

                TextColumn::make('total_data')
                    ->label(__('wifi::filament/customer/widgets/wifi-connected-clients.columns.total_data'))
                    ->getStateUsing(fn ($record) => static::formatBytes(
                        (int) $record->acctinputoctets + (int) $record->acctoutputoctets
                    )),
            ])
            ->defaultPaginationPageOption(10);

        // Add Kick action only if MqttAccessPointService exists
        if (class_exists('\\Webkul\\Wifi\\Services\\MqttAccessPointService')) {
            $tableBuilder->actions([
                Action::make('kick_client')
                    ->label(__('wifi::filament/customer/widgets/wifi-connected-clients.actions.kick'))
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('wifi::filament/customer/widgets/wifi-connected-clients.actions.kick_heading'))
                    ->modalDescription(__('wifi::filament/customer/widgets/wifi-connected-clients.actions.kick_description'))
                    ->action(function (Radacct $record): void {
                        try {
                            app('\\Webkul\\Wifi\\Services\\MqttAccessPointService')
                                ->kickClientByNasIdentifier(
                                    $record->nasidentifier,
                                    $record->callingstationid,
                                );

                            Notification::make()
                                ->title(__('wifi::filament/customer/widgets/wifi-connected-clients.actions.kick_success'))
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            report($exception);

                            Notification::make()
                                ->title(__('wifi::filament/customer/widgets/wifi-connected-clients.actions.kick_failed'))
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
        }

        return $tableBuilder;
    }

    /**
     * Format bytes into a human-readable string (KB, MB, GB).
     */
    public static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1_073_741_824) {
            return number_format($bytes / 1_073_741_824, 2) . ' GB';
        }

        if ($bytes >= 1_048_576) {
            return number_format($bytes / 1_048_576, 2) . ' MB';
        }

        return number_format($bytes / 1_024, 2) . ' KB';
    }
}
