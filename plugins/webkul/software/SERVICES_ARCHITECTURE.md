# Software Plugin - Business Logic Services

## Overview

هذا الملف يوضح البنية الجديدة للـ Business Logic في Software Plugin. تم فصل منطق الأعمال عن واجهة المستخدم لتحسين قابلية إعادة الاستخدام والاختبار.

## Services المُنفذة

### 1. LicenseManager (الموصل الرئيسي)

**الموقع:** `plugins/webkul/software/src/Services/LicenseManager.php`

المسؤول عن:

- `billLicense()` - فاتورة ترخيص جديد
- `renewLicense()` - تجديد ترخيص موجود
- `activateLicense()` - تفعيل ترخيص
- `deactivateLicense()` - تعطيل ترخيص
- `expireLicense()` - انتهاء صلاحية ترخيص

```php
$manager = app(LicenseManager::class);
$result = $manager->billLicense($license, $editionId, 'annual');
// Returns: ['license' => License, 'invoice' => LicenseInvoice, 'invoiceNumber' => string]
```

### 2. SubscriptionManager (إدارة الاشتراكات)

**الموقع:** `plugins/webkul/software/src/Services/SubscriptionManager.php`

المسؤول عن:

- `createForLicense()` - إنشاء اشتراكات جديدة
- `renewForLicense()` - تجديد الاشتراكات
- `expireForLicense()` - انتهاء صلاحية الاشتراكات
- `suspendForLicense()` - تعليق الاشتراكات
- `reactivateForLicense()` - تفعيل الاشتراكات

### 3. LicenseInvoiceManager (إدارة الفواتير)

**الموقع:** `plugins/webkul/software/src/Services/LicenseInvoiceManager.php`

المسؤول عن:

- `createInvoice()` - إنشاء فاتورة محاسبية
- `createAccountMove()` - إنشاء حركة محاسبية (AccountMove)
- `createEditionInvoiceLine()` - إضافة خط الإصدار
- `createFeatureInvoiceLines()` - إضافة خطوط المميزات
- `getSalesJournal()` - الحصول على دفتر المبيعات

## الدمج في Filament

### قبل (الطريقة القديمة):

```php
Action::make('billLicense')
    ->action(function (License $record, array $data): void {
        // 270+ سطر من الكود المباشر!
        try {
            $result = DB::transaction(function () use ($record, $data, $company): array {
                // كل شيء هنا...
                return ['invoiceNumber' => ..., 'accountMove' => ...];
            });
            // الإشعار
        } catch (\Throwable $exception) {
            // التعامل مع الأخطاء
        }
    })
```

### بعد (الطريقة الجديدة):

```php
Action::make('billLicense')
    ->action(function (License $record, array $data): void {
        try {
            $result = app(LicenseManager::class)->billLicense(
                $record,
                (int) $data['edition_id'],
                (string) $data['license_plan']
            );

            Notification::make()
                ->title('Invoice created successfully')
                ->body('Invoice No: ' . $result['invoiceNumber'])
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            Notification::make()
                ->title('Failed to create invoice')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    })
```

## الفوائد

| الميزة                     | التفصيل                                     |
| -------------------------- | ------------------------------------------- |
| **Reusability**            | استخدم نفس الـ Service من API, Cron, Events |
| **Testability**            | Unit test كل service بشكل مستقل             |
| **Maintainability**        | تعديل البرامج في مكان واحد                  |
| **Separation of Concerns** | UI منفصل عن Business Logic                  |
| **Error Handling**         | معالجة مركزية للأخطاء                       |

## استخدام APIs المستقبلي

```php
// في API Controller
$manager = app(LicenseManager::class);
$result = $manager->billLicense($license, $editionId, 'monthly');

response()->json([
    'success' => true,
    'license' => $result['license'],
    'invoice_number' => $result['invoiceNumber'],
]);
```

## الـ Transactions

جميع العمليات محمية بـ Database transactions:

```php
// في LicenseManager::billLicense()
return DB::transaction(function () use ($license, $editionId, $licensePlan) {
    // تحديث License
    // إنشاء Invoice
    // إنشاء Subscriptions
    // كل هذا يتم معاً أو لا شيء
});
```

## المرحلة التالية

بعد اختبار هذه الـ Services، يمكن:

1. بناء REST API سادسة للترخيص
2. إنشاء Scheduled Jobs للتجديد التلقائي
3. إضافة Events و Listeners
4. بناء Customer Portal

---

**تاريخ الإنشاء:** 2026-04-15  
**الإصدار:** 1.0
