# خطة تطوير: نظام إدارة الخزائن والحسابات البنكية

> تاريخ التحليل: أبريل 2026
> المشروع: AureusERP — Custom Cashbox Management

---

## 📊 تحليل الوضع الحالي (ما هو موجود في النظام)

### ✅ موجود بالفعل

| الميزة              | المكان في الكود                                          | الوصف                                  |
| ------------------- | -------------------------------------------------------- | -------------------------------------- |
| حسابات بنكية متعددة | `JournalResource` (type: bank) + `BankAccountResource`   | ينفع تضيف أكتر من حساب بنكي            |
| خزائن كاش متعددة    | `JournalResource` (type: cash)                           | كل Journal نوع cash هو خزينة مستقلة    |
| تسجيل المدفوعات     | `PaymentResource`                                        | تسجيل دفع من/لعميل باختيار الـ Journal |
| التسوية التلقائية   | `AccountFacade::reconcile()`                             | ربط الدفعة بالفاتورة تلقائياً          |
| هيستوري المعاملات   | `accounts_account_moves` + `accounts_account_move_lines` | كل عملية مسجلة كـ Move                 |
| حالات الدفع         | Draft → In Process → Paid / Cancelled                    | Lifecycle كامل                         |

### ❌ غير موجود (يحتاج تطوير)

| الميزة                    | الوصف                                       | التعقيد |
| ------------------------- | ------------------------------------------- | ------- |
| ربط موظف بخزينة           | تعيين Admin معين مسؤولاً عن Journal (خزينة) | متوسط   |
| طلب دفع (Payment Request) | العميل أو الموظف يطلب دفع → موظف يقبله      | مرتفع   |
| تحويل بين الخزائن         | من خزينة موظف → الخزينة الرئيسية مع موافقة  | مرتفع   |
| سير عمل الموافقة          | طلب → معلق → موافقة/رفض مع تسجيل المسؤول    | مرتفع   |

---

## 🏗️ المتطلبات التفصيلية

### 1. الخزائن المتعددة مع تعيين المسؤول

- كل Journal نوع `cash` يكون مرتبط بـ User (موظف مسؤول)
- الموظف يقدر يشوف ويدير خزينته فقط
- الأدمن الرئيسي يشوف كل الخزائن

### 2. طلب الدفع (Payment Request)

```
العميل/الموظف → ينشئ طلب دفع → يظهر عند الموظف المسؤول عن الخزينة
    ↓
الموظف يقبل → تسجيل الدفع في خزينته تلقائياً
    ↓
رصيد الخزينة يزيد
```

### 3. تحويل من الخزينة الفرعية للرئيسية

```
الموظف → ينشئ طلب تحويل (من خزينته → الرئيسية)
    ↓
المسؤول عن الخزينة الرئيسية → يقبل أو يرفض
    ↓
بالقبول → تسجيل عملية تحويل في كلا الخزينتين
```

### 4. الهيستوري (Audit Trail)

- كل طلب دفع مسجل بالتاريخ والمستخدم والحالة
- كل تحويل مسجل: من/إلى/مبلغ/مَن طلب/مَن قبل/تاريخ

---

## 📐 مخطط قاعدة البيانات المقترح

### جدول: `cashbox_journal_users` (ربط الموظف بالخزينة)

```sql
id
journal_id        FK → accounts_journals.id
user_id           FK → users.id
is_responsible    boolean  -- هو مسؤول الخزينة؟
created_at
updated_at
```

### جدول: `cashbox_payment_requests` (طلبات الدفع)

```sql
id
journal_id        FK → accounts_journals.id  -- الخزينة المستهدفة
partner_id        FK → partners_partners.id  -- العميل
requested_by      FK → users.id             -- مَن طلب
approved_by       FK → users.id NULL
amount            decimal(12,4)
currency_id       FK → currencies.id
status            enum: pending|approved|rejected|cancelled
move_id           FK → accounts_account_moves.id NULL  -- الفاتورة بعد القبول
notes             text NULL
approved_at       timestamp NULL
created_at
updated_at
```

### جدول: `cashbox_transfers` (التحويلات بين الخزائن)

```sql
id
from_journal_id   FK → accounts_journals.id
to_journal_id     FK → accounts_journals.id
requested_by      FK → users.id
approved_by       FK → users.id NULL
amount            decimal(12,4)
currency_id       FK → currencies.id
status            enum: pending|approved|rejected
from_move_id      FK → accounts_account_moves.id NULL
to_move_id        FK → accounts_account_moves.id NULL
notes             text NULL
approved_at       timestamp NULL
created_at
updated_at
```

---

## 🔧 خطة التطوير (مراحل)

### المرحلة الأولى: الأساسيات (ربط موظف بخزينة)

1. إضافة `responsible_user_id` على Journal (أو جدول pivot `cashbox_journal_users`)
2. تعديل `JournalResource` لعرض المسؤول
3. فلتر العرض حسب الصلاحية

### المرحلة الثانية: طلبات الدفع

1. إنشاء `CashboxPaymentRequest` موديل + migration
2. `CashboxPaymentRequestResource` في Filament
3. Action "اقبل" يؤدي إلى:
    - إنشاء `Invoice` أو `Payment` في `AccountFacade`
    - تسجيل move في الخزينة المستهدفة
4. API endpoint للعملاء إذا مطلوب

### المرحلة الثالثة: تحويل بين الخزائن

1. `CashboxTransfer` موديل + migration
2. `CashboxTransferResource` في Filament
3. Action "اقبل التحويل" (مسؤول الخزينة الرئيسية فقط)
4. عند القبول: إنشاء move من الخزينة المصدر وإيداع في الخزينة المستهدفة عبر `AccountFacade`

### المرحلة الرابعة: الهيستوري والتقارير

1. ربط Spatie Activity Log بالنماذج الجديدة
2. صفحة هيستوري لكل خزينة
3. تقرير ملخص: إجمالي الدخول/الخروج/الرصيد الحالي

---

## 🔗 الملفات ذات الصلة للرجوع إليها

| الملف                                                                | الغرض                                  |
| -------------------------------------------------------------------- | -------------------------------------- |
| `plugins/webkul/accounts/src/Models/Journal.php`                     | موديل الخزينة/الجورنال                 |
| `plugins/webkul/accounts/src/Models/Payment.php`                     | موديل الدفع                            |
| `plugins/webkul/accounts/src/Filament/Resources/JournalResource.php` | واجهة الجورنالات                       |
| `plugins/webkul/accounts/src/Filament/Resources/PaymentResource.php` | واجهة المدفوعات                        |
| `plugins/webkul/accounts/src/AccountManager.php`                     | منطق الحسابات                          |
| `plugins/webkul/accounts/src/Facades/Account.php`                    | AccountFacade (confirmMove, reconcile) |

---

## ⚡ ملاحظات تقنية مهمة

1. **AccountFacade**: مطلوب استخدامه لأي عملية محاسبية (`confirmMove`, `reconcile`)
2. **Journal type**: كل خزينة هي Journal نوع `cash` — لا تنشئ موديل منفصل للخزينة
3. **Move**: كل تحويل/دفع يُسجل كـ `Move` في `accounts_account_moves` للهيستوري التلقائي
4. **Filament Actions**: استخدم Actions مع تأكيد Modal لعمليات القبول/الرفض
5. **Policy**: استخدم Laravel Policies لتقييد من يقدر يقبل طلبات التحويل
