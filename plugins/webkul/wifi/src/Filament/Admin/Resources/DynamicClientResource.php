<?php

namespace Webkul\Wifi\Filament\Admin\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Webkul\Wifi\Filament\Admin\Resources\DynamicClientResource\Pages;
use Webkul\Wifi\Models\DynamicClient;

class DynamicClientResource extends Resource
{
    protected static ?string $model = DynamicClient::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wifi';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/resources/dynamic_client.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Placeholder::make('cloud_name')
                ->label(__('wifi::filament/resources/dynamic_client.table.columns.cloud'))
                ->content(fn (?DynamicClient $record): string => $record?->cloud?->name ?? '-'),
            TextInput::make('name')
                ->label(__('wifi::filament/resources/dynamic_client.table.columns.name'))
                ->disabled()
                ->dehydrated(),
            TextInput::make('nasidentifier')
                ->label(__('wifi::filament/resources/dynamic_client.table.columns.nasidentifier'))
                ->disabled()
                ->dehydrated(),
            TextInput::make('last_contact')
                ->label('Last Contact')
                ->disabled()
                ->dehydrated(),
            TextInput::make('last_contact_ip')
                ->label('Last Contact IP')
                ->disabled()
                ->dehydrated(),
            TextInput::make('zero_ip')
                ->label('ZeroTier IP')
                ->disabled()
                ->dehydrated(),
            FileUpload::make('Picture')
                ->label('Picture')
                ->image()
                ->directory('dynamic_client')
                ->visibility('public')
                ->required()
                ->appendFiles()
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('wifi::filament/resources/dynamic_client.table.columns.id'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('cloud.name')
                    ->label(__('wifi::filament/resources/dynamic_client.table.columns.cloud'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('wifi::filament/resources/dynamic_client.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nasidentifier')
                    ->label(__('wifi::filament/resources/dynamic_client.table.columns.nasidentifier'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('last_contact')
                    ->badge()
                    ->label(__('wifi::filament/resources/dynamic_client.table.columns.last_contact'))
                    ->color(function ($state) {
                        if ($state) {
                            $now = Carbon::now('Africa/Cairo'); // Set timezone to Egypt
                            $lastContact = Carbon::parse($state, 'Africa/Cairo'); // Set timezone to Egypt

                            $diffInMinutes = $lastContact->diffInMinutes($now);

                            if ($diffInMinutes <= 60) {
                                return 'success'; // green
                            } elseif ($diffInMinutes > 60 && $diffInMinutes <= 120) {
                                return 'warning'; // yellow
                            } elseif ($diffInMinutes > 120) {
                                return 'danger'; // red
                            }
                        }

                        return 'secondary'; // gray
                    })
                    ->since()->dateTimeTooltip()->weight(FontWeight::Bold),
                TextColumn::make('last_contact_ip')->label(__('wifi::filament/resources/dynamic_client.table.columns.last_contact_ip'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('zero_ip')
                    ->label(__('wifi::filament/resources/dynamic_client.table.columns.zero_ip'))
                    ->searchable(),
                BooleanColumn::make('Picture')->label(__('wifi::filament/resources/dynamic_client.table.columns.picture'))->getStateUsing(function ($record) {
                    return ! empty($record->Picture);
                }),
            ])
            ->defaultSort('cloud.name')
            ->filters([

                Filter::make('picture')
                    ->label(__('wifi::filament/resources/dynamic_client.table.columns.picture_uploaded'))
                    ->query(function ($query) {
                        return $query->whereNotNull('Picture');
                    }),
                Filter::make('no_picture')
                    ->label(__('wifi::filament/resources/dynamic_client.table.columns.no_picture'))
                    ->query(function ($query) {
                        return $query->whereNull('Picture');
                    }),

                // Last Contact Filters
                Filter::make('last_contact_less_than_1_day')
                    ->label(__('wifi::filament/resources/dynamic_client.table.columns.last_contact_less_than_1_day'))
                    ->query(function ($query) {
                        $now = Carbon::now('Africa/Cairo');

                        return $query->where('last_contact', '>=', $now->subday());
                    }),
                Filter::make('last_contact_more_than_1_day_less_than_1_week')
                    ->label(__('wifi::filament/resources/dynamic_client.table.columns.last_contact_more_than_1_day_less_than_1_week'))
                    ->query(function ($query) {
                        $now = Carbon::now('Africa/Cairo');
                        $oneDayAgo = $now->copy()->subDay();
                        $oneWeekAgo = $now->copy()->subWeek();

                        return $query->whereBetween('last_contact', [$oneWeekAgo, $oneDayAgo]);
                    }),
                Filter::make('last_contact_more_than_1_week_less_than_1_month')
                    ->label(__('wifi::filament/resources/dynamic_client.table.columns.last_contact_more_than_1_week_less_than_1_month'))
                    ->query(function ($query) {
                        $now = Carbon::now('Africa/Cairo');
                        $oneWeekAgo = $now->copy()->subWeek();
                        $oneMonthAgo = $now->copy()->subMonth();

                        return $query->whereBetween('last_contact', [$oneMonthAgo, $oneWeekAgo]);
                    }),
                Filter::make('last_contact_more_than_1_month')
                    ->label(__('wifi::filament/resources/dynamic_client.table.columns.last_contact_more_than_1_month'))
                    ->query(function ($query) {
                        $now = Carbon::now('Africa/Cairo');

                        return $query->where('last_contact', '<', $now->subMonth());
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDynamicClients::route('/'),
            'view'  => Pages\ViewDynamicClient::route('/{record}'),
        ];
    }
}
