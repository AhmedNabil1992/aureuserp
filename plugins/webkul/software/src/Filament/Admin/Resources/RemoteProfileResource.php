<?php

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Software\Filament\Admin\Clusters\Licensing;
use Webkul\Software\Filament\Admin\Resources\RemoteProfileResource\Pages\ManageRemoteProfiles;
use Webkul\Software\Models\RemoteProfile;

class RemoteProfileResource extends Resource
{
    protected static ?string $model = RemoteProfile::class;

    protected static ?string $slug = 'remote-profiles';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wifi';

    protected static ?string $cluster = Licensing::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('license_id')->relationship('license', 'serial_number')->searchable()->preload()->required(),
            TextInput::make('anydesk')->maxLength(50),
            TextInput::make('teamviewer')->maxLength(50),
            TextInput::make('rustdesk')->maxLength(50),
            Textarea::make('remark')->rows(3)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('license.serial_number')->label('License')->searchable(),
            TextColumn::make('anydesk'),
            TextColumn::make('teamviewer'),
            TextColumn::make('rustdesk'),
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
            'index' => ManageRemoteProfiles::route('/'),
        ];
    }
}
