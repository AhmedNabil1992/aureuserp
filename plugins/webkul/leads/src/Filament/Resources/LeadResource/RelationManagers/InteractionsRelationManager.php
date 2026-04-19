<?php

namespace Webkul\Lead\Filament\Resources\LeadResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Lead\Enums\InteractionType;

class InteractionsRelationManager extends RelationManager
{
    protected static string $relationship = 'interactions';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('leads::filament/resources/lead/relation-managers/interactions.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('type')
                            ->label(__('leads::filament/resources/lead/relation-managers/interactions.form.fields.type'))
                            ->options(InteractionType::class)
                            ->required(),
                        TextInput::make('subject')
                            ->label(__('leads::filament/resources/lead/relation-managers/interactions.form.fields.subject'))
                            ->nullable()
                            ->maxLength(255),
                        DateTimePicker::make('interaction_date')
                            ->label(__('leads::filament/resources/lead/relation-managers/interactions.form.fields.interaction-date'))
                            ->required()
                            ->default(now()),
                        Textarea::make('notes')
                            ->label(__('leads::filament/resources/lead/relation-managers/interactions.form.fields.notes'))
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('outcome')
                            ->label(__('leads::filament/resources/lead/relation-managers/interactions.form.fields.outcome'))
                            ->nullable()
                            ->maxLength(255),
                        TextInput::make('next_action')
                            ->label(__('leads::filament/resources/lead/relation-managers/interactions.form.fields.next-action'))
                            ->nullable()
                            ->maxLength(255),
                        DateTimePicker::make('follow_up_date')
                            ->label(__('leads::filament/resources/lead/relation-managers/interactions.form.fields.follow-up-date'))
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label(__('leads::filament/resources/lead/relation-managers/interactions.table.columns.type'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('subject')
                    ->label(__('leads::filament/resources/lead/relation-managers/interactions.table.columns.subject'))
                    ->searchable(),
                TextColumn::make('interaction_date')
                    ->label(__('leads::filament/resources/lead/relation-managers/interactions.table.columns.interaction-date'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('outcome')
                    ->label(__('leads::filament/resources/lead/relation-managers/interactions.table.columns.outcome'))
                    ->toggleable(),
                TextColumn::make('follow_up_date')
                    ->label(__('leads::filament/resources/lead/relation-managers/interactions.table.columns.follow-up-date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label(__('leads::filament/resources/lead/relation-managers/interactions.table.columns.user'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('leads::filament/resources/lead/relation-managers/interactions.table.filters.type'))
                    ->options(InteractionType::class),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('interaction_date', 'desc');
    }
}
