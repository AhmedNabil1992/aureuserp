<?php

return [
    'discount_types' => ['fixed' => 'مبلغ ثابت', 'percentage' => 'نسبة مئوية'],
    'statuses'       => ['pending' => 'بانتظار السداد', 'earned' => 'مستحقة', 'reversed' => 'معكوسة', 'void' => 'ملغاة'],
    'contexts'       => ['license' => 'تراخيص البرامج', 'online_subscription' => 'الأنظمة الأونلاين', 'sales_invoice' => 'فواتير المبيعات'],
    'campaigns'      => ['title' => 'حملات الإحالة', 'singular' => 'حملة إحالة', 'rules' => 'قواعد الحملة'],
    'codes'          => ['title' => 'أكواد الإحالة', 'singular' => 'كود إحالة', 'auto_help' => 'اتركه فارغًا لتوليد كود آمن تلقائيًا.'],
    'redemptions'    => ['title' => 'عمليات الإحالة', 'singular' => 'عملية إحالة', 'details' => 'تفاصيل عملية الإحالة'],
    'fields'         => [
        'name'            => 'الاسم', 'company' => 'الشركة', 'currency' => 'العملة', 'contexts' => 'مسارات البيع المؤهلة',
        'products'        => 'المنتجات المؤهلة', 'discount_type' => 'نوع خصم العميل الجديد', 'customer_discount' => 'خصم العميل الجديد',
        'referrer_reward' => 'مكافأة صاحب الكود', 'minimum_eligible_amount' => 'الحد الأدنى للأصناف المؤهلة', 'journal' => 'دفتر قيد المكافآت',
        'expense_account' => 'حساب مصروف تسويق الإحالات', 'max_redemptions' => 'الحد الأقصى للاستخدامات', 'starts_at' => 'تبدأ في',
        'ends_at'         => 'تنتهي في', 'first_purchase_only' => 'أول شراء مؤهل فقط', 'active' => 'نشط', 'redemptions' => 'الاستخدامات',
        'customer'        => 'العميل', 'code' => 'الكود', 'referred_customer' => 'العميل الجديد', 'referrer' => 'صاحب الكود', 'campaign' => 'الحملة',
        'context'         => 'مسار البيع', 'invoice' => 'الفاتورة', 'reward_entry' => 'قيد المكافأة', 'reward_reversal_entry' => 'قيد عكس المكافأة',
    ],
    'customer' => [
        'navigation' => 'كود الإحالة الخاص بي', 'title' => 'رشّح عملاء واكسب رصيدًا',
        'share_help' => 'شارك هذا الكود، وتصبح مكافأتك متاحة بعد سداد الفاتورة المؤهلة.',
        'copy'       => 'نسخ الكود', 'copied' => 'تم نسخ الكود', 'earned' => 'المكافآت المستحقة', 'pending' => 'مكافآت بانتظار السداد',
    ],
    'validation' => [
        'company_required'            => 'يجب تحديد شركة لإنشاء كود الإحالة.',
        'draft_only'                  => 'يمكن تطبيق كود الإحالة على فاتورة مسودة فقط.',
        'invalid_code'                => 'كود الإحالة غير صحيح أو تابع لشركة أخرى.',
        'self_referral'               => 'لا يمكن للعميل استخدام كود الإحالة الخاص به.',
        'already_applied'             => 'تم تطبيق كود إحالة آخر على هذه الفاتورة.',
        'no_campaign'                 => 'لا توجد حملة إحالة نشطة تنطبق على المنتجات المختارة.',
        'minimum_not_met'             => 'قيمة المنتجات المؤهلة أقل من الحد الأدنى للحملة.',
        'campaign_limit'              => 'وصلت حملة الإحالة إلى الحد الأقصى للاستخدامات.',
        'first_purchase_only'         => 'هذه الحملة متاحة لأول عملية شراء مؤهلة فقط.',
        'discount_too_large'          => 'خصم الإحالة أكبر من قيمة المنتجات المؤهلة.',
        'receivable_account_required' => 'لا يوجد حساب عملاء مدين مضبوط لصاحب كود الإحالة.',
    ],
];
