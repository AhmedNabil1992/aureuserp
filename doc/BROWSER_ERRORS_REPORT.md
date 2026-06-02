# تقرير أخطاء المتصفح - AureusERP (eTech)

**تاريخ التقرير:** 2026-06-02  
**بيئة الاختبار:** `http://aureuserp.test`  
**المستخدم:** admin@etech-valley.com

---

## ملخص النتائج

| #   | النوع                        | الشدة                | الحالة                |
| --- | ---------------------------- | -------------------- | --------------------- |
| 1   | خطأ JavaScript - `offsetTop` | متوسط (non-blocking) | يظهر في كل الصفحات    |
| 2   | خطأ JavaScript - `scrollTo`  | منخفض (non-blocking) | صفحة تسجيل الدخول فقط |
| 3   | فاتورة مورد متأخرة           | تنبيه                | يحتاج مراجعة          |
| 4   | روابط URL خاطئة (404)        | منخفض                | توثيقية فقط           |

---

## الخطأ #1 — TypeError: Cannot read properties of null (reading 'offsetTop')

### الوصف

خطأ JavaScript يظهر في **جميع صفحات** لوحة الإدارة بدون استثناء.

### التأثير

- **لا يؤثر على الوظائف** — جميع الصفحات تعمل بشكل طبيعي
- يظهر في **Console** فقط، غير مرئي للمستخدم
- يحدث عند كل تحميل صفحة

### السبب المحتمل

مكون التنقل/الـ sidebar في Filament يحاول قراءة `.offsetTop` لعنصر DOM غير موجود وقت تنفيذ الكود.  
الكود موجود في الجافاسكريبت المُصغَّر (minified) للمشروع.

### أرقام الأسطر المُسجَّلة (كل صفحة لها رقم سطر مختلف في نفس الملف)

| الصفحة                  | URL                                                           | رقم السطر |
| ----------------------- | ------------------------------------------------------------- | --------- |
| Dashboard               | `/admin`                                                      | 2934      |
| Projects                | `/admin/projects/projects`                                    | 3565      |
| RFQs (Purchase)         | `/admin/purchase/orders/quotations`                           | 3777      |
| Journal Entries         | `/admin/accounting/accounting/journal-entries`                | 7004      |
| Vendor Bills            | `/admin/accounting/vendors/bills`                             | 6688      |
| Create Bill             | `/admin/accounting/vendors/bills/create`                      | 5448      |
| Sale Orders             | `/admin/sale/orders/orders`                                   | 4229      |
| Create Sale Order       | `/admin/sale/orders/orders/create`                            | 5138      |
| Website Dashboard       | `/admin/website`                                              | 1933      |
| Blog Posts              | `/admin/website/posts`                                        | 3861      |
| Manufacturing Orders    | `/admin/manufacturing/operations/manufacturing-orders`        | 4479      |
| Create MO               | `/admin/manufacturing/operations/manufacturing-orders/create` | 3543      |
| Employees               | `/admin/employees/employees`                                  | 6363      |
| Recruitment Applicants  | `/admin/recruitment/recruitment/applications`                 | 4863      |
| Partners/Contacts       | `/admin/partners`                                             | 4607      |
| Users                   | `/admin/users`                                                | 3777      |
| Roles                   | `/admin/shield/roles`                                         | 2107      |
| Settings > Manage Tasks | `/admin/settings/manage-tasks`                                | 3365      |
| Settings > Manage Users | `/admin/settings/manage-users`                                | 3723      |

### خطوات الإصلاح المقترحة

1. البحث في الكود المصدري عن الكومبوننت الذي يحسب `offsetTop`
2. إضافة null-check قبل الوصول إلى الخاصية:
    ```javascript
    // بدل
    element.offsetTop;
    // استخدم
    element?.offsetTop ?? 0;
    ```
3. التحقق من إصدارات Filament / Livewire لمعرفة إن كان هناك patch معروف

---

## الخطأ #2 — TypeError: Cannot read properties of null (reading 'scrollTo')

### الوصف

خطأ JavaScript يظهر فقط في صفحة **تسجيل الدخول**.

### التفاصيل

- **URL:** `http://aureuserp.test/admin/login`
- **السطر:** 645
- **النوع:** `TypeError: Cannot read properties of null (reading 'scrollTo')`

### التأثير

- لا يؤثر على الوظائف — تسجيل الدخول يعمل بشكل طبيعي
- غير مرئي للمستخدم

---

## المشكلة #3 — فاتورة مورد متأخرة (Overdue Bill)

### الوصف

فاتورة **BILL/2026/8** في قسم المشتريات وقسم المحاسبة تظهر على أنها متأخرة السداد.

### التفاصيل

- **الرقم:** BILL/2026/8
- **الحالة:** Posted (مرحَّلة)
- **تاريخ الاستحقاق:** متأخر بـ "1 week ago" (وقت التقرير)
- **الموجود في:**
    - `Accounting > Vendor Bills` (`/admin/accounting/vendors/bills`)
    - `Purchases > Vendor Bills`

### الإجراء المطلوب

- مراجعة الفاتورة وتحديد إن كانت يجب دفعها
- إما: تسجيل دفعة (Register Payment)، أو مراجعة تاريخ الاستحقاق

---

## المشكلة #4 — روابط URL خاطئة تُعطي 404

هذه روابط قد يُستخدم بعضها بشكل مباشر أو في توثيق خارجي:

| الرابط القديم / الخاطئ | الرابط الصحيح                          | الملاحظة      |
| ---------------------- | -------------------------------------- | ------------- |
| `/admin/purchases`     | `/admin/purchase/orders/quotations`    | 404 Not Found |
| `/admin/contact`       | `/admin/partners`                      | 404 Not Found |
| `/admin/inventories`   | `/admin/inventory/operations/receipts` | 404 Not Found |

---

## ملخص الصفحات المُختبَرة والنتائج

### ✅ يعمل بشكل صحيح

| القسم         | الصفحة                                       | الملاحظات                                                                      |
| ------------- | -------------------------------------------- | ------------------------------------------------------------------------------ |
| Dashboard     | `/admin`                                     | يعرض Top Assignees و Top Projects                                              |
| Purchase      | RFQs                                         | PO/1 و PO/2 موجودان                                                            |
| Purchase      | Purchase Orders                              | يعمل                                                                           |
| Purchase      | Create RFQ                                   | النموذج يعمل                                                                   |
| Purchase      | Vendors                                      | "Abo Zyad" موجود                                                               |
| Purchase      | Products                                     | 4 منتجات (Adapter 5V, Relay Module, ESP8266, Access Point 7200)                |
| Purchase      | Vendor Bills                                 | BILL/2026/8 و BILL/2026/4 (Posted)                                             |
| Purchase      | Receipts                                     | PO/2 فارغة                                                                     |
| Purchase      | Agreements                                   | تُحمَّل                                                                        |
| Purchase      | Configurations                               | Vendor Prices, Currencies, Categories, Attributes, Packagings                  |
| Inventory     | Receipts                                     | WH/IN/1 و WH/IN/2 مرتبطان بـ PO/1 و PO/2                                       |
| Inventory     | Deliveries                                   | فارغة (لا توجد تسليمات)                                                        |
| Inventory     | Quantity Adjustments                         | Access Point 7200 = 2 وحدة متاحة                                               |
| Inventory     | Scraps, Internals, Dropships, Replenishments | تُحمَّل                                                                        |
| Inventory     | Products, Lots, Packages                     | تُحمَّل                                                                        |
| Inventory     | Configurations                               | Warehouses, Locations, Operation Types تُحمَّل                                 |
| Accounting    | Customer Invoices                            | فارغة                                                                          |
| Accounting    | Vendor Bills                                 | BILL/2026/8 (متأخرة) + BILL/2026/4                                             |
| Accounting    | Journal Entries                              | تُحمَّل ببيانات                                                                |
| Accounting    | Create Bill                                  | النموذج يُحمَّل                                                                |
| Sales         | Orders                                       | فارغة                                                                          |
| Sales         | Create Order                                 | النموذج يعمل (Customer, Expiration, Quotation Date, Payment Term, Order Lines) |
| Manufacturing | Manufacturing Orders                         | فارغة                                                                          |
| Manufacturing | Create MO                                    | النموذج يُحمَّل                                                                |
| Employees     | Employees                                    | Ahmed Nabil - CEO (1 سجل)                                                      |
| Employees     | Departments, Configurations                  | تُحمَّل                                                                        |
| Recruitment   | Applicants                                   | فارغة - Kanban بالـ Stage                                                      |
| Recruitment   | Job Positions, Candidates                    | تُحمَّل                                                                        |
| Website       | Dashboard                                    | تُحمَّل (date filter, author filter)                                           |
| Website       | Blog Posts                                   | فارغة                                                                          |
| Website       | Pages, Contacts                              | تُحمَّل                                                                        |
| Partners      | Contacts                                     | تُحمَّل                                                                        |
| Users         | Users                                        | تُحمَّل                                                                        |
| Shield        | Roles                                        | Role "Admin" بـ 1831 permission                                                |
| Settings      | Manage Tasks, Manage Users                   | تُحمَّل                                                                        |

---

## الأولويات

1. **أولوية عالية (عمل فعلي):** مراجعة BILL/2026/8 ودفعها أو تسوية وضعها
2. **أولوية متوسطة (تطوير):** إصلاح خطأ `offsetTop` - يمكن التحقيق في مصدره في ملفات Filament JS
3. **أولوية منخفضة (توثيق):** تحديث أي توثيق يحتوي على روابط خاطئة
