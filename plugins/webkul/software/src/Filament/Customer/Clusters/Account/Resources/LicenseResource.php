<?php

namespace Webkul\Software\Filament\Customer\Clusters\Account\Resources;

use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Software\Filament\Customer\Clusters\Account\Resources\LicenseResource\Pages\ListLicenses;
use Webkul\Software\Filament\Customer\Clusters\Account\Resources\LicenseResource\Pages\ViewLicense;
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
        return __('قائمة البرامج');
    }

    public static function getModelLabel(): string
    {
        return __('ترخيص برنامج');
    }

    public static function getPluralModelLabel(): string
    {
        return __('تراخيص البرامج');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serial_number')
                    ->label(__('رقم السيريال'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('program.name')
                    ->label(__('اسم البرنامج'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('edition.name')
                    ->label(__('الإصدار'))
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'success',
                        'inactive'  => 'gray',
                        'suspended' => 'warning',
                        'expired'   => 'danger',
                        default     => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label(__('تاريخ البداية'))
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('تاريخ النهاية'))
                    ->date('Y-m-d')
                    ->sortable()
                    ->color(fn ($record) => $record->end_date < now() ? 'danger' : 'success'),

                TextColumn::make('devices_count')
                    ->label(__('عدد الأجهزة'))
                    ->counts('devices')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('الحالة'))
                    ->options([
                        'active'    => __('نشط'),
                        'inactive'  => __('غير نشط'),
                        'suspended' => __('معلق'),
                        'expired'   => __('منتهي الصلاحية'),
                    ])
                    ->multiple(),

                SelectFilter::make('program_id')
                    ->label(__('البرنامج'))
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
