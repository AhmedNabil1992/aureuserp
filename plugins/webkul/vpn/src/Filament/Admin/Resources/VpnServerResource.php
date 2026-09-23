<?php

declare(strict_types=1);

namespace Webkul\Vpn\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Throwable;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Vpn\Filament\Admin\Resources\VpnServerResource\Pages\ManageVpnServers;
use Webkul\Vpn\Models\VpnServer;
use Webkul\Vpn\Services\SoftEtherClientFactory;

class VpnServerResource extends Resource
{
    protected static ?string $model = VpnServer::class;

    protected static ?string $slug = 'vpn-servers';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-server-stack';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Vpn;
    }

    public static function getNavigationLabel(): string
    {
        return __('vpn::app.servers.title');
    }

    public static function getModelLabel(): string
    {
        return __('vpn::app.servers.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('vpn::app.servers.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('vpn::app.servers.connection'))
                ->schema([
                    TextInput::make('name')->label(__('vpn::app.fields.name'))->required()->maxLength(255),
                    TextInput::make('host')
                        ->label(__('vpn::app.fields.host'))
                        ->helperText(__('vpn::app.fields.host_help'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('port')->label(__('vpn::app.fields.port'))->numeric()->minValue(1)->maxValue(65535)->default(443)->required(),
                    TextInput::make('admin_password')
                        ->label(__('vpn::app.fields.admin_password'))
                        ->password()
                        ->revealable()
                        ->required(fn (?VpnServer $record): bool => $record === null)
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->helperText(__('vpn::app.fields.password_help')),
                    TextInput::make('timeout_seconds')->label(__('vpn::app.fields.timeout'))->numeric()->minValue(2)->maxValue(60)->default(10)->required(),
                    Toggle::make('verify_tls')->label(__('vpn::app.fields.verify_tls'))->default(true),
                    Toggle::make('is_active')->label(__('vpn::app.fields.active'))->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('vpn::app.fields.name'))->searchable()->sortable(),
                TextColumn::make('host')->label(__('vpn::app.fields.host'))->formatStateUsing(fn (VpnServer $record) => "{$record->host}:{$record->port}")->copyable(),
                TextColumn::make('known_hubs')->label(__('vpn::app.fields.hubs'))->formatStateUsing(fn (?array $state) => implode(', ', $state ?? []))->placeholder('—')->wrap(),
                IconColumn::make('is_active')->label(__('vpn::app.fields.active'))->boolean(),
                TextColumn::make('last_connected_at')->label(__('vpn::app.fields.last_connected'))->since()->placeholder(__('vpn::app.common.never'))->sortable(),
                TextColumn::make('last_error')->label(__('vpn::app.fields.last_error'))->limit(45)->tooltip(fn (VpnServer $record) => $record->last_error)->placeholder('—')->color('danger'),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('test')
                        ->label(__('vpn::app.actions.test'))
                        ->icon('heroicon-o-signal')
                        ->authorize(fn (VpnServer $record) => auth()->user()?->can('update', $record) ?? false)
                        ->action(function (VpnServer $record, SoftEtherClientFactory $factory): void {
                            try {
                                $client = $factory->make($record);
                                $info = $client->serverInfo();
                                $hubs = collect($client->hubs())->pluck('HubName_str')->filter()->values()->all();
                                $record->forceFill(['known_hubs' => $hubs])->save();
                                Notification::make()
                                    ->success()
                                    ->title(__('vpn::app.messages.connected'))
                                    ->body(($info['ServerProductName_str'] ?? 'SoftEther').' — '.count($hubs).' hub(s)')
                                    ->send();
                            } catch (Throwable $exception) {
                                Notification::make()->danger()->title(__('vpn::app.messages.connection_failed'))->body($exception->getMessage())->send();
                            }
                        }),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageVpnServers::route('/')];
    }
}
