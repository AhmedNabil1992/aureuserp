<?php
return [
    'title' => 'المستخدمون الدائمون',
    'table' => [
        'columns' => [
            'cloud' => 'السحابة',
            'realm' => 'مجموعة',
            'username' => 'إسم المستخدم',
            'profile' => 'الملف الشخصي',
            'last_accept_time' => 'آخر وقت قبول',
            'last_reject_time' => 'آخر وقت رفض',
            'last_accept_nas' => 'آخر جهاز قبول',
            'last_reject_nas' => 'آخر جهاز رفض',
            'last_reject_message' => 'آخر رسالة رفض',
            'created' => 'تم الإنشاء',
            'modified' => 'تم التعديل',
            'active' => 'نشط',
        ],
    ],
    'actions' => [
        'edit' => 'تعديل',
        'delete' => 'حذف',
    ],
    'notifications' => [
        'delete' => [
            'success' => 'تم حذف المستخدم الدائم بنجاح.',
            'error' => 'حدث خطأ أثناء حذف المستخدم الدائم.',
        ],
    ],
    'headeractions' => [
        'label' => 'إضافة مستخدم جديد',
        'create' => 'إنشاء مستخدم دائم جديد',
        'form' => [
            'cloud' => 'السحابة',
            'realm' => 'المجموعة',
            'username' => 'إسم المستخدم',
            'password' => 'كلمة المرور',
            'profile' => 'الباقة',
            'username_helper' => 'يجب أن يتكون من حروف وأرقام فقط،لا يقل عن 5 احرف ولا يزيد عن 20 حرفًا',
            'password_helper' => 'يجب أن تتكون من حروف وأرقام فقط،لا يقل عن 5 احرف ولا يزيد عن 20 حرفًا',
        ],
        'notifications' => [
            'create' => [
                'success' => 'تم إنشاء المستخدم الدائم بنجاح.',
                'error' => 'حدث خطأ أثناء إنشاء المستخدم الدائم.',
            ],
        ],
    ],
];