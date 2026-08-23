<?php

return [
    'navigation' => [
        'label' => 'Support Tickets',
        'title' => 'Support Tickets',
    ],
    'form' => [
        'fields' => [
            'ticket_number'   => 'Ticket Number',
            'status'          => 'Status',
            'priority'        => 'Priority',
            'assign_to'       => 'Assign To',
            'customer'        => 'Customer',
            'service_type'    => 'Service Type',
            'license'         => 'License',
            'program'         => 'Program',
            'wifi_cloud'      => 'Wi-Fi Cloud',
            'service_details' => 'Service Details',
            'title'           => 'Title',
            'description'     => 'Description',
            'attachments'     => 'Attachments',
            'message'         => 'Message',
            'voice_note'      => 'Voice Note (Optional)',
            'opened_at'       => 'Opened At',
        ],
        'placeholders' => [
            'unassigned' => 'Unassigned',
        ],
    ],
    'sidebar' => [
        'active_tickets'    => 'Active Tickets',
        'no_active_tickets' => 'No active tickets',
        'new_badge'         => 'New',
    ],
    'table' => [
        'columns' => [
            'number'       => 'Ticket #',
            'title'        => 'Title',
            'customer'     => 'Customer',
            'service_type' => 'Service Type',
            'status'       => 'Status',
            'priority'     => 'Priority',
            'assigned_to'  => 'Assigned To',
            'last_update'  => 'Last Updated',
        ],
    ],
    'infolist' => [
        'sections' => [
            'details' => [
                'title' => 'Ticket Details',
            ],
            'conversation' => [
                'title' => 'Conversation Thread',
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
