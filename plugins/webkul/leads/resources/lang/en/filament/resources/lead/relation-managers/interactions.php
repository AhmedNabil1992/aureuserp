<?php

return [
    'title' => 'Interactions',

    'form' => [
        'fields' => [
            'type'             => 'Type',
            'subject'          => 'Subject',
            'interaction-date' => 'Date',
            'notes'            => 'Notes',
            'outcome'          => 'Outcome',
            'next-action'      => 'Next Action',
            'follow-up-date'   => 'Follow-up Date',
        ],
    ],

    'table' => [
        'columns' => [
            'type'             => 'Type',
            'subject'          => 'Subject',
            'interaction-date' => 'Date',
            'outcome'          => 'Outcome',
            'follow-up-date'   => 'Follow-up Date',
            'user'             => 'Logged By',
        ],
        'filters' => [
            'type' => 'Type',
        ],
    ],
];
