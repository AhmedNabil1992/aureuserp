<?php

namespace Webkul\TechnicalSupport\Services;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
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
        $serviceType = $ticket->service_type->value ?? $ticket->service_type;
        $query = ServiceStaffAssignment::where('service_type', $serviceType);

        // Check specific reference assignments (Software Program / Online System / Wifi Cloud)
        $refId = $ticket->program_id ?? $ticket->online_id ?? $ticket->cloud_id ?? null;

        if ($refId) {
            $specificStaffIds = (clone $query)->where('service_reference_id', $refId)->pluck('user_id');
            if ($specificStaffIds->isNotEmpty()) {
                return User::whereIn('id', $specificStaffIds)->get();
            }
        }

        // Check general staff for this service type
        $generalStaffIds = $query->whereNull('service_reference_id')->pluck('user_id');
        if ($generalStaffIds->isNotEmpty()) {
            return User::whereIn('id', $generalStaffIds)->get();
        }

        // If ticket already has an assigned staff member
        if ($ticket->assignedTo) {
            return collect([$ticket->assignedTo]);
        }

        // Fallback to all users/admins
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
            $notification = Notification::make()
                ->title("تذكرة جديدة #{$ticket->ticket_number} - {$serviceName}")
                ->body(($ticket->partner?->name ?? 'عميل') . ': ' . strip_tags(Str::limit($ticket->title, 60)))
                ->icon('heroicon-o-ticket')
                ->actions([
                    Action::make('view')
                        ->label('عرض التذكرة')
                        ->url($url),
                ]);

            // 1. Send Database Notification (Reliable, persistent in Filament bell)
            $notification->sendToDatabase($staff);

            // 2. Broadcast via WebSockets (non-blocking)
            try {
                $notification->broadcast($staff);
            } catch (\Throwable) {}
        } catch (\Throwable $e) {
            report($e);
        }
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
            $notification = Notification::make()
                ->title("رد جديد على التذكرة #{$ticket->ticket_number}")
                ->body(($ticket->partner?->name ?? 'العميل') . ': ' . strip_tags(Str::limit($event->content ?? 'رسالة جديدة', 80)))
                ->icon('heroicon-o-chat-bubble-left-right')
                ->actions([
                    Action::make('view')
                        ->label('عرض التذكرة')
                        ->url($url),
                ]);

            // 1. Send Database Notification
            $notification->sendToDatabase($staff);

            // 2. Broadcast via WebSockets
            try {
                $notification->broadcast($staff);
            } catch (\Throwable) {}
        } catch (\Throwable $e) {
            report($e);
        }
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
            $notification = Notification::make()
                ->title("رد جديد على التذكرة #{$ticket->ticket_number}")
                ->body('فريق الدعم الفني: ' . strip_tags(Str::limit($event->content ?? 'رسالة جديدة', 80)))
                ->icon('heroicon-o-chat-bubble-left-right')
                ->actions([
                    Action::make('view')
                        ->label('عرض التذكرة')
                        ->url($url),
                ]);

            // 1. Send Database Notification
            $notification->sendToDatabase($ticket->partner);

            // 2. Broadcast via WebSockets
            try {
                $notification->broadcast($ticket->partner);
            } catch (\Throwable) {}
        } catch (\Throwable $e) {
            report($e);
        }
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
