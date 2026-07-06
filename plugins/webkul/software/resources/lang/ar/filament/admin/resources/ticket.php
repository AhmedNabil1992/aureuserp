<?php

return [
    'navigation' => [
        'label' => 'التذاكر',
    ],

    'form' => [
        'fields' => [
            'ticket_number' => 'رقم التذكرة',
            'assign_to' => 'إسناد إلى',
            'customer' => 'العميل',
            'license' => 'الترخيص',
            'program' => 'البرنامج',
            'description' => 'الوصف',
            'attachments' => 'المرفقات',
        ],
    ],

    'table' => [
        'columns' => [
            'number' => '#',
            'customer' => 'العميل',
            'license' => 'الترخيص',
            'program' => 'البرنامج',
            'assigned_to' => 'المسند إليه',
            'last_update' => 'آخر تحديث',
        ],
    ],
];
