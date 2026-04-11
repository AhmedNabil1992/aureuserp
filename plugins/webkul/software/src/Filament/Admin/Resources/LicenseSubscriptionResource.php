<?php

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Software\Enums\ServiceType;
use Webkul\Software\Filament\Admin\Clusters\Licensing;
use Webkul\Software\Filament\Admin\Resources\LicenseSubscriptionResource\Pages\ManageLicenseSubscriptions;
use Webkul\Software\Models\LicenseSubscription;

class LicenseSubscriptionResource extends Resource
{
    protected static ?string $model = LicenseSubscription::class;

    protected static ?string $slug = 'license-subscriptions';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $cluster = Licensing::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('license_id')->relationship('license', 'serial_number')->searchable()->preload()->required(),
            Select::make('service_type')
                ->options(collect(ServiceType::cases())->mapWithKeys(fn (ServiceType $case): array => [$case->value => ucfirst(str_replace('_', ' ', $case->value))])->all())
                ->required(),
            DatePicker::make('start_date')->native(false),
            DatePicker::make('end_date')->native(false),
            Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('license.serial_number')->label('License')->searchable(),
            TextColumn::make('service_type')->badge(),
            TextColumn::make('start_date')->date(),
            TextColumn::make('end_date')->date(),
            IconColumn::make('is_active')->boolean(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLicenseSubscriptions::route('/'),
        ];
    }
}
