<?php

namespace Webkul\Software\Filament\Customer\Clusters\Account\Resources\TicketResource\Pages;

use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Webkul\Software\Enums\TicketPriority;
use Webkul\Software\Enums\TicketStatus;
use Webkul\Software\Filament\Customer\Clusters\Account\Resources\TicketResource;
use Webkul\Software\Livewire\TicketConversationPanel;
use Webkul\Software\Models\Ticket;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Group::make()
                ->schema([
                    Section::make()
                        ->schema([
                            TextEntry::make('ticket_number')
                                ->label('Ticket #')
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

                            TextEntry::make('license.serial_number')
                                ->label('License'),

                            TextEntry::make('program.name')
                                ->label('Product'),

                            TextEntry::make('created_at')
                                ->label('Opened')
                                ->dateTime(),
                        ])
                        ->columns(3),

                    Section::make('Conversation')
                        ->schema([
                            Livewire::make(TicketConversationPanel::class, fn (Ticket $record): array => [
                                'ticket'     => $record,
                                'senderType' => 'customer',
                                'canReply'   => true,
                            ]),
                        ])
                        ->collapsible(false),
                ])
                ->columnSpanFull(),
        ]);
    }
}
