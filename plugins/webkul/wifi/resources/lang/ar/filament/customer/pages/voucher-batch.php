<?php

return [
    'title'   => 'ملفات الكروت (Batches)',
    'empty'   => 'لا توجد ملفات كروت للعرض',
    'columns' => [
        'nasidentifier'      => 'رقم الراوتر',
        'qty'                => 'الكمية',
        'remaining_vouchers' => 'المتبقي (جديد)',
        'batch_code'         => 'إسم الملف',
        'caption'            => 'التسمية التوضيحية',
        'never_expire'       => 'تاريخ الإنتهاء',
        'created_at'         => 'تاريخ الإنشاء',
    ],
    'actions' => [
        'download'       => 'تحميل',
        'edit_caption'   => 'تعديل التسمية',
        'copied_message' => 'تم النسخ بنجاح!',
    ],
    'never_expire_options' => [
        'yes' => 'بدون تاريخ انتهاء',
    ],
];
