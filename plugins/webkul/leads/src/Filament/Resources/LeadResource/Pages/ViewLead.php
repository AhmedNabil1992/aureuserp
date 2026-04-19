<?php

namespace Webkul\Lead\Filament\Resources\LeadResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Lead\Filament\Resources\LeadResource;
use Webkul\Security\Models\User;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource),

            Action::make('assignLead')
                ->label('Assign Lead')
                ->icon('heroicon-o-user-plus')
                ->color('warning')
                ->form([
                    Select::make('assigned_to')
                        ->label('Assign To')
                        ->options(User::query()->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->default(fn () => $this->record->assigned_to),
                ])
                ->action(function (array $data): void {
                    $previousUser = $this->record->assignedUser?->name ?? 'Unassigned';

                    $this->record->update(['assigned_to' => $data['assigned_to']]);

                    $newUser = User::find($data['assigned_to'])?->name;

                    $this->record->addMessage([
                        'type' => 'notification',
                        'body' => "Lead reassigned from **{$previousUser}** to **{$newUser}**",
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Lead assigned successfully.')
                        ->send();
                }),

            EditAction::make(),
            RestoreAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
