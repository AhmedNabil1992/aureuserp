<?php

return [
    'model_label'   => 'Canned Reply',
    'plural_label'  => 'Canned Replies',
    'fields'        => [
        'title'        => 'Title',
        'shortcut'     => 'Shortcut',
        'service_type' => 'Service / System',
        'is_active'    => 'Active',
        'content'      => 'Content',
        'created_at'   => 'Created At',
    ],
    'helpers'       => [
        'shortcut' => 'Shortcut to trigger this response in chat (e.g. /welcome)',
    ],
    'placeholders'  => [
        'all_services' => 'All Services',
    ],
];
