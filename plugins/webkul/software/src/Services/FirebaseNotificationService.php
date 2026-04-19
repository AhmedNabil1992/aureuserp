<?php

namespace Webkul\Software\Services;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Webkul\Software\Models\FcmToken;
use Webkul\Software\Models\Ticket;

class FirebaseNotificationService
{
    public function __construct(protected Messaging $messaging) {}

    /**
     * Notify all FCM tokens that belong to the ticket's customer (partner).
     */
    public function notifyCustomer(Ticket $ticket, string $title, string $body): void
    {
        if (! $ticket->partner_id) {
            return;
        }

        $tokens = FcmToken::query()
            ->where('partner_id', $ticket->partner_id)
            ->pluck('token')
            ->toArray();

        $this->sendToTokens($tokens, $title, $body, [
            'ticket_id'     => (string) $ticket->id,
            'ticket_number' => (string) $ticket->ticket_number,
            'type'          => 'new_reply',
        ]);
    }

    /**
     * Notify all admin FCM tokens about a customer reply.
     * Admins register tokens using their user_id.
     */
    public function notifyAdmins(Ticket $ticket, string $title, string $body): void
    {
        $tokens = FcmToken::query()
            ->whereNotNull('user_id')
            ->pluck('token')
            ->toArray();

        $this->sendToTokens($tokens, $title, $body, [
            'ticket_id'     => (string) $ticket->id,
            'ticket_number' => (string) $ticket->ticket_number,
            'type'          => 'customer_reply',
        ]);
    }

    /**
     * Send a multicast message to an array of FCM registration tokens.
     * Invalid / expired tokens are silently pruned from the DB.
     *
     * @param  array<int, string>  $tokens
     * @param  array<string, string>  $data
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): void
    {
        if (empty($tokens)) {
            return;
        }

        $notification = FcmNotification::create($title, $body);

        foreach (array_chunk($tokens, 500) as $chunk) {
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            $report = $this->messaging->sendMulticast($message, $chunk);

            // Prune tokens that Firebase says are invalid / unregistered
            foreach ($report->invalidTokens() as $invalidToken) {
                FcmToken::where('token', $invalidToken)->delete();
            }
        }
    }
}
