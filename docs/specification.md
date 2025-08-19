# مواصفات النظام - AureusERP

## 1. المتطلبات التقنية

### متطلبات الخادم

-   **PHP**: 8.2 أو أحدث
-   **Laravel**: 11.x
-   **FilamentPHP**: 3.x
-   **قاعدة البيانات**: MySQL 8.0+ أو SQLite
-   **Composer**: أحدث إصدار
-   **Node.js & NPM**: أحدث إصدار مستقر
-   **خادم الويب**: Apache/Nginx مع امتدادات PHP المطلوبة

### امتدادات PHP المطلوبة

-   OpenSSL
-   PDO
-   Mbstring
-   Tokenizer
-   XML
-   Ctype
-   JSON
-   BCMath
-   Fileinfo
-   GD

## 2. هيكل النظام

### البنية العامة

```
aureuserp/
├── app/                    # التطبيق الأساسي
├── plugins/webkul/         # الوحدات/البلجنز
├── config/                 # ملفات الإعدادات
├── database/              # قاعدة البيانات والهجرة
├── resources/             # الموارد (CSS, JS, Views)
├── routes/                # مسارات النظام
├── storage/               # التخزين
├── public/                # الملفات العامة
└── docs/                  # التوثيق
```

### نظام البلجنز

النظام يعتمد على بنية البلجنز المعيارية:

#### البلجنز الأساسية (Core)

هذه البلجنز أساسية ولا يمكن إلغاؤها:

| البلجن      | الوصف                   | الحالة |
| ----------- | ----------------------- | ------ |
| Analytics   | أدوات التحليل والتقارير | نشط    |
| Chatter     | التواصل الداخلي         | نشط    |
| Fields      | إدارة الحقول المخصصة    | نشط    |
| Security    | الأمان والصلاحيات       | نشط    |
| Support     | الدعم والمساعدة         | نشط    |
| Table Views | عرض البيانات            | نشط    |

#### البلجنز القابلة للتثبيت

| البلجن       | الوصف              | التبعيات            | الحالة       |
| ------------ | ------------------ | ------------------- | ------------ |
| Accounts     | النظام المحاسبي    | -                   | قابل للتثبيت |
| Blogs        | إدارة المدونات     | -                   | قابل للتثبيت |
| Contacts     | إدارة جهات الاتصال | -                   | قابل للتثبيت |
| Employees    | إدارة الموظفين     | Contacts            | قابل للتثبيت |
| Inventories  | إدارة المخزون      | Products            | قابل للتثبيت |
| Invoices     | نظام الفواتير      | Accounts, Contacts  | قابل للتثبيت |
| Partners     | إدارة الشركاء      | Contacts            | قابل للتثبيت |
| Payments     | نظام المدفوعات     | Accounts            | قابل للتثبيت |
| Products     | إدارة المنتجات     | -                   | قابل للتثبيت |
| Projects     | إدارة المشاريع     | Employees           | قابل للتثبيت |
| Purchases    | نظام المشتريات     | Products, Contacts  | قابل للتثبيت |
| Recruitments | التوظيف            | Employees           | قابل للتثبيت |
| Sales        | إدارة المبيعات     | Products, Contacts  | قابل للتثبيت |
| Time-off     | إدارة الإجازات     | Employees           | قابل للتثبيت |
| Timesheets   | تتبع الوقت         | Employees, Projects | قابل للتثبيت |
| Website      | الموقع الإلكتروني  | Products            | قابل للتثبيت |

## 3. قاعدة البيانات

### الجداول الأساسية

#### جدول المستخدمين (users)

```sql
- id (bigint, primary key)
- name (varchar)
- email (varchar, unique)
- email_verified_at (timestamp)
- password (varchar)
- resource_permission (json)
- remember_token (varchar)
- created_at (timestamp)
- updated_at (timestamp)
```

#### جدول الصلاحيات (permissions)

```sql
- id (bigint, primary key)
- name (varchar)
- guard_name (varchar)
- created_at (timestamp)
- updated_at (timestamp)
```

#### جدول الأدوار (roles)

```sql
- id (bigint, primary key)
- name (varchar)
- guard_name (varchar)
- created_at (timestamp)
- updated_at (timestamp)
```

#### جدول الإعدادات (settings)

```sql
- id (bigint, primary key)
- group (varchar)
- name (varchar)
- locked (boolean)
- payload (json)
- created_at (timestamp)
- updated_at (timestamp)
```

### جداول البلجنز

كل بلجن له جداوله الخاصة التي يتم إنشاؤها عند التثبيت.

## 4. نظام الصلاحيات والأمان

### FilamentShield

النظام يستخدم `bezhansalleh/filament-shield` لإدارة الصلاحيات:

#### الأدوار الأساسية

-   **Super Admin**: صلاحيات كاملة
-   **Admin**: صلاحيات إدارية
-   **Manager**: صلاحيات إدارة القسم
-   **Employee**: صلاحيات محدودة
-   **User**: صلاحيات المستخدم العادي

#### نظام الصلاحيات

-   **Create**: إنشاء السجلات
-   **Read**: قراءة البيانات
-   **Update**: تحديث السجلات
-   **Delete**: حذف السجلات
-   **View**: عرض الصفحات
-   **Manage**: إدارة شاملة

## 5. واجهة المستخدم

### FilamentPHP Components

#### الصفحات الأساسية

-   **Dashboard**: لوحة التحكم الرئيسية
-   **Resources**: إدارة الموارد
-   **Pages**: الصفحات المخصصة
-   **Widgets**: الودجت والإحصائيات

#### المكونات

-   **Forms**: النماذج التفاعلية
-   **Tables**: الجداول مع البحث والترشيح
-   **Actions**: الإجراءات والعمليات
-   **Notifications**: الإشعارات
-   **Modals**: النوافذ المنبثقة

### التصميم

-   **TailwindCSS**: للتنسيق
-   **Alpine.js**: للتفاعل
-   **Livewire**: للديناميكية
-   **Responsive Design**: متجاوب مع جميع الأجهزة

## 6. نظام التقارير

### التقارير الأساسية

-   تقارير المبيعات
-   تقارير المشتريات
-   التقارير المالية
-   تقارير المخزون
-   تقارير الموظفين

### أدوات التصدير

-   PDF Export (dompdf)
-   Excel Export
-   CSV Export
-   Print View

## 7. نظام التكامل

### APIs

-   RESTful API (مخطط للمستقبل)
-   GraphQL (مخطط للمستقبل)
-   WebHooks

### الخدمات الخارجية

-   خدمات البريد الإلكتروني
-   بوابات الدفع
-   خدمات الرسائل النصية
-   خدمات التخزين السحابي

## 8. الأداء والتحسين

### التحسينات الحالية

-   **Caching**: تخزين مؤقت للبيانات
-   **Optimization**: تحسين الاستعلامات
-   **Lazy Loading**: تحميل تدريجي
-   **Compression**: ضغط الملفات

### المقاييس المستهدفة

-   وقت التحميل: أقل من 3 ثواني
-   معدل الاستجابة: أقل من 500ms
-   الدعم: 1000+ مستخدم متزامن
-   قاعدة البيانات: 10M+ سجل

## 9. النسخ الاحتياطي والأمان

### النسخ الاحتياطي

-   نسخ احتياطية يومية تلقائية
-   نسخ احتياطية حسب الطلب
-   استعادة سريعة للبيانات

### الأمان

-   تشفير البيانات الحساسة
-   HTTPS إجباري
-   حماية من SQL Injection
-   حماية من XSS/CSRF
-   مراقبة محاولات الاختراق

## 10. متطلبات النشر

### البيئات المدعومة

-   **Shared Hosting**: استضافة مشتركة
-   **VPS**: خادم افتراضي خاص
-   **Dedicated Server**: خادم مخصص
-   **Cloud**: خدمات سحابية (AWS, Digital Ocean)
-   **Docker**: حاويات Docker

### متطلبات الإنتاج

-   PHP 8.2+ مع OPcache
-   MySQL/MariaDB مع InnoDB
-   Redis للتخزين المؤقت
-   SSL Certificate
-   CDN للملفات الثابتة

## 11. التحديثات والصيانة

### نظام التحديث

```bash
# تحديث النظام الأساسي
composer update

# تحديث البلجنز
php artisan plugin:update

# تطبيق التحديثات
php artisan migrate
php artisan config:clear
```

### جدولة الصيانة

-   تحديثات أمنية فورية
-   تحديثات ميزات شهرية
-   تحديثات رئيسية ربع سنوية

## 12. مواصفات البلجنز المستقبلية

### CRM المتقدم

-   إدارة العملاء المحتملين
-   تتبع الفرص التجارية
-   إدارة حملات التسويق
-   تحليلات سلوك العملاء

### التصنيع والإنتاج

-   تخطيط الإنتاج
-   إدارة المواد الخام
-   تتبع دورة الإنتاج
-   مراقبة الجودة

### التجارة الإلكترونية

-   واجهة متجر إلكتروني
-   سلة التسوق المتقدمة
-   إدارة الطلبات
-   تكامل وسائل الدفع

### ذكاء الأعمال

-   لوحات تحكم تفاعلية
-   تحليلات متقدمة
-   تنبؤات ذكية
-   تقارير مخصصة

## 13. معايير الجودة

### كود البرمجة

-   PSR Standards
-   Laravel Best Practices
-   FilamentPHP Guidelines
-   Automated Testing

### الاختبارات

-   Unit Tests
-   Feature Tests
-   Browser Tests
-   Performance Tests

### التوثيق

-   كود موثق بالكامل
-   دليل المستخدم
-   دليل المطور
-   دليل API

---

_آخر تحديث: أغسطس 2025_
