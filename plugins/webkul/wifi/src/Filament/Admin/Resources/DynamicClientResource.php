<?php

namespace Webkul\Wifi\Filament\Admin\Resources;

use Filament\Actions\ViewAction;
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

    protected static string|\UnitEnum|null $navigationGroup = 'Wi-Fi';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('cloud.name')
                    ->label('Cloud')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nasidentifier')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('last_contact')
                    ->badge()
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
                TextColumn::make('last_contact_ip')->label('Last Contact IP')->sortable()->searchable(),

                TextColumn::make('zero_ip')
                    ->label('ZeroTier IP')
                    ->searchable(),
                BooleanColumn::make('Picture')->label('Picture Uploaded')->getStateUsing(function ($record) {
                    return ! empty($record->Picture);
                }),
            ])
            ->defaultSort('cloud.name')
            ->filters([

                Filter::make('picture')
                    ->label('Picture Uploaded')
                    ->query(function ($query) {
                        return $query->whereNotNull('Picture');
                    }),
                Filter::make('no_picture')
                    ->label('No Picture')
                    ->query(function ($query) {
                        return $query->whereNull('Picture');
                    }),

                // Last Contact Filters
                Filter::make('last_contact_less_than_1_day')
                    ->label('Last Contact Less Than 1 Day')
                    ->query(function ($query) {
                        $now = Carbon::now('Africa/Cairo');

                        return $query->where('last_contact', '>=', $now->subday());
                    }),
                Filter::make('last_contact_more_than_1_day_less_than_1_week')
                    ->label('Last Contact More Than 1 Day and Less Than 1 Week')
                    ->query(function ($query) {
                        $now = Carbon::now('Africa/Cairo');
                        $oneDayAgo = $now->copy()->subDay();
                        $oneWeekAgo = $now->copy()->subWeek();

                        return $query->whereBetween('last_contact', [$oneWeekAgo, $oneDayAgo]);
                    }),
                Filter::make('last_contact_more_than_1_week_less_than_1_month')
                    ->label('Last Contact More Than 1 Week and Less Than 1 Month')
                    ->query(function ($query) {
                        $now = Carbon::now('Africa/Cairo');
                        $oneWeekAgo = $now->copy()->subWeek();
                        $oneMonthAgo = $now->copy()->subMonth();

                        return $query->whereBetween('last_contact', [$oneMonthAgo, $oneWeekAgo]);
                    }),
                Filter::make('last_contact_more_than_1_month')
                    ->label('Last Contact More Than 1 Month')
                    ->query(function ($query) {
                        $now = Carbon::now('Africa/Cairo');

                        return $query->where('last_contact', '<', $now->subMonth());
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
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
