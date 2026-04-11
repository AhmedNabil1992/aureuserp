<?php

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Software\Filament\Admin\Clusters\Licensing;
use Webkul\Software\Filament\Admin\Resources\LicenseDeviceResource\Pages\ManageLicenseDevices;
use Webkul\Software\Models\LicenseDevice;

class LicenseDeviceResource extends Resource
{
    protected static ?string $model = LicenseDevice::class;

    protected static ?string $slug = 'license-devices';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $cluster = Licensing::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('license_id')->relationship('license', 'serial_number')->searchable()->preload()->required(),
            TextInput::make('computer_id')->required()->maxLength(255),
            TextInput::make('license_key')->maxLength(255),
            TextInput::make('device_name')->maxLength(255),
            TextInput::make('bios_id')->maxLength(255),
            TextInput::make('disk_id')->maxLength(255),
            TextInput::make('base_id')->maxLength(255),
            TextInput::make('video_id')->maxLength(255),
            TextInput::make('mac_id')->maxLength(255),
            Toggle::make('is_primary')->default(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('license.serial_number')->label('License')->searchable(),
            TextColumn::make('computer_id')->searchable(),
            TextColumn::make('device_name')->searchable(),
            IconColumn::make('is_primary')->boolean(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
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
            'index' => ManageLicenseDevices::route('/'),
        ];
    }
}
