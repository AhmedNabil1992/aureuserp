# Software Plugin API Call Guide

هذا الملف يوثق كل API الحالية الموجودة في بلجن Software بناءً على المسارات الفعلية في routes.

## 1) Base URL

استخدم دومين المشروع، مثال:

- `http://127.0.0.1:8000`

## 2) Authentication Summary

- Admin V1 APIs: تحتاج `auth:sanctum` (Bearer Token).
- Customer V1 APIs: تحتاج `auth:customer` (حسب إعداد المشروع: Session/Cookie أو آلية guard الخاصة بالعميل).
- Legacy APIs: تحتاج مفتاح API عبر Middleware `VerifyLegacyApiKey`:
    - Header: `X-Legacy-Api-Key: YOUR_KEY`
    - أو Query: `?api_key=YOUR_KEY`

---

## 3) Admin V1 APIs

Base prefix:

- `/admin/api/v1/software`

### 3.1 List Tickets

- Method: `GET`
- URL: `/admin/api/v1/software/tickets`
- Auth: Sanctum
- Query (اختياري):
    - `per_page` (default: 20)
    - `filter[status]`
    - `filter[priority]`
    - `filter[partner_id]`
    - `filter[assigned_to]`
    - `sort` (مثل: `-updated_at`)
    - `include` (مثل: `partner,program,license,assignedTo,attachments`)

```bash
curl -X GET "http://127.0.0.1:8000/admin/api/v1/software/tickets?per_page=20&sort=-updated_at&include=partner,program" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json"
```

### 3.2 Create Ticket

- Method: `POST`
- URL: `/admin/api/v1/software/tickets`
- Auth: Sanctum
- Content-Type: `multipart/form-data` عند رفع مرفقات، أو `application/json` بدون مرفقات.
- Body:
    - `partner_id` required integer
    - `title` required string
    - `content` required string
    - `license_id` nullable integer
    - `program_id` nullable integer
    - `assigned_to` nullable integer
    - `priority` nullable enum: `low|normal|high|urgent`
    - `status` nullable enum: `open|pending|closed`
    - `attachments[]` files (max 10MB/file)

```bash
curl -X POST "http://127.0.0.1:8000/admin/api/v1/software/tickets" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json" \
  -F "partner_id=12" \
  -F "title=Activation issue" \
  -F "content=<p>Customer cannot activate.</p>" \
  -F "priority=high" \
  -F "attachments[]=@/path/to/screenshot.png"
```

### 3.3 Show Ticket

- Method: `GET`
- URL: `/admin/api/v1/software/tickets/{ticket}`
- Auth: Sanctum

```bash
curl -X GET "http://127.0.0.1:8000/admin/api/v1/software/tickets/101" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json"
```

### 3.4 Update Ticket

- Method: `PUT` or `PATCH`
- URL: `/admin/api/v1/software/tickets/{ticket}`
- Auth: Sanctum
- Body: نفس حقول الإنشاء لكن في التحديث تكون غالبًا `sometimes`.

```bash
curl -X PATCH "http://127.0.0.1:8000/admin/api/v1/software/tickets/101" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "pending",
    "assigned_to": 3,
    "priority": "normal"
  }'
```

### 3.5 Delete Ticket

- Method: `DELETE`
- URL: `/admin/api/v1/software/tickets/{ticket}`
- Auth: Sanctum

```bash
curl -X DELETE "http://127.0.0.1:8000/admin/api/v1/software/tickets/101" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json"
```

### 3.6 Add Reply to Ticket

- Method: `POST`
- URL: `/admin/api/v1/software/tickets/{ticket}/replies`
- Auth: Sanctum
- Body:
    - `content` required string
    - `is_private` nullable boolean
    - `attachments[]` optional files

```bash
curl -X POST "http://127.0.0.1:8000/admin/api/v1/software/tickets/101/replies" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json" \
  -F "content=<p>Issue fixed. Please verify.</p>" \
  -F "is_private=0"
```

### 3.7 List Ticket Replies

- Method: `GET`
- URL: `/admin/api/v1/software/tickets/{ticket}/replies`
- Auth: Sanctum

```bash
curl -X GET "http://127.0.0.1:8000/admin/api/v1/software/tickets/101/replies" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json"
```

### 3.8 Register FCM Token (Admin)

- Method: `POST`
- URL: `/admin/api/v1/software/fcm-tokens`
- Auth: Sanctum
- Body:
    - `token` required string
    - `device_name` nullable string

```bash
curl -X POST "http://127.0.0.1:8000/admin/api/v1/software/fcm-tokens" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "token": "FCM_TOKEN_VALUE",
    "device_name": "Office Desktop"
  }'
```

### 3.9 Remove FCM Token (Admin)

- Method: `DELETE`
- URL: `/admin/api/v1/software/fcm-tokens`
- Auth: Sanctum
- Body:
    - `token` required string

```bash
curl -X DELETE "http://127.0.0.1:8000/admin/api/v1/software/fcm-tokens" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"token":"FCM_TOKEN_VALUE"}'
```

---

## 4) Customer V1 APIs

Base prefix:

- `/customer/api/v1/software`

### 4.1 Register FCM Token (Customer)

- Method: `POST`
- URL: `/customer/api/v1/software/fcm-tokens`
- Auth: `auth:customer`
- Body:
    - `token` required
    - `device_name` optional

```bash
curl -X POST "http://127.0.0.1:8000/customer/api/v1/software/fcm-tokens" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Cookie: YOUR_CUSTOMER_SESSION_COOKIE" \
  -d '{
    "token": "FCM_TOKEN_VALUE",
    "device_name": "Flutter Android"
  }'
```

### 4.2 Remove FCM Token (Customer)

- Method: `DELETE`
- URL: `/customer/api/v1/software/fcm-tokens`
- Auth: `auth:customer`

```bash
curl -X DELETE "http://127.0.0.1:8000/customer/api/v1/software/fcm-tokens" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Cookie: YOUR_CUSTOMER_SESSION_COOKIE" \
  -d '{"token":"FCM_TOKEN_VALUE"}'
```

### 4.3 List Customer Notifications

- Method: `GET`
- URL: `/customer/api/v1/software/notifications`
- Auth: `auth:customer`
- Query:
    - `per_page` optional (default 20)

```bash
curl -X GET "http://127.0.0.1:8000/customer/api/v1/software/notifications?per_page=20" \
  -H "Accept: application/json" \
  -H "Cookie: YOUR_CUSTOMER_SESSION_COOKIE"
```

### 4.4 Mark Notification as Read

- Method: `POST`
- URL: `/customer/api/v1/software/notifications/{notification}/read`
- Auth: `auth:customer`

```bash
curl -X POST "http://127.0.0.1:8000/customer/api/v1/software/notifications/50/read" \
  -H "Accept: application/json" \
  -H "Cookie: YOUR_CUSTOMER_SESSION_COOKIE"
```

### 4.5 Mark All Notifications as Read

- Method: `POST`
- URL: `/customer/api/v1/software/notifications/read-all`
- Auth: `auth:customer`

```bash
curl -X POST "http://127.0.0.1:8000/customer/api/v1/software/notifications/read-all" \
  -H "Accept: application/json" \
  -H "Cookie: YOUR_CUSTOMER_SESSION_COOKIE"
```

---

## 5) Legacy APIs

Base prefix:

- `/api`

Authentication required for all legacy endpoints:

- Header: `X-Legacy-Api-Key: YOUR_KEY`
- أو Query: `?api_key=YOUR_KEY`

Legacy requests تقبل JSON، وأيضًا payload خام بصيغة قديمة (form/x-www-form-urlencoded) في بعض endpoints.

### 5.1 Insert License

- Method: `POST`
- URL: `/api/insert-licenses`
- Required:
    - `CompanyName`
    - `ProductID` (exists: software_programs.id)
    - `ClientID` (exists: partners_partners.id)
- Optional:
    - `GoverID`, `CityID`, `Address`, `LicenseType`, `Period`

```bash
curl -X POST "http://127.0.0.1:8000/api/insert-licenses" \
  -H "X-Legacy-Api-Key: YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "CompanyName":"Acme Co",
    "ProductID":1,
    "ClientID":12,
    "GoverID":3,
    "CityID":25,
    "Address":"Nasr City",
    "LicenseType":"annual",
    "Period":365
  }'
```

### 5.2 Insert Device Keys

- Method: `POST`
- URL: `/api/insert-keys`
- Required:
    - `License_ID`
    - `Computer_ID`
    - `Bios_ID`
    - `Disk_ID`
    - `Base_ID`
    - `Video_ID`
    - `Mac_ID`

```bash
curl -X POST "http://127.0.0.1:8000/api/insert-keys" \
  -H "X-Legacy-Api-Key: YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "License_ID":123,
    "Computer_ID":"PC-001",
    "Bios_ID":"BIOS-AAA",
    "Disk_ID":"DISK-BBB",
    "Base_ID":"BASE-CCC",
    "Video_ID":"GPU-DDD",
    "Mac_ID":"00-11-22-33-44-55"
  }'
```

### 5.3 License Info (Validate Product Key)

- Method: `POST`
- URL: `/api/license-info`
- Alias URL: `/api/LicGen/info`
- Required:
    - `Computer_ID`
    - `ProductKey`

```bash
curl -X POST "http://127.0.0.1:8000/api/license-info" \
  -H "X-Legacy-Api-Key: YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "Computer_ID":"PC-001",
    "ProductKey":"ABCDE-FGHIJ-KLMNO-PQRST"
  }'
```

### 5.4 Get Client ID by Email

- Method: `GET` or `POST`
- URL: `/api/client-id`
- Required:
    - `email`

```bash
curl -X GET "http://127.0.0.1:8000/api/client-id?email=user@example.com" \
  -H "X-Legacy-Api-Key: YOUR_KEY"
```

### 5.5 Get Product Editions by Program Name

- Method: `GET`
- URL: `/api/product`
- Required query:
    - `name` (program name)

```bash
curl -X GET "http://127.0.0.1:8000/api/product?name=MyProgram" \
  -H "X-Legacy-Api-Key: YOUR_KEY"
```

### 5.6 List Governorates

- Method: `GET`
- URL: `/api/governorates`

```bash
curl -X GET "http://127.0.0.1:8000/api/governorates" \
  -H "X-Legacy-Api-Key: YOUR_KEY"
```

### 5.7 List Cities by Governorate

- Method: `GET`
- URL: `/api/city`
- Required query:
    - `goverID` (exists in `states.id`)

```bash
curl -X GET "http://127.0.0.1:8000/api/city?goverID=3" \
  -H "X-Legacy-Api-Key: YOUR_KEY"
```

---

## 6) Common Error Cases

- `401 Unauthorized` في Legacy: تحقق من `X-Legacy-Api-Key`.
- `422 Validation Error`: الحقول ناقصة أو IDs غير موجودة.
- `403` في Customer notifications: محاولة الوصول لإشعار لا يخص العميل.
- `500`: خطأ داخلي (في الإنتاج الرسالة تكون مختصرة).

## 7) Notes

- Tickets API تدعم include/filter/sort عبر Spatie Query Builder.
- رفع المرفقات في Tickets/Replies يكون multipart.
- Legacy API تم تصميمها لتدعم payload القديم بجانب JSON.
