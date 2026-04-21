<?php

return [
    'navigation' => [
        'title' => 'عملاء ديناميكيون',
        'group' => 'شبكة واي فاي',
    ],

    'model-label'        => 'عميل ديناميكي',
    'plural-model-label' => 'عملاء ديناميكيون',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'معلومات العميل',
                'fields' => [
                    'cloud'         => 'اسم السحابة',
                    'name'          => 'اسم العميل',
                    'nasidentifier' => 'معرف NAS',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                                              => 'المعرف',
            'cloud'                                           => 'السحابة',
            'name'                                            => 'الاسم',
            'nasidentifier'                                   => 'معرف NAS',
            'last_contact'                                    => 'آخر اتصال',
            'last_contact_ip'                                 => 'IP آخر اتصال',
            'zero_ip'                                         => 'IP زيرو',
            'picture'                                         => 'صورة',
            'picture_uploaded'                                => 'تم رفع الصورة',
            'no_picture'                                      => 'لا توجد صورة',
            'last_contact_less_than_1_day'                    => 'آخر اتصال أقل من يوم',
            'last_contact_more_than_1_day_less_than_1_week'   => 'آخر اتصال أكثر من يوم وأقل من أسبوع',
            'last_contact_more_than_1_week_less_than_1_month' => 'آخر اتصال أكثر من أسبوع وأقل من شهر',
            'last_contact_more_than_1_month'                  => 'آخر اتصال أكثر من شهر',
        ],
    ],
];
