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

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Catalog::class;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.software');
    }

    public static function getNavigationLabel(): string
    {
        return __('software::filament/admin/resources/program-release.navigation.label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('program_id')->label(__('software::filament/admin/resources/program-release.form.fields.program'))->relationship('program', 'name')->searchable()->preload()->required(),
            TextInput::make('version_number')->label(__('software::filament/admin/resources/program-release.form.fields.version_number'))->required()->maxLength(50),
            TextInput::make('update_link')->label(__('software::filament/admin/resources/program-release.form.fields.update_link'))->url()->maxLength(500),
            TextInput::make('file_name')->label(__('software::filament/admin/resources/program-release.form.fields.file_name'))->maxLength(255),
            DatePicker::make('release_date')->label(__('software::filament/admin/resources/program-release.form.fields.release_date'))->native(false),
            Toggle::make('is_db_update')->label(__('software::filament/admin/resources/program-release.form.fields.is_db_update'))->default(false),
            TextInput::make('db_link')->label(__('software::filament/admin/resources/program-release.form.fields.db_link'))->maxLength(500),
            Toggle::make('is_active')->label(__('software::filament/admin/resources/program-release.form.fields.is_active'))->default(true),
            Textarea::make('app_terminate')->label(__('software::filament/admin/resources/program-release.form.fields.app_terminate'))->rows(2)->columnSpanFull(),
            Textarea::make('remark')->label(__('software::filament/admin/resources/program-release.form.fields.remark'))->rows(2)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('program.name')->label(__('software::filament/admin/resources/program-release.table.columns.program'))->searchable(),
            TextColumn::make('version_number')->label(__('software::filament/admin/resources/program-release.table.columns.version_number'))->searchable()->sortable(),
            TextColumn::make('release_date')->label(__('software::filament/admin/resources/program-release.table.columns.release_date'))->date()->sortable(),
            IconColumn::make('is_active')->label(__('software::filament/admin/resources/program-release.table.columns.is_active'))->boolean(),
            IconColumn::make('is_db_update')->label(__('software::filament/admin/resources/program-release.table.columns.is_db_update'))->boolean(),
            TextColumn::make('download_times')->label(__('software::filament/admin/resources/program-release.table.columns.download_times'))->numeric(),
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
