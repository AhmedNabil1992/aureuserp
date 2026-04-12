<?php

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Product\Models\Product;
use Webkul\Software\Filament\Admin\Clusters\Catalog;
use Webkul\Software\Filament\Admin\Resources\ProgramEditionResource\Pages\ManageProgramEditions;
use Webkul\Software\Models\ProgramEdition;

class ProgramEditionResource extends Resource
{
    protected static ?string $model = ProgramEdition::class;

    protected static ?string $slug = 'program-editions';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Catalog::class;

    public static function getNavigationGroup(): string
    {
        return 'Software';
    }

    public static function getNavigationLabel(): string
    {
        return 'Program Editions';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('program_id')->relationship('program', 'name')->searchable()->preload()->required(),
            TextInput::make('name')->required()->maxLength(100),
            Select::make('product_id')
                ->label('Linked Product (Service)')
                ->options(fn (): array => Product::query()
                    ->where('type', 'service')
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('Used to generate accounting invoices when billing a license.'),
            TextInput::make('max_devices')->numeric()->minValue(1),
            TextInput::make('license_cost')->numeric(),
            TextInput::make('license_price')->numeric(),
            TextInput::make('monthly_renewal')->numeric(),
            TextInput::make('annual_renewal')->numeric(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('program.name')->label('Program')->searchable(),
            TextColumn::make('name')->searchable(),
            TextColumn::make('max_devices')->numeric(),
            TextColumn::make('license_price')->money('EGP'),
            TextColumn::make('license_cost')->money('EGP'),
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
            'index' => ManageProgramEditions::route('/'),
        ];
    }
}
