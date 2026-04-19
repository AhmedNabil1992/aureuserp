<?php

namespace Webkul\Marketing\Filament\Resources\CampaignResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AdPlanRelationManager extends RelationManager
{
    protected static string $relationship = 'adPlan';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('marketing::filament/resources/campaign/relation-managers/ad-plan.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('marketing::filament/resources/campaign/relation-managers/ad-plan.form.sections.planned.title'))
                    ->schema([
                        TextInput::make('planned_budget')
                            ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.form.fields.planned-budget'))
                            ->numeric()
                            ->prefix('EGP')
                            ->required(),
                        TextInput::make('planned_reach')
                            ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.form.fields.planned-reach'))
                            ->numeric()
                            ->required(),
                        TextInput::make('planned_messages')
                            ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.form.fields.planned-messages'))
                            ->numeric()
                            ->required(),
                        TextInput::make('planned_conversions')
                            ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.form.fields.planned-conversions'))
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make(__('marketing::filament/resources/campaign/relation-managers/ad-plan.form.sections.actual.title'))
                    ->schema([
                        TextInput::make('actual_budget')
                            ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.form.fields.actual-budget'))
                            ->numeric()
                            ->prefix('EGP')
                            ->nullable(),
                        TextInput::make('actual_reach')
                            ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.form.fields.actual-reach'))
                            ->numeric()
                            ->nullable(),
                        TextInput::make('actual_messages')
                            ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.form.fields.actual-messages'))
                            ->numeric()
                            ->nullable(),
                        TextInput::make('actual_conversions')
                            ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.form.fields.actual-conversions'))
                            ->numeric()
                            ->nullable(),
                        TextInput::make('actual_leads')
                            ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.form.fields.actual-leads'))
                            ->numeric()
                            ->nullable(),
                    ])
                    ->columns(2),

                Section::make()
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.form.fields.notes'))
                            ->nullable()
                            ->rows(3),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('planned_budget')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.table.columns.planned-budget'))
                    ->money('EGP'),
                TextColumn::make('actual_budget')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.table.columns.actual-budget'))
                    ->money('EGP'),
                TextColumn::make('planned_messages')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.table.columns.planned-messages'))
                    ->numeric(),
                TextColumn::make('actual_messages')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.table.columns.actual-messages'))
                    ->numeric(),
                TextColumn::make('planned_conversions')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.table.columns.planned-conversions'))
                    ->numeric(),
                TextColumn::make('actual_conversions')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.table.columns.actual-conversions'))
                    ->numeric(),
                TextColumn::make('actual_leads')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/ad-plan.table.columns.actual-leads'))
                    ->numeric(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => ! $this->getOwnerRecord()->adPlan()->exists()),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
