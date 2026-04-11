<?php

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Software\Filament\Admin\Clusters\Licensing;
use Webkul\Software\Filament\Admin\Resources\LicenseActivityResource\Pages\ManageLicenseActivities;
use Webkul\Software\Models\LicenseActivity;

class LicenseActivityResource extends Resource
{
    protected static ?string $model = LicenseActivity::class;

    protected static ?string $slug = 'license-activities';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-signal';

    protected static ?string $cluster = Licensing::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('license_id')->relationship('license', 'serial_number')->searchable()->preload()->required(),
            TextInput::make('current_version')->maxLength(50)->required(),
            DateTimePicker::make('last_online_at')->required(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('license.serial_number')->label('License')->searchable(),
            TextColumn::make('current_version')->searchable(),
            TextColumn::make('last_online_at')->dateTime()->sortable(),
        ])->recordActions([
            DeleteAction::make(),
        ])->toolbarActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLicenseActivities::route('/'),
        ];
    }
}
