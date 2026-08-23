<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource\Pages;

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
use Webkul\TechnicalSupport\Enums\ServiceType;
use Webkul\TechnicalSupport\Enums\TicketPriority;
use Webkul\TechnicalSupport\Enums\TicketStatus;
use Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource;
use Webkul\TechnicalSupport\Livewire\OpenTicketsSidebar;
use Webkul\TechnicalSupport\Livewire\TicketConversationPanel;
use Webkul\TechnicalSupport\Models\Ticket;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->is_unread_admin) {
            $this->record->update(['is_unread_admin' => false]);
        }
    }

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
                            Section::make(__('technical-support::filament/admin/resources/ticket.infolist.sections.details.title'))
                                ->schema([
                                    TextEntry::make('ticket_number')
                                        ->label(__('technical-support::filament/admin/resources/ticket.form.fields.ticket_number'))
                                        ->size(TextSize::Large)
                                        ->weight(FontWeight::Bold)
                                        ->formatStateUsing(fn (int $state): string => '#'.$state),

                                    TextEntry::make('service_type')
                                        ->label(__('technical-support::filament/admin/resources/ticket.form.fields.service_type'))
                                        ->badge(),

                                    TextEntry::make('status')
                                        ->label(__('technical-support::filament/admin/resources/ticket.form.fields.status'))
                                        ->badge(),

                                    TextEntry::make('priority')
                                        ->label(__('technical-support::filament/admin/resources/ticket.form.fields.priority'))
                                        ->badge(),

                                    TextEntry::make('title')
                                        ->label(__('technical-support::filament/admin/resources/ticket.form.fields.title'))
                                        ->columnSpanFull()
                                        ->weight(FontWeight::SemiBold),

                                    TextEntry::make('partner.name')
                                        ->label(__('technical-support::filament/admin/resources/ticket.form.fields.customer')),

                                    TextEntry::make('service_label')
                                        ->label(__('technical-support::filament/admin/resources/ticket.form.fields.service_details')),

                                    TextEntry::make('assignedTo.name')
                                        ->label(__('technical-support::filament/admin/resources/ticket.form.fields.assign_to'))
                                        ->placeholder(__('technical-support::filament/admin/resources/ticket.form.placeholders.unassigned')),

                                    TextEntry::make('created_at')
                                        ->label(__('technical-support::filament/admin/resources/ticket.form.fields.opened_at'))
                                        ->dateTime(),

                                    TextEntry::make('updated_at')
                                        ->label(__('technical-support::filament/admin/resources/ticket.table.columns.last_update'))
                                        ->dateTime(),
                                ])
                                ->columns(3)
                                ->collapsible()
                                ->collapsed(true),

                            Section::make(__('technical-support::filament/admin/resources/ticket.infolist.sections.conversation.title'))
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
