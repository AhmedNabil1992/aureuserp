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
use Webkul\Product\Models\Product;
use Webkul\Software\Enums\ServiceType;
use Webkul\Software\Filament\Admin\Clusters\Catalog;
use Webkul\Software\Filament\Admin\Resources\ProgramFeatureResource\Pages\ManageProgramFeatures;
use Webkul\Software\Models\ProgramFeature;

class ProgramFeatureResource extends Resource
{
    protected static ?string $model = ProgramFeature::class;

    protected static ?string $slug = 'program-features';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Catalog::class;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.software');
    }

    public static function getNavigationLabel(): string
    {
        return __('software::filament/admin/resources/program-feature.navigation.label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('program_id')->relationship('program', 'name')->searchable()->preload()->required(),
            TextInput::make('name')->required()->maxLength(255),
            Select::make('service_type')
                ->label(__('software::filament/admin/resources/program-feature.form.fields.subscription_type'))
                ->options(collect(ServiceType::cases())->mapWithKeys(fn (ServiceType $case): array => [
                    $case->value => ucfirst(str_replace('_', ' ', $case->value)),
                ])->all())
                ->nullable()
                ->helperText(__('software::filament/admin/resources/program-feature.form.helper_text.subscription_type')),
            Select::make('product_id')
                ->label(__('software::filament/admin/resources/program-feature.form.fields.service_product'))
                ->options(fn (): array => Product::query()
                    ->where('type', 'service')
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText(__('software::filament/admin/resources/program-feature.form.helper_text.service_product')),
            TextInput::make('amount')->numeric(),
            Textarea::make('description')->rows(3)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('program.name')->label(__('software::filament/admin/resources/program-feature.table.columns.program'))->searchable(),
            TextColumn::make('name')->searchable(),
            TextColumn::make('service_type')->badge()->label(__('software::filament/admin/resources/program-feature.table.columns.subscription_type')),
            TextColumn::make('product.name')->label(__('software::filament/admin/resources/program-feature.table.columns.service_product'))->searchable(),
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
