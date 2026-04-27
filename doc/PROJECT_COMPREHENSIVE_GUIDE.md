# 🏢 Aureus ERP - الدليل الشامل للمشروع

**تاريخ الإنشاء:** 27 أبريل 2026  
**الإصدار:** 1.3.0  
**البناء على:** Laravel 11 + Filament 5 + Livewire 3  
**الترخيص:** MIT

---

## 📑 جدول المحتويات

1. [نظرة عامة على المشروع](#نظرة-عامة)
2. [معمارية النظام](#معمارية-النظام)
3. [Plugins والموديولات](#plugins-والموديولات)
4. [دليل المستخدم](#دليل-المستخدم)
5. [دليل المطورين](#دليل-المطورين)
6. [Workflows والعمليات](#workflows-والعمليات)
7. [بنية قاعدة البيانات](#بنية-قاعدة-البيانات)
8. [APIs والتكامل](#apis-والتكامل)
9. [الأمان والصلاحيات](#الأمان-والصلاحيات)
10. [الإعدادات والتكوين](#الإعدادات-والتكوين)

---

## نظرة عامة

### ما هو Aureus ERP؟

Aureus ERP هو نظام تخطيط موارد المؤسسات (ERP) مفتوح المصدر مبني على Laravel بتصميم modular يسمح بالتخصيص الكامل.

### المميزات الأساسية

- 🏗️ **معمارية قابلة للتوسع**: نظام plugins متقدم
- 🔐 **آمن**: Filament Shield + Policies + ACL متكاملة
- 🌍 **متعدد اللغات**: دعم العربية والإنجليزية وغيرها
- 📱 **API-First**: REST APIs منفصلة للتطبيقات الخارجية
- 🧪 **قابل للاختبار**: PHPUnit + Pest testing
- ⚡ **سريع**: Laravel Octane support
- 💱 **دعم عملات متعددة**: الآن EGP كعملة أساسية

---

## معمارية النظام

### البنية الأساسية

```
aureuserp/
├── app/                           # كود التطبيق الأساسي
│   ├── Http/                      # Controllers, Requests, Resources
│   ├── Models/                    # Eloquent Models
│   ├── Policies/                  # Authorization Policies
│   └── Providers/                 # Service Providers
├── bootstrap/                     # Bootstrap files
│   ├── app.php                    # App configuration
│   └── providers.php              # Provider registration
├── config/                        # Configuration files
├── database/
│   ├── migrations/                # Migrations
│   ├── seeders/                   # Seeders
│   └── factories/                 # Model factories
├── plugins/webkul/               # Plugin directory
│   ├── support/                   # Support plugin (core)
│   ├── accounts/                  # Accounting module
│   ├── sales/                     # Sales module
│   ├── purchases/                 # Purchases module
│   ├── inventories/               # Inventory management
│   ├── employees/                 # HR management
│   ├── recruitments/              # Recruitment system
│   ├── projects/                  # Project management
│   └── ... (18+ more plugins)
├── resources/
│   ├── views/                     # Blade templates
│   ├── css/                       # CSS files (Tailwind 4)
│   └── js/                        # JavaScript files
├── routes/
│   ├── web.php                    # Web routes
│   ├── api.php                    # API routes
│   └── console.php                # Console commands
├── storage/                       # Storage directory
└── tests/                         # Test files
```

### معمارية الـ Plugins

كل plugin يتبع هذا الهيكل:

```
plugin-name/
├── src/
│   ├── Models/                    # Eloquent Models
│   ├── Filament/
│   │   ├── Resources/            # Filament Resources
│   │   ├── Clusters/             # Resource clusters
│   │   ├── Pages/                # Custom pages
│   │   └── Widgets/              # Dashboard widgets
│   ├── Http/
│   │   ├── Controllers/          # API Controllers
│   │   ├── Requests/             # Form requests
│   │   └── Resources/            # API Resources
│   ├── Services/                 # Business logic
│   ├── Traits/                   # Reusable traits
│   ├── Policies/                 # Authorization policies
│   ├── Events/                   # Events
│   └── PluginServiceProvider.php # Plugin registration
├── database/
│   ├── migrations/               # Plugin migrations
│   ├── seeders/                  # Plugin seeders
│   └── factories/                # Model factories
├── routes/
│   ├── api.php                   # API routes
│   └── web.php                   # Web routes
├── resources/
│   ├── lang/                     # Translations
│   └── views/                    # Blade templates
└── tests/                        # Plugin tests
```

### Lifecycle - دورة حياة Plugin

```
1. Plugin Registration
   ↓
2. ServiceProvider Boot
   ↓
3. Routes Registered
   ↓
4. Filament Resources Registered
   ↓
5. Events Listeners Registered
   ↓
6. Plugin Fully Active
```

---

## Plugins والموديولات

### Core Plugins (لا تُحذف)

#### 1. **Support Plugin** 🆘

- **المسؤول عن**: الإعدادات العامة، العملات، الدول، الشركات، الأنشطة
- **النماذج الرئيسية**:
    - `Currency` - إدارة العملات
    - `Country`, `State`, `City` - الموقع الجغرافي
    - `Company` - بيانات الشركة
    - `Calendar`, `ActivityType`, `ActivityPlan` - الأنشطة
- **الميزات**:
    - إدارة شاملة للعملات (حاليًا EGP كأساسية)
    - Exchange rates التاريخية
    - البيانات الجغرافية

#### 2. **Security Plugin** 🔐

- **المسؤول عن**: المستخدمين، الأدوار، الصلاحيات
- **النماذج الرئيسية**:
    - `User` - بيانات المستخدم
    - `Role`, `Permission` - إدارة الصلاحيات (Filament Shield)
- **الميزات**:
    - تسجيل الدخول الآمن
    - إدارة الأدوار والصلاحيات
    - Audit logging

#### 3. **Plugin Manager Plugin** 📦

- **المسؤول عن**: إدارة دورة حياة الـ Plugins
- **الميزات**:
    - تفعيل/تعطيل الـ plugins
    - إدارة الـ dependencies
    - Seeding الـ plugins

#### 4. **Payments Plugin** 💳

- **المسؤول عن**: إدارة الدفعات والمحافظ
- **النماذج الرئيسية**:
    - `Payment` - سجل الدفعات
    - `PaymentMethod` - طرق الدفع
    - `PaymentToken` - رموز الدفع
- **الميزات**:
    - تسجيل الدفعات
    - طرق دفع متعددة
    - التوفق المالي

#### 5. **Settings Plugin** ⚙️

- **المسؤول عن**: إعدادات التطبيق
- **النماذج الرئيسية**:
    - `Setting` - الإعدادات العامة
- **الميزات**:
    - إدارة إعدادات النظام
    - الإعدادات المخصصة

#### 6. **Website Plugin** 🌐

- **المسؤول عن**: الواجهة العامة والعملاء
- **النماذج الرئيسية**:
    - `Partner` - بيانات العملاء/الموردين
    - `CustomerAuth` - مصادقة العملاء
- **الميزات**:
    - واجهة عامة للعملاء
    - بوابة عملاء
    - API عام

#### 7. **WiFi Plugin** 📡

- **المسؤول عن**: إدارة حزم الإنترنت اللاسلكي
- **النماذج الرئيسية**:
    - `WifiPackage` - حزم WiFi
    - `WifiPurchase` - عمليات شراء WiFi
- **الميزات**:
    - إدارة حزم الإنترنت
    - تتبع الاشتراكات

---

### Installable Plugins (قابلة للحذف)

#### 📊 **Accounting Plugin** - إدارة الحسابات

**الهدف**: إدارة كاملة للحسابات المالية

**النماذج الرئيسية**:

- `Journal` - دفاتر اليوميات (مبيعات، مشتريات، بنك، نقد)
- `Account` - الحسابات المحاسبية
- `Move` / `MoveLine` - القيود المحاسبية
- `CurrencyRate` - أسعار الصرف
- `Reconcile` - التوفيق المحاسبي

**العمليات الرئيسية**:

1. تسجيل القيود اليومية
2. تسجيل الفواتير والدفعات
3. إعداد الأرصدة
4. التوفيق المحاسبي

**العلاقات**:

```
Account → Currency
Journal → Account, Company
Move → Journal, Company, Currency
MoveLine → Move, Account
CurrencyRate → Currency, Company
```

---

#### 💼 **Sales Plugin** - إدارة المبيعات

**الهدف**: إدارة دورة حياة المبيعات كاملة

**النماذج الرئيسية**:

- `Order` - أوامر المبيعات
- `OrderLine` - بنود الأوامر
- `Team` - فريق المبيعات
- `Quote` - عروض الأسعار (Quotation)

**الـ Workflows**:

```
تقديم عرض سعر
    ↓
قبول العرض
    ↓
إنشاء أمر مبيعات
    ↓
تأكيد الأمر
    ↓
إصدار فاتورة
    ↓
شحن المنتجات
    ↓
تسليم النهائي
    ↓
تسجيل الدفع
```

**المميزات**:

- إدارة عروض الأسعار
- تحويل العروض إلى أوامر
- إدارة فريق المبيعات
- تقارير المبيعات

---

#### 🛒 **Purchase Plugin** - إدارة الشراء

**الهدف**: إدارة دورة حياة الشراء

**النماذج الرئيسية**:

- `Requisition` / `PurchaseAgreement` - طلبات الشراء
- `RequisitionLine` - بنود الطلبات
- `VendorPrice` - أسعار الموردين
- `PurchaseOrder` - أوامر الشراء

**الـ Workflows**:

```
طلب شراء داخلي
    ↓
الموافقة على الطلب
    ↓
اختيار المورد
    ↓
إنشاء اتفاق شراء
    ↓
استقبال السلع
    ↓
التحقق من الجودة
    ↓
تسجيل الفاتورة
    ↓
سداد الدفعة
```

**المميزات**:

- إدارة متطلبات الشراء
- اتفاقيات شراء محددة المدة
- تسعير متعدد المستويات
- تتبع الاستقبال

---

#### 📦 **Inventories Plugin** - إدارة المخزون

**الهدف**: إدارة كاملة للمخزن والتخزين

**النماذج الرئيسية**:

- `Product` - المنتجات
- `ProductCategory` - فئات المنتجات
- `Warehouse` - المستودعات
- `Location` - مواقع التخزين داخل المستودع
- `Stock` / `StockMove` - حركات المخزون
- `OperationType` - أنواع العمليات (استقبال، استخراج، نقل)

**الـ Workflows**:

```
استقبال سلع
    ↓
وضع في مكان تخزين
    ↓
تسجيل المخزون
    ↓
عند البيع: سحب من المخزون
    ↓
تحديث الكمية المتاحة
    ↓
تنبيهات عند نقص المخزون
```

**المميزات**:

- إدارة متعددة المستودعات
- تتبع موقع المنتج بدقة
- عمليات نقل بين المستودعات
- تنبيهات المخزون المنخفض

---

#### 👥 **Employees Plugin** - إدارة الموارد البشرية

**الهدف**: إدارة شاملة للموظفين والموارد البشرية

**النماذج الرئيسية**:

- `Employee` - بيانات الموظفين
- `Department` - الأقسام
- `JobPosition` - المسميات الوظيفية
- `EmployeeJobPosition` - الموظف + المسمى الوظيفي
- `Calendar` - تقويم العمل
- `Skill` / `SkillLevel` - المهارات

**الميزات**:

- ملفات موظفين شاملة
- إدارة الأقسام والمسميات
- تقاويم العمل
- إدارة المهارات
- الأجازات (عبر Time-Off plugin)

**العلاقات**:

```
Employee → Department, JobPosition
Employee → Company
Employee → Calendar
EmployeeJobPosition → Employee, JobPosition
```

---

#### 🎯 **Recruitments Plugin** - إدارة الاستقطاب

**الهدف**: إدارة دورة حياة الاستقطاب من البداية للنهاية

**النماذج الرئيسية**:

- `JobPosition` - المناصب المطلوبة
- `Applicant` / `Candidate` - المتقدمون
- `Stage` - مراحل الاستقطاب
- `RefuseReason` - أسباب الرفض
- `Degree` - المؤهلات

**الـ Workflows**:

```
إعلان عن وظيفة
    ↓
استقبال الطلبات
    ↓
اختبار أولي
    ↓
مقابلة أولى
    ↓
اختبار تقني
    ↓
مقابلة نهائية
    ↓
قرار الموافقة/الرفض
    ↓
تعيين الموظف (إذا موافقة)
    ↓
إضافة للموظفين
```

**المميزات**:

- إدارة الوظائف المفتوحة
- تتبع المتقدمين
- مراحل الاستقطاب المرنة
- أسباب الرفض
- تحويل الموافقين إلى موظفين

---

#### 📋 **Projects Plugin** - إدارة المشاريع

**الهدف**: إدارة المشاريع والمهام

**النماذج الرئيسية**:

- `Project` - المشاريع
- `Task` - المهام
- `Stage` - مراحل المشروع
- `TaskStage` - مراحل المهام

**الميزات**:

- إنشاء وتتبع المشاريع
- تقسيم المشاريع إلى مهام
- تعيين المهام للموظفين
- مراحل العمل المرنة

---

#### 📲 **Partners Plugin** - إدارة الجهات (عملاء/موردين)

**الهدف**: إدارة موحدة للعملاء والموردين والشركاء

**النماذج الرئيسية**:

- `Partner` - بيانات الشريك
- `BankAccount` - الحسابات البنكية
- `Industry` - صناعات الشركات

**الميزات**:

- ملفات موحدة للشركاء
- بيانات البنك للتحويلات
- تصنيف حسب الصناعة

---

#### 💰 **Invoices Plugin** - إدارة الفواتير

**الهدف**: إنشاء وإدارة الفواتير

**النماذج الرئيسية**:

- `Invoice` - الفواتير
- `InvoiceLine` - بنود الفاتورة
- `InvoiceLineItem` - عناصر البند

**الـ Workflows**:

```
إنشاء فاتورة من أمر مبيعات
    ↓
إضافة بنود
    ↓
حساب الضرائب
    ↓
تأكيد الفاتورة
    ↓
إرسال للعميل
    ↓
تسجيل الدفع
```

---

#### ⏰ **Time-Off Plugin** - إدارة الأجازات

**الهدف**: إدارة أجازات وإجازات الموظفين

**النماذج الرئيسية**:

- `LeaveType` - أنواع الإجازات (سنوية، مرضية، إلخ)
- `LeaveAccrualPlan` - خطط استحقاق الإجازات
- `Leave` - طلبات الإجازات

**الميزات**:

- أنواع إجازات متعددة
- خطط استحقاق مرنة
- إدارة طلبات الإجازات

---

#### 📦 **Products Plugin** - إدارة المنتجات

**الهدف**: إدارة كاملة لبيانات المنتجات

**النماذج الرئيسية**:

- `Product` - المنتجات
- `ProductCategory` - الفئات
- `ProductCombination` - المتغيرات (مثل: ألوان، أحجام)
- `PriceList` - قوائم الأسعار

**الميزات**:

- إدارة متغيرات المنتج
- قوائم أسعار متعددة
- تصنيف المنتجات
- معلومات تفصيلية للمنتج

---

## دليل المستخدم

### 1. لوحة التحكم (Dashboard)

عند تسجيل الدخول تشاهد:

- ملخص المبيعات الحالية
- أحدث الأوامر
- الإشعارات المعلقة
- رسوم بيانية الأداء

### 2. إدارة المبيعات

#### إنشاء عرض سعر (Quotation)

```
القائمة الجانبية → Sales → Quotations
    ↓
انقر "New Quote"
    ↓
اختر العميل
    ↓
أضف المنتجات والكميات
    ↓
اضبط الأسعار والخصومات
    ↓
انقر "Save"
    ↓
انقر "Send" لإرسال للعميل
```

#### تحويل العرض إلى أمر مبيعات

```
عرض السعر المحفوظ
    ↓
انقر على القائمة "⋯"
    ↓
اختر "Convert to Sales Order"
    ↓
سيتم إنشاء أمر مبيعات جديد تلقائياً
```

#### إنشاء فاتورة

```
أمر المبيعات
    ↓
انقر "Create Invoice"
    ↓
تحقق من البيانات
    ↓
انقر "Post" لتأكيد
    ↓
الفاتورة جاهزة للإرسال للعميل
```

### 3. إدارة الشراء

#### إنشاء طلب شراء

```
القائمة الجانبية → Purchase → Requisitions
    ↓
انقر "New Requisition"
    ↓
أضف المنتجات المطلوبة
    ↓
اضبط الكميات
    ↓
انقر "Submit for Approval"
```

#### الموافقة والشراء

```
الطلب المقدم (حسب الصلاحيات)
    ↓
انقر "Approve"
    ↓
اختر المورد من قائمة الموردين
    ↓
انقر "Create Purchase Agreement"
    ↓
سيتم إنشاء اتفاق شراء
```

#### استقبال السلع

```
اتفاق الشراء
    ↓
انقر "Receive"
    ↓
أضف كميات الاستقبال
    ↓
تحقق من الجودة
    ↓
انقر "Confirm Receipt"
```

### 4. إدارة المخزون

#### عرض المخزون

```
Inventory → Products
    ↓
ستشاهد قائمة بكل المنتجات والكميات المتاحة
```

#### نقل بين مستودعات

```
Inventory → Stock Moves
    ↓
انقر "New Stock Move"
    ↓
اختر الـ source warehouse والـ destination
    ↓
أضف المنتجات والكميات
    ↓
انقر "Confirm"
```

#### عد المخزون الدوري

```
Inventory → Inventory Count
    ↓
انقر "New Inventory"
    ↓
حدد المستودع والمنتجات
    ↓
أدخل الكميات الفعلية
    ↓
انقر "Validate" للمقارنة والتسوية
```

### 5. إدارة الموارد البشرية

#### إضافة موظف جديد

```
Employees → Employees
    ↓
انقر "New Employee"
    ↓
ملئ البيانات الشخصية
    ↓
حدد القسم والمسمى الوظيفي
    ↓
انقر "Save"
```

#### طلب إجازة

```
Employees → My Leave Requests
    ↓
انقر "New Leave Request"
    ↓
اختر نوع الإجازة
    ↓
حدد التواريخ
    ↓
انقر "Submit"
    ↓
انتظر الموافقة من المدير
```

### 6. إدارة الاستقطاب

#### فتح وظيفة جديدة

```
Recruitments → Job Positions
    ↓
انقر "New Position"
    ↓
أدخل تفاصيل الوظيفة
    ↓
انقر "Save" و "Open Position"
```

#### تقييم المتقدمين

```
Recruitments → Applicants
    ↓
اختر المتقدم
    ↓
اقرأ السيرة الذاتية
    ↓
انقر على Stage الحالية
    ↓
انقر "Move to Next Stage" أو "Refuse"
```

#### تحويل إلى موظف

```
متقدم تم قبوله
    ↓
انقر "Hire"
    ↓
ستتم إضافته تلقائياً للموظفين
```

### 7. إدارة المشاريع

#### إنشاء مشروع

```
Projects → Projects
    ↓
انقر "New Project"
    ↓
أدخل التفاصيل
    ↓
انقر "Save"
```

#### إضافة مهام

```
المشروع المحفوظ
    ↓
انقر على "Tasks"
    ↓
انقر "New Task"
    ↓
أدخل تفاصيل المهمة
    ↓
عيّن المسؤول
    ↓
حدد التاريخ المستحق
```

### 8. التقارير والتحليلات

#### تقرير المبيعات

```
Sales → Reports
    ↓
اختر الفترة الزمنية
    ↓
الرسوم البيانية تظهر تلقائياً
    ↓
يمكنك تحميل كـ PDF أو Excel
```

#### تقرير المخزون

```
Inventory → Reports → Stock Valuation
    ↓
سيظهر قيمة المخزون الحالية بـ EGP
```

---

## دليل المطورين

### البيئة والإعدادات

#### متطلبات التطوير

```bash
PHP: 8.3+
Laravel: 11
MySQL/PostgreSQL/SQLite
Node.js: 18+
```

#### الإعدادات البيئية

```env
APP_NAME=eTech
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_CURRENCY=EGP              # العملة الأساسية

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aureuserp
DB_USERNAME=root
DB_PASSWORD=
```

#### التثبيت للمطورين

```bash
# نسخ المشروع
git clone https://github.com/aureuserp/aureuserp.git
cd aureuserp

# تثبيت المتطلبات
composer install
npm install

# إعداد البيئة
cp .env.example .env
php artisan key:generate

# إعداد قاعدة البيانات
php artisan migrate
php artisan db:seed

# تشغيل التطبيق
php artisan serve
npm run dev
```

### إنشاء Plugin جديد

#### الخطوات الأساسية

```bash
# استخدم Artisan command
php artisan make:plugin MyNewPlugin

# سيتم إنشاء هيكل Plugin كامل
```

#### هيكل Plugin

```
plugins/webkul/my-new-plugin/
├── src/
│   ├── Models/
│   │   └── MyModel.php
│   ├── Filament/
│   │   └── Resources/
│   │       └── MyModelResource.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── MyModelController.php
│   │   └── Requests/
│   │       └── MyModelRequest.php
│   ├── Services/
│   │   └── MyModelService.php
│   ├── Policies/
│   │   └── MyModelPolicy.php
│   └── MyPluginServiceProvider.php
├── database/
│   ├── migrations/
│   │   └── 2026_04_27_000000_create_my_models_table.php
│   ├── seeders/
│   │   └── MyModelSeeder.php
│   └── factories/
│       └── MyModelFactory.php
├── routes/
│   ├── api.php
│   └── web.php
├── resources/
│   ├── lang/
│   │   ├── en/
│   │   └── ar/
│   └── views/
└── tests/
    ├── Feature/
    └── Unit/
```

#### تسجيل Plugin

في `MyPluginServiceProvider.php`:

```php
<?php

namespace Webkul\MyNewPlugin;

use Illuminate\Support\ServiceProvider;

class MyPluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mynewplugin');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mynewplugin');
    }

    public function register(): void
    {
        // تسجيل services
    }
}
```

### إنشاء Filament Resource

#### مثال على Resource كامل

```php
<?php

namespace Webkul\MyNewPlugin\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use Webkul\MyNewPlugin\Models\MyModel;

class MyModelResource extends Resource
{
    protected static ?string $model = MyModel::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'My Plugin';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->maxLength(1000),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyModels::route('/'),
            'create' => Pages\CreateMyModel::route('/create'),
            'edit' => Pages\EditMyModel::route('/{record}/edit'),
        ];
    }
}
```

### إنشاء API Endpoint

#### مثال على API Controller

```php
<?php

namespace Webkul\MyNewPlugin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Webkul\MyNewPlugin\Models\MyModel;
use Webkul\MyNewPlugin\Http\Resources\MyModelResource;

class MyModelController extends Controller
{
    public function index(): JsonResponse
    {
        $models = MyModel::paginate(15);
        return response()->json(MyModelResource::collection($models));
    }

    public function show(MyModel $model): JsonResponse
    {
        return response()->json(new MyModelResource($model));
    }

    public function store(Request $request): JsonResponse
    {
        $model = MyModel::create($request->validated());
        return response()->json(new MyModelResource($model), 201);
    }

    public function update(Request $request, MyModel $model): JsonResponse
    {
        $model->update($request->validated());
        return response()->json(new MyModelResource($model));
    }

    public function destroy(MyModel $model): JsonResponse
    {
        $model->delete();
        return response()->json(null, 204);
    }
}
```

#### تسجيل الـ Routes

في `routes/api.php`:

```php
Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('my-models', MyModelController::class);
    });
});
```

### إنشاء Model

#### مثال على Model كامل

```php
<?php

namespace Webkul\MyNewPlugin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyModel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
```

### إنشاء Policy

#### مثال على Policy

```php
<?php

namespace Webkul\MyNewPlugin\Policies;

use Webkul\Security\Models\User;
use Webkul\MyNewPlugin\Models\MyModel;

class MyModelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_mynewplugin_mymodel');
    }

    public function view(User $user, MyModel $model): bool
    {
        return $user->can('view_mynewplugin_mymodel');
    }

    public function create(User $user): bool
    {
        return $user->can('create_mynewplugin_mymodel');
    }

    public function update(User $user, MyModel $model): bool
    {
        return $user->can('update_mynewplugin_mymodel');
    }

    public function delete(User $user, MyModel $model): bool
    {
        return $user->can('delete_mynewplugin_mymodel');
    }
}
```

### Testing

#### اختبار Feature

```php
<?php

namespace Webkul\MyNewPlugin\Tests\Feature;

use Tests\TestCase;
use Webkul\MyNewPlugin\Models\MyModel;

class MyModelTest extends TestCase
{
    public function test_can_list_models(): void
    {
        $models = MyModel::factory(3)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/my-models');

        $response->assertOk()
            ->assertJsonCount(3);
    }

    public function test_can_create_model(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/my-models', [
                'name' => 'Test',
                'description' => 'Test description',
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('my_models', ['name' => 'Test']);
    }
}
```

### Code Formatting والتنسيق

```bash
# استخدام Pint للتنسيق
vendor/bin/pint --dirty --format agent

# استخدام Rector للتحديث التلقائي
vendor/bin/rector process src/

# تشغيل الاختبارات
php artisan test

# التحقق من الأخطاء
php artisan tinker
```

---

## Workflows والعمليات

### 1. Workflow المبيعات الكامل

```
Step 1: العميل يطلب عرض سعر
  │
  ├─ إنشاء Quotation في Sales
  ├─ تضمين المنتجات والأسعار
  ├─ حساب الخصومات والضرائب
  └─ إرسال للعميل

Step 2: الموافقة على العرض
  │
  └─ العميل يوافق على العرض

Step 3: إنشاء أمر مبيعات
  │
  ├─ تحويل Quotation إلى Sales Order
  ├─ حفظ التفاصيل
  └─ إعلام المستودع بالشحن

Step 4: إعداد الشحن
  │
  ├─ التحقق من توفر المنتجات في المخزون
  ├─ سحب المنتجات من المستودع
  ├─ تحديث كمية المخزون
  └─ إعداد الشحنة

Step 5: إصدار الفاتورة
  │
  ├─ إنشاء Invoice من Order
  ├─ تضمين تفاصيل الشحن
  ├─ حساب الضرائب النهائية
  └─ تأكيد الفاتورة (Post)

Step 6: الشحن والتسليم
  │
  ├─ تسجيل رقم التتبع
  ├─ إرسال الفاتورة والمنتجات
  └─ تأكيد الاستقبال من العميل

Step 7: تسجيل الدفع
  │
  ├─ استقبال الدفعة
  ├─ تسجيل في نظام الدفع
  ├─ ربط بالفاتورة
  └─ تحديث الميزانية المحاسبية

Step 8: إغلاق الطلب
  │
  └─ الطلب مكتمل
```

### 2. Workflow الشراء الكامل

```
Step 1: تحديد الحاجة
  │
  ├─ إنشاء Requisition من قسم محدد
  ├─ تحديد المنتجات المطلوبة
  ├─ حساب الكمية المطلوبة
  └─ إرسال للموافقة

Step 2: الموافقة على الطلب
  │
  ├─ مدير المشتريات يراجع
  ├─ التحقق من الميزانية
  ├─ الموافقة أو الرفض
  └─ إخطار المتقدم

Step 3: اختيار المورد
  │
  ├─ البحث عن موردين متاحين
  ├─ مقارنة الأسعار والشروط
  ├─ اختيار أفضل عرض
  └─ التفاوض إن لزم الأمر

Step 4: إنشاء اتفاق شراء
  │
  ├─ تحويل الطلب إلى Purchase Agreement
  ├─ تحديد المورد والأسعار
  ├─ تحديد شروط الدفع والتسليم
  └─ إرسال للمورد

Step 5: استقبال السلع
  │
  ├─ استلام المنتجات
  ├─ فحص الجودة والكمية
  ├─ تسجيل الاستقبال في النظام
  ├─ تحديث المخزون
  └─ ترتيب السلع في المستودع

Step 6: معالجة الفاتورة
  │
  ├─ استقبال فاتورة المورد
  ├─ مطابقة مع الطلب والاستقبال (3-way matching)
  ├─ تسجيل الفاتورة
  └─ حساب الدين للمورد

Step 7: دفع الفاتورة
  │
  ├─ تحديد تاريخ الدفع المستحق
  ├─ معالجة الدفع
  ├─ تسجيل في الحسابات
  └─ إغلاق الفاتورة

Step 8: إنهاء العملية
  │
  └─ العملية مكتملة
```

### 3. Workflow الاستقطاب

```
Step 1: فتح وظيفة
  │
  ├─ إنشاء Job Position
  ├─ تحديد المسمى والمتطلبات
  ├─ اعتماد الميزانية
  └─ نشر الإعلان

Step 2: استقبال الطلبات
  │
  ├─ تسجيل طلبات جديدة
  ├─ مراجعة السير الذاتية
  └─ فلترة أولية

Step 3: المرحلة الأولى
  │
  ├─ اختبار كتابي أو أونلاين
  ├─ تقييم الإجابات
  └─ تصفية غير المؤهلين

Step 4: المقابلة الأولى
  │
  ├─ مقابلة هاتفية أو فيديو
  ├─ تقييم المهارات الأساسية
  └─ تقدير الملاءمة

Step 5: الاختبار التقني
  │
  ├─ اختبار عملي في التخصص
  ├─ تقييم المستوى التقني
  └─ تقدير الأداء

Step 6: المقابلة النهائية
  │
  ├─ مقابلة مع الإدارة العليا
  ├─ مناقشة الراتب والعقد
  └─ القرار النهائي

Step 7: القبول
  │
  ├─ إصدار عرض وظيفي
  ├─ موافقة المتقدم
  └─ تحضير الوثائق

Step 8: التحويل إلى موظف
  │
  ├─ تحويل من Applicant إلى Employee
  ├─ تسجيل البيانات الكاملة
  ├─ تعيين القسم والراتب
  └─ بداية العمل

Step 9: التدريب الأولي
  │
  └─ برنامج تدريب للموظف الجديد
```

### 4. Workflow إدارة المخزون

```
Step 1: استقبال سلع جديدة
  │
  ├─ فحص الكميات والأصناف
  ├─ التحقق من الجودة
  └─ إنشاء Stock Move استقبال

Step 2: إدخال المستودع
  │
  ├─ تحديد مكان التخزين
  ├─ تسجيل الموقع
  └─ تحديث المخزون الفعلي

Step 3: التخزين المنظم
  │
  ├─ ترتيب حسب الفئة
  ├─ وضع في رفوف مناسبة
  └─ تسهيل الوصول

Step 4: تتبع المخزون
  │
  ├─ مراقبة الكميات
  ├─ إنذارات الكمية المنخفضة
  └─ تنبيهات الصلاحية

Step 5: عند الطلب
  │
  ├─ سحب المنتج المطلوب
  ├─ تحديث الكمية المتاحة
  └─ إنشاء Stock Move خروج

Step 6: عد دوري
  │
  ├─ حصر شامل للمخزون
  ├─ مقارنة مع السجلات
  └─ تسوية الفروقات

Step 7: نقل بين المستودعات
  │
  ├─ إنشاء Stock Move نقل
  ├─ من المستودع الأول
  └─ إلى المستودع الثاني

Step 8: تقاويم المخزون
  │
  └─ تقارير دورية عن قيمة المخزون
```

---

## بنية قاعدة البيانات

### الجداول الأساسية

#### جدول Users

```sql
CREATE TABLE users (
    id INT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    is_active BOOLEAN,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### جدول Companies

```sql
CREATE TABLE companies (
    id INT PRIMARY KEY,
    name VARCHAR(255),
    tax_id VARCHAR(50),
    currency_id INT,
    is_active BOOLEAN,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### جدول Currencies

```sql
CREATE TABLE currencies (
    id INT PRIMARY KEY,
    code VARCHAR(3),              -- EGP, USD, EUR
    name VARCHAR(255),            -- Egyptian Pound
    symbol VARCHAR(10),           -- E£
    active BOOLEAN,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- القيم الأساسية:
INSERT INTO currencies VALUES (1, 'EGP', 'Egyptian Pound', 'E£', true, ...);
INSERT INTO currencies VALUES (2, 'USD', 'US Dollar', '$', false, ...);
```

#### جدول Products

```sql
CREATE TABLE products (
    id INT PRIMARY KEY,
    name VARCHAR(255),
    sku VARCHAR(100),
    category_id INT,
    price DECIMAL(10,2),
    currency_id INT,
    stock_qty INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id),
    FOREIGN KEY (currency_id) REFERENCES currencies(id)
);
```

#### جدول Orders

```sql
CREATE TABLE orders (
    id INT PRIMARY KEY,
    order_number VARCHAR(100),
    customer_id INT,
    order_date DATE,
    total_amount DECIMAL(12,2),
    currency_id INT,
    status ENUM('draft', 'confirmed', 'shipped', 'delivered'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES partners(id),
    FOREIGN KEY (currency_id) REFERENCES currencies(id)
);
```

#### جدول OrderLines

```sql
CREATE TABLE order_lines (
    id INT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    unit_price DECIMAL(10,2),
    line_total DECIMAL(12,2),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

#### جدول Invoices

```sql
CREATE TABLE invoices (
    id INT PRIMARY KEY,
    invoice_number VARCHAR(100),
    order_id INT,
    invoice_date DATE,
    subtotal DECIMAL(12,2),
    tax DECIMAL(12,2),
    total_amount DECIMAL(12,2),
    currency_id INT,
    status ENUM('draft', 'posted', 'paid', 'cancelled'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (currency_id) REFERENCES currencies(id)
);
```

#### جدول Employees

```sql
CREATE TABLE employees (
    id INT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(255),
    department_id INT,
    position_id INT,
    hire_date DATE,
    salary DECIMAL(12,2),
    is_active BOOLEAN,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (position_id) REFERENCES job_positions(id)
);
```

### العلاقات الرئيسية

```
User (1) ──── (M) Companies
  ├─ إنشاء وتعديل
  └─ تعيين للقطاعات

Company (1) ──── (M) Orders
  └─ كل أمر متعلق بشركة

Partner (1) ──── (M) Orders
  └─ عميل يمكن له عدة أوامر

Currency (1) ──── (M) Invoices
  └─ كل فاتورة بعملة

Product (1) ──── (M) OrderLines
  └─ كل منتج في عدة أوامر

Order (1) ──── (M) OrderLines
  └─ كل أمر له عدة بنود

Order (1) ──── (1) Invoice
  └─ كل أمر يحتوي فاتورة واحدة

Department (1) ──── (M) Employees
  └─ كل قسم له موظفين

JobPosition (1) ──── (M) Employees
  └─ كل مسمى وظيفي له موظفين
```

---

## APIs والتكامل

### التوثيق والمصادقة

#### باستخدام Sanctum (للموارد المحمية)

```bash
# 1. الحصول على Token
POST /api/v1/auth/login
{
    "email": "user@example.com",
    "password": "password"
}

# الرد:
{
    "token": "1|abcdefghijk...",
    "user": { ... }
}

# 2. استخدام Token في الطلبات
Authorization: Bearer 1|abcdefghijk...
```

### API Endpoints الأساسية

#### Sales Module

```
POST   /api/v1/orders                    # إنشاء أمر جديد
GET    /api/v1/orders                    # قائمة الأوامر
GET    /api/v1/orders/{id}              # تفاصيل أمر
PATCH  /api/v1/orders/{id}              # تعديل أمر
DELETE /api/v1/orders/{id}              # حذف أمر

POST   /api/v1/quotes                    # إنشاء عرض سعر
GET    /api/v1/quotes                    # قائمة العروض
GET    /api/v1/quotes/{id}              # تفاصيل عرض
```

#### Purchase Module

```
POST   /api/v1/requisitions              # إنشاء طلب شراء
GET    /api/v1/requisitions              # قائمة الطلبات
GET    /api/v1/requisitions/{id}        # تفاصيل طلب
PATCH  /api/v1/requisitions/{id}        # تعديل طلب

POST   /api/v1/purchase-agreements       # اتفاق شراء
GET    /api/v1/purchase-agreements       # قائمة الاتفاقيات
```

#### Inventory Module

```
POST   /api/v1/products                  # إنشاء منتج
GET    /api/v1/products                  # قائمة المنتجات
GET    /api/v1/products/{id}            # تفاصيل منتج
PATCH  /api/v1/products/{id}            # تعديل منتج

POST   /api/v1/stock-moves               # حركة مخزون
GET    /api/v1/stock-moves               # قائمة الحركات
```

#### Employees Module

```
POST   /api/v1/employees                 # إضافة موظف
GET    /api/v1/employees                 # قائمة الموظفين
GET    /api/v1/employees/{id}           # تفاصيل موظف
PATCH  /api/v1/employees/{id}           # تعديل موظف
```

### مثال على الطلب والرد

```bash
# الطلب
curl -X POST https://aureuserp.test/api/v1/orders \
  -H "Authorization: Bearer token" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "order_date": "2026-04-27",
    "lines": [
      {
        "product_id": 1,
        "quantity": 5,
        "unit_price": 100.00
      }
    ]
  }'

# الرد
{
    "data": {
        "id": 1,
        "order_number": "ORD-001",
        "customer": {
            "id": 1,
            "name": "ABC Company"
        },
        "total_amount": 500.00,
        "currency": {
            "id": 1,
            "code": "EGP",
            "name": "Egyptian Pound"
        },
        "status": "confirmed",
        "created_at": "2026-04-27T10:30:00Z"
    }
}
```

---

## الأمان والصلاحيات

### نموذج الأمان

#### Filament Shield Integration

```
Admin Panel ← Filament Shield
    ├─ Roles (إدارة الأدوار)
    ├─ Permissions (الصلاحيات المحددة)
    └─ Policy Checks (التحقق من السياسات)
```

#### الصلاحيات الأساسية

```
// مثال: صلاحيات الفاتورة
create_invoices_invoice
view_invoices_invoice
view_any_invoices_invoice
update_invoices_invoice
delete_invoices_invoice
delete_any_invoices_invoice
```

#### إنشاء Policy

```php
// في PluginServiceProvider أو Provider
Gate::policy(Invoice::class, InvoicePolicy::class);

// ثم في InvoicePolicy
public function view(User $user, Invoice $invoice): bool
{
    return $user->can('view_invoices_invoice');
}
```

#### التحقق من الصلاحيات

```php
// في Controller
$this->authorize('view', $invoice);

// أو في Blade
@can('view', $invoice)
    <!-- عرض الفاتورة -->
@endcan
```

### مستويات الوصول

```
Super Admin
  └─ الوصول الكامل لكل الأنظمة

Admin
  ├─ إدارة المستخدمين والأدوار
  ├─ الإعدادات العامة
  └─ التقارير

Manager
  ├─ إدارة الأقسام
  ├─ مراجعة الطلبات
  └─ الموافقات

Employee
  ├─ إنشاء الطلبات
  ├─ عرض البيانات الشخصية
  └─ الأنشطة المخصصة

Customer (من Website)
  ├─ عرض الأوامر
  ├─ الفواتير
  └─ تحميل الوثائق
```

---

## الإعدادات والتكوين

### متغيرات البيئة الأساسية

```env
# الأساسيات
APP_NAME=eTech
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Africa/Cairo
APP_CURRENCY=EGP                # ⭐ تم تحديثها إلى EGP

# قاعدة البيانات
DB_CONNECTION=mysql
DB_HOST=db.example.com
DB_PORT=3306
DB_DATABASE=aureuserp
DB_USERNAME=erp_user
DB_PASSWORD=secure_password

# البريد
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=noreply@example.com
MAIL_PASSWORD=mail_password

# التطبيق
APP_KEY=base64:...
APP_URL=https://aureuserp.com

# Filament
FILAMENT_TIMEZONE=Africa/Cairo
```

### تثبيت العملة EGP

العملة الأساسية الآن معيّنة على **EGP** (جنيه مصري):

1. ✅ `APP_CURRENCY=EGP` في ملف `.env`
2. ✅ `config/app.php` يرجع `EGP` كقيمة افتراضية
3. ✅ السيدرز تفعّل عملة EGP تلقائياً عند الـ migration
4. ✅ جميع الحقول النقدية تستخدم `EGP` كعملة أساسية

---

## الخلاصة والملاحظات المهمة

### النقاط الحاسمة

1. **معمارية Plugin**: كل وظيفة في plugin منفصل يسهل صيانته وتطويره
2. **الأمان**: كل عملية محمية بـ policies وصلاحيات
3. **العملات**: العملة الأساسية الآن **EGP** في كل العمليات المالية
4. **الـ APIs**: جميع الوحدات متاحة عبر REST APIs
5. **الـ Workflows**: كل عملية لها workflow محدد يسهل التتبع

### الملفات والمجلدات المهمة للتطوير

```
المهمة جداً:
├── config/app.php               # إعدادات التطبيق
├── bootstrap/app.php            # تسجيل الـ middleware
├── bootstrap/providers.php       # Service providers
├── plugins/webkul/              # جميع الـ plugins
├── routes/api.php               # API routes
├── database/migrations/          # Migrations أساسية
└── database/seeders/            # Seeders أساسية
```

### أوامر مهمة للتطوير

```bash
# تشغيل الـ migrations
php artisan migrate
php artisan migrate:fresh

# تشغيل الـ seeders
php artisan db:seed
php artisan db:seed --class=DatabaseSeeder

# إنشاء plugin جديد
php artisan make:plugin PluginName

# إنشاء model مع migration
php artisan make:model PluginName/Models/ModelName -m

# تشغيل الاختبارات
php artisan test

# تنسيق الكود
vendor/bin/pint --dirty --format agent

# Tinker للاختبار السريع
php artisan tinker
```

### الاتصالات بين الأجزاء

```
Events → Listeners → Services → Policies → APIs
  ↓
Filament Resources
  ↓
Web Routes (لو وجدت)
  ↓
Database (Models, Migrations)
```

---

## المراجع والروابط

- **GitHub الرسمي**: https://github.com/aureuserp/aureuserp
- **دليل المطورين**: https://devdocs.aureuserp.com/
- **دليل المستخدمين**: https://docs.aureuserp.com/
- **Laravel Documentation**: https://laravel.com/docs/11
- **Filament Documentation**: https://filamentphp.com/docs

---

**آخر تحديث**: 27 أبريل 2026  
**الإصدار**: 1.3.0  
**العملة الأساسية**: EGP (جنيه مصري) ✅

---

_ملف شامل لكل جوانب Aureus ERP - للمطورين والمستخدمين_
