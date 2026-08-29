<?php

return [
    'model_label'   => 'رابط / برنامج سريع',
    'plural_label'  => 'روابط وبرامج سريعة',
    'fields'        => [
        'title'           => 'اسم البرنامج / الملف',
        'service_type'    => 'الخدمة / النظام',
        'version'         => 'الإصدار',
        'file_size'       => 'حجم الملف',
        'upload_file'     => 'رفع ملف من السيرفر',
        'external_url'    => 'رابط تحميل خارجي مباشر',
        'description'     => 'الوصف والتفاصيل',
        'is_active'       => 'متاح للتحميل',
        'downloads_count' => 'عدد التحميلات',
        'created_at'      => 'تاريخ الإضافة',
    ],
    'helpers'       => [
        'upload_file'  => 'يتم رفع الملف على السيرفر في مجلد عام لسهولة التحميل بدون تسجيل دخول',
        'external_url' => 'أو يمكنك وضع رابط مباشر لبرنامج أو درايف إذا كان الملف خارجي',
    ],
    'placeholders'  => [
        'all_services' => 'جميع الخدمات',
    ],
    'actions'       => [
        'download' => 'تحميل الملف',
    ],
    'tabs' => [
        'active'   => 'التحميلات المتاحة',
        'inactive' => 'غير المتاحة',
        'archived' => 'المؤرشفة',
    ],
];
