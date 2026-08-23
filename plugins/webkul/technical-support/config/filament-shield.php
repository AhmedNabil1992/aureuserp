<?php

return [
    'resources' => [
        'manage' => [
            'technical_support_ticket' => 'manage_technical_support_ticket',
            'technical_support_tag'    => 'manage_technical_support_tag',
            'technical_support_event'  => 'manage_technical_support_event',
        ],
        'exclude' => [],
    ],
    'pages' => [
        'manage' => [
            'manage_service_staff' => 'manage_technical_support_staff_routing',
        ],
        'exclude' => [],
    ],
    'widgets' => [
        'manage' => [],
        'exclude' => [],
    ],
];
