<?php

namespace Webkul\Wifi\Filament\Customer\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Facades\Filament;
use Webkul\Wifi\Models\WifiPartnerCloud;
use Webkul\Wifi\Models\PermanentUser;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Webkul\Wifi\Services\PermanentUserService;
use Filament\Notifications\Notification;

class PermanentUsers extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'wifi::filament.customer.clusters.wi-fi.pages.permanent-users';

    // protected static ?string $cluster = WiFiCluster::class;

    protected static ?string $title = 'Permanent Users';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

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

        $query = PermanentUser::query();

        if ($partnerId) {
            $cloudIds = WifiPartnerCloud::where('partner_id', $partnerId)->pluck('cloud_id')->toArray();
            $query->whereIn('cloud_id', $cloudIds);
        } else {
            $query->whereRaw('1=0');
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('cloud.name')->label('Cloud'),    
                TextColumn::make('realms.name')->label('Realm'),
                TextColumn::make('username')->searchable()->sortable(),
                TextColumn::make('profiles.name')->label('Profile'),
                TextColumn::make('last_accept_time')->label('Last Accept Time')->dateTime()->sortable()->since()->dateTimeTooltip(),
                TextColumn::make('last_reject_time')->label('Last Reject Time')->dateTime()->sortable()->since()->dateTimeTooltip(),
                TextColumn::make('last_accept_nas')->label('Last Accept NAS')->searchable()->sortable(),
                TextColumn::make('last_reject_nas')->label('Last Reject NAS')->searchable()->sortable(),
                TextColumn::make('last_reject_message')->label('Last Reject Message')->searchable()->sortable(),
                TextColumn::make('created')->label('Created')->dateTime()->sortable()->since()->dateTimeTooltip(),
                TextColumn::make('modified')->label('Modified')->dateTime()->sortable()->since()->dateTimeTooltip(),
                IconColumn::make('active')->label('Active')->boolean()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (PermanentUser $record) {
                        app(PermanentUserService::class)->delete($record);
                        Notification::make()
                            ->title('Permanent User deleted successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }
}
