<?php

namespace Webkul\Software\Filament\Customer\Resources;

use Webkul\Software\Filament\Customer\Clusters\Licensing;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Software\Filament\Customer\Resources\LicenseResource\Pages\ListLicenses;
use Webkul\Software\Filament\Customer\Resources\LicenseResource\Pages\ViewLicense;
use Webkul\Software\Models\License;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $slug = 'licenses';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?int $navigationSort = 5;

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationLabel(): string
    {
        return __('software::filament/customer/license.navigation.label');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.software');
    }
    
    public static function getModelLabel(): string
    {
        return __('software::filament/customer/license.models.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('software::filament/customer/license.models.plural');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serial_number')
                    ->label(__('software::filament/customer/license.table.columns.serial_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('program.name')
                    ->label(__('software::filament/customer/license.table.columns.program_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('edition.name')
                    ->label(__('software::filament/customer/license.table.columns.edition'))
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('software::filament/customer/license.table.columns.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label(__('software::filament/customer/license.table.columns.start_date'))
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('software::filament/customer/license.table.columns.end_date'))
                    ->date('Y-m-d')
                    ->sortable()
                    ->color(fn ($record) => $record->end_date < now() ? 'danger' : 'success'),

                TextColumn::make('devices_count')
                    ->label(__('software::filament/customer/license.table.columns.devices_count'))
                    ->counts('devices')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('software::filament/customer/license.table.filters.status'))
                    ->options([
                        'active'    => __('software::filament/customer/license.statuses.active'),
                        'inactive'  => __('software::filament/customer/license.statuses.inactive'),
                        'suspended' => __('software::filament/customer/license.statuses.suspended'),
                        'expired'   => __('software::filament/customer/license.statuses.expired'),
                    ])
                    ->multiple(),

                SelectFilter::make('program_id')
                    ->label(__('software::filament/customer/license.table.filters.program'))
                    ->relationship('program', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->paginated([10, 25, 50])
            ->defaultSort('start_date', 'desc')
            ->modifyQueryUsing(function (Builder $query): Builder {
                $partnerId = Auth::guard('customer')->id();

                return $query
                    ->where('partner_id', $partnerId)
                    ->orderByDesc('created_at');
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLicenses::route('/'),
            'view'  => ViewLicense::route('/{record}'),
        ];
    }
}
