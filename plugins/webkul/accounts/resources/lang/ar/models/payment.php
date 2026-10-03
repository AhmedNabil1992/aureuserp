<?php

return [
    'title'          => 'الدفع',

    'log-attributes' => [
        'date'              => 'التاريخ',
        'payment-type'      => 'نوع الدفع',
        'partner-type'      => 'نوع الشريك',
        'memo'              => 'ملاحظة',
        'payment-reference' => 'مرجع الدفع',
        'amount'            => 'المبلغ',
        'state'             => 'الحالة',
        'is-sent'           => 'تم الإرسال',
        'is-reconciled'     => 'تمت التسوية',
        'is-matched'        => 'تمت المطابقة',
        'partner'           => 'الشريك',
        'partner-bank'      => 'بنك الشريك',
        'payment-method'    => 'طريقة الدفع',
        'currency'          => 'العملة',
    ],

    'activities' => [
        'confirmed'        => 'تم تأكيد الدفعة.',
        'reset-to-draft'   => 'تمت إعادة الدفعة إلى مسودة.',
        'canceled'         => 'تم إلغاء الدفعة.',
        'rejected'         => 'تم رفض الدفعة.',
        'status-changed'   => 'تم تغيير حالة الدفعة.',
        'marked-as-sent'   => 'تم تعليم الدفعة كمرسلة.',
        'marked-as-unsent' => 'تم إلغاء تعليم الدفعة كمرسلة.',
    ],
];
