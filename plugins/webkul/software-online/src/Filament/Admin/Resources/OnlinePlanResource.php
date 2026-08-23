<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlinePlanResource\Pages\CreateOnlinePlan;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlinePlanResource\Pages\EditOnlinePlan;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlinePlanResource\Pages\ListOnlinePlans;
use Webkul\SoftwareOnline\Models\OnlineSystemPlan;

class OnlinePlanResource extends Resource
{
    protected static ?string $model = OnlineSystemPlan::class;

    protected static ?string $slug = 'online-plans';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return \Webkul\Support\Enums\NavigationGroup::SoftwareOnline;
    }

    public static function getNavigationLabel(): string
    {
        return __('software-online::filament/admin/resources/plan.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('software-online::filament/admin/resources/plan.models.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('software-online::filament/admin/resources/plan.models.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('software-online::filament/admin/resources/plan.sections.general'))
                ->schema([
                    Select::make('system_id')
                        ->label(__('software-online::filament/admin/resources/plan.fields.system'))
                        ->relationship('system', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Select::make('product_id')
                        ->label(__('software-online::filament/admin/resources/plan.fields.product'))
                        ->relationship('product', 'name', fn ($query) => $query->where('type', 'service'))
                        ->searchable()
                        ->preload()
                        ->helperText(__('software-online::filament/admin/resources/plan.fields.product_helper')),
                    TextInput::make('name')
                        ->label(__('software-online::filament/admin/resources/plan.fields.name'))
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')
                        ->label(__('software-online::filament/admin/resources/plan.fields.slug'))
                        ->required(),
                    Textarea::make('description')
                        ->label(__('software-online::filament/admin/resources/plan.fields.description'))
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make(__('software-online::filament/admin/resources/plan.sections.pricing'))
                ->schema([
                    TextInput::make('monthly_price')
                        ->label(__('software-online::filament/admin/resources/plan.fields.monthly_price'))
                        ->numeric()
                        ->prefix('EGP')
                        ->required()
                        ->default(0.00),
                    TextInput::make('annual_price')
                        ->label(__('software-online::filament/admin/resources/plan.fields.annual_price'))
                        ->numeric()
                        ->prefix('EGP')
                        ->required()
                        ->default(0.00),
                    TextInput::make('trial_days')
                        ->label(__('software-online::filament/admin/resources/plan.fields.trial_days'))
                        ->numeric()
                        ->default(0),
                    TextInput::make('max_users')
                        ->label(__('software-online::filament/admin/resources/plan.fields.max_users'))
                        ->numeric()
                        ->placeholder(__('software-online::filament/admin/resources/plan.placeholders.unlimited')),
                    TextInput::make('max_branches')
                        ->label(__('software-online::filament/admin/resources/plan.fields.max_branches'))
                        ->numeric()
                        ->placeholder(__('software-online::filament/admin/resources/plan.placeholders.unlimited')),
                    Toggle::make('is_active')
                        ->label(__('software-online::filament/admin/resources/plan.fields.is_active'))
                        ->default(true),
                ])->columns(3),

            Section::make(__('software-online::filament/admin/resources/plan.sections.features'))
                ->schema([
                    Repeater::make('features')
                        ->label(__('software-online::filament/admin/resources/plan.fields.features_list'))
                        ->simple(
                            TextInput::make('feature')
                                ->placeholder(__('software-online::filament/admin/resources/plan.placeholders.feature_example'))
                                ->required()
                        )
                        ->columnSpanFull(),
                    KeyValue::make('custom_api_payload')
                        ->label(__('software-online::filament/admin/resources/plan.fields.custom_api_payload'))
                        ->helperText(__('software-online::filament/admin/resources/plan.fields.custom_api_payload_helper'))
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('system.name')
                    ->label(__('software-online::filament/admin/resources/plan.fields.system'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('name')
                    ->label(__('software-online::filament/admin/resources/plan.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('monthly_price')
                    ->label(__('software-online::filament/admin/resources/plan.fields.monthly_price'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('annual_price')
                    ->label(__('software-online::filament/admin/resources/plan.fields.annual_price'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('instances_count')
                    ->label(__('software-online::filament/admin/resources/plan.fields.instances_count'))
                    ->counts('instances')
                    ->badge()
                    ->color('success'),
                IconColumn::make('is_active')
                    ->label(__('software-online::filament/admin/resources/plan.fields.is_active'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('system_id')
                    ->label(__('software-online::filament/admin/resources/plan.fields.system'))
                    ->relationship('system', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOnlinePlans::route('/'),
            'create' => CreateOnlinePlan::route('/create'),
            'edit'   => EditOnlinePlan::route('/{record}/edit'),
        ];
    }
}
