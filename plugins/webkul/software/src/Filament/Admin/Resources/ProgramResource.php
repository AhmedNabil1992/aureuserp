<?php

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Webkul\Product\Models\Product;
use Webkul\Software\Filament\Admin\Clusters\Catalog;
use Webkul\Software\Filament\Admin\Resources\ProgramResource\Pages\ManagePrograms;
use Webkul\Software\Models\Program;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $slug = 'programs';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $cluster = Catalog::class;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.software');
    }

    public static function getNavigationLabel(): string
    {
        return __('software::filament/admin/resources/program.navigation.label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('software::filament/admin/resources/program.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set): void {
                        if (filled($state)) {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')
                    ->label(__('software::filament/admin/resources/program.form.fields.slug'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('product_id')
                    ->label(__('software::filament/admin/resources/program.form.fields.base_service_product'))
                    ->options(fn (): array => Product::query()
                        ->where('type', 'service')
                        ->whereNull('parent_id')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText(__('software::filament/admin/resources/program.form.helper_text.base_service_product')),
                Textarea::make('description')
                    ->label(__('software::filament/admin/resources/program.form.fields.description'))
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('installation_notes')
                    ->label(__('software::filament/admin/resources/program.form.fields.installation_notes'))
                    ->rows(4)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(__('software::filament/admin/resources/program.form.fields.active'))
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('software::filament/admin/resources/program.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('software::filament/admin/resources/program.table.columns.description'))
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('software::filament/admin/resources/program.table.columns.slug'))
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label(__('software::filament/admin/resources/program.table.columns.base_product'))
                    ->searchable(),
                TextColumn::make('installation_notes')
                    ->label(__('software::filament/admin/resources/program.table.columns.installation_notes'))
                    ->limit(50),
                TextColumn::make('creator.name')
                    ->label(__('software::filament/admin/resources/program.table.columns.creator'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('software::filament/admin/resources/program.table.columns.created_at'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('software::filament/admin/resources/program.table.columns.active'))
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(__('software::filament/admin/resources/program.table.columns.updated_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePrograms::route('/'),
        ];
    }
}
