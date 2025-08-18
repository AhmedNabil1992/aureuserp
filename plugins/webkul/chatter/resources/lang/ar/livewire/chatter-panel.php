<?php

return [
    'placeholders' => [
        'no-record-found' => 'لا يوجد سجلات.',
        'loading' => 'جارٍ تحميل Chatter...',
    ],

    'activity-infolist' => [
        'title' => 'الأنشطة',
    ],

    'cancel-activity-plan-action' => [
        'title' => 'إلغاء النشاط',
    ],

    'delete-message-action' => [
        'title' => 'حذف الرسالة',
    ],

    'edit-activity' => [
        'title' => 'تعديل النشاط',

        'form' => [
            'fields' => [
                'activity-plan' => 'خطة النشاط',
                'plan-date' => 'تاريخ الخطة',
                'plan-summary' => 'ملخص الخطة',
                'activity-type' => 'نوع النشاط',
                'due-date' => 'تاريخ الاستحقاق',
                'summary' => 'الملخص',
                'assigned-to' => 'مخصص إلى',
            ],
        ],

        'action' => [
            'notification' => [
                'success' => [
                    'title' => 'تم تحديث النشاط',
                    'body' => 'تم تحديث النشاط بنجاح.',
                ],
            ],
        ],
    ],

    'process-message' => [
        'original-note' => '<br><div><span class="font-bold">الملاحظة الأصلية</span>: :body</div>',
        'feedback' => '<div><span class="font-bold">الملاحظات</span>: <p>:feedback</p></div>',
    ],

    'mark-as-done' => [
        'title' => 'وضع كمكتمل',
        'form' => [
            'fields' => [
                'feedback' => 'ملاحظات',
            ],
        ],

        'footer-actions' => [
            'label' => 'تم & جدولة التالي',

            'actions' => [
                'notification' => [
                    'mark-as-done' => [
                        'title' => 'تم وضع النشاط كمكتمل',
                        'body' => 'تم وضع النشاط كمكتمل بنجاح.',
                    ],
                ],
            ],
        ],
    ],
];
