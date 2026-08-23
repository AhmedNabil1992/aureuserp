<?php

return [
    'navigation' => [
        'label' => 'تذاكر الدعم',
        'title' => 'تذاكر الدعم',
    ],
    'form' => [
        'fields' => [
            'ticket_number'   => 'رقم التذكرة',
            'status'          => 'الحالة',
            'priority'        => 'الأولوية',
            'assign_to'       => 'تعيين إلى',
            'customer'        => 'العميل',
            'service_type'    => 'نوع الخدمة',
            'license'         => 'الترخيص / النسخة',
            'program'         => 'البرنامج',
            'wifi_cloud'      => 'شبكة الواي فاي (الكلاود)',
            'service_details' => 'تفاصيل الخدمة',
            'title'           => 'عنوان المشكلة',
            'description'     => 'تفاصيل المشكلة',
            'attachments'     => 'المرفقات',
            'message'         => 'الرسالة',
            'voice_note'      => 'رسالة صوتية (اختياري)',
            'opened_at'       => 'تاريخ الفتح',
        ],
        'placeholders' => [
            'unassigned' => 'غير معيّن',
        ],
    ],
    'sidebar' => [
        'active_tickets'    => 'التذاكر النشطة',
        'no_active_tickets' => 'لا توجد تذاكر نشطة',
        'new_badge'         => 'جديد',
    ],
    'table' => [
        'columns' => [
            'number'       => 'رقم التذكرة',
            'title'        => 'العنوان',
            'customer'     => 'العميل',
            'service_type' => 'نوع الخدمة',
            'status'       => 'الحالة',
            'priority'     => 'الأولوية',
            'assigned_to'  => 'المسؤول',
            'last_update'  => 'آخر تحديث',
        ],
    ],
    'infolist' => [
        'sections' => [
            'details' => [
                'title' => 'تفاصيل التذكرة',
            ],
            'conversation' => [
                'title' => 'المحادثة والردود',
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
