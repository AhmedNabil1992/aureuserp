<?php

declare(strict_types=1);

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Software\Filament\Admin\Clusters\Catalog;
use Webkul\Software\Filament\Admin\Resources\FirmwareResource\Pages\ManageFirmwares;
use Webkul\Software\Models\Firmware;

class FirmwareResource extends Resource
{
    protected static ?string $model = Firmware::class;

    protected static ?string $slug = 'iot-firmwares';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = Catalog::class;

    public static function getNavigationLabel(): string
    {
        return __('software::filament/admin/resources/firmware.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('software::filament/admin/resources/firmware.navigation.singular');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('device_model')
                ->label(__('software::filament/admin/resources/firmware.form.fields.device_model'))
                ->helperText(__('software::filament/admin/resources/firmware.form.fields.device_model_helper'))
                ->required()
                ->alphaDash()
                ->maxLength(100)
                ->unique(ignoreRecord: true),
            TextInput::make('version')
                ->label(__('software::filament/admin/resources/firmware.form.fields.version'))
                ->helperText(__('software::filament/admin/resources/firmware.form.fields.version_helper'))
                ->required()
                ->maxLength(50),
            FileUpload::make('file_path')
                ->label(__('software::filament/admin/resources/firmware.form.fields.file'))
                ->helperText(__('software::filament/admin/resources/firmware.form.fields.file_helper'))
                ->disk('local')
                ->directory('iot-firmware')
                ->visibility('private')
                ->acceptedFileTypes(['application/octet-stream', 'application/macbinary'])
                ->rules(['extensions:bin'])
                ->storeFileNamesIn('original_file_name')
                ->required()
                ->columnSpanFull(),
            Toggle::make('is_active')
                ->label(__('software::filament/admin/resources/firmware.form.fields.is_active'))
                ->default(true),
            Textarea::make('notes')
                ->label(__('software::filament/admin/resources/firmware.form.fields.notes'))
                ->rows(3)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('device_model')->label(__('software::filament/admin/resources/firmware.table.columns.device_model'))->searchable()->sortable(),
            TextColumn::make('version')->label(__('software::filament/admin/resources/firmware.table.columns.version'))->searchable()->sortable(),
            TextColumn::make('original_file_name')->label(__('software::filament/admin/resources/firmware.table.columns.file')),
            TextColumn::make('file_size')->label(__('software::filament/admin/resources/firmware.table.columns.file_size'))->formatStateUsing(fn (int $state): string => number_format($state / 1024, 1).' KB'),
            IconColumn::make('is_active')->label(__('software::filament/admin/resources/firmware.table.columns.is_active'))->boolean(),
            TextColumn::make('updated_at')->label(__('software::filament/admin/resources/firmware.table.columns.updated_at'))->dateTime()->sortable(),
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
            'index' => ManageFirmwares::route('/'),
        ];
    }
}
