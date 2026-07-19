<?php

return [
    'title' => 'Access Points',
    'table' => [
        'columns' => [
            'cloud' => 'Cloud',
            'realm' => 'Group',
            'name' => 'Name',
            'nasidentifier' => 'Device Identifier',
            'last_contact' => 'Last Contact',
            'last_contact_ip' => 'Last Contact IP',
            'picture' => 'Picture',
            'active' => 'Active',
        ],
    ],
    'filters' => [
        'realm' => 'Group',
    ],
    'actions' => [
        'title' => 'Edit Picture',
        'modal_heading' => 'Edit Card Logo Picture Only',
        'fileupload_placeholder' => 'Choose New Picture',
    ],
];