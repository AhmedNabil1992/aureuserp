<?php

namespace Webkul\Marketing\Filament\Resources\CampaignResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Lead\Enums\LeadStatus;
use Webkul\Lead\Enums\LeadTemperature;
use Webkul\Lead\Filament\Resources\LeadResource;

class LeadsRelationManager extends RelationManager
{
    protected static string $relationship = 'leads';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('marketing::filament/resources/campaign/relation-managers/leads.title');
    }

    public function form(Schema $schema): Schema
    {
        return LeadResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/leads.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/leads.columns.phone'))
                    ->searchable(),
                TextColumn::make('service.name')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/leads.columns.service-type'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/leads.columns.status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('temperature')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/leads.columns.temperature'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/leads.columns.assigned-to'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/leads.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/leads.filters.status'))
                    ->options(LeadStatus::class),
                SelectFilter::make('temperature')
                    ->label(__('marketing::filament/resources/campaign/relation-managers/leads.filters.temperature'))
                    ->options(LeadTemperature::class),
            ])
            ->recordActions([
                Action::make('changeStatus')
                    ->label('Change Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options(LeadStatus::class)
                            ->required()
                            ->default(fn ($record) => $record->status?->value),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update(['status' => $data['status']]);

                        Notification::make()
                            ->success()
                            ->title('Status updated successfully.')
                            ->send();
                    }),
                ViewAction::make()
                    ->url(fn ($record) => LeadResource::getUrl('view', ['record' => $record])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
