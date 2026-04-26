# WiFi Plugin - Complete Customer and Business Guide

## الهدف

هذا الملف يشرح Plugin الواي فاي بالكامل، ليس فقط الـ API.
التركيز هنا على:

- ماذا يفعل البلجن حالياً داخل النظام.
- كيف تدور دورة العمل من الإدارة إلى العميل.
- ما هي المتطلبات التقنية والتشغيلية.
- ما الذي ينقص من APIs للعميل في الموبايل.

## نظرة عامة على البلجن

Plugin الواي فاي في AureusERP يقوم بثلاثة أدوار رئيسية:

1. إدارة منتجات وباقات الواي فاي داخل ERP.
2. ربط العملاء بالكلاودز (Clouds) الفعلية.
3. إنشاء دفعات Vouchers وإرسالها إلى مزود خارجي للواي فاي، ثم تنزيل PDF البطاقات.

## الاعتمادات (Dependencies) المطلوبة

البلجن يعتمد على Plugins أخرى:

- `accounts`
- `partners`

وذلك ظاهر في Service Provider.

## مصادر البيانات في البلجن

البلجن يعتمد على نوعين من البيانات:

1. جداول محلية داخل قاعدة بيانات ERP:

- `wifi_packages`
- `wifi_purchases`
- `wifi_voucher_batches`
- `wifi_partner_clouds`

2. جداول خارجية عبر اتصال `mariadb` (نظام الواي فاي الخارجي):

- `clouds`
- `dynamic_clients`
- `dynamic_client_realms`
- `realms`
- `profiles`
- `vouchers`
- `sales`

## إعدادات مهمة في البيئة (Environment)

### 1) اتصال mariadb

لازم يكون اتصال `mariadb` معرف في إعدادات قاعدة البيانات، لأن موديلات كثيرة في البلجن تعمل عليه مباشرة.

### 2) إعدادات API توليد الـ Vouchers

توجد إعدادات في `config/services.php`:

- `WIFI_VOUCHER_API_ENDPOINT`
- `WIFI_VOUCHER_API_TOKEN`
- `WIFI_VOUCHER_API_LANGUAGE`
- `WIFI_VOUCHER_DOWNLOAD_BASE_URL`

بدون هذه الإعدادات، توليد دفعات الـ vouchers لن يعمل.

## مكونات الإدارة (Admin) في Filament

البلجن يسجل موارد إدارية داخل Panel `admin`:

1. `CloudResource`

- عرض الكلاودز فقط (قراءة).

2. `DynamicClientResource`

- عرض الأجهزة/نقاط الوصول.
- فلترة حسب الصورة وحسب آخر اتصال.
- إمكانية تحديث الصورة.

3. `WifiPackageResource`

- إنشاء باكدج مربوطة بمنتج خدمة Service Product.
- تحديد الكمية والسعر والعملة ونوع الباكدج.

4. `WifiPartnerCloudResource`

- ربط عميل (Partner) بكلاود معين.
- هذا الربط هو الأساس لأي APIs عميل خاصة بالواي فاي.

5. `WifiPurchaseResource`

- إنشاء عملية شراء واي فاي مرتبطة بعميل.
- أثناء الإنشاء يتم توليد Invoice وMove Line تلقائياً.
- يوجد منطق لمطابقة الدفعات المقدمة (auto reconciliation) إن وجدت.

6. `WifiVoucherBatchResource`

- إنشاء Batch لبطاقات الواي فاي.
- عند الإنشاء يتم استدعاء خدمة توليد vouchers الخارجية تلقائياً.
- إمكانية تنزيل PDF للـ batch.

## دورة العمل (Workflow) الحالية من البداية للنهاية

1. إعداد `Wi-Fi Package` (الخدمة/الكمية/السعر/العملة).
2. ربط العميل بكلاود عبر `WifiPartnerCloud`.
3. إنشاء `WifiPurchase` للعميل على كلاود محدد.
4. إنشاء `WifiVoucherBatch` بناءً على Purchase.
5. `VoucherGenerationService` يستدعي API خارجي لتوليد الأكواد.
6. تنزيل بطاقات PDF عبر route الويب.
7. العميل يستهلك البيانات عبر APIs العميل (clouds + dynamic_clients).

## قواعد العمل المهمة (Business Rules)

### WifiPackage

- الكمية يجب أن تكون 1 أو أكثر.
- المبالغ لا تكون سالبة.
- لازم عملة.
- لازم المنتج يكون من نوع Service.

### WifiPurchase

- لازم package و invoice line متوافقين على نفس المنتج.
- كمية الشراء لا تقل عن الكمية التي تم توليدها بالفعل.
- يتم حساب `remaining_quantity` تلقائياً.
- يجب أن يكون cloud تابعاً للعميل من خلال `wifi_partner_clouds`.

### WifiVoucherBatch

- لازم purchase صالح.
- الكمية لا تتجاوز المتاح من purchase.
- cloud في الـ batch يجب يطابق cloud في purchase.
- في حال اختيار access point لازم يكون موجود.

## الصلاحيات الحالية

حسب `filament-shield` في البلجن:

- `CloudResource`: عرض فقط.
- `DynamicClientResource`: عرض فقط.
- `WifiPackageResource`: عرض + إنشاء + تعديل + حذف.
- `WifiPurchaseResource`: عرض + إنشاء + تعديل + حذف.
- `WifiVoucherBatchResource`: عرض + إنشاء + تعديل + حذف.

## الراوتس الحالية في البلجن

### Web route

- `GET /wifi/voucher-batches/{batchCode}/download`
- لتحميل PDF بطاقات batch.

### Customer API routes (المضافة حالياً)

1. `POST /customer/api/v1/wifi/clouds`
2. `POST /customer/api/v1/wifi/dynamic-clients`

## تفاصيل API العميل (الحالي)

### المصادقة المطلوبة

- `Authorization: Bearer {token}`
- Body:
    - `customer_id`
    - `token`

### التحقق المطبق

- المستخدم لازم يكون موثق بالتوكن.
- `customer_id` لازم يطابق المستخدم الحالي.
- `token` في body لازم يساوي Bearer token.

### المخرجات الحالية

1. Clouds المشتركة للعميل.
2. Dynamic clients المرتبطة بكلاودات العميل.

## النقاط التي تحتاج قرار Business قبل التوسع

1. هل العميل يرى كل `dynamic_clients` أم فقط Active؟
2. هل العميل يرى أكواد vouchers نفسها أم إحصائيات فقط؟
3. هل يسمح للعميل بتنزيل PDF البطاقات مباشرة من الموبايل؟
4. ما هي صلاحية عرض البيانات القديمة (تاريخي/آخر 30 يوم فقط)؟
5. هل نحتاج pagination و filtering في APIs العميل من الآن؟

## APIs مقترحة مطلوبة غالباً للعميل

1. `GET /customer/api/v1/wifi/clouds/{id}`

- تفاصيل كلاود واحدة مع التحقق من الملكية.

2. `GET /customer/api/v1/wifi/dynamic-clients/{id}`

- تفاصيل AP واحدة مع التحقق من cloud ownership.

3. `GET /customer/api/v1/wifi/dashboard`

- إحصائيات سريعة:
    - عدد الكلاودز.
    - عدد الأجهزة.
    - عدد الأجهزة النشطة.
    - آخر اتصال.

4. `GET /customer/api/v1/wifi/voucher-batches`

- قائمة الدفعات المرتبطة بالعميل.

5. `GET /customer/api/v1/wifi/voucher-batches/{id}/download-url`

- رابط تنزيل PDF للدفعة.

6. `GET /customer/api/v1/wifi/vouchers`

- عرض الفاوتشرات (اختياري حسب قرار الأعمال).

7. `GET /customer/api/v1/wifi/sales-summary`

- ملخص استهلاك/مبيعات من جدول `sales`.

## المخاطر الفنية التي يجب الانتباه لها

1. اعتماد كبير على `mariadb` خارجي:

- أي انقطاع أو اختلاف schema يؤثر على الاستجابات.

2. اختلاف naming في الجداول الخارجية:

- مثل `Picture` و `cloudID`.

3. البيانات قد تكون ضخمة:

- يلزم pagination في APIs العميل قريباً.

4. التعامل مع token داخل body:

- حالياً مطلوب حسب الطلب، لكن أمنياً يكفي Header.

## قائمة تنفيذ مقترحة (Roadmap)

1. إضافة endpoints تفاصيل Cloud وDynamic Client.
2. إضافة Dashboard endpoint.
3. إضافة Voucher Batches endpoints للعميل.
4. إضافة Pagination + Filters + Sort للـ lists.
5. إضافة اختبارات Feature للـ APIs الجديدة في Plugin WiFi.
6. إضافة توحيد رسائل API بالعربي أو ثنائية اللغة حسب قرار المنتج.

## الملفات المرجعية الرئيسية في الكود

- `plugins/webkul/wifi/src/WifiServiceProvider.php`
- `plugins/webkul/wifi/src/WifiPlugin.php`
- `plugins/webkul/wifi/src/Services/VoucherGenerationService.php`
- `plugins/webkul/wifi/src/Http/Controllers/VoucherBatchPdfController.php`
- `plugins/webkul/wifi/src/Http/Controllers/API/V1/CustomerWifiController.php`
- `plugins/webkul/wifi/src/Models/WifiPackage.php`
- `plugins/webkul/wifi/src/Models/WifiPurchase.php`
- `plugins/webkul/wifi/src/Models/WifiVoucherBatch.php`
- `plugins/webkul/wifi/src/Models/WifiPartnerCloud.php`
- `plugins/webkul/wifi/src/Models/Cloud.php`
- `plugins/webkul/wifi/src/Models/DynamicClient.php`
- `plugins/webkul/wifi/routes/web.php`
- `plugins/webkul/wifi/routes/api.php`

## أمثلة عملية للـ API الحالي (Call / Response)

### Base URL

- `https://your-domain.com`

### Headers المطلوبة لكل Request

```http
Authorization: Bearer 1|abc123xyz...
Accept: application/json
Content-Type: application/json
```

### 1) API: جلب Clouds الخاصة بالعميل

#### Endpoint

- Method: `POST`
- URL: `/customer/api/v1/wifi/clouds`

#### Request Body

```json
{
    "customer_id": 1,
    "token": "1|abc123xyz..."
}
```

#### cURL Example

```bash
curl --request POST 'https://your-domain.com/customer/api/v1/wifi/clouds' \
  --header 'Authorization: Bearer 1|abc123xyz...' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
    "customer_id": 1,
    "token": "1|abc123xyz..."
  }'
```

#### Success Response (200)

```json
{
    "message": "Clouds fetched successfully.",
    "data": [
        {
            "id": 3,
            "name": "Main Cloud",
            "created": "2026-04-20 10:00:00",
            "modified": "2026-04-25 14:30:00"
        }
    ]
}
```

### 2) API: جلب Dynamic Clients الخاصة بالعميل

#### Endpoint

- Method: `POST`
- URL: `/customer/api/v1/wifi/dynamic-clients`

#### Request Body

```json
{
    "customer_id": 1,
    "token": "1|abc123xyz..."
}
```

#### cURL Example

```bash
curl --request POST 'https://your-domain.com/customer/api/v1/wifi/dynamic-clients' \
  --header 'Authorization: Bearer 1|abc123xyz...' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
    "customer_id": 1,
    "token": "1|abc123xyz..."
  }'
```

#### Success Response (200)

```json
{
    "message": "Dynamic clients fetched successfully.",
    "data": [
        {
            "id": 11,
            "name": "AP-Branch-1",
            "nasidentifier": "NAS-001",
            "cloud_id": 3,
            "last_contact": "2026-04-25T10:15:00+00:00",
            "last_contact_ip": "192.168.1.10",
            "active": true,
            "picture": null,
            "zero_ip": "10.0.0.1",
            "created": "2026-04-20 10:00:00",
            "modified": "2026-04-25 14:30:00"
        }
    ]
}
```

### Responses للأخطاء المتوقعة

#### 401 Unauthenticated

```json
{
    "message": "Unauthenticated."
}
```

#### 403 لو `customer_id` لا يطابق صاحب التوكن

```json
{
    "message": "هذا التوكين لا يخص هذا العميل."
}
```

#### 403 لو `token` في body لا يطابق Bearer token

```json
{
    "message": "قيمة التوكين المرسلة لا تطابق توكين الجلسة."
}
```
