<?php

return [
    'navigation' => [
        'label' => 'تذاكر الدعم الفني',
        'title' => 'تذاكر الدعم الفني',
    ],
    'models' => [
        'singular' => 'تذكرة دعم',
        'plural'   => 'تذاكر الدعم',
    ],
    'form' => [
        'fields' => [
            'service_type'          => 'الخدمة المعنية بالطلب',
            'license_or_product'    => 'الترخيص / البرنامج',
            'wifi_cloud'            => 'شبكة الواي فاي (الكلاود)',
            'priority'              => 'الأولوية',
            'title'                 => 'عنوان المشكلة باختصار',
            'describe_issue'        => 'شرح تفاصيل المشكلة',
            'voice_note'            => 'رسالة صوتية (اختياري)',
            'attachments_optional'  => 'مرفقات وصور (اختياري)',
        ],
    ],
    'table' => [
        'columns' => [
            'number'      => 'رقم التذكرة',
            'title'       => 'العنوان',
            'service'     => 'الخدمة',
            'status'      => 'الحالة',
            'priority'    => 'الأولوية',
            'opened_at'   => 'تاريخ الفتح',
            'last_update' => 'آخر تحديث',
        ],
    ],
    'infolist' => [
        'sections' => [
            'details' => [
                'title' => 'تفاصيل التذكرة',
            ],
            'conversation' => [
                'title' => 'المحادثة والردود المباشرة',
            ],
        ],
    ],
    'chat' => [
        'internal_note_title'  => 'ملاحظة داخلية خاصة بالفريق',
        'internal_note_active' => 'وضع الملاحظة الداخلية نشط',
        'internal_note_toggle' => 'ملاحظة داخلية خاصة بالفريق (لا يراها العميل)',
        'support_staff'        => 'فريق الدعم الفني',
        'customer'             => 'العميل',
        'staff_badge'          => 'الدعم',
        'customer_badge'       => 'عميل',
        'voice_message'        => 'رسالة صوتية',
        'voice_recorded'       => 'تم تسجيل رسالة صوتية جاهزة للإرسال',
        'cancel_recording'     => 'إلغاء التسجيل',
        'no_messages'          => 'لا توجد رسائل سابقة',
        'start_conversation'   => 'ابدأ المحادثة الآن بإرسال رد',
        'placeholder_message'  => 'اكتب رسالتك هنا... (اضغط Enter للإرسال)',
        'placeholder_note'     => 'اكتب ملاحظة داخلية خاصة بالفريق...',
        'ticket_closed_notice' => 'هذه التذكرة مغلقة ولا يمكن إضافة ردود جديدة عليها.',
        'close_lightbox'       => 'إغلاق',
        'browser_no_audio'     => 'متصفحك لا يدعم تشغيل الصوت',
        'record_voice'         => 'تسجيل صوتي',
    ],
    'actions' => [
        'reply'         => 'إرسال رد',
        'reply_heading' => 'الرد على التذكرة #:number',
    ],
    'notifications' => [
        'reply_sent' => 'تم إرسال الرد بنجاح',
    ],
];
