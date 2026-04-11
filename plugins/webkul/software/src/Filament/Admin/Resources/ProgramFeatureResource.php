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
use Webkul\Software\Filament\Admin\Clusters\Catalog;
use Webkul\Software\Filament\Admin\Resources\ProgramFeatureResource\Pages\ManageProgramFeatures;
use Webkul\Software\Models\ProgramFeature;

class ProgramFeatureResource extends Resource
{
    protected static ?string $model = ProgramFeature::class;

    protected static ?string $slug = 'program-features';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $cluster = Catalog::class;

    public static function getNavigationGroup(): string
    {
        return 'Software';
    }

    public static function getNavigationLabel(): string
    {
        return 'Program Features';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('program_id')->relationship('program', 'name')->searchable()->preload()->required(),
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('amount')->numeric(),
            Textarea::make('description')->rows(3)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('program.name')->label('Program')->searchable(),
            TextColumn::make('name')->searchable(),
            TextColumn::make('amount')->numeric(),
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
            'index' => ManageProgramFeatures::route('/'),
        ];
    }
}
