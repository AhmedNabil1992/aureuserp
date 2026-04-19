<?php

namespace Webkul\Lead\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Webkul\Lead\Models\Lead;

class PendingFollowUpsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('leads::filament/widgets/pending-follow-ups.heading');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::query()->pendingFollowUp()
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('leads::filament/widgets/pending-follow-ups.columns.name'))
                    ->searchable()
                    ->url(fn ($record) => route('filament.admin.resources.leads.leads.view', $record)),
                TextColumn::make('phone')
                    ->label(__('leads::filament/widgets/pending-follow-ups.columns.phone')),
                TextColumn::make('status')
                    ->label(__('leads::filament/widgets/pending-follow-ups.columns.status'))
                    ->badge(),
                TextColumn::make('interactions_max_follow_up')
                    ->label(__('leads::filament/widgets/pending-follow-ups.columns.follow-up-date'))
                    ->getStateUsing(fn ($record) => $record->interactions()
                        ->whereDate('follow_up_date', today())
                        ->value('follow_up_date'))
                    ->dateTime(),
                TextColumn::make('assignedUser.name')
                    ->label(__('leads::filament/widgets/pending-follow-ups.columns.assigned-to')),
            ]);
    }
}
