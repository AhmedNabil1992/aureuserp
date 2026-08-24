<?php

return [
    'navigation' => [
        'label' => 'Working Hours & Auto Reply',
    ],
    'title' => 'Working Hours & Auto-Reply Settings',
    'sections' => [
        'emergency' => [
            'title'       => 'Emergency Mode & Instant Pause',
            'description' => 'Pause incoming replies and notify customers about emergencies immediately upon ticket creation.',
        ],
        'welcome' => [
            'title'       => 'Welcome Auto-Reply',
            'description' => 'Send an instant greeting to customers when a new ticket is opened during business hours.',
        ],
        'business_hours' => [
            'title'       => 'Official Business Hours',
            'description' => 'Define weekly work schedule and auto-responder message for off-hours.',
        ],
    ],
    'fields' => [
        'is_emergency_mode'         => 'Enable Emergency Mode (Pause Replies)',
        'emergency_message'         => 'Emergency Message Content',
        'is_auto_reply_enabled'     => 'Enable Welcome Auto-Reply',
        'welcome_message'           => 'Welcome Message Content',
        'is_business_hours_enabled' => 'Enable Business Hours Tracking',
        'work_days'                 => 'Working Days of Week',
        'work_start_time'           => 'Daily Start Time',
        'work_end_time'             => 'Daily End Time',
        'timezone'                  => 'Timezone',
        'out_of_hours_message'      => 'Off-Hours Auto-Response Message',
    ],
    'helpers' => [
        'is_emergency_mode'         => 'When enabled, the emergency message will be sent immediately upon ticket opening instead of standard greeting.',
        'is_auto_reply_enabled'     => 'Automatically sends a confirmation message that the ticket was received and is in progress.',
        'is_business_hours_enabled' => 'Checks ticket creation time and dispatches the off-hours notice if opened outside schedule.',
        'out_of_hours_message'      => 'Sent automatically to the customer if a ticket is created outside defined work hours.',
    ],
];
