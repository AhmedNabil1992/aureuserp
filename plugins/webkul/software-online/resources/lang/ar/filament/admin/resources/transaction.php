<?php

return [
    'navigation' => [
        'title' => 'سجل المعاملات والاشتراكات',
    ],
    'models' => [
        'singular' => 'معاملة اشتراك',
        'plural'   => 'معاملات الاشتراكات',
    ],
    'fields' => [
        'instance'      => 'رقم الموقع',
        'customer'      => 'العميل',
        'type'          => 'نوع المعاملة',
        'billing_cycle' => 'دورة الفوترة',
        'amount'        => 'المبلغ المدفوع',
        'period_start'  => 'بداية الفترة',
        'period_end'    => 'نهاية الفترة',
        'created_at'    => 'تاريخ الدفع',
    ],
];
