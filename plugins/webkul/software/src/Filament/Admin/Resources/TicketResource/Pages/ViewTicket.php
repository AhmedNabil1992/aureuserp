<?php

namespace Webkul\Software\Filament\Admin\Resources\TicketResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Software\Enums\TicketPriority;
use Webkul\Software\Enums\TicketStatus;
use Webkul\Software\Filament\Admin\Resources\TicketResource;
use Webkul\Software\Livewire\OpenTicketsSidebar;
use Webkul\Software\Livewire\TicketConversationPanel;
use Webkul\Software\Models\Ticket;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(4)
                ->schema([
                    // ── LEFT SIDEBAR: Active Tickets Navigator ──────────────
                    Section::make()
                        ->schema([
                            Livewire::make(OpenTicketsSidebar::class, fn (Ticket $record): array => [
                                'currentTicketId' => $record->id,
                            ])->key('open-tickets-sidebar'),
                        ])
                        ->columnSpan(1)
                        ->extraAttributes([
                            'style' => 'position: sticky; top: 1.5rem; align-self: start; overflow-y: auto; max-height: calc(100vh - 8rem);',
                        ]),

                    // ── MAIN CONTENT ─────────────────────────────────────────
                    Group::make()
                        ->schema([
                            Section::make('Ticket Details')
                                ->schema([
                                    TextEntry::make('ticket_number')
                                        ->label('Ticket #')
                                        ->size(TextSize::Large)
                                        ->weight(FontWeight::Bold)
                                        ->formatStateUsing(fn (int $state): string => '#'.$state),

                                    TextEntry::make('status')
                                        ->badge()
                                        ->color(fn (TicketStatus $state): string => match ($state) {
                                            TicketStatus::Open    => 'success',
                                            TicketStatus::Pending => 'warning',
                                            TicketStatus::Closed  => 'danger',
                                        }),

                                    TextEntry::make('priority')
                                        ->badge()
                                        ->color(fn (TicketPriority $state): string => match ($state) {
                                            TicketPriority::Low    => 'gray',
                                            TicketPriority::Normal => 'info',
                                            TicketPriority::High   => 'warning',
                                            TicketPriority::Urgent => 'danger',
                                        }),

                                    TextEntry::make('title')
                                        ->columnSpanFull()
                                        ->weight(FontWeight::SemiBold),

                                    TextEntry::make('partner.name')
                                        ->label('Customer'),

                                    TextEntry::make('license.serial_number')
                                        ->label('License'),

                                    TextEntry::make('program.name')
                                        ->label('Program'),

                                    TextEntry::make('assignedTo.name')
                                        ->label('Assigned To')
                                        ->placeholder('Unassigned'),

                                    TextEntry::make('created_at')
                                        ->label('Opened')
                                        ->dateTime(),

                                    TextEntry::make('updated_at')
                                        ->label('Last Updated')
                                        ->dateTime(),
                                ])
                                ->columns(3),

                            Section::make('Conversation')
                                ->schema([
                                    Livewire::make(TicketConversationPanel::class, fn (Ticket $record): array => [
                                        'ticket'     => $record,
                                        'senderType' => 'admin',
                                        'canReply'   => true,
                                    ]),
                                ])
                                ->collapsible(false),
                        ])
                        ->columnSpan(3),
                ])
                ->columnSpanFull(),
        ]);
    }
}
