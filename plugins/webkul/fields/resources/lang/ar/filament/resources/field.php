<?php

return [
    'navigation' => [
        'title' => 'الحقول المخصصة',
        'group' => 'الإعدادات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'name' => 'الاسم',
                    'code' => 'الرمز',
                    'code-helper-text' => 'يجب أن يبدأ الرمز بحرف أو شرطة سفلية، ويمكن أن يحتوي فقط على أحرف وأرقام وشرطات سفلية.',
                ],
            ],

            'options' => [
                'title' => 'الخيارات',

                'fields' => [
                    'add-option' => 'إضافة خيار',
                ],
            ],

            'form-settings' => [
                'title' => 'إعدادات النموذج',

                'field-sets' => [
                    'validations' => [
                        'title' => 'التحقق',

                        'fields' => [
                            'validation' => 'التحقق',
                            'field' => 'الحقل',
                            'value' => 'القيمة',
                            'add-validation' => 'إضافة تحقق',
                        ],
                    ],

                    'additional-settings' => [
                        'title' => 'إعدادات إضافية',

                        'fields' => [
                            'setting' => 'الإعداد',
                            'value' => 'القيمة',
                            'color' => 'اللون',
                            'add-setting' => 'إضافة إعداد',

                            'color-options' => [
                                'danger' => 'خطر',
                                'info' => 'معلومات',
                                'primary' => 'أساسي',
                                'secondary' => 'ثانوي',
                                'warning' => 'تحذير',
                                'success' => 'نجاح',
                            ],

                            'grid-options' => [
                                'row' => 'صف',
                                'column' => 'عمود',
                            ],

                            'input-modes' => [
                                'text' => 'نص',
                                'email' => 'بريد إلكتروني',
                                'numeric' => 'رقمي',
                                'integer' => 'عدد صحيح',
                                'password' => 'كلمة المرور',
                                'tel' => 'هاتف',
                                'url' => 'رابط',
                                'color' => 'لون',
                                'none' => 'بدون',
                                'decimal' => 'عشري',
                                'search' => 'بحث',
                                'url' => 'رابط',
                            ],
                        ],
                    ],
                ],

                'validations' => [
                    'common' => [
                        'gt' => 'أكبر من',
                        'gte' => 'أكبر من أو يساوي',
                        'lt' => 'أقل من',
                        'lte' => 'أقل من أو يساوي',
                        'max-size' => 'الحجم الأقصى',
                        'min-size' => 'الحجم الأدنى',
                        'multiple-of' => 'مضاعف لـ',
                        'nullable' => 'قابل للإلغاء',
                        'prohibited' => 'ممنوع',
                        'prohibited-if' => 'ممنوع إذا',
                        'prohibited-unless' => 'ممنوع إلا إذا',
                        'prohibits' => 'يمنع',
                        'required' => 'إجباري',
                        'required-if' => 'إجباري إذا',
                        'required-if-accepted' => 'إجباري إذا تم القبول',
                        'required-unless' => 'إجباري إلا إذا',
                        'required-with' => 'إجباري مع',
                        'required-with-all' => 'إجباري مع الكل',
                        'required-without' => 'إجباري بدون',
                        'required-without-all' => 'إجباري بدون الكل',
                        'rules' => 'قواعد مخصصة',
                        'unique' => 'فريد',
                    ],

                    'text' => [
                        'alpha-dash' => 'أحرف وشرطات سفلية وواصلات',
                        'alpha-num' => 'أحرف وأرقام',
                        'ascii' => 'ASCII',
                        'doesnt-end-with' => 'لا ينتهي بـ',
                        'doesnt-start-with' => 'لا يبدأ بـ',
                        'ends-with' => 'ينتهي بـ',
                        'filled' => 'معبأ',
                        'ip' => 'IP',
                        'ipv4' => 'IPv4',
                        'ipv6' => 'IPv6',
                        'length' => 'الطول',
                        'mac-address' => 'عنوان MAC',
                        'max-length' => 'الطول الأقصى',
                        'min-length' => 'الطول الأدنى',
                        'regex' => 'نمط Regex',
                        'starts-with' => 'يبدأ بـ',
                        'ulid' => 'ULID',
                        'uuid' => 'UUID',
                    ],

                    'textarea' => [
                        'filled' => 'معبأ',
                        'max-length' => 'الطول الأقصى',
                        'min-length' => 'الطول الأدنى',
                    ],

                    'select' => [
                        'different' => 'مختلف',
                        'exists' => 'موجود',
                        'in' => 'في',
                        'not-in' => 'ليس في',
                        'same' => 'نفسه',
                    ],

                    'radio' => [],

                    'checkbox' => [
                        'accepted' => 'مقبول',
                        'declined' => 'مرفوض',
                    ],

                    'toggle' => [
                        'accepted' => 'مقبول',
                        'declined' => 'مرفوض',
                    ],

                    'checkbox-list' => [
                        'in' => 'في',
                        'max-items' => 'الحد الأقصى للعناصر',
                        'min-items' => 'الحد الأدنى للعناصر',
                    ],

                    'datetime' => [
                        'after' => 'بعد',
                        'after-or-equal' => 'بعد أو يساوي',
                        'before' => 'قبل',
                        'before-or-equal' => 'قبل أو يساوي',
                    ],

                    'editor' => [
                        'filled' => 'معبأ',
                        'max-length' => 'الطول الأقصى',
                        'min-length' => 'الطول الأدنى',
                    ],

                    'markdown' => [
                        'filled' => 'معبأ',
                        'max-length' => 'الطول الأقصى',
                        'min-length' => 'الطول الأدنى',
                    ],

                    'color' => [
                        'hex-color' => 'لون Hex',
                    ],
                ],

                'settings' => [
                    'text' => [
                        'autocapitalize' => 'تلقائي رأس الحرف',
                        'autocomplete' => 'الإكمال التلقائي',
                        'autofocus' => 'تركيز تلقائي',
                        'default' => 'القيمة الافتراضية',
                        'disabled' => 'معطل',
                        'helper-text' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hint-color' => 'لون التلميح',
                        'hint-icon' => 'أيقونة التلميح',
                        'id' => 'المعرف',
                        'input-mode' => 'وضع الإدخال',
                        'mask' => 'قناع',
                        'placeholder' => 'عنصر نائب',
                        'prefix' => 'بادئة',
                        'prefix-icon' => 'أيقونة بادئة',
                        'prefix-icon-color' => 'لون أيقونة البادئة',
                        'read-only' => 'للقراءة فقط',
                        'step' => 'خطوة',
                        'suffix' => 'لاحقة',
                        'suffix-icon' => 'أيقونة لاحقة',
                        'suffix-icon-color' => 'لون أيقونة اللاحقة',
                    ],

                    'textarea' => [
                        'autofocus' => 'تركيز تلقائي',
                        'autosize' => 'حجم تلقائي',
                        'cols' => 'أعمدة',
                        'default' => 'القيمة الافتراضية',
                        'disabled' => 'معطل',
                        'helperText' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hintColor' => 'لون التلميح',
                        'hintIcon' => 'أيقونة التلميح',
                        'id' => 'المعرف',
                        'placeholder' => 'عنصر نائب',
                        'read-only' => 'للقراءة فقط',
                        'rows' => 'صفوف',
                    ],

                    'select' => [
                        'default' => 'القيمة الافتراضية',
                        'disabled' => 'معطل',
                        'helper-text' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hint-color' => 'لون التلميح',
                        'hint-icon' => 'أيقونة التلميح',
                        'id' => 'المعرف',
                        'loading-message' => 'رسالة التحميل',
                        'no-search-results-message' => 'لا توجد نتائج بحث',
                        'options-limit' => 'حد الخيارات',
                        'preload' => 'تحميل مسبق',
                        'searchable' => 'قابل للبحث',
                        'search-debounce' => 'تأخير البحث',
                        'searching-message' => 'رسالة البحث',
                        'search-prompt' => 'موجه البحث',
                    ],

                    'radio' => [
                        'default' => 'القيمة الافتراضية',
                        'disabled' => 'معطل',
                        'helper-text' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hint-color' => 'لون التلميح',
                        'hint-icon' => 'أيقونة التلميح',
                        'id' => 'المعرف',
                    ],

                    'checkbox' => [
                        'default' => 'القيمة الافتراضية',
                        'disabled' => 'معطل',
                        'helper-text' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hint-color' => 'لون التلميح',
                        'hint-icon' => 'أيقونة التلميح',
                        'id' => 'المعرف',
                        'inline' => 'ضمني',
                    ],

                    'toggle' => [
                        'default' => 'القيمة الافتراضية',
                        'disabled' => 'معطل',
                        'helper-text' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hint-color' => 'لون التلميح',
                        'hint-icon' => 'أيقونة التلميح',
                        'id' => 'المعرف',
                        'off-color' => 'لون الإيقاف',
                        'off-icon' => 'أيقونة الإيقاف',
                        'on-color' => 'لون التشغيل',
                        'on-icon' => 'أيقونة التشغيل',
                    ],

                    'checkbox-list' => [
                        'bulk-toggleable' => 'قابل للتبديل الجماعي',
                        'columns' => 'أعمدة',
                        'default' => 'القيمة الافتراضية',
                        'disabled' => 'معطل',
                        'grid-direction' => 'اتجاه الشبكة',
                        'helper-text' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hint-color' => 'لون التلميح',
                        'hint-icon' => 'أيقونة التلميح',
                        'id' => 'المعرف',
                        'max-items' => 'الحد الأقصى للعناصر',
                        'min-items' => 'الحد الأدنى للعناصر',
                        'no-search-results-message' => 'لا توجد نتائج بحث',
                        'searchable' => 'قابل للبحث',
                    ],

                    'datetime' => [
                        'close-on-date-selection' => 'إغلاق عند اختيار التاريخ',
                        'default' => 'القيمة الافتراضية',
                        'disabled' => 'معطل',
                        'disabled-dates' => 'تواريخ معطلة',
                        'display-format' => 'تنسيق العرض',
                        'first-fay-of-week' => 'أول يوم في الأسبوع',
                        'format' => 'تنسيق',
                        'helper-text' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hint-color' => 'لون التلميح',
                        'hint-icon' => 'أيقونة التلميح',
                        'hours-step' => 'خطوة الساعات',
                        'id' => 'المعرف',
                        'locale' => 'اللغة',
                        'minutes-step' => 'خطوة الدقائق',
                        'seconds' => 'ثواني',
                        'seconds-step' => 'خطوة الثواني',
                        'timezone' => 'المنطقة الزمنية',
                        'week-starts-on-monday' => 'يبدأ الأسبوع يوم الاثنين',
                        'week-starts-on-sunday' => 'يبدأ الأسبوع يوم الأحد',
                    ],

                    'editor' => [
                        'default' => 'القيمة الافتراضية',
                        'disabled' => 'معطل',
                        'helper-text' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hint-color' => 'لون التلميح',
                        'hint-icon' => 'أيقونة التلميح',
                        'id' => 'المعرف',
                        'placeholder' => 'عنصر نائب',
                        'read-only' => 'للقراءة فقط',
                    ],

                    'markdown' => [
                        'default' => 'القيمة الافتراضية',
                        'disabled' => 'معطل',
                        'helper-text' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hint-color' => 'لون التلميح',
                        'hint-icon' => 'أيقونة التلميح',
                        'id' => 'المعرف',
                        'placeholder' => 'عنصر نائب',
                        'read-only' => 'للقراءة فقط',
                    ],

                    'color' => [
                        'default' => 'القيمة الافتراضية',
                        'disabled' => 'معطل',
                        'helper-text' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hint-color' => 'لون التلميح',
                        'hint-icon' => 'أيقونة التلميح',
                        'hsl' => 'HSL',
                        'id' => 'المعرف',
                        'rgb' => 'RGB',
                        'rgba' => 'RGBA',
                    ],

                    'file' => [
                        'accepted-file-types' => 'أنواع الملفات المقبولة',
                        'append-files' => 'إلحاق ملفات',
                        'deletable' => 'قابل للحذف',
                        'directory' => 'دليل',
                        'downloadable' => 'قابل للتنزيل',
                        'fetch-file-information' => 'جلب معلومات الملف',
                        'file-attachments-directory' => 'دليل مرفقات الملف',
                        'file-attachments-visibility' => 'رؤية مرفقات الملف',
                        'image' => 'صورة',
                        'image-crop-aspect-ratio' => 'نسبة اقتصاص الصورة',
                        'image-editor' => 'محرر الصور',
                        'image-editor-aspect-ratios' => 'نسب محرر الصور',
                        'image-editor-empty-fill-color' => 'لون تعبئة فارغ لمحرر الصور',
                        'image-editor-mode' => 'وضع محرر الصور',
                        'image-preview-height' => 'ارتفاع معاينة الصورة',
                        'image-resize-mode' => 'وضع تغيير حجم الصورة',
                        'image-resize-target-height' => 'ارتفاع الهدف لتغيير الحجم',
                        'image-resize-target-width' => 'عرض الهدف لتغيير الحجم',
                        'loading-indicator-position' => 'موضع مؤشر التحميل',
                        'move-files' => 'نقل الملفات',
                        'openable' => 'قابل للفتح',
                        'orient-images-from-exif' => 'توجيه الصور من EXIF',
                        'panel-aspect-ratio' => 'نسبة أبعاد اللوحة',
                        'panel-layout' => 'تخطيط اللوحة',
                        'previewable' => 'قابل للمعاينة',
                        'remove-uploaded-file-button-position' => 'موضع زر إزالة الملف المرفوع',
                        'reorderable' => 'قابل لإعادة الترتيب',
                        'store-files' => 'تخزين الملفات',
                        'upload-button-position' => 'موضع زر التحميل',
                        'uploading-message' => 'رسالة التحميل',
                        'upload-progress-indicator-position' => 'موضع مؤشر تقدم التحميل',
                        'visibility' => 'الرؤية',
                    ],
                ],
            ],

            'table-settings' => [
                'title' => 'إعدادات الجدول',

                'fields' => [
                    'use-in-table' => 'استخدام في الجدول',
                    'setting' => 'الإعداد',
                    'value' => 'القيمة',
                    'color' => 'اللون',
                    'alignment' => 'المحاذاة',
                    'font-weight' => 'سماكة الخط',
                    'icon-position' => 'موضع الأيقونة',
                    'size' => 'الحجم',
                    'add-setting' => 'إضافة إعداد',

                    'color-options' => [
                        'danger' => 'خطر',
                        'info' => 'معلومات',
                        'primary' => 'أساسي',
                        'secondary' => 'ثانوي',
                        'warning' => 'تحذير',
                        'success' => 'نجاح',
                    ],

                    'alignment-options' => [
                        'start' => 'بداية',
                        'left' => 'يسار',
                        'center' => 'وسط',
                        'end' => 'نهاية',
                        'right' => 'يمين',
                        'justify' => 'ضبط',
                        'between' => 'بين',
                    ],

                    'font-weight-options' => [
                        'extra-light' => 'خفيف جداً',
                        'light' => 'خفيف',
                        'normal' => 'عادي',
                        'medium' => 'متوسط',
                        'semi-bold' => 'شبه عريض',
                        'bold' => 'عريض',
                        'extra-bold' => 'عريض جداً',
                    ],

                    'icon-position-options' => [
                        'before' => 'قبل',
                        'after' => 'بعد',
                    ],

                    'size-options' => [
                        'extra-small' => 'صغير جداً',
                        'small' => 'صغير',
                        'medium' => 'متوسط',
                        'large' => 'كبير',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end' => 'محاذاة النهاية',
                        'alignment' => 'المحاذاة',
                        'align-start' => 'محاذاة البداية',
                        'badge' => 'شارة',
                        'boolean' => 'قيمة منطقية',
                        'color' => 'اللون',
                        'copyable' => 'قابل للنسخ',
                        'copy-message' => 'رسالة النسخ',
                        'copy-message-duration' => 'مدة رسالة النسخ',
                        'default' => 'افتراضي',
                        'filterable' => 'قابل للتصفية',
                        'groupable' => 'قابل للتجميع',
                        'grow' => 'تمدد',
                        'icon' => 'أيقونة',
                        'icon-color' => 'لون الأيقونة',
                        'icon-position' => 'موضع الأيقونة',
                        'label' => 'تسمية',
                        'limit' => 'حد',
                        'line-clamp' => 'تقييد السطر',
                        'money' => 'مال',
                        'placeholder' => 'عنصر نائب',
                        'prefix' => 'بادئة',
                        'searchable' => 'قابل للبحث',
                        'size' => 'الحجم',
                        'sortable' => 'قابل للترتيب',
                        'suffix' => 'لاحقة',
                        'toggleable' => 'قابل للتبديل',
                        'tooltip' => 'تلميح',
                        'vertical-alignment' => 'محاذاة عمودية',
                        'vertically-align-start' => 'محاذاة عمودية للبداية',
                        'weight' => 'سماكة',
                        'width' => 'العرض',
                        'words' => 'كلمات',
                        'wrap-header' => 'تغليف الرأس',
                        'column-span' => 'امتداد العمود',
                        'helper-text' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hint-color' => 'لون التلميح',
                        'hint-icon' => 'أيقونة التلميح',
                    ],

                    'datetime' => [
                        'date' => 'تاريخ',
                        'date-time' => 'تاريخ ووقت',
                        'date-time-tooltip' => 'تلميح تاريخ ووقت',
                        'since' => 'منذ',
                    ],
                ],
            ],

            'infolist-settings' => [
                'title' => 'إعدادات قائمة المعلومات',

                'fields' => [
                    'setting' => 'الإعداد',
                    'value' => 'القيمة',
                    'color' => 'اللون',
                    'font-weight' => 'سماكة الخط',
                    'icon-position' => 'موضع الأيقونة',
                    'size' => 'الحجم',
                    'add-setting' => 'إضافة إعداد',

                    'color-options' => [
                        'danger' => 'خطر',
                        'info' => 'معلومات',
                        'primary' => 'أساسي',
                        'secondary' => 'ثانوي',
                        'warning' => 'تحذير',
                        'success' => 'نجاح',
                    ],

                    'font-weight-options' => [
                        'extra-light' => 'خفيف جداً',
                        'light' => 'خفيف',
                        'normal' => 'عادي',
                        'medium' => 'متوسط',
                        'semi-bold' => 'شبه عريض',
                        'bold' => 'عريض',
                        'extra-bold' => 'عريض جداً',
                    ],

                    'icon-position-options' => [
                        'before' => 'قبل',
                        'after' => 'بعد',
                    ],

                    'size-options' => [
                        'extra-small' => 'صغير جداً',
                        'small' => 'صغير',
                        'medium' => 'متوسط',
                        'large' => 'كبير',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end' => 'محاذاة النهاية',
                        'alignment' => 'المحاذاة',
                        'align-start' => 'محاذاة البداية',
                        'badge' => 'شارة',
                        'boolean' => 'قيمة منطقية',
                        'color' => 'اللون',
                        'copyable' => 'قابل للنسخ',
                        'copy-message' => 'رسالة النسخ',
                        'copy-message-duration' => 'مدة رسالة النسخ',
                        'default' => 'افتراضي',
                        'filterable' => 'قابل للتصفية',
                        'groupable' => 'قابل للتجميع',
                        'grow' => 'تمدد',
                        'icon' => 'أيقونة',
                        'icon-color' => 'لون الأيقونة',
                        'icon-position' => 'موضع الأيقونة',
                        'label' => 'تسمية',
                        'limit' => 'حد',
                        'line-clamp' => 'تقييد السطر',
                        'money' => 'مال',
                        'placeholder' => 'عنصر نائب',
                        'prefix' => 'بادئة',
                        'searchable' => 'قابل للبحث',
                        'size' => 'الحجم',
                        'sortable' => 'قابل للترتيب',
                        'suffix' => 'لاحقة',
                        'toggleable' => 'قابل للتبديل',
                        'tooltip' => 'تلميح',
                        'vertical-alignment' => 'محاذاة عمودية',
                        'vertically-align-start' => 'محاذاة عمودية للبداية',
                        'weight' => 'سماكة',
                        'width' => 'العرض',
                        'words' => 'كلمات',
                        'wrap-header' => 'تغليف الرأس',
                        'column-span' => 'امتداد العمود',
                        'helper-text' => 'نص مساعد',
                        'hint' => 'تلميح',
                        'hint-color' => 'لون التلميح',
                        'hint-icon' => 'أيقونة التلميح',
                    ],

                    'datetime' => [
                        'date' => 'تاريخ',
                        'date-time' => 'تاريخ ووقت',
                        'date-time-tooltip' => 'تلميح تاريخ ووقت',
                        'since' => 'منذ',
                    ],

                    'checkbox-list' => [
                        'separator' => 'فاصل',
                        'list-with-line-breaks' => 'قائمة بفواصل الأسطر',
                        'bulleted' => 'منقطة',
                        'limit-list' => 'قائمة محدودة',
                        'expandable-limited-list' => 'قائمة محدودة قابلة للتوسيع',
                    ],

                    'select' => [
                        'separator' => 'فاصل',
                        'list-with-line-breaks' => 'قائمة بفواصل الأسطر',
                        'bulleted' => 'منقطة',
                        'limit-list' => 'قائمة محدودة',
                        'expandable-limited-list' => 'قائمة محدودة قابلة للتوسيع',
                    ],

                    'checkbox' => [
                        'boolean' => 'قيمة منطقية',
                        'false-icon' => 'أيقونة غير صحيحة',
                        'true-icon' => 'أيقونة صحيحة',
                        'true-color' => 'لون صحيح',
                        'false-color' => 'لون غير صحيح',
                    ],

                    'toggle' => [
                        'boolean' => 'قيمة منطقية',
                        'false-icon' => 'أيقونة غير صحيحة',
                        'true-icon' => 'أيقونة صحيحة',
                        'true-color' => 'لون صحيح',
                        'false-color' => 'لون غير صحيح',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'fields' => [
                    'type' => 'النوع',
                    'input-type' => 'نوع الإدخال',
                    'is-multiselect' => 'متعدد الاختيارات',
                    'sort-order' => 'ترتيب الفرز',

                    'type-options' => [
                        'text' => 'إدخال نص',
                        'textarea' => 'منطقة نصية',
                        'select' => 'اختيار',
                        'checkbox' => 'مربع اختيار',
                        'radio' => 'زر راديو',
                        'toggle' => 'تبديل',
                        'checkbox-list' => 'قائمة مربعات اختيار',
                        'datetime' => 'منتقي التاريخ والوقت',
                        'editor' => 'محرر نص غني',
                        'markdown' => 'محرر ماركداون',
                        'color' => 'منتقي اللون',
                    ],

                    'input-type-options' => [
                        'text' => 'نص',
                        'email' => 'بريد إلكتروني',
                        'numeric' => 'رقمي',
                        'integer' => 'عدد صحيح',
                        'password' => 'كلمة المرور',
                        'tel' => 'هاتف',
                        'url' => 'رابط',
                        'color' => 'لون',
                    ],
                ],
            ],

            'resource' => [
                'title' => 'المورد',

                'fields' => [
                    'resource' => 'المورد',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code' => 'الرمز',
            'name' => 'الاسم',
            'type' => 'النوع',
            'resource' => 'المورد',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'groups' => [
        ],

        'filters' => [
            'type' => 'النوع',
            'resource' => 'المورد',

            'type-options' => [
                'text' => 'إدخال نص',
                'textarea' => 'منطقة نصية',
                'select' => 'اختيار',
                'checkbox' => 'مربع اختيار',
                'radio' => 'زر راديو',
                'toggle' => 'تبديل',
                'checkbox-list' => 'قائمة مربعات اختيار',
                'datetime' => 'منتقي التاريخ والوقت',
                'editor' => 'محرر نص غني',
                'markdown' => 'محرر ماركداون',
                'color' => 'منتقي اللون',
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الحقل',
                    'body' => 'تم استعادة الحقل بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الحقل',
                    'body' => 'تم حذف الحقل بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الحقل نهائياً',
                    'body' => 'تم حذف الحقل نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الحقول',
                    'body' => 'تم استعادة الحقول بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الحقول',
                    'body' => 'تم حذف الحقول بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الحقول نهائياً',
                    'body' => 'تم حذف الحقول نهائياً بنجاح.',
                ],
            ],
        ],
    ],
];
