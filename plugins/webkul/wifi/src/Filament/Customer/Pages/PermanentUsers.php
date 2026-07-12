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
                TextColumn::make('username')->searchable()->sortable(),
                TextColumn::make('cloud.name')->label('Cloud')->searchable()->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
