# Customer Mobile Auth API

هذا الملف هو المرجع الأساسي لربط مشروع Flutter الحالي مع واجهة تسجيل ودخول العملاء.

## Base Path

- `POST /customer/api/v1/auth/register`
- `POST /customer/api/v1/auth/login`
- `GET /customer/api/v1/auth/me`
- `POST /customer/api/v1/auth/logout`

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
    "password": "password123",
    "password_confirmation": "password123",
    "device_name": "flutter-android"
}
```

### Success Response `201`

```json
{
    "message": "Customer registered successfully.",
    "token": "1|plain-text-token",
    "token_type": "Bearer",
    "data": {
        "id": 1,
        "name": "Ahmed Ali",
        "email": "customer@example.com",
        "phone": null,
        "mobile": null,
        "avatar_url": null,
        "is_active": true,
        "email_verified_at": null,
        "created_at": "2026-04-19T12:00:00.000000Z",
        "updated_at": "2026-04-19T12:00:00.000000Z"
    }
}
```

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
        "avatar_url": null,
        "is_active": true,
        "email_verified_at": null,
        "created_at": "2026-04-19T12:00:00.000000Z",
        "updated_at": "2026-04-19T12:00:00.000000Z"
    }
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

## Flutter Flow

1. عند التسجيل أو الدخول، خزّن قيمة `token` في `flutter_secure_storage`.
2. أرسل الـ token في `Authorization` لكل request محمي.
3. بعد فتح التطبيق، استدعِ `/customer/api/v1/auth/me` للتأكد من أن الجلسة ما زالت صالحة.
4. عند تسجيل الخروج، استدعِ `/customer/api/v1/auth/logout` ثم احذف الـ token محلياً.

## Suggested Dart Models

### Auth response fields

- `message`
- `token`
- `token_type`
- `data`

### Customer fields

- `id`
- `name`
- `email`
- `phone`
- `mobile`
- `avatar_url`
- `is_active`
- `email_verified_at`
- `created_at`
- `updated_at`

## Notes

- الـ API الحالية موجهة للأساسيات فقط: register, login, me, logout.
- باقي customer endpoints في Flutter يمكن ربطها لاحقاً على نفس نمط `Bearer token`.
- إذا كان التطبيق سيستخدم refresh/session strategy مختلفة لاحقاً، يمكن إضافة endpoint خاص بتجديد التوكنات في المرحلة التالية.
