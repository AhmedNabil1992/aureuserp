<?php

namespace Webkul\TechnicalSupport\Filament\Customer\Resources\TicketResource\Pages;

use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\TechnicalSupport\Enums\TicketPriority;
use Webkul\TechnicalSupport\Enums\TicketStatus;
use Webkul\TechnicalSupport\Filament\Customer\Resources\TicketResource;
use Webkul\TechnicalSupport\Livewire\TicketConversationPanel;
use Webkul\TechnicalSupport\Models\Ticket;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->is_unread_client) {
            $this->record->update(['is_unread_client' => false]);
        }
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)
                ->schema([
                    Section::make(__('technical-support::filament/customer/ticket.infolist.sections.details.title'))
                        ->schema([
                            TextEntry::make('ticket_number')
                                ->label(__('technical-support::filament/customer/ticket.table.columns.number'))
                                ->size(TextSize::Large)
                                ->weight(FontWeight::Bold)
                                ->formatStateUsing(fn (int $state): string => '#'.$state),

                            TextEntry::make('status')
                                ->label(__('technical-support::filament/customer/ticket.table.columns.status'))
                                ->badge(),

                            TextEntry::make('priority')
                                ->label(__('technical-support::filament/customer/ticket.table.columns.priority'))
                                ->badge(),

                            TextEntry::make('title')
                                ->label(__('technical-support::filament/customer/ticket.table.columns.title'))
                                ->columnSpanFull()
                                ->weight(FontWeight::SemiBold),

                            TextEntry::make('service_label')
                                ->label(__('technical-support::filament/customer/ticket.table.columns.service')),

                            TextEntry::make('created_at')
                                ->label(__('technical-support::filament/customer/ticket.table.columns.opened_at'))
                                ->dateTime(),

                            TextEntry::make('updated_at')
                                ->label(__('technical-support::filament/customer/ticket.table.columns.last_update'))
                                ->dateTime(),
                        ])
                        ->columns(3)
                        ->columnSpanFull()
                        ->collapsible()
                        ->collapsed(true),

                    Section::make(__('technical-support::filament/customer/ticket.infolist.sections.conversation.title'))
                        ->schema([
                            Livewire::make(TicketConversationPanel::class, fn (Ticket $record): array => [
                                'ticket'     => $record,
                                'senderType' => 'customer',
                                'canReply'   => true,
                            ]),
                        ])
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
