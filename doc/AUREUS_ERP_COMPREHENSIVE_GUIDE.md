# Aureus ERP - ملخص شامل تفصيلي

> **آخر تحديث**: مارس 2026  
> **الإصدار**: 1.3.0  
> **الترخيص**: MIT  
> **المستودعات**:
>
> - [Dev Docs](https://devdocs.aureuserp.com/)
> - [User Guide](https://docs.aureuserp.com/)
> - [GitHub](https://github.com/aureuserp/aureuserp)

---

## 📖 جدول المحتويات

1. [مقدمة عن Aureus ERP](#مقدمة-عن-aureus-erp)
2. [معمارية النظام](#معمارية-النظام)
3. [المتطلبات والتثبيت](#المتطلبات-والتثبيت)
4. [الـ Plugins والـ Modules](#الـ-plugins-والـ-modules)
5. [بنية قاعدة البيانات](#بنية-قاعدة-البيانات)
6. [Frontend Stack والواجهات](#frontend-stack-والواجهات)
7. [الـ Admin و Customer Panels](#الـ-admin-و-customer-panels)
8. [دليل المستخدم والـ Features](#دليل-المستخدم-والـ-features)
9. [العمليات والـ Workflows](#العمليات-والـ-workflows)
10. [التطوير والـ APIs](#التطوير-والـ-apis)

---

## مقدمة عن Aureus ERP

### ما هو Aureus ERP؟

**Aureus ERP** هو نظام تخطيط الموارد المؤسسية (Enterprise Resource Planning) مفتوح المصدر، مبني على **Laravel** و**FilamentPHP**، ويوفر حلاً شاملاً وقابلاً للتوسع للشركات الصغيرة والمتوسطة والكبيرة.

### المميزات الرئيسية

#### 🏗️ البنية المعمارية

- **مفتوح المصدر**: كود شفاف قابل للتخصيص والتعديل
- **معمارية قائمة على الـ Plugins**: كل ميزة مغلفة في plugin منفصل قابل للتثبيت والحذف
- **قابل للتوسع**: يمكن إضافة plugins جديدة دون تعديل الكود الأساسي
- **Event-Driven**: يستخدم نظام الأحداث لربط العمليات

#### 🛠️ تقنيات حديثة

- **Backend**: Laravel 11.x مع PHP 8.2+
- **Admin Panel**: FilamentPHP 5.x
- **Frontend**: Livewire 3 مع Alpine.js و Tailwind CSS 4
- **Database**: MySQL 8.0+ أو SQLite
- **Build Tool**: Vite

#### 📊 الـ Modules المتاحة

- 📌 **إدارة المشاريع** (Projects)
- 👥 **إدارة جهات الاتصال** (Contacts)
- 🛒 **إدارة المبيعات** (Sales)
- 📦 **إدارة المشتريات** (Purchases)
- 💰 **إدارة الفواتير والمدفوعات** (Invoices & Payments)
- 📊 **إدارة المخزون والمستودعات** (Inventory & Warehouse)
- 👨‍💼 **إدارة الموارد البشرية** (Employees & HR)
- 🎓 **إدارة التوظيف** (Recruitment)
- 📝 **إدارة الإجازات** (Time Off)
- 📚 **إدارة المحتوى والمدونات** (Website & Blogs)
- 💼 **إدارة العلاقات مع الشركاء** (Partners)
- 💾 **إدارة الحسابات** (Accounts)
- 📈 **الإحصائيات والتقارير** (Analytics)
- 💬 **التواصل الداخلي** (Chatter)

---

## معمارية النظام

### البنية العامة

```
Aureus ERP
├── Core Foundation (Laravel 11)
├── Admin Panel (FilamentPHP 5)
├── Frontend (Livewire 3 + Alpine.js + Tailwind CSS 4)
├── Database Layer (Eloquent ORM)
└── Plugin System (Modular Architecture)
```

### مبادئ المعمارية

#### 1. المعمارية القائمة على الـ Plugins

```
┌─────────────────────────────────────┐
│      Aureus ERP Core System         │
│                                     │
│  ┌────────┐ ┌────────┐ ┌────────┐ │
│  │Plugin 1│ │Plugin 2│ │Plugin 3│ │
│  └────────┘ └────────┘ └────────┘ │
│                                     │
│  ┌─────────────────────────────┐   │
│  │    Plugin Manager           │   │
│  │ - Install/Uninstall        │   │
│  │ - Register Resources        │   │
│  │ - Load Dependencies         │   │
│  └─────────────────────────────┘   │
└─────────────────────────────────────┘
```

#### 2. العمليات المدفوعة بالأحداث (Event-Driven)

- كل plugin يستطيع تسجيل listeners لأحداث معينة
- تسلسل العمليات يتم من خلال الأحداث بدلاً من الاستدعاءات المباشرة
- مرونة في التكامل دون تعديل الكود الأساسي

#### 3. فصل الاهتمامات (Separation of Concerns)

```
Models (Database) → Resources (API/UI) → Filament (Admin Panel)
                        ↓
                    Controllers
                        ↓
                    Business Logic
```

---

## المتطلبات والتثبيت

### 1. متطلبات الخادم

| المكون       | الإصدار الأدنى   | الملاحظات               |
| ------------ | ---------------- | ----------------------- |
| **OS**       | Linux / Windows  | مع WSL للـ Windows أفضل |
| **Server**   | Apache 2 / NGINX | موصى به NGINX           |
| **PHP**      | 8.2 +            | 8.3+ مدعوم              |
| **MySQL**    | 8.0+             | SQLite بديل             |
| **Node.js**  | 18+ LTS          | للـ Frontend Build      |
| **Composer** | Latest           | Package Manager للـ PHP |
| **RAM**      | 4GB+             | 8GB+ موصى به            |
| **Disk**     | 2GB+             | للتثبيت والـ Storage    |

### 2. متطلبات PHP Extensions

```php
- php-intl       // دعم التدويل والـ Localization
- php-gd         // معالجة الصور
- php-xml        // معالجة الـ XML
- php-json       // دعم الـ JSON
- php-ctype      // فحص أنواع الأحرف
- php-tokenizer  // معالجة الـ Code Tokens
- php-mbstring   // دعم Unicode
- php-openssl    // التشفير والـ SSL
- php-pdo        // قاعدة البيانات
```

### 3. تكوين PHP

```ini
memory_limit = 4G
max_execution_time = 360
date.timezone = Asia/Riyadh  ; حسب المنطقة الزمنية
upload_max_filesize = 100M
post_max_size = 100M
```

### 4. خطوات التثبيت

#### الطريقة الأولى: التثبيت التقليدي

```bash
# 1. استنساخ المستودع
git clone https://github.com/aureuserp/aureuserp.git
cd aureuserp

# 2. تثبيت المتعلقات
composer install
npm install

# 3. نسخ ملف البيئة
cp .env.example .env

# 4. تكوين قاعدة البيانات في .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aureuserp
DB_USERNAME=root
DB_PASSWORD=password

# 5. توليد مفتاح التشفير
php artisan key:generate

# 6. تشغيل البرنامج التثبيتي
php artisan erp:install

# 7. بناء الـ Frontend Assets
npm run build

# 8. تشغيل الخادم
php artisan serve
```

#### الطريقة الثانية: استخدام Docker

```bash
# 1. سحب الـ Image
docker pull webkul/aureuserp:latest

# 2. تشغيل الـ Container
docker run -itd \
  -p 80:80 \
  -p 3306:3306 \
  --name aureuserp \
  webkul/aureuserp:latest

# 3. التحقق من التشغيل
docker ps

# 4. الوصول إلى التطبيق
http://localhost

# بيانات الدخول الافتراضية:
# Email: admin@example.com
# Password: admin@123
```

### 5. بيانات الدخول الافتراضية

| النوع           | URL                      | Email             | كلمة المرور |
| --------------- | ------------------------ | ----------------- | ----------- |
| Admin Panel     | `http://localhost/admin` | admin@example.com | admin@123   |
| Customer Portal | `http://localhost/`      | -                 | -           |

---

## الـ Plugins والـ Modules

### تصنيفات الـ Plugins

#### 1️⃣ Core Plugins (مدمجة - لا يمكن حذفها)

| الـ Plugin     | الوصف                     | الميزات                                 |
| -------------- | ------------------------- | --------------------------------------- |
| **Security**   | إدارة الأدوار والصلاحيات  | Role-based permissions, User management |
| **Analytics**  | التحليلات والتقارير       | Dashboards, KPIs, Charts                |
| **Chatter**    | نظام التواصل الداخلي      | Messaging, Notifications, Activity Logs |
| **Fields**     | إدارة الحقول المخصصة      | Custom Fields, Dynamic Forms            |
| **Partners**   | إدارة العلاقات مع الشركاء | Partner Management                      |
| **Support**    | نظام الدعم                | Help Desk, Documentation                |
| **Table View** | إدارة عرض الجداول         | List Views, Filters, Sorting            |

#### 2️⃣ Installable Plugins (اختيارية)

##### 📊 المحاسبة والمالية

**Accounts (قاعدة المحاسبة)**

```
الدور: طبقة أساسية للمحاسبة
المسؤوليات:
├── Chart of Accounts (الدليل المحاسبي)
├── Journals & Ledgers (اليوميات والـ Ledgers)
├── Currency Management (إدارة العملات)
└── Fiscal Configurations (التكوينات المالية)

العلاقات:
└─→ يتم استخدامها من قبل Accounting و Invoice و Payment
```

**Accounting (محرك المحاسبة)**

```
الدور: تنفيذ العمليات المحاسبية الفعلية
المسؤوليات:
├── Journal Entries (قيود اليومية)
├── Financial Reports
│   ├── Profit & Loss
│   ├── Balance Sheet
│   └── Trial Balance
├── Period Closing
├── Adjustments
└── Audit Trails

المتطلبات:
└─→ يعتمد على Accounts Plugin
```

**Invoices (الفواتير)**

```
الدول: إدارة الفواتير والإيصالات
المسؤوليات:
├── Customer Invoices (فواتير البيع)
├── Vendor Bills (فواتير الشراء)
├── Recurring Invoices (الفواتير المتكررة)
├── Multi-currency Support
├── Tax Management
└── Payment Tracking

الأطفال:
├── → Payments (الدفع)
├── → Accounts (للمحاسبة)
└── → Products (الـ Items)
```

**Payments (الدفع)**

```
الدول: إدارة الدفعات والسلام
المسؤوليات:
├── Payment Methods
│   ├── Cash
│   ├── Bank Transfer
│   ├── Check
│   └── Online
├── Payment Tracking
├── Reconciliation
└── Payment History

التكاملات:
├── → Invoices
├── → Accounts
└── → Bank Integration
```

##### 🛒 المبيعات والشراء

**Sales (إدارة المبيعات)**

```
الدول: إدارة عملية البيع من البداية إلى النهاية
العمليات الرئيسية:
├── Lead Management (إدارة العملاء المحتملين)
├── Quotations (عروض الأسعار)
├── Sales Orders (طلبات البيع)
├── Invoicing (الفواتير)
└── Commission Tracking (تتبع العمولات)

المخرجات:
└─→ Sales Invoices تُرسل إلى Invoices Plugin
```

**Purchases (إدارة الشراء)**

```
الدول: إدارة عملية الشراء من الطلب إلى الاستقبال
العمليات الرئيسية:
├── Request for Quotation (طلب عروض أسعار)
├── Purchase Orders (طلبات الشراء)
├── Purchase Agreements (اتفاقيات الشراء)
├── Goods Receipt (استلام البضائع)
└── Vendor Management (إدارة الموردين)

المخرجات:
└─→ Bills تُرسل إلى Invoices Plugin
```

##### 📦 المخزون والمستودعات

**Products (إدارة المنتجات)**

```
الدول: كتالوج المنتجات والخدمات
البيانات المخزنة:
├── Product Details
│   ├── SKU
│   ├── Name & Description
│   ├── Pricing
│   ├── Categories
│   └── Images
├── Variants
├── Attributes
└── Inventory Links

التكاملات:
├── → Sales
├── → Purchases
└── → Inventory
```

**Inventory (إدارة المخزون)**

```
الدول: تتبع المخزون في الوقت الفعلي
المسؤوليات:
├── Stock Levels (مستويات المخزون)
├── Stock Transfers (تحويل المخزون)
├── Stock Adjustments (تعديلات المخزون)
├── Warehouse Management (إدارة المستودعات)
├── Lot/Serial Numbers (متتبع اللفائف والأرقام)
├── Reorder Points (نقاط إعادة الطلب)
└── Stock Valuation (تقييم المخزون)

الأحداث المهمة:
├── Stock Changed
├── Low Stock Warning
├── Stock Transferred
└── Reorder Required
```

##### 👥 الموارد البشرية

**Employees (إدارة الموظفين)**

```
الدول: ملفات الموظفين الشاملة
البيانات:
├── Personal Information
│   ├── Name, DOB, Gender
│   ├── Address, Contact Details
│   └── Family Information
├── Work Information
│   ├── Department
│   ├── Job Position
│   ├── Manager
│   ├── Working Hours
│   └── Work Location
├── Skills & Certifications
├── Education
├── Work Permit/Visa
└── Bank Details

التكاملات:
├── → Timesheets
├── → Time Off
└── → Recruitment
```

**Timesheets (تسجيل الساعات)**

```
الدول: تتبع ساعات عمل الموظفين
المسؤوليات:
├── Daily/Weekly Time Logging
├── Project Time Allocation
├── Overtime Tracking
├── Timesheet Approval
└── Export for Payroll

البيانات:
├── Employee
├── Date
├── Hours Worked
├── Project (Optional)
├── Task (Optional)
└── Description
```

**Time Off (إدارة الإجازات)**

```
الدول: إدارة الإجازات والإجازات المرضية
المسؤوليات:
├── Leave Request Management
├── Approval Workflows
├── Leave Balance Tracking
├── Leave Types (Annual, Sick, etc.)
├── Leave Policies
└── Attendance Integration

الأنواع:
├── Annual Leave
├── Sick Leave
├── Unpaid Leave
├── Sabbatical
└── Other
```

**Recruitment (إدارة التوظيف)**

```
الدول: إدارة عملية التوظيف الكاملة
المسؤوليات:
├── Job Posting
├── Application Tracking
├── Candidate Management
├── Interview Scheduling
├── Offer Management
└── Onboarding

مراحل التقديم:
├── Application Received
├── Initial Screening
├── Interview Round 1
├── Interview Round 2
├── Offer Extended
├── Offer Accepted
└── Onboarding Complete
```

##### 🏗️ إدارة المشاريع

**Projects (إدارة المشاريع)**

```
الدول: إدارة المشاريع والمهام والفريق
المسؤوليات:
├── Project Creation & Configuration
├── Task Management
│   ├── Create Tasks
│   ├── Assign to Team Members
│   ├── Set Deadlines
│   └── Track Progress
├── Sub-tasks
├── Milestones (اختياري)
├── Gantt Charts
├── Time Tracking (مع Timesheets)
├── Resource Allocation
└── Collaboration Tools (مع Chatter)

مراحل المهام:
├── In Progress
├── Change Requested
├── Approved
├── Cancelled
└── Done

العلاقات:
├── → Timesheets (لتتبع الوقت)
├── → Employees (تعيين الفريق)
└── → Contacts (العملاء)
```

##### 📋 إدارة جهات الاتصال

**Contacts (جهات الاتصال)**

```
الدول: مركز إدارة جهات الاتصال المركزي
أنواع الجهات:
├── Customers (العملاء)
├── Vendors (الموردون)
├── Partners (الشركاء)
└── Employees (الموظفون)

البيانات المخزنة:
├── General Information
│   ├── Name/Company Name
│   ├── Email & Phone
│   ├── Website
│   ├── Tax ID
│   └── Tags
├── Addresses (متعددة)
│   ├── Type: Permanent, Present, Invoice, Delivery, Other
│   └── Complete Address
├── Sub-Contacts (جهات فرعية)
└── Sales & Purchase Info

التكاملات:
├── → Sales & Purchases
├── → Projects
├── → Invoices
└── → CRM
```

##### 🌐 إدارة الموقع والمحتوى

**Website (إدارة الموقع)**

```
الدول: نظام إدارة المحتوى (CMS)
المسؤوليات:
├── Page Management
├── Blog Management
├── Product Catalog
├── Customer Management
├── SEO Optimization
└── Website Settings

المميزات:
├── Drag & Drop Builder
├── Template Support
├── Multi-language
├── Category Management
└── Publishing Workflow
```

**Blogs (إدارة المدونات)**

```
الدول: إدارة منشورات المدونة
المسؤوليات:
├── Post Creation & Publishing
├── Categories & Tags
├── Author Management
├── Comments & Feedback
├── SEO Optimization
└── Publishing Scheduler

الإعدادات:
├── Draft/Published Status
├── Publishing Date
├── Featured Image
└── Meta Tags
```

### 3. نموذج الاعتماديات (Dependencies)

```
Contacts
  ├─→ Sales
  │    ├─→ Invoices
  │    │    ├─→ Accounts
  │    │    ├─→ Payments
  │    │    └─→ Products
  │    └─→ Analytics
  │
  ├─→ Purchases
  │    ├─→ Invoices
  │    │    ├─→ Accounts
  │    │    ├─→ Payments
  │    │    └─→ Products
  │    └─→ Analytics
  │
  ├─→ Projects
  │    ├─→ Timesheets
  │    ├─→ Employees
  │    └─→ Chatter
  │
  └─→ Invoices
       ├─→ Accounts (أساسي)
       ├─→ Payments
       └─→ Products

Products
  ├─→ Inventory
  │    ├─→ Warehouse Management
  │    └─→ Stock Tracking
  │
  ├─→ Sales
  │    └─→ Invoices
  │
  └─→ Purchases
       └─→ Invoices

Employees
  ├─→ Timesheets
  ├─→ Time Off
  │
  ├─→ Recruitment
  │
  └─→ Projects
       └─→ Task Assignment
```

### 4. تثبيت وإلغاء تثبيت الـ Plugins

```bash
# تثبيت plugin
php artisan <plugin-name>:install

# إلغاء تثبيت plugin
php artisan <plugin-name>:uninstall

# أمثلة فعلية
php artisan blogs:install
php artisan products:uninstall
php artisan accounting:install
```

---

## بنية قاعدة البيانات

### 1. المبادئ الأساسية

- **Eloquent ORM**: استخدام Laravel's Eloquent بدلاً من الـ Raw SQL
- **Relationships**: تعريف العلاقات بين الـ Models
- **Soft Deletes**: الحذف الناعم بدلاً من الحذف الدائم
- **Timestamps**: تتبع تاريخ الإنشاء والتحديث

### 2. هيكل الـ Plugin في قاعدة البيانات

```
plugins/
├── webkul/
│   ├── blogs/
│   │   ├── database/
│   │   │   ├── migrations/
│   │   │   │   ├── 2025_01_06_create_blogs_posts_table.php
│   │   │   │   ├── 2025_01_07_create_blogs_categories_table.php
│   │   │   │   └── 2025_01_08_create_blogs_tags_table.php
│   │   │   ├── factories/
│   │   │   │   └── PostFactory.php
│   │   │   ├── seeders/
│   │   │   │   └── BlogSeeder.php
│   │   │   └── settings/
│   │   │       └── blog-settings.php
│   │   │
│   │   └── src/
│   │       ├── Models/
│   │       │   ├── Post.php
│   │       │   ├── Category.php
│   │       │   └── Tag.php
│   │       │
│   │       ├── Filament/
│   │       │   ├── Clusters/
│   │       │   │   └── Blogs.php
│   │       │   │
│   │       │   └── Blogs/
│   │       │       └── Resources/
│   │       │           └── PostResource/
│   │       │               ├── Pages/
│   │       │               │   ├── CreatePost.php
│   │       │               │   ├── EditPost.php
│   │       │               │   └── ListPosts.php
│   │       │               └── PostResource.php
│   │       │
│   │       └── Policies/
│   │           └── PostPolicy.php
│   │
│   └── [other plugins...]
```

### 3. مثال: نموذج Post من Blog Plugin

```php
<?php

namespace Webkul\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blogs_posts';

    // الحقول التي يمكن إسناد قيمها جماعياً
    protected $fillable = [
        'title',
        'sub_title',
        'content',
        'slug',
        'image',
        'author_name',
        'is_published',
        'published_at',
        'visits',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'category_id',
        'author_id',
        'creator_id',
        'last_editor_id',
    ];

    // تحويل البيانات تلقائياً
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // العلاقات
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'blogs_post_tags',
            'post_id',
            'tag_id'
        );
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function lastEditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_editor_id');
    }

    // Accessors (الخصائص المحسوبة)
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        return Storage::url($this->image);
    }

    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200);
        return $minutes . ' min read';
    }

    // Query Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->where('published_at', '<=', now());
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Factory
    protected static function newFactory()
    {
        return PostFactory::new();
    }
}
```

### 4. جداول قاعدة البيانات الرئيسية

| الجدول            | الوصف             | الـ Plugin |
| ----------------- | ----------------- | ---------- |
| `users`           | بيانات المستخدمين | Core       |
| `roles`           | الأدوار           | Security   |
| `permissions`     | الصلاحيات         | Security   |
| `contacts`        | جهات الاتصال      | Contacts   |
| `products`        | المنتجات          | Products   |
| `sales_orders`    | طلبات البيع       | Sales      |
| `purchase_orders` | طلبات الشراء      | Purchases  |
| `invoices`        | الفواتير          | Invoices   |
| `invoice_items`   | بنود الفاتورة     | Invoices   |
| `payments`        | الدفعات           | Payments   |
| `inventory_stock` | مستويات المخزون   | Inventory  |
| `employees`       | بيانات الموظفين   | Employees  |
| `projects`        | المشاريع          | Projects   |
| `project_tasks`   | مهام المشاريع     | Projects   |
| `timesheets`      | تسجيل الساعات     | Timesheets |
| `blogs_posts`     | منشورات المدونة   | Blogs      |

### 5. التركيب الموحد للجداول

```sql
-- تاريخ الإنشاء والتحديث
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

-- الحذف الناعم
deleted_at TIMESTAMP NULL

-- تتبع المستخدم
created_by UNSIGNED BIGINT (Foreign Key to users)
updated_by UNSIGNED BIGINT (Foreign Key to users)

-- معرفات فريدة
id UNSIGNED BIGINT PRIMARY KEY AUTO_INCREMENT
uuid VARCHAR(36) UNIQUE

-- السلاج (Slug)
slug VARCHAR(255) UNIQUE

-- الحالة
status ENUM('active', 'inactive') DEFAULT 'active'
```

---

## Frontend Stack والواجهات

### 1. تقنيات الـ Frontend

| التقنية             | الإصدار | الدور                    |
| ------------------- | ------- | ------------------------ |
| **FilamentPHP**     | 5.x     | Admin Panel Framework    |
| **Livewire**        | 3.x     | Dynamic UI Components    |
| **Alpine.js**       | 3.x     | Lightweight Interactions |
| **Tailwind CSS**    | 4.x     | Styling & Layout         |
| **Blade Templates** | Latest  | Server-side Rendering    |
| **Vite**            | Latest  | Build Tool               |

### 2. FilamentPHP - لماذا؟

FilamentPHP هو framework حديث لبناء Admin Panels:

**المميزات الرئيسية:**

- ✅ **Resources**: CRUD operations مباشرة من الـ Eloquent Models
- ✅ **Forms**: Dynamic forms مع validation
- ✅ **Tables**: Interactive tables مع filtering و sorting
- ✅ **Pages**: Custom pages للعمليات المعقدة
- ✅ **Widgets**: Dashboard widgets للإحصائيات
- ✅ **Actions**: Context actions دون refresh
- ✅ **Clusters**: تنظيم Resources في مجموعات
- ✅ **Policies**: Authorization integration

### 3. Livewire - المكونات التفاعلية

Livewire يسمح بإنشاء مكونات تفاعلية بدون API:

```php
// بدلاً من JavaScript و API
class CreateInvoice extends Component
{
    public array $items = [];
    public float $total = 0;

    public function addItem()
    {
        $this->items[] = ['product_id' => null, 'qty' => 1];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->recalculateTotal();
    }

    public function recalculateTotal()
    {
        $this->total = collect($this->items)
            ->sum('subtotal');
    }

    public function render()
    {
        return view('livewire.create-invoice');
    }
}
```

### 4. Alpine.js - الـ Lightweight

Alpine.js يستخدم للتفاعلات البسيطة:

```html
<!-- Toggle Dropdown -->
<div x-data="{ open: false }">
    <button @click="open = !open">Menu</button>
    <div x-show="open">Content</div>
</div>

<!-- Form Validation -->
<form @submit.prevent="submit">
    <input x-model="email" @blur="validateEmail" required />
</form>
```

### 5. Tailwind CSS - الـ Utility-First

```html
<!-- زر جميل بـ Tailwind -->
<button
    class="
    px-4 py-2 
    bg-blue-500 hover:bg-blue-600 
    text-white font-semibold 
    rounded-lg 
    transition duration-200
"
>
    Click Me
</button>

<!-- Responsive Design -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
    <!-- Content -->
</div>
```

### 6. دورة حياة الطلب (Request Lifecycle)

```
User Request
    ↓
Browser (Alpine.js + Tailwind)
    ↓
Livewire Component / Filament Resource
    ↓
Controller / Action
    ↓
Model (Eloquent)
    ↓
Database
    ↓
Response (HTML/JSON)
    ↓
Browser (Re-render)
    ↓
User sees update
```

---

## الـ Admin و Customer Panels

### 1. Admin Panel Provider

**الموقع**: `app/Providers/Filament/AdminPanelProvider.php`

**التكوين الأساسي:**

```php
->default()                           // الـ Panel الافتراضي
->id('admin')                         // معرّف فريد
->path('admin')                       // المسار: /admin
->login()                             // صفحة تسجيل الدخول
->passwordReset()                     // استرجاع كلمة المرور
->emailVerification()                 // التحقق من البريد
->profile()                           // ملف الحساب
```

**الـ Branding والـ UI:**

```php
->favicon(asset('images/favicon.ico'))
->brandLogo(asset('images/logo.svg'))
->brandLogoHeight('2rem')
->colors(['primary' => Color::Blue])
->unsavedChangesAlerts()              // تنبيهات عند الخروج بدون حفظ
->topNavigation()                     // شريط التنقل العلوي
->maxContentWidth(Width::Full)        // عرض المحتوى الكامل
```

**الـ Plugins والـ Middleware:**

```php
->plugins([
    FilamentShieldPlugin::make(),     // إدارة الأدوار والصلاحيات
    PluginManager::make(),            // مدير الـ Plugins
])
->middleware([
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    AuthenticateSession::class,
    // ... middleware أخرى
])
->authMiddleware([
    Authenticate::class,              // التحقق من المستخدم
])
```

**مجموعات التنقل:**

```php
->navigationGroups([
    NavigationGroup::make()->label(__('admin.navigation.sale')),
    NavigationGroup::make()->label(__('admin.navigation.accounting')),
    NavigationGroup::make()->label(__('admin.navigation.inventory')),
    NavigationGroup::make()->label(__('admin.navigation.hr')),
])
```

### 2. Customer Panel Provider

**الموقع**: `app/Providers/Filament/CustomerPanelProvider.php`

**الاختلافات عن Admin Panel:**

```php
->id('customer')                      // معرف مختلف
->path('/')                           // المسار الرئيسي
->homeUrl('/')                        // الصفحة الرئيسية
->authGuard('customer')               // guard مختلف
->registration()                      // تفعيل التسجيل الذاتي
->authPasswordBroker('customers')     // password broker مختلف
->darkMode(false)                     // تعطيل الـ Dark Mode افتراضياً
```

**للعملاء:**

- الوصول إلى طلباتهم وفواتيرهم فقط
- عدم الوصول إلى البيانات الحساسة
- واجهة مبسطة وودية

### 3. نظام إدارة الـ Plugins

```php
// Custom Plugin Manager
class PluginManager implements Plugin
{
    public function register(Panel $panel): void
    {
        $plugins = $this->getPlugins();

        foreach ($plugins as $modulePlugin) {
            $panel->plugin($modulePlugin::make());
        }
    }

    protected function getPlugins(): array
    {
        $plugins = require bootstrap_path('plugins.php');

        return collect($plugins)
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }
}
```

### 4. التسجيل التلقائي للـ Resources

كل plugin يوفر:

- **Resources**: للـ CRUD
- **Pages**: للعمليات المخصصة
- **Widgets**: للإحصائيات

```php
// داخل BlogPlugin
public function boot(Panel $panel): void
{
    $panel
        ->resources([
            Resources\BlogResource::class,
        ])
        ->pages([
            Pages\Dashboard::class,
        ])
        ->widgets([
            Widgets\BlogStatsWidget::class,
        ]);
}
```

---

## دليل المستخدم والـ Features

### 1. إدارة الفواتير والدفع

#### 📌 نموذج العمل

```
Customer Invoice (فاتورة البيع)
    ├── Creation
    ├── Line Items with Products
    ├── Tax Calculation
    ├── Discount Application
    ├── Payment Tracking
    └── Follow-up

Vendor Bill (فاتورة الشراء)
    ├── Creation
    ├── Line Items with Products
    ├── Tax Calculation
    ├── Payment Terms
    ├── Payment Tracking
    └── Reconciliation
```

#### ✅ خطوات إنشاء فاتورة

1. **الذهاب إلى**: Invoices → Invoices → New Invoice
2. **ملء البيانات الأساسية**:
    - نوع الفاتورة (Customer/Vendor)
    - الجهة المسؤولة
    - تاريخ الإصدار
    - تاريخ الاستحقاق

3. **إضافة البنود**:
    - المنتج/الخدمة
    - الكمية
    - السعر
    - الضريبة (اختياري)
    - الخصم (اختياري)

4. **التحكم في الدفع**:
    - حالة الدفع (مدفوع/جزئي/معلق)
    - طريقة الدفع
    - ملاحظات

5. **الحفظ والعمليات**:
    - حفظ الفاتورة
    - طباعة/تصدير
    - الإرسال عبر البريد
    - التتبع

### 2. إدارة المبيعات

#### 🎯 خط سير المبيعات (Sales Pipeline)

```
Lead (عميل محتمل)
    ↓
Quotation (عرض سعر)
    ↓ [Accept/Reject]
Sales Order (طلب بيع)
    ↓
Invoice (فاتورة)
    ↓
Payment (سداد)
    ↓
Completed
```

#### 📊 البيانات المطلوبة

| المرحلة       | البيانات                                  |
| ------------- | ----------------------------------------- |
| **Lead**      | Name, Email, Phone, Company, Value        |
| **Quotation** | Customer, Products, Qty, Price, Validity  |
| **Order**     | Quotation Reference, Delivery Date, Terms |
| **Invoice**   | Products, Tax, Discount, Payment Terms    |
| **Payment**   | Amount, Method, Reference                 |

### 3. إدارة الشراء

#### 🔄 خط سير الشراء (Purchase Pipeline)

```
Purchase Requirement
    ↓
Request for Quotation (RFQ)
    ↓ [Select Best Quote]
Purchase Order
    ↓
Goods Receipt (GR)
    ↓
Bill Reception
    ↓
Payment
    ↓
Completed
```

#### 📋 البيانات المطلوبة

| المرحلة     | البيانات                                     |
| ----------- | -------------------------------------------- |
| **RFQ**     | Vendor, Products, Qty, Deadline              |
| **Quote**   | Vendor Response, Price, Terms                |
| **PO**      | Selected Quote, Qty, Delivery, Payment Terms |
| **GR**      | Received Qty, Quality Check, Warehouse       |
| **Bill**    | Invoice #, Amount, Tax, Payment Terms        |
| **Payment** | Amount, Method, Reference                    |

### 4. إدارة المخزون

#### 📦 العمليات الأساسية

**Stock Transfer** (تحويل بين مستودعات)

```
From Warehouse A → To Warehouse B
├── Select Products
├── Qty to Transfer
├── Transfer Date
└── Reason
```

**Stock Adjustment** (تصحيح المخزون)

```
Actual Count ≠ System Count
├── Identify Discrepancy
├── Reason (Damage, Theft, Count Error)
├── Adjustment Qty
└── Approval
```

**Low Stock Alert**

```
System Monitors → Current Stock < Reorder Point
├── Notification to Manager
├── Auto-create Purchase Requisition (Optional)
└── Action Required
```

### 5. إدارة الموظفين

#### 👨‍💼 بيانات الموظف

**المعلومات الشخصية**

```
├── Full Name
├── Email & Phone
├── Address (Home & Work)
├── Date of Birth
├── Gender
├── Marital Status
├── Emergency Contact
└── Bank Account
```

**معلومات العمل**

```
├── Employee ID
├── Department
├── Job Position
├── Manager
├── Working Hours
├── Work Location
├── Skills & Certifications
├── Employment Status
└── Departure Reason (if applicable)
```

**المستندات**

```
├── Identification
├── Work Permit
├── Tax ID
├── Passport
└── Resumes
```

#### 🎓 إدارة المهارات

```
Manage Skills
├── Skill Type (Technical, Soft, Language)
├── Skill Name (e.g., JavaScript, Leadership)
├── Proficiency Level (1-5)
└── Certification (Optional)
```

### 6. إدارة المشاريع

#### 🏗️ هيكل المشروع

```
Project
├── Details
│   ├── Name & Description
│   ├── Manager
│   ├── Customer
│   ├── Start & End Date
│   ├── Allocated Hours
│   └── Status
│
├── Tasks
│   ├── Task 1
│   │   ├── Title & Description
│   │   ├── Assignees
│   │   ├── Deadline
│   │   ├── Status (In Progress, Done, etc.)
│   │   └── Sub-tasks
│   │
│   └── Task 2
│       ├── ...
│       └── Sub-tasks
│
├── Milestones (Optional)
│   ├── Design Complete
│   ├── Development Complete
│   └── Testing Complete
│
└── Timesheets (If Enabled)
    ├── Daily Logs
    ├── Hours Tracked
    └── Project Allocation
```

#### ✏️ مثال: إنشاء مشروع

```
1. New Project:
   - Name: "Website Redesign"
   - Description: "Complete redesign of company website"
   - Manager: John Doe
   - Customer: XYZ Company
   - Start: 2025-04-01
   - End: 2025-06-30

2. Add Tasks:
   - Task 1: "Design UI/UX" (Assigned to Designer)
   - Task 2: "Frontend Development" (Assigned to Dev)
   - Task 3: "Backend Integration" (Assigned to Dev)
   - Task 4: "Testing" (Assigned to QA)
   - Task 5: "Deployment" (Assigned to DevOps)

3. Add Milestones:
   - Design Complete (2025-04-30)
   - Development Complete (2025-05-31)
   - Testing Complete (2025-06-15)

4. Enable Timesheets:
   - Team logs hours daily
   - Tracked against each task
   - Used for billing and analytics
```

### 7. إدارة جهات الاتصال

#### 📇 أنواع الجهات

| النوع        | الوصف             | الاستخدام               |
| ------------ | ----------------- | ----------------------- |
| **Customer** | عملاء البيع       | Sales, Invoices         |
| **Vendor**   | الموردون          | Purchases, Bills        |
| **Partner**  | الشركاء التجاريين | Partnerships, Referrals |
| **Employee** | موظفون            | HR, Payroll             |

#### 💾 البيانات المخزنة

```
Contact
├── General Information
│   ├── Type (Individual/Company)
│   ├── Name/Company Name
│   ├── Title
│   ├── Email & Phone
│   ├── Website
│   ├── Tax ID
│   └── Tags
│
├── Addresses (Multiple)
│   ├── Type (Permanent, Invoice, Delivery, Other)
│   └── Full Address
│
├── Sub-Contacts
│   ├── Additional Representatives
│   └── Different Roles
│
└── Sales & Purchase Info
    ├── Responsible Person
    ├── Credit Limit
    └── Payment Terms
```

### 8. إدارة التوظيف (Recruitment)

#### 📝 مراحل التقديم

```
Job Posted
    ↓
Applications Received
    ↓ [Review]
Initial Screening
    ↓ [Short List]
Interview Round 1
    ↓ [Feedback]
Interview Round 2
    ↓ [Selection]
Offer Extended
    ↓ [Response]
Offer Accepted
    ↓
Onboarding
    ↓
Employee Created
```

### 9. إدارة الإجازات

#### 📅 أنواع الإجازات

```
Annual Leave (الإجازة السنوية)
├── Allocate per employee (20-30 days)
├── Carry-over policy
└── Expiry date

Sick Leave (الإجازة المرضية)
├── Unlimited or with limit
├── Medical certificate required
└── Tracked separately

Unpaid Leave (إجازة بدون راتب)
├── Manager approval required
└── For personal reasons

Special Leave (إجازات خاصة)
├── Maternity
├── Paternity
├── Bereavement
└── Other
```

#### ⚙️ سير العملية

```
1. Employee Requests Leave
2. Manager Reviews
3. Approves/Rejects
4. If Approved:
   - Deduct from Balance
   - Block in Calendar
   - Notify Employee
5. If Rejected:
   - Notify Employee
   - Provide Reason
```

### 10. إدارة الموقع والمدونة

#### 🌐 صفحات الموقع

```
Website
├── Home Page
├── Product Pages
├── About Us
├── Contact
├── Blog
└── Customer Portal
```

#### 📝 إدارة المدونة

```
Blog Post
├── Title & URL Slug
├── Category & Tags
├── Featured Image
├── Content
├── Author
├── Publishing Status
│   ├── Draft
│   ├── Scheduled
│   └── Published
├── Comments (if enabled)
└── Analytics
```

---

## العمليات والـ Workflows

### 1. دورة حياة الفاتورة (Invoice Lifecycle)

```
┌─────────────────────────────────────────────────────────┐
│                  Invoice Lifecycle                      │
└─────────────────────────────────────────────────────────┘

                        Created
                            ↓
                        Drafted
                            ↓
          ┌─────────────────┴──────────────────┐
          ↓                                     ↓
      Sent to                            Not Sent
      Customer
          ↓
          ├─→ Partially Paid ──┐
          ├─→ Fully Paid       │
          ├─→ Overdue          │
          └─→ Cancelled        │
                                ↓
                            Closed/Resolved
```

**الأحداث المهمة:**

- Invoice Created → Trigger accounting entries
- Invoice Sent → Send notification
- Invoice Paid → Update stock, Create payment record
- Invoice Overdue → Send reminder
- Invoice Cancelled → Reverse entries

### 2. دورة حياة طلب الشراء (PO Lifecycle)

```
┌────────────────────────────────────────────────────────┐
│               Purchase Order Lifecycle                 │
└────────────────────────────────────────────────────────┘

    Draft PO
        ↓
    Submitted for Approval
        ↓
    Approved/Rejected
        ↓
    Confirmed with Vendor
        ↓
    Goods in Transit
        ↓
    Goods Received (GR)
        ↓
    Invoice Received
        ↓
    Matched & Reconciled
        ↓
    Payment Processed
        ↓
    Closed
```

**الخطوات:**

1. **Create**: تحديد الاحتياجات
2. **Approve**: موافقة الإدارة
3. **Send**: إرسال للمورد
4. **Track**: متابعة الشحنة
5. **Receive**: استقبال البضائع
6. **Match**: مطابقة الفاتورة
7. **Pay**: الدفع

### 3. دورة حياة المشروع (Project Lifecycle)

```
┌────────────────────────────────────────────────────────┐
│               Project Lifecycle                        │
└────────────────────────────────────────────────────────┘

    Planning
        ├── Define Scope
        ├── Assign Resources
        └── Set Timeline
            ↓
    Execution
        ├── Create Tasks
        ├── Assign Team Members
        ├── Track Progress
        └── Log Time
            ↓
    Monitoring
        ├── Track Milestones
        ├── Monitor Budget
        └── Manage Risks
            ↓
    Closing
        ├── Complete Tasks
        ├── Final Report
        └── Archive
```

### 4. دورة حياة الموظف (Employee Lifecycle)

```
┌────────────────────────────────────────────────────────┐
│               Employee Lifecycle                       │
└────────────────────────────────────────────────────────┘

    Recruitment
        ↓
    Onboarding
        ├── Create Account
        ├── Assign Equipment
        └── Training
            ↓
    Active Employment
        ├── Performance Reviews
        ├── Salary Management
        ├── Leave Management
        └── Development
            ↓
    Exit
        ├── Final Formalities
        ├── Equipment Return
        └── Account Closure
```

### 5. نموذج الموافقة (Approval Workflow)

```
Request Initiated
    ↓
Send to Manager 1
    ↓
├─→ Manager 1 Approves → Send to Manager 2
│   Manager 1 Rejects → Send Back to Requester
│
└─→ Manager 2 Approves → Send to Finance
    Manager 2 Rejects → Send Back to Requester

        Finance Approves → Execute/Process
        Finance Rejects → Send Back to Requester
```

### 6. حساب الضريبة (Tax Calculation)

```
Subtotal = Σ(Item Price × Qty)

Discounts Applied:
- Line Item Discount
- Invoice-level Discount

Taxable Amount = Subtotal - Discounts

Tax Amount = Taxable Amount × Tax Rate

Total = Taxable Amount + Tax Amount
```

### 7. حساب الشحنة (Shipping Calculation)

```
Shipping Cost Based On:
├── Weight of Items
├── Destination
├── Shipping Method
└── Insurance (Optional)

Total with Shipping = Total + Shipping + Insurance
```

---

## التطوير والـ APIs

### 1. بنية الـ Plugin الكاملة

```php
// plugins/webkul/example/src/ExampleServiceProvider.php

namespace Webkul\Example;

use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;

class ExampleServiceProvider extends PackageServiceProvider
{
    public static string $name = 'example';
    public static string $viewNamespace = 'example';

    public function configureCustomPackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2025_01_01_create_examples_table',
            ])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command) {
                // Installation logic
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {
                // Uninstallation logic
            });
    }

    public function packageBooted(): void
    {
        // Plugin booted logic
    }
}
```

### 2. إنشاء Filament Resource

```php
// plugins/webkul/example/src/Filament/Resources/ExampleResource.php

namespace Webkul\Example\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Webkul\Example\Models\Example;

class ExampleResource extends Resource
{
    protected static ?string $model = Example::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Examples';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // Filters
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

### 3. نظام الـ Policies (التفويض)

```php
// plugins/webkul/example/src/Policies/ExamplePolicy.php

namespace Webkul\Example\Policies;

use App\Models\User;
use Webkul\Example\Models\Example;

class ExamplePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-example');
    }

    public function view(User $user, Example $example): bool
    {
        return $user->hasPermission('view-example');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create-example');
    }

    public function update(User $user, Example $example): bool
    {
        return $user->hasPermission('update-example');
    }

    public function delete(User $user, Example $example): bool
    {
        return $user->hasPermission('delete-example');
    }

    public function restore(User $user, Example $example): bool
    {
        return $user->hasPermission('delete-example');
    }

    public function forceDelete(User $user, Example $example): bool
    {
        return $user->hasPermission('force-delete-example');
    }
}
```

### 4. Events والـ Listeners

```php
// plugins/webkul/example/src/Events/ExampleCreated.php

namespace Webkul\Example\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Webkul\Example\Models\Example;

class ExampleCreated
{
    use Dispatchable;

    public function __construct(public Example $example) {}
}

// plugins/webkul/example/src/Listeners/LogExampleCreation.php

namespace Webkul\Example\Listeners;

class LogExampleCreation
{
    public function handle(ExampleCreated $event): void
    {
        logger()->info(
            "Example '{$event->example->name}' created"
        );
    }
}

// في Service Provider:
protected $listen = [
    ExampleCreated::class => [
        LogExampleCreation::class,
    ],
];
```

### 5. Migrations

```php
// plugins/webkul/example/database/migrations/2025_01_01_create_examples_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examples', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examples');
    }
};
```

### 6. API Resources (للـ REST API)

```php
// plugins/webkul/example/src/Filament/Resources/ExampleResource/Api/ExampleApiResource.php

namespace Webkul\Example\Filament\Resources\ExampleResource\Api;

use Illuminate\Http\Request;
use Filament\Resources\Http\ApiResource;

class ExampleApiResource extends ApiResource
{
    protected static ?string $model = Example::class;

    public static function endpoints(): array
    {
        return [
            Endpoints\ListRecords::class,
            Endpoints\GetRecord::class,
            Endpoints\CreateRecord::class,
            Endpoints\UpdateRecord::class,
            Endpoints\DeleteRecord::class,
        ];
    }
}
```

### 7. API Endpoints الشائعة

```
// الفواتير
GET    /api/invoices                      # قائمة الفواتير
GET    /api/invoices/{id}                 # فاتورة واحدة
POST   /api/invoices                      # إنشاء فاتورة
PUT    /api/invoices/{id}                 # تعديل فاتورة
DELETE /api/invoices/{id}                 # حذف فاتورة

// المنتجات
GET    /api/products                      # قائمة المنتجات
GET    /api/products/{id}                 # منتج واحد
POST   /api/products                      # إنشاء منتج
PUT    /api/products/{id}                 # تعديل منتج
DELETE /api/products/{id}                 # حذف منتج

// الموظفين
GET    /api/employees                     # قائمة الموظفين
GET    /api/employees/{id}                # موظف واحد
POST   /api/employees                     # إنشاء موظف
PUT    /api/employees/{id}                # تعديل موظف
DELETE /api/employees/{id}                # حذف موظف
```

### 8. Authentication (الحماية)

```php
// استخدام Sanctum للـ API

// في Model:
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
}

// في Route:
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// الحصول على Token:
$token = $user->createToken('api-token')->plainTextToken;

// الاستخدام:
curl -H "Authorization: Bearer $token" \
     https://example.com/api/invoices
```

### 9. Validation

```php
// Form Requests للـ API

namespace Webkul\Example\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('create-example');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'email' => 'required|email|unique:examples',
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'email.unique' => 'البريد الإلكتروني موجود بالفعل',
        ];
    }
}
```

### 10. قوائم التحقق (Testing)

```php
// Feature Tests

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_can_create_example()
    {
        $response = $this->post('/api/examples', [
            'name' => 'Test Example',
            'description' => 'Test Description',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('examples', [
            'name' => 'Test Example',
        ]);
    }

    public function test_can_update_example()
    {
        $example = Example::factory()->create();

        $response = $this->put("/api/examples/{$example->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('examples', [
            'id' => $example->id,
            'name' => 'Updated Name',
        ]);
    }
}
```

---

## 📊 مخطط العلاقات بين الـ Modules

```
┌─────────────────────────────────────────────────────────────┐
│                    Aureus ERP Ecosystem                    │
└─────────────────────────────────────────────────────────────┘

                          Security
                          ├─→ Users
                          └─→ Roles & Permissions
                                  ↓
    ┌───────────────────────────────────────────────────────┐
    │                                                       │
    ↓                    ↓                      ↓            ↓
Contacts          Sales Pipeline          Purchase          Inventory
├─ Customers      ├─ Quotations           Pipeline          ├─ Products
├─ Vendors        ├─ Orders               ├─ RFQs          ├─ Stock Levels
├─ Partners       └─ Invoicing            ├─ POs           ├─ Transfers
└─ Employees                              └─ Invoicing     └─ Adjustments
    ↓                                            ↓                ↓
    └────────────────────────┬──────────────────┴────────────────┘
                             ↓
                        Invoicing & Payments
                        ├─ Customer Invoices
                        ├─ Vendor Bills
                        ├─ Payments & Receipts
                        └─ Reconciliation
                             ↓
                        Accounting & Finance
                        ├─ Chart of Accounts
                        ├─ Journal Entries
                        ├─ Financial Reports
                        └─ Period Closing
                             ↓
                        Analytics & Reporting
                        ├─ Dashboards
                        ├─ KPIs
                        ├─ Charts
                        └─ Custom Reports

    Human Resources        Projects & Collaboration
    ├─ Employees          ├─ Projects
    ├─ Timesheets         ├─ Tasks & Subtasks
    ├─ Time Off           ├─ Milestones
    ├─ Recruitment        ├─ Team Members
    └─ Skills             ├─ Time Tracking
                          └─ Communication (Chatter)

                    Website & Content
                    ├─ Pages
                    ├─ Blog
                    ├─ Products Catalog
                    └─ Customer Portal
```

---

## 🎯 الخلاصة والنقاط الرئيسية

### ✅ المميزات الأساسية

1. **معمارية قائمة على الـ Plugins**
    - كل ميزة معزولة وقابلة للتثبيت/الحذف
    - سهل التخصيص والتوسع

2. **تقنيات حديثة**
    - Laravel 11 + FilamentPHP 5
    - Livewire 3 لـ Real-time Updates
    - Tailwind CSS 4 للتصميم الحديث

3. **قاعدة بيانات قوية**
    - Eloquent ORM للتفاعل السلس
    - العلاقات المعقدة والـ Soft Deletes
    - Migrations للتحكم الكامل

4. **Security & Authorization**
    - Role-based permissions (Spatie)
    - Filament Shield لإدارة الأدوار
    - Multi-tenant support

5. **Multi-Panel Architecture**
    - Admin Panel للمسؤولين
    - Customer Portal للعملاء
    - Separate authentication guards

### 🚀 الخطوات التالية

1. **التثبيت**
    - استخدام الـ Installation Wizard أو Docker
    - تكوين قاعدة البيانات والبريد

2. **التخصيص**
    - تثبيت الـ Plugins المطلوبة
    - تكوين الأدوار والصلاحيات
    - إضافة البيانات الأساسية

3. **التطوير**
    - إنشاء plugins مخصصة
    - تطوير APIs مخصصة
    - تكامل مع أنظمة خارجية

4. **المراقبة والصيانة**
    - تتبع الأداء
    - النسخ الاحتياطية المنتظمة
    - التحديثات والتصحيحات

---

## 📚 المراجع والموارد

| المورد          | الرابط                                 |
| --------------- | -------------------------------------- |
| **Dev Docs**    | https://devdocs.aureuserp.com/         |
| **User Guide**  | https://docs.aureuserp.com/            |
| **GitHub**      | https://github.com/aureuserp/aureuserp |
| **Laravel**     | https://laravel.com/docs               |
| **FilamentPHP** | https://filamentphp.com/docs           |
| **Livewire**    | https://livewire.laravel.com/          |

---

**آخر تحديث**: مارس 2026  
**الترخيص**: MIT  
**الحالة**: مشروع نشط ومدعوم
