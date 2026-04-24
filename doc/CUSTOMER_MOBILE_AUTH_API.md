# Customer Mobile Auth API

هذا الملف هو المرجع الأساسي لربط مشروع Flutter الحالي مع واجهة تسجيل ودخول العملاء.

## Base Path

- `POST /customer/api/v1/auth/register`
- `POST /customer/api/v1/auth/login`
- `GET /customer/api/v1/auth/me`
- `POST /customer/api/v1/auth/logout`
- `GET /customer/api/v1/auth/locations/countries`
- `GET /customer/api/v1/auth/locations/states?country_id={id}`
- `GET /customer/api/v1/auth/locations/cities?state_id={id}`

استخدم الهيدر التالي في أي endpoint محمي:

```http
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

## 1) Register

### Request

```json
{
    "name": "Ahmed Ali",
    "email": "customer@example.com",
    "phone": "+201001234567",
    "country_id": 65,
    "state_id": 1524,
    "city_id": 32012,
    "street1": "Nasr City - Street 10",
    "password": "password123",
    "password_confirmation": "password123",
    "device_name": "flutter-android"
}
```

### Success Response `201`

```json
{
    "message": "Customer registered successfully. Please verify your email before login.",
    "email_verification": {
        "required": true,
        "verified": false
    },
    "data": {
        "id": 1,
        "name": "Ahmed Ali",
        "email": "customer@example.com",
        "phone": "+201001234567",
        "mobile": null,
        "country_id": 65,
        "state_id": 1524,
        "city": "Cairo",
        "street1": "Nasr City - Street 10",
        "avatar_url": null,
        "is_active": true,
        "email_verified_at": null,
        "created_at": "2026-04-19T12:00:00.000000Z",
        "updated_at": "2026-04-19T12:00:00.000000Z"
    }
}
```

### Important Behavior

- بعد التسجيل لا يتم إرجاع `token`.
- يتم إرسال رسالة تأكيد بريد إلكتروني للعميل.
- لا يمكن للعميل تسجيل الدخول من الموبايل قبل تأكيد البريد.

## 2) Login

### Request

```json
{
    "email": "customer@example.com",
    "password": "password123",
    "device_name": "flutter-ios"
}
```

### Success Response `200`

```json
{
    "message": "Login successful.",
    "token": "1|plain-text-token",
    "token_type": "Bearer",
    "data": {
        "id": 1,
        "name": "Ahmed Ali",
        "email": "customer@example.com",
        "phone": null,
        "mobile": null,
        "country_id": 65,
        "state_id": 1524,
        "city": "Cairo",
        "street1": "Nasr City - Street 10",
        "avatar_url": null,
        "is_active": true,
        "email_verified_at": null,
        "created_at": "2026-04-19T12:00:00.000000Z",
        "updated_at": "2026-04-19T12:00:00.000000Z"
    }
}
```

### Error Response `403`

```json
{
    "message": "Please verify your email before logging in."
}
```

### Error Response `422`

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The provided credentials are incorrect."]
    }
}
```

## 3) Get Current Customer

### Request

- Method: `GET`
- URL: `/customer/api/v1/auth/me`
- Header: `Authorization: Bearer {token}`

### Success Response `200`

```json
{
    "data": {
        "id": 1,
        "name": "Ahmed Ali",
        "email": "customer@example.com",
        "phone": null,
        "mobile": null,
        "country_id": 65,
        "state_id": 1524,
        "city": "Cairo",
        "street1": "Nasr City - Street 10",
        "avatar_url": null,
        "is_active": true,
        "email_verified_at": null,
        "created_at": "2026-04-19T12:00:00.000000Z",
        "updated_at": "2026-04-19T12:00:00.000000Z"
    }
}
```

## 4) Logout

### Request

- Method: `POST`
- URL: `/customer/api/v1/auth/logout`
- Header: `Authorization: Bearer {token}`

### Success Response `200`

```json
{
    "message": "Logout successful."
}
```

## 5) Countries List

### Request

- Method: `GET`
- URL: `/customer/api/v1/auth/locations/countries`

### Success Response `200`

```json
{
    "data": [
        {
            "id": 65,
            "name": "Egypt",
            "code": "EG",
            "phone_code": "20"
        }
    ]
}
```

## 6) States List By Country

### Request

- Method: `GET`
- URL: `/customer/api/v1/auth/locations/states?country_id=65`

### Success Response `200`

```json
{
    "data": [
        {
            "id": 1524,
            "name": "Cairo",
            "code": "C",
            "country_id": 65
        }
    ]
}
```

## 7) Cities List By State

### Request

- Method: `GET`
- URL: `/customer/api/v1/auth/locations/cities?state_id=1524`

### Success Response `200`

```json
{
    "data": [
        {
            "id": 32012,
            "name": "Nasr City",
            "state_id": 1524
        }
    ]
}
```

## Flutter Flow

1. عند التسجيل، اعرض للمستخدم رسالة واضحة أنه يجب تأكيد البريد الإلكتروني قبل تسجيل الدخول.
2. لا تتوقع وجود `token` في رد التسجيل.
3. عند تسجيل الدخول، إذا عاد الرد `403` فاعرض للمستخدم أن البريد غير مؤكد بعد.
4. عند نجاح تسجيل الدخول فقط، خزّن قيمة `token` في `flutter_secure_storage`.
5. قبل شاشة التسجيل، حمّل `countries`.
6. بعد اختيار الدولة (country)، حمّل `states` باستخدام `country_id`.
7. بعد اختيار المحافظة/الولاية (state)، حمّل `cities` باستخدام `state_id`.
8. أرسل `country_id`, `state_id`, `city_id` في register.
9. أرسل الـ token في `Authorization` لكل request محمي.
10. بعد فتح التطبيق، استدعِ `/customer/api/v1/auth/me` للتأكد من أن الجلسة ما زالت صالحة.
11. عند تسجيل الخروج، استدعِ `/customer/api/v1/auth/logout` ثم احذف الـ token محلياً.

## Suggested Dart Models

### Auth response fields

- `message`
- `email_verification.required` في رد التسجيل
- `email_verification.verified` في رد التسجيل
- `token` في رد تسجيل الدخول فقط
- `token_type` في رد تسجيل الدخول فقط
- `data`

### Customer fields

- `id`
- `name`
- `email`
- `phone`
- `mobile`
- `country_id`
- `state_id`
- `city`
- `street1`
- `avatar_url`
- `is_active`
- `email_verified_at`
- `created_at`
- `updated_at`

## Notes

- الـ API الحالية موجهة للأساسيات فقط: register, login, me, logout.
- التسجيل يتطلب تأكيد البريد الإلكتروني قبل أول login.
- رد التسجيل لم يعد يحتوي على token.
- لو حاول العميل تسجيل الدخول قبل تأكيد البريد سيرجع API كود `403`.
- باقي customer endpoints في Flutter يمكن ربطها لاحقاً على نفس نمط `Bearer token`.
- إذا كان التطبيق سيستخدم refresh/session strategy مختلفة لاحقاً، يمكن إضافة endpoint خاص بتجديد التوكنات في المرحلة التالية.
