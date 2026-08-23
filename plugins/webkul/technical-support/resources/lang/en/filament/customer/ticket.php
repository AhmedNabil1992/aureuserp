<?php

return [
    'navigation' => [
        'label' => 'Support Tickets',
        'title' => 'Support Tickets',
    ],
    'models' => [
        'singular' => 'Support Ticket',
        'plural'   => 'Support Tickets',
    ],
    'form' => [
        'fields' => [
            'service_type'          => 'Target Service',
            'license_or_product'    => 'License / Product',
            'wifi_cloud'            => 'Wi-Fi Cloud',
            'priority'              => 'Priority',
            'title'                 => 'Issue Subject',
            'describe_issue'        => 'Describe your issue in detail',
            'voice_note'            => 'Voice Note (Optional)',
            'attachments_optional'  => 'Attachments & Images (Optional)',
        ],
    ],
    'table' => [
        'columns' => [
            'number'      => 'Ticket #',
            'title'       => 'Title',
            'service'     => 'Service',
            'status'      => 'Status',
            'priority'    => 'Priority',
            'opened_at'   => 'Opened At',
            'last_update' => 'Last Updated',
        ],
    ],
    'infolist' => [
        'sections' => [
            'details' => [
                'title' => 'Ticket Details',
            ],
            'conversation' => [
                'title' => 'Live Conversation',
            ],
        ],
    ],
    'chat' => [
        'internal_note_title'  => 'Internal Staff Note',
        'internal_note_active' => 'Internal Note Mode Active',
        'internal_note_toggle' => 'Internal Staff Note (Hidden from Customer)',
        'support_staff'        => 'Support Staff',
        'customer'             => 'Customer',
        'staff_badge'          => 'Staff',
        'customer_badge'       => 'Customer',
        'voice_message'        => 'Voice Message',
        'voice_recorded'       => 'Voice message recorded and ready to send',
        'cancel_recording'     => 'Cancel Recording',
        'no_messages'          => 'No previous messages',
        'start_conversation'   => 'Start the conversation by sending a reply',
        'placeholder_message'  => 'Type your message here... (Press Enter to send)',
        'placeholder_note'     => 'Type internal staff note...',
        'ticket_closed_notice' => 'This ticket is closed and cannot receive new replies.',
        'close_lightbox'       => 'Close',
        'browser_no_audio'     => 'Your browser does not support audio playback',
        'record_voice'         => 'Voice Note',
    ],
    'actions' => [
        'reply'         => 'Reply',
        'reply_heading' => 'Reply to Ticket #:number',
    ],
    'notifications' => [
        'reply_sent' => 'Reply sent successfully',
    ],
];
