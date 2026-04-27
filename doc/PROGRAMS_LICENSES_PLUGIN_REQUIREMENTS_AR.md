# متطلبات Plugin البرامج والتراخيص + كتالوج الجداول الحالية

تاريخ الإعداد: 2026-04-09

## 1) المطلوب إرساله للمشروع الثاني (صياغة جاهزة)

نريد بناء Plugin جديدة داخل Aureus ERP باسم مبدئي Programs & Licenses.

نطاق العمل:

- إدارة أكثر من برنامج (Multi-Program) داخل نفس النظام.
- لكل برنامج خدمات مستقلة (Technical Support, Mail, Remote, وغيرها).
- لكل خدمة أسعار اشتراك وتجديد مستقلة حسب الخطة (Monthly, Annual, Full).
- إدارة إصدارات البرامج والتحديثات.
- إدارة Error Histories وربطها بالبرنامج والترخيص والجهاز.
- إدارة Subscription lifecycle (تفعيل، انتهاء، تعليق، تجديد).
- إدارة Ticket System مرتبط بالبرنامج والترخيص والعميل.

متطلبات معمارية:

- التصميم Domain-first وليس CRUD فقط.
- قابلية إضافة برنامج جديد دون تعديل جذري في الكود.
- فصل الوحدات إلى Domains واضحة: Programs, Pricing, Licenses, Subscriptions, Updates, Logs, Tickets.
- توفير طبقة توافق API للعميل القديم أثناء الانتقال.

## 2) ملاحظات حرجة قبل التنفيذ

- الجداول الحالية Legacy بأسماء SQL Server مختلطة الحروف.
- يوجد اعتماد على إجراءات مخزنة Stored Procedures داخل التدفق الحالي.
- لا توجد Migrations كاملة لكل الجداول داخل هذا repository، لذلك يلزم Schema Freeze من قاعدة البيانات الفعلية قبل البناء النهائي.

## 3) Data Catalog للجداول الحالية (من الكود الحالي)

مهم:

- الأعمدة أدناه مستخرجة من Models + Queries في الكود.
- قد توجد أعمدة إضافية في قاعدة البيانات لم تظهر في الكود.

### A) البرامج والإصدارات

### Table: Products

- الغرض: تعريف البرامج الأساسية.
- المفتاح: ID.
- أعمدة مستخدمة: Product_Name, Description, Version_Number, License_Cost, Edition, Price.
- علاقات: يرتبط مع Licenses عبر ProductID. يرتبط مع Tickets عبر product_id. يرتبط مع product_editions عبر product_id.

### Table: product_editions

- الغرض: تعريف نسخ/إصدارات تجارية للبرنامج وتسعيرها.
- أعمدة مستخدمة: id, product_id, edition, cost, price, devices.
- علاقات: مع Products. ومع Licenses عبر Edition_ID.

### Table: ApplicationVersions

- الغرض: إدارة إصدارات التطبيق وروابط التحديث.
- المفتاح: ID.
- أعمدة مستخدمة: ApplicationName, VersionNumber, UpdateLink, ReleaseDate, FileName, AppTerminate, IsDBUpdate, DBLink, Download_Times, IsActive, Remark.
- استخدامات: فحص التحديث عبر API + زيادة Download_Times عند سحب تحديث.

### B) التراخيص والمفاتيح

### Table: Licenses

- الغرض: السجل الرئيسي للترخيص.
- المفتاح: ID.
- أعمدة مستخدمة: Company_Name, ProductID, ClientID, GoverID, CityID, Address, LicenseType, Period, StartDate, EndDate, Cost, Paid, Remain, PayStatus, SupportBalance, Application_Version, Approved_By, LastOnline, IsActive, Edition_ID, Server_IP.
- علاقات: مع Clients, Products, governorate, city, Keys, Mail, Technical_Support, Remote_Sub, Remote, product_editions.

### Table: Keys

- الغرض: مفاتيح أجهزة العميل المرتبطة بالترخيص.
- المفتاح: ID.
- أعمدة مستخدمة: License_ID, Computer_ID, License_Key, Bios_ID, Disk_ID, Base_ID, Video_ID, Mac_ID, device_name, is_main.
- علاقات: مع Licenses. ومع Logging عبر Device_Key = Computer_ID.

### C) الاشتراكات والخدمات

### Table: Technical_Support

- الغرض: اشتراك الدعم الفني.
- المفتاح: ID.
- أعمدة مستخدمة: LicenseID, Start_Date, End_Date, IsActive.
- علاقات: مع Licenses.

### Table: Mail

- الغرض: اشتراك خدمة البريد المرتبطة بالترخيص.
- المفتاح: ID.
- أعمدة مستخدمة: License_ID, Start_Date, End_Date, IsActive.
- علاقات: مع Licenses.

### Table: Remote_Sub

- الغرض: اشتراك خدمة الريموت.
- المفتاح: ID.
- أعمدة مستخدمة: License_ID, Start_Date, End_Date, IsActive.
- علاقات: مع Licenses.
- ملاحظة: يوجد Scope في الكود لفحص الاشتراك الفعال activeNow.

### Table: Remote

- الغرض: بيانات الوصول عن بعد لكل ترخيص.
- المفتاح: ID.
- أعمدة مستخدمة: license_id, Anydesk, Teamviewer, Rustdesk.
- علاقات: مع Licenses.

### Table: product_rent

- الغرض: أسعار التجديد الدوري للمنتج.
- أعمدة مستخدمة: product_id, monthly, yearly.
- استخدامات: تحديد تكلفة التجديد من لوحة Licenses ومن لوحة العميل.

### D) العملاء والموقع الجغرافي

### Table: Clients

- الغرض: حسابات العملاء (Customer guard).
- المفتاح: id.
- أعمدة مستخدمة: name, PhoneNo, email, ReferralBalance, password, Address, IsDist, Cloud_ID, avatar_url, email_verified_at.
- استخدامات: خصم الرصيد عند التجديد العميلي.

### Table: governorate

- الغرض: المحافظات.
- المفتاح: ID.
- أعمدة مستخدمة: Governorate.
- علاقات: مع Licenses.

### Table: city

- الغرض: المدن.
- المفتاح: ID.
- أعمدة مستخدمة: GoverID, City.
- علاقات: مع Licenses.

### E) سجل الأخطاء

### Table: Logging

- الغرض: تجميع أخطاء البرامج من العميل.
- المفتاح: ID.
- أعمدة مستخدمة: Device_Key, Date, Message, Trace, Form_Name, ImageURL, EID, Status, App_Version, Checked_By.
- علاقات: غير مباشرة مع Licenses عبر Keys.Computer_ID.
- استخدامات: صفحة Error Histories تعرض الأخطاء غير المعالجة Status = 0.

### Table: MailConfig

- الغرض: إعدادات البريد المركزي المستخدمة في check-mail API.
- أعمدة مستخدمة: Description, Value.
- مفاتيح وصف مستخدمة: Server, Email, Password, HeloName, Status.

### F) التذاكر

### Table: tickets

- الغرض: التذكرة الرئيسية.
- أعمدة مستخدمة: id, ticket_number, unread, unread_user, title, content, file, closed, client_id, user_id, edited_title, first_closed_at, last_closed_at, closed_by, reopened, product_id, license_id.
- علاقات: مع Clients, Users, Products, Licenses.

### Table: ticket_events

- الغرض: الرسائل/الأحداث داخل التذكرة.
- أعمدة مستخدمة: id, type, content, ticket_id, user_id, file, private, client_id.
- استخدامات: عند إنشاء event من موظف يتم تحديث user_tickets.

### Table: tag_tickets

- الغرض: Pivot بين tickets وtags.
- أعمدة مستخدمة: ticket_id, tag_id.

### Table: tags

- الغرض: تصنيفات التذاكر.
- أعمدة مستخدمة: id, name, color.

### Table: user_tickets

- الغرض: إسناد التذكرة لموظف.
- أعمدة مستخدمة: ticket_id, user_id.

### Table: ticket_clients

- الغرض: Pivot إضافي لربط التذكرة بالعميل.
- أعمدة مستخدمة: ticket_id, client_id.

## 4) إجراءات مخزنة مهمة حالية

- Insert_Subscription
- Insert_Subscription_Client
- Generate_License

ملاحظة:

- هذه الإجراءات جزء من منطق الأعمال الحالي. عند النقل إلى Aureus يجب اتخاذ قرار:
- إما الحفاظ على نفس الإجراءات في مرحلة الانتقال.
- أو استبدالها بـ Service Layer داخل Plugin مع Transaction واضحة.

## 5) APIs الحالية المرتبطة بالنطاق

- POST /api/check-for-update
- POST /api/update-rust-desk
- POST /api/insert-logging
- POST /api/insert-logging-2
- POST /api/check-mail
- POST /api/check-valid
- POST /api/insert-licenses
- POST /api/insert-keys
- POST /api/get-key
- POST /api/check-key
- POST /api/licenses-info
- POST /api/get-tech-support-info

## 6) تصميم Plugin مقترح في Aureus

اسم مبدئي:

- programs-licenses

الوحدات الداخلية:

- Programs
- ProgramEditions
- PricingPolicies
- Licenses
- LicenseKeys
- Subscriptions (Technical, Mail, Remote)
- Updates
- ErrorLogs
- Tickets
- Compatibility API Layer

## 7) أول Backlog تنفيذي (Sprint-1)

- عمل Schema Freeze من قاعدة البيانات الفعلية للجداول المذكورة.
- إنشاء Migration baseline لكل جدول في Plugin الجديدة.
- بناء Domain Models بعلاقات Eloquent القياسية.
- نقل منطق update check + logging insertion + subscription status كخدمات مستقلة.
- بناء واجهة Filament أولية لــ Programs + Licenses + ApplicationVersions + ErrorLogs + Tickets.

## 8) تعريف نجاح المرحلة الأولى

- إضافة برنامج جديد من النظام دون كسر البرامج الحالية.
- إنشاء ترخيص مع ربط key وخدمة اشتراك واحدة على الأقل.
- ظهور logs مرتبطة بترخيص/جهاز داخل صفحة Error Histories.
- إنشاء Ticket وربطه ببرنامج + عميل + ترخيص.
- نجاح endpoint check-for-update على نموذج اختبار واحد على الأقل.
