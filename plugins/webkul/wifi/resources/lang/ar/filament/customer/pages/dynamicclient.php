<?php

return [
    'title' => 'بيانات الاكسسيس بوينت',
    'table' => [
        'columns' => [
            'cloud' => 'السحابه',
            'realm' => 'مجموعة',
            'name' => 'الاسم',
            'nasidentifier' => 'معرف الجهاز',
            'last_contact' => 'آخر اتصال',
            'last_contact_ip' => 'آخر اتصال IP',
            'picture' => 'صورة',
            'active' => 'نشط',
        ],
    ],
    'filters' => [
        'realm' => 'مجموعة',
    ],
    'actions' => [
        'title' => 'تعديل الصورة',
        'modal_heading' => 'تعديل صورة لوجو الكروت فقط',
        'fileupload_placeholder' => 'اختر الصورة الجديدة',
    ],
];