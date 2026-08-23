<?php

return [
    'navigation' => [
        'title' => 'فئات وباقات الأسعار',
    ],
    'models' => [
        'singular' => 'باقة نظام',
        'plural'   => 'باقات الأنظمة',
    ],
    'sections' => [
        'general'  => 'بيانات الباقة',
        'pricing'  => 'الأسعار والحدود',
        'features' => 'الميزات وحمولة الـ API',
    ],
    'fields' => [
        'system'                    => 'النظام التابع له',
        'product'                   => 'الصنف الخدمي المرتبط (Service Product)',
        'product_helper'            => 'ربط الباقة بصنف من نوع خدمة لتسجيل الفواتير والبنود تلقائياً في الحسابات.',
        'name'                      => 'اسم الباقة / الفئة',
        'slug'                      => 'المعرف (Slug)',
        'description'               => 'الوصف',
        'monthly_price'             => 'السعر الشهري',
        'annual_price'              => 'السعر السنوي',
        'trial_days'                => 'أيام التجربة المجانية',
        'max_users'                 => 'أقصى عدد مستخدمين',
        'max_branches'              => 'أقصى عدد فروع',
        'is_active'                 => 'مفعلة',
        'instances_count'           => 'الاشتراكات الحالية',
        'features_list'             => 'قائمة المميزات',
        'custom_api_payload'        => 'معاملات الـ API المخصصة (Payload)',
        'custom_api_payload_helper' => 'قيم إضافية يتم إرسالها للـ API عند إنشاء التينانت (مثل plan_code, storage_limit).',
    ],
    'placeholders' => [
        'unlimited'       => 'غير محدود (فارغ)',
        'feature_example' => 'دعم نقاط البيع والفواتير الإلكترونية',
    ],
];
