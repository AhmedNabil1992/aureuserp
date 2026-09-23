<?php

return [
    'navigation' => ['group' => 'إدارة VPN'],
    'servers'    => ['title' => 'سيرفرات VPN', 'singular' => 'سيرفر VPN', 'connection' => 'اتصال SoftEther'],
    'console'    => ['title' => 'لوحة تحكم VPN'],
    'users'      => ['title' => 'المستخدمون', 'empty' => 'لا يوجد مستخدمون على هذا الـ Hub.'],
    'sessions'   => ['title' => 'الجلسات الحالية', 'empty' => 'لا توجد جلسات نشطة على هذا الـ Hub.'],
    'details'    => ['title' => 'التفاصيل'],
    'fields'     => [
        'name'           => 'الاسم', 'host' => 'الدومين أو IP', 'host_help' => 'اكتب الدومين أو IP فقط؛ سيتم إضافة HTTPS و /api/ تلقائياً.',
        'port'           => 'منفذ HTTPS', 'admin_password' => 'كلمة مرور مدير السيرفر', 'password_help' => 'تُحفظ مشفرة. اتركها فارغة عند التعديل للاحتفاظ بالقيمة الحالية.',
        'timeout'        => 'مهلة الاتصال (ثانية)', 'verify_tls' => 'التحقق من شهادة TLS', 'active' => 'نشط', 'hubs' => 'الـ Hubs',
        'last_connected' => 'آخر اتصال ناجح', 'last_error' => 'آخر خطأ', 'server' => 'السيرفر', 'hub' => 'Virtual Hub',
        'username'       => 'اسم المستخدم', 'password' => 'كلمة المرور', 'real_name' => 'الاسم الحقيقي', 'group' => 'المجموعة', 'expires_at' => 'تاريخ الانتهاء', 'note' => 'ملاحظات',
        'auth_type'      => 'نوع التحقق', 'logins' => 'مرات الدخول', 'last_login' => 'آخر اتصال', 'session' => 'الجلسة',
        'ip_address'     => 'IP العميل', 'started_at' => 'بدأت في', 'last_communication' => 'آخر تواصل',
    ],
    'actions'  => ['add_server' => 'إضافة سيرفر', 'test' => 'اختبار الاتصال', 'create_user' => 'مستخدم جديد', 'refresh' => 'تحديث', 'details' => 'التفاصيل', 'delete' => 'حذف', 'disconnect' => 'قطع الجلسة'],
    'messages' => [
        'connected'           => 'تم الاتصال بنجاح', 'connection_failed' => 'فشل الاتصال', 'operation_failed' => 'فشلت العملية',
        'user_created'        => 'تم إنشاء المستخدم', 'user_deleted' => 'تم حذف المستخدم', 'session_disconnected' => 'تم قطع الجلسة',
        'confirm_delete_user' => 'هل تريد حذف هذا المستخدم من SoftEther؟', 'confirm_disconnect' => 'هل تريد قطع جلسة VPN الحالية؟',
    ],
    'common' => ['choose' => 'اختر…', 'never' => 'لم يحدث', 'actions' => 'إجراءات', 'yes' => 'نعم', 'no' => 'لا'],
    'auth'   => ['0' => 'بدون تحقق', '1' => 'كلمة مرور', '2' => 'شهادة مستخدم', '3' => 'شهادة جذر', '4' => 'RADIUS', '5' => 'NT Domain'],
];
