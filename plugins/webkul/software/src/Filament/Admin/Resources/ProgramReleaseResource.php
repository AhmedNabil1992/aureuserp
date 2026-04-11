<?php

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Software\Filament\Admin\Clusters\Catalog;
use Webkul\Software\Filament\Admin\Resources\ProgramReleaseResource\Pages\ManageProgramReleases;
use Webkul\Software\Models\ProgramRelease;

class ProgramReleaseResource extends Resource
{
    protected static ?string $model = ProgramRelease::class;

    protected static ?string $slug = 'program-releases';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $cluster = Catalog::class;

    public static function getNavigationGroup(): string
    {
        return 'Software';
    }

    public static function getNavigationLabel(): string
    {
        return 'Program Releases';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('program_id')->relationship('program', 'name')->searchable()->preload()->required(),
            TextInput::make('version_number')->required()->maxLength(50),
            TextInput::make('update_link')->url()->maxLength(500),
            TextInput::make('file_name')->maxLength(255),
            DatePicker::make('release_date')->native(false),
            Toggle::make('is_db_update')->default(false),
            TextInput::make('db_link')->maxLength(500),
            Toggle::make('is_active')->default(true),
            Textarea::make('app_terminate')->rows(2)->columnSpanFull(),
            Textarea::make('remark')->rows(2)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('program.name')->label('Program')->searchable(),
            TextColumn::make('version_number')->searchable()->sortable(),
            TextColumn::make('release_date')->date()->sortable(),
            IconColumn::make('is_active')->boolean(),
            IconColumn::make('is_db_update')->boolean(),
            TextColumn::make('download_times')->numeric(),
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
            'index' => ManageProgramReleases::route('/'),
        ];
    }
}
