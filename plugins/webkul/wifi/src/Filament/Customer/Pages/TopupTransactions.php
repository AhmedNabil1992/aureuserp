<?php

namespace Webkul\Wifi\Filament\Customer\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Facades\Filament;
use Webkul\Wifi\Models\TopUpTransaction;
use Webkul\Wifi\Models\PermanentUser;
use Webkul\Wifi\Models\WifiPartnerCloud;
use Filament\Tables\Columns\TextColumn;

class TopupTransactions extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'wifi::filament.customer.pages.topup-transactions';

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/customer/pages/topuptransactions.title');
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        if (! Schema::hasTable('wifi_partner_clouds')) {
            return false;
        }

        return WifiPartnerCloud::where('partner_id', $user->id)->exists();
    }

    public function table(Table $table): Table
    {
        $user = Filament::auth()->user();
        $partnerId = $user?->id;

        $query = TopUpTransaction::query();

        if ($partnerId) {
            $cloudIds = WifiPartnerCloud::where('partner_id', $partnerId)->pluck('cloud_id')->toArray();
            $query->whereIn('permanent_user_id', PermanentUser::whereIn('cloud_id', $cloudIds)->where('profile','TopUp_U')->pluck('id')->toArray());
        } else {
            $query->whereRaw('1=0');
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('permanent_user')->label(__('wifi::filament/customer/pages/topuptransactions.table.columns.permanent_user'))->searchable()->sortable(),
                TextColumn::make('top_up_id')->label(__('wifi::filament/customer/pages/topuptransactions.table.columns.top_up_id'))->searchable(),
                TextColumn::make('type')->label(__('wifi::filament/customer/pages/topuptransactions.table.columns.type'))->sortable(),
                TextColumn::make('action')->label(__('wifi::filament/customer/pages/topuptransactions.table.columns.action'))->badge(),
                TextColumn::make('radius_attribute')->label(__('wifi::filament/customer/pages/topuptransactions.table.columns.radius_attribute')),
                TextColumn::make('old_value')->label(__('wifi::filament/customer/pages/topuptransactions.table.columns.old_value'))
                    ->formatStateUsing(function ($state, $record) {
                            if ($record->radius_attribute !== 'Rd-Total-Data') {
                                return $state;
                            }

                            if ($state === null || $state === '') {
                                return '-';
                            }

                            if (!is_numeric($state)) {
                                return (string) $state;
                            }

                            $bytes = (float) $state;

                            if ($bytes >= 1073741824) {
                                return number_format($bytes / 1073741824, 2) . ' GB';
                            }

                            return number_format($bytes / 1048576, 2) . ' MB';
                        }),
                TextColumn::make('new_value')->label(__('wifi::filament/customer/pages/topuptransactions.table.columns.new_value'))
                    ->formatStateUsing(function ($state, $record) {
                            if ($record->radius_attribute !== 'Rd-Total-Data') {
                                return $state;
                            }

                            if ($state === null || $state === '') {
                                return '-';
                            }

                            if (!is_numeric($state)) {
                                return (string) $state;
                            }

                            $bytes = (float) $state;

                            if ($bytes >= 1073741824) {
                                return number_format($bytes / 1073741824, 2) . ' GB';
                            }

                            return number_format($bytes / 1048576, 2) . ' MB';
                        }),
                TextColumn::make('created')->label(__('wifi::filament/customer/pages/topuptransactions.table.columns.created'))->dateTime()->sortable()->since()->dateTimeTooltip(),
                TextColumn::make('updated')->label(__('wifi::filament/customer/pages/topuptransactions.table.columns.modified'))->dateTime()->sortable()->since()->dateTimeTooltip(),
            ])
            ->filters([
                //
            ])
            ->actions([
                
            ])
            ->bulkActions([
                //
            ]);
    }

}
