<?php

namespace Webkul\TechnicalSupport\Services;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource as AdminTicketResource;
use Webkul\TechnicalSupport\Filament\Customer\Resources\TicketResource as CustomerTicketResource;
use Webkul\TechnicalSupport\Models\ServiceStaffAssignment;
use Webkul\TechnicalSupport\Models\Ticket;
use Webkul\TechnicalSupport\Models\TicketEvent;

class TicketNotificationService
{
    /**
     * Get staff users assigned to handle this ticket based on service assignment settings.
     *
     * @return Collection<int, User>
     */
    public function getStaffForTicket(Ticket $ticket): Collection
    {
        $query = ServiceStaffAssignment::where('service_type', $ticket->service_type->value);

        if ($ticket->program_id) {
            $specificStaffIds = (clone $query)->where('service_reference_id', $ticket->program_id)->pluck('user_id');
            if ($specificStaffIds->isNotEmpty()) {
                return User::whereIn('id', $specificStaffIds)->get();
            }
        }

        $generalStaffIds = $query->whereNull('service_reference_id')->pluck('user_id');

        if ($generalStaffIds->isNotEmpty()) {
            return User::whereIn('id', $generalStaffIds)->get();
        }

        // If no specific staff is assigned, fallback to all users or admins
        return User::all();
    }

    /**
     * Notify assigned staff that a new ticket was created by a customer.
     */
    public function notifyStaffNewTicket(Ticket $ticket): void
    {
        $staff = $this->getStaffForTicket($ticket);

        if ($staff->isEmpty()) {
            return;
        }

        $url = route('filament.admin.resources.tickets.view', ['record' => $ticket->id]);
        $serviceName = $ticket->service_label;

        try {
            Notification::make()
                ->title("تذكرة جديدة #{$ticket->ticket_number} - {$serviceName}")
                ->body(($ticket->partner?->name ?? 'عميل') . ': ' . strip_tags(Str::limit($ticket->title, 60)))
                ->actions([
                    Action::make('view')
                        ->label('عرض التذكرة')
                        ->url($url),
                ])
                ->sendToDatabase($staff)
                ->broadcast($staff);
        } catch (\Throwable) {}
    }

    /**
     * Notify staff that customer added a reply.
     */
    public function notifyStaffNewReply(Ticket $ticket, TicketEvent $event): void
    {
        $staff = $this->getStaffForTicket($ticket);

        if ($ticket->assignedTo && ! $staff->contains('id', $ticket->assignedTo->id)) {
            $staff->push($ticket->assignedTo);
        }

        if ($staff->isEmpty()) {
            return;
        }

        $url = route('filament.admin.resources.tickets.view', ['record' => $ticket->id]);

        try {
            Notification::make()
                ->title("رد جديد على التذكرة #{$ticket->ticket_number}")
                ->body(($ticket->partner?->name ?? 'العميل') . ': ' . strip_tags(Str::limit($event->content ?? 'رسالة جديدة', 80)))
                ->actions([
                    Action::make('view')
                        ->label('عرض التذكرة')
                        ->url($url),
                ])
                ->sendToDatabase($staff)
                ->broadcast($staff);
        } catch (\Throwable) {}
    }

    /**
     * Notify customer that support staff replied to their ticket.
     */
    public function notifyCustomerNewReply(Ticket $ticket, TicketEvent $event): void
    {
        if (! $ticket->partner) {
            return;
        }

        $url = route('filament.customer.resources.support-tickets.view', ['record' => $ticket->id]);

        try {
            Notification::make()
                ->title("رد جديد على التذكرة #{$ticket->ticket_number}")
                ->body('فريق الدعم الفني: ' . strip_tags(Str::limit($event->content ?? 'رسالة جديدة', 80)))
                ->actions([
                    Action::make('view')
                        ->label('عرض التذكرة')
                        ->url($url),
                ])
                ->sendToDatabase($ticket->partner)
                ->broadcast($ticket->partner);
        } catch (\Throwable) {}
    }

    public function notifyCustomerTicketReply(Ticket $ticket, TicketEvent $event): void
    {
        $this->notifyCustomerNewReply($ticket, $event);
    }

    public function notifyStaffTicketReply(Ticket $ticket, TicketEvent $event): void
    {
        $this->notifyStaffNewReply($ticket, $event);
    }
}
