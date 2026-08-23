<?php

return [
    'navigation' => [
        'title' => 'الأنظمة والمنصات',
    ],
    'models' => [
        'singular' => 'نظام أونلاين',
        'plural'   => 'الأنظمة الأونلاين',
    ],
    'sections' => [
        'general'    => 'البيانات الأساسية للنظام',
        'api_config' => 'إعدادات الربط البرمجي (API Endpoints & Authentication)',
    ],
    'fields' => [
        'name'              => 'اسم النظام',
        'slug'              => 'المعرف البرمجي (Slug)',
        'base_url'          => 'رابط النظام الأساسي',
        'base_url_helper'   => 'يمكنك استخدام {subdomain} ليتم استبداله برابط المتجر تلقائياً.',
        'icon'              => 'أيقونة Heroicon',
        'description'       => 'وصف النظام ومميزاته',
        'is_active'         => 'مفعل',
        'sort_order'        => 'ترتيب العرض',
        'plans_count'       => 'الباقات',
        'instances_count'   => 'المواقع المنشأة',
        'updated_at'        => 'آخر تحديث',
        'api_base_url'      => 'رابط الـ API الأساسي (Base URL)',
        'api_token'         => 'مفتاح الترخيص / Bearer Token',
        'api_headers'       => 'ترويسات إضافية (Custom Headers)',
        'create_endpoint'   => 'مسار إنشاء تينانت جديد (Create Tenant)',
        'renew_endpoint'    => 'مسار تجديد الاشتراك (Renew Tenant)',
        'suspend_endpoint'  => 'مسار إيقاف التينانت (Suspend Tenant)',
        'activate_endpoint' => 'مسار تفعيل التينانت (Activate Tenant)',
        'sync_endpoint'     => 'مسار فحص الحالة (Sync Status)',
    ],
    'actions' => [
        'test_api' => 'فحص الاتصال بالـ API',
    ],
    'notifications' => [
        'api_connected' => 'تم الاتصال بالـ API بنجاح',
        'api_failed'    => 'فشل الاتصال بالـ API',
    ],
];
