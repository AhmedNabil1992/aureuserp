<?php

return [
    'navigation' => [
        'label' => 'Program Releases',
    ],

    'title' => [
        'index' => 'Program Releases',
        'create' => 'Add Program Release',
        'edit' => 'Edit Program Release',
    ],

    'form' => [
        'fields' => [
            'program' => 'Program',
            'version_number' => 'Version Number',
            'update_link' => 'Update Link',
            'file_name' => 'File Name',
            'db_link' => 'DB Link',
            'app_terminate' => 'App Terminate',
            'remark' => 'Remark',
            'release_date' => 'Release Date',
            'is_active' => 'Is Active',
            'is_db_update' => 'Is DB Update',
            'download_times' => 'Download Times',
        ],
    ],

    'table' => [
        'columns' => [
            'program' => 'Program',
            'version_number' => 'Version Number',
            'release_date' => 'Release Date',
            'is_active' => 'Is Active',
            'is_db_update' => 'Is DB Update',
            'download_times' => 'Download Times',
        ],
    ],
];
