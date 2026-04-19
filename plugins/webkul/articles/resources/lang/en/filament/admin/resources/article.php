<?php

return [
    'navigation' => [
        'title' => 'Articles',
        'group' => 'Articles',
    ],

    'model-label'        => 'Article',
    'plural-model-label' => 'Articles',

    'global-search' => [
        'category' => 'Category',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Article Content',
                'fields' => [
                    'title'           => 'Title',
                    'slug'            => 'Slug',
                    'summary'         => 'Summary',
                    'content'         => 'Content',
                    'video-embed-url' => 'Video Embed URL',
                    'cover-image'     => 'Cover Image',
                    'files'           => 'Downloadable Files',
                ],
            ],
            'settings' => [
                'title'  => 'Settings',
                'fields' => [
                    'category'           => 'Category',
                    'tags'               => 'Tags',
                    'programs'           => 'Visible to Programs',
                    'programs-helper'    => 'Leave empty to make visible to all customers (unless Internal is checked).',
                    'is-internal'        => 'Internal (Admin Only)',
                    'is-internal-helper' => 'Internal articles are only visible to administrators, never to customers.',
                    'is-published'       => 'Published',
                    'published-at'       => 'Published At',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Title',
            'category'     => 'Category',
            'is-internal'  => 'Internal',
            'is-published' => 'Published',
            'published-at' => 'Published At',
            'creator'      => 'Created By',
            'created-at'   => 'Created At',
        ],
        'filters' => [
            'category'      => 'Category',
            'internal-only' => 'Internal Only',
            'customer-only' => 'Customer Facing',
            'published'     => 'Published',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Article Content',
                'entries' => [
                    'title'           => 'Title',
                    'summary'         => 'Summary',
                    'content'         => 'Content',
                    'video-embed-url' => 'Video URL',
                    'cover-image'     => 'Cover Image',
                ],
            ],
            'settings' => [
                'title'   => 'Settings',
                'entries' => [
                    'category'     => 'Category',
                    'tags'         => 'Tags',
                    'programs'     => 'Linked Programs',
                    'is-internal'  => 'Internal',
                    'is-published' => 'Published',
                    'published-at' => 'Published At',
                    'creator'      => 'Created By',
                ],
            ],
        ],
    ],
];
