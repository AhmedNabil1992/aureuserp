<?php

return [
    'navigation' => [
        'title' => 'مواقع واشتراكات العملاء',
    ],
    'models' => [
        'singular' => 'موقع / اشتراك عميل',
        'plural'   => 'مواقع واشتراكات العملاء',
    ],
    'sections' => [
        'general'      => 'بيانات الموقع والعميل',
        'subscription' => 'بيانات الاشتراك والصلاحية',
        'remote_sync'  => 'حالة المزامنة والـ API',
    ],
    'fields' => [
        'instance_number'  => 'رقم الاشتراك',
        'customer'         => 'العميل',
        'system'           => 'النظام',
        'plan'             => 'الباقة',
        'name'             => 'اسم الموقع / المتجر',
        'subdomain'        => 'النطاق الفرعي (Subdomain)',
        'custom_domain'    => 'نطاق مخصص (Custom Domain)',
        'instance_url'     => 'رابط الوصول المباشر',
        'status'           => 'الحالة',
        'billing_cycle'    => 'دورة الفوترة',
        'price'            => 'السعر',
        'starts_at'        => 'تاريخ البدء',
        'expires_at'       => 'تاريخ الانتهاء',
        'auto_renew'       => 'تجديد تلقائي',
        'remote_tenant_id' => 'معرف التينانت البعيد (Remote Tenant ID)',
        'last_api_error'   => 'آخر خطأ API',
        'remote_data'      => 'بيانات الاستجابة البعيدة',
    ],
    'actions' => [
        'visit_website' => 'فتح الموقع',
        'provision_api' => 'إنشاء التينانت عبر الـ API',
        'renew'         => 'تجديد الاشتراك',
    ],
    'notifications' => [
        'provision_success' => 'تم إنشاء وتفعيل التينانت بنجاح عبر الـ API',
        'provision_failed'  => 'فشل إنشاء التينانت في النظام البعيد',
        'renew_success'     => 'تم تجديد اشتراك الموقع بنجاح',
        'renew_failed'      => 'فشل تجديد الاشتراك',
    ],
];
