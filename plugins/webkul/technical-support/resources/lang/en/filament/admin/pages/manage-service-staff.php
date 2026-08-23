<?php

return [
    'navigation' => [
        'title' => 'Service Staff Routing',
    ],
    'title' => 'Support Staff Service Assignments',
    'sections' => [
        'wifi' => [
            'title'       => 'Wi-Fi Support',
            'description' => 'Staff members responsible for receiving and handling Wi-Fi cloud tickets.',
        ],
        'software' => [
            'title'       => 'Software Programs Support',
            'description' => 'Assign specific support staff per software program.',
        ],
        'online' => [
            'title'       => 'Online Systems Support',
            'description' => 'Staff members responsible for online web instances tickets.',
        ],
    ],
    'fields' => [
        'assigned_staff' => 'Assigned Staff',
    ],
    'actions' => [
        'save' => 'Save Assignments',
    ],
    'notifications' => [
        'saved' => 'Staff service assignments saved successfully',
    ],
];
