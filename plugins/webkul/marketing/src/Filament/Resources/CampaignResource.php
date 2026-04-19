<?php

namespace Webkul\Marketing\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Marketing\Enums\AdPlatform;
use Webkul\Marketing\Enums\CampaignStatus;
use Webkul\Marketing\Filament\Resources\CampaignResource\Pages\CreateCampaign;
use Webkul\Marketing\Filament\Resources\CampaignResource\Pages\EditCampaign;
use Webkul\Marketing\Filament\Resources\CampaignResource\Pages\ListCampaigns;
use Webkul\Marketing\Filament\Resources\CampaignResource\Pages\ViewCampaign;
use Webkul\Marketing\Filament\Resources\CampaignResource\RelationManagers\AdPlanRelationManager;
use Webkul\Marketing\Filament\Resources\CampaignResource\RelationManagers\LeadsRelationManager;
use Webkul\Marketing\Models\Campaign;
use Webkul\Security\Models\User;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $slug = 'marketing/campaigns';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('marketing::filament/resources/campaign.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('marketing::filament/resources/campaign.navigation.group');
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-megaphone';
    }

    public static function getModelLabel(): string
    {
        return __('marketing::filament/resources/campaign.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('marketing::filament/resources/campaign.plural-model-label');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('marketing::filament/resources/campaign.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('marketing::filament/resources/campaign.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255),
                                Select::make('platform')
                                    ->label(__('marketing::filament/resources/campaign.form.sections.general.fields.platform'))
                                    ->options(AdPlatform::class)
                                    ->required(),
                                DatePicker::make('month')
                                    ->label(__('marketing::filament/resources/campaign.form.sections.general.fields.month'))
                                    ->required()
                                    ->displayFormat('M Y')
                                    ->format('Y-m-01'),
                                Textarea::make('description')
                                    ->label(__('marketing::filament/resources/campaign.form.sections.general.fields.description'))
                                    ->nullable()
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('marketing::filament/resources/campaign.form.sections.settings.title'))
                            ->schema([
                                Select::make('status')
                                    ->label(__('marketing::filament/resources/campaign.form.sections.settings.fields.status'))
                                    ->options(CampaignStatus::class)
                                    ->required(),
                                Select::make('assigned_to')
                                    ->label(__('marketing::filament/resources/campaign.form.sections.settings.fields.assigned-to'))
                                    ->options(User::query()->pluck('name', 'id'))
                                    ->searchable()
                                    ->nullable(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('marketing::filament/resources/campaign.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('platform')
                    ->label(__('marketing::filament/resources/campaign.table.columns.platform'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('month')
                    ->label(__('marketing::filament/resources/campaign.table.columns.month'))
                    ->date('M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('marketing::filament/resources/campaign.table.columns.status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('adPlan.planned_budget')
                    ->label(__('marketing::filament/resources/campaign.table.columns.planned-budget'))
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('adPlan.actual_budget')
                    ->label(__('marketing::filament/resources/campaign.table.columns.actual-budget'))
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('adPlan.planned_messages')
                    ->label(__('marketing::filament/resources/campaign.table.columns.planned-messages'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('adPlan.actual_messages')
                    ->label(__('marketing::filament/resources/campaign.table.columns.actual-messages'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('adPlan.actual_leads')
                    ->label(__('marketing::filament/resources/campaign.table.columns.actual-leads'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('leads_count')
                    ->label(__('marketing::filament/resources/campaign.table.columns.leads-count'))
                    ->counts('leads')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->toggleable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('marketing::filament/resources/campaign.table.columns.assigned-to'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('marketing::filament/resources/campaign.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('platform')
                    ->label(__('marketing::filament/resources/campaign.table.filters.platform'))
                    ->options(AdPlatform::class),
                SelectFilter::make('status')
                    ->label(__('marketing::filament/resources/campaign.table.filters.status'))
                    ->options(CampaignStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    RestoreAction::make(),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->withoutGlobalScope(SoftDeletingScope::class);
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('marketing::filament/resources/campaign.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.general.entries.name')),
                                TextEntry::make('platform')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.general.entries.platform'))
                                    ->badge(),
                                TextEntry::make('month')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.general.entries.month'))
                                    ->date('F Y'),
                                TextEntry::make('description')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.general.entries.description'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make(__('marketing::filament/resources/campaign.infolist.sections.plan.title'))
                            ->schema([
                                TextEntry::make('adPlan.planned_budget')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.plan.entries.planned-budget'))
                                    ->money('EGP'),
                                TextEntry::make('adPlan.actual_budget')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.plan.entries.actual-budget'))
                                    ->money('EGP'),
                                TextEntry::make('adPlan.planned_reach')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.plan.entries.planned-reach'))
                                    ->numeric(),
                                TextEntry::make('adPlan.actual_reach')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.plan.entries.actual-reach'))
                                    ->numeric(),
                                TextEntry::make('adPlan.planned_messages')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.plan.entries.planned-messages'))
                                    ->numeric(),
                                TextEntry::make('adPlan.actual_messages')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.plan.entries.actual-messages'))
                                    ->numeric(),
                                TextEntry::make('adPlan.planned_conversions')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.plan.entries.planned-conversions'))
                                    ->numeric(),
                                TextEntry::make('adPlan.actual_conversions')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.plan.entries.actual-conversions'))
                                    ->numeric(),
                                TextEntry::make('adPlan.actual_leads')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.plan.entries.actual-leads'))
                                    ->numeric(),
                                TextEntry::make('adPlan.notes')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.plan.entries.notes'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->visible(fn ($record) => $record->adPlan !== null),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('marketing::filament/resources/campaign.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('status')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.settings.entries.status'))
                                    ->badge(),
                                TextEntry::make('assignedUser.name')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.settings.entries.assigned-to')),
                                TextEntry::make('creator.name')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.settings.entries.creator')),
                                TextEntry::make('created_at')
                                    ->label(__('marketing::filament/resources/campaign.infolist.sections.settings.entries.created-at'))
                                    ->dateTime(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getRelationManagers(): array
    {
        return [
            AdPlanRelationManager::class,
            LeadsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCampaigns::route('/'),
            'create' => CreateCampaign::route('/create'),
            'view'   => ViewCampaign::route('/{record}'),
            'edit'   => EditCampaign::route('/{record}/edit'),
        ];
    }
}
