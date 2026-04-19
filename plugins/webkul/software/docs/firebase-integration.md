# Firebase Integration — Software Plugin

## نظرة عامة

تم دمج Firebase في الـ Software plugin لتحقيق هدفين:

1. **Push Notifications (FCM):** إشعارات فورية للعميل على Flutter عند رد الـ admin، وللـ admins عند رد العميل — بدلاً من `wire:poll`.
2. **Real-time RTDB Listener:** المتصفح (Admin Panel) يستمع لـ Firebase Realtime Database ويعمل `$wire.$refresh()` تلقائياً عند وصول رد جديد — بدون polling.

---

## الملفات المُنشأة / المُعدَّلة

### Config

| الملف                 | التغيير                                                                  |
| --------------------- | ------------------------------------------------------------------------ |
| `config/firebase.php` | نُشر من الـ package — يُحدَّد بـ `FIREBASE_CREDENTIALS`                  |
| `config/sanctum.php`  | أُضيف `'customer'` للـ guards حتى يعمل `auth:customer` مع Sanctum tokens |
| `config/services.php` | أُضيف section `firebase_web` لإعدادات الـ Web SDK                        |

### Database

**`plugins/webkul/software/database/migrations/2026_04_19_000030_create_software_fcm_tokens_table.php`**

جدول `software_fcm_tokens` لتخزين FCM registration tokens:

| العمود        | النوع           | الوصف                             |
| ------------- | --------------- | --------------------------------- |
| `user_id`     | FK nullable     | Admin (من `users`)                |
| `partner_id`  | FK nullable     | Customer (من `partners_partners`) |
| `token`       | string unique   | FCM registration token من الجهاز  |
| `device_name` | string nullable | اسم الجهاز (اختياري)              |

### Models

**`plugins/webkul/software/src/Models/FcmToken.php`**

Eloquent model بسيط مع علاقتين:

- `user()` → `BelongsTo(User::class)`
- `partner()` → `BelongsTo(Partner::class)`

### Services

**`plugins/webkul/software/src/Services/FirebaseNotificationService.php`**

| الـ method                                    | الوصف                                                          |
| --------------------------------------------- | -------------------------------------------------------------- |
| `notifyCustomer($ticket, $title, $body)`      | يجيب كل FCM tokens للـ partner ويرسل إشعار                     |
| `notifyAdmins($ticket, $title, $body)`        | يجيب كل FCM tokens للـ admins (`user_id NOT NULL`) ويرسل إشعار |
| `sendToTokens($tokens, $title, $body, $data)` | multicast بـ chunks من 500، يحذف الـ invalid tokens تلقائياً   |

**`plugins/webkul/software/src/Services/TicketService.php`** — (مُعدَّل)

بعد كل `replyToTicket()`:

1. يـ dispatch الـ `NotifyTicketUpdate` job (queued).
2. يكتب signal في Firebase RTDB على `tickets/{ticket_id}/last_event`.

### Jobs

**`plugins/webkul/software/src/Jobs/NotifyTicketUpdate.php`**

Queued job يحدد نوع الرد ويوجّه الإشعار للطرف المناسب:

```
Admin رد  → notifyCustomer()
Customer رد → notifyAdmins()
```

النص المُرسَل هو أول 100 حرف من الرد (مسلوب الـ HTML tags).

### HTTP

**`plugins/webkul/software/src/Http/Requests/FcmTokenRequest.php`**

Form Request للـ validation:

- `token` — required, string, max 255
- `device_name` — nullable, string, max 100

**`plugins/webkul/software/src/Http/Controllers/API/V1/FcmTokenController.php`**

| الـ method  | Route               | الوصف                             |
| ----------- | ------------------- | --------------------------------- |
| `store()`   | `POST fcm-tokens`   | تسجيل أو تحديث FCM token (upsert) |
| `destroy()` | `DELETE fcm-tokens` | حذف FCM token (عند logout)        |

الـ controller يحدد تلقائياً هل المُستخدم admin أو customer بناءً على الـ guard.

### Routes

**`plugins/webkul/software/routes/api.php`**

```
# Admin
POST   /admin/api/v1/software/fcm-tokens    [auth:sanctum]
DELETE /admin/api/v1/software/fcm-tokens    [auth:sanctum]

# Customer (Flutter)
POST   /customer/api/v1/software/fcm-tokens [auth:customer]
DELETE /customer/api/v1/software/fcm-tokens [auth:customer]
```

### Frontend (Blades)

تم إزالة `wire:poll` واستبداله بـ Firebase RTDB JS listeners:

| الملف                                 | التغيير                                                                              |
| ------------------------------------- | ------------------------------------------------------------------------------------ |
| `ticket-conversation-panel.blade.php` | Alpine component `ticketConversation(ticketId)` — يستمع لـ `tickets/{id}/last_event` |
| `open-tickets-sidebar.blade.php`      | Alpine component `openTicketsSidebar()` — يستمع لـ `tickets/` بالكامل                |

كلاهما عند تغيير الـ RTDB value ينادي `this.$wire.$refresh()` ليعيد render الـ Livewire component.

---

## إعداد البيئة (`.env`)

### 1. Firebase Admin SDK (Server-side)

نزّل الـ Service Account JSON من Firebase Console:
**Project Settings → Service Accounts → Generate new private key**

```env
FIREBASE_CREDENTIALS=/absolute/path/to/service-account.json
```

أو ضع الـ JSON كـ base64:

```env
FIREBASE_CREDENTIALS={"type":"service_account","project_id":"..."}
```

### 2. Firebase Web SDK (Browser JS)

من Firebase Console: **Project Settings → Your Apps → Web App → Config**

```env
FIREBASE_WEB_API_KEY=AIzaSy...
FIREBASE_WEB_AUTH_DOMAIN=yourapp.firebaseapp.com
FIREBASE_WEB_DATABASE_URL=https://yourapp-default-rtdb.firebaseio.com
FIREBASE_WEB_PROJECT_ID=yourapp
FIREBASE_WEB_STORAGE_BUCKET=yourapp.appspot.com
FIREBASE_WEB_MESSAGING_SENDER_ID=123456789
FIREBASE_WEB_APP_ID=1:123:web:abc
```

> **ملاحظة:** هذه القيم آمنة لأنها تُعرَض في الـ browser — Firebase تحمي البيانات بالـ Security Rules وليس بإخفاء الـ config.

---

## إعداد Flutter

### تسجيل FCM Token

بعد تسجيل دخول العميل، ارسل الـ token للـ API:

```dart
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:http/http.dart' as http;

final token = await FirebaseMessaging.instance.getToken();

await http.post(
  Uri.parse('https://your-domain.com/customer/api/v1/software/fcm-tokens'),
  headers: {
    'Authorization': 'Bearer $sanctumToken',
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: jsonEncode({
    'token': token,
    'device_name': 'Samsung S24',
  }),
);
```

### حذف FCM Token عند Logout

```dart
await http.delete(
  Uri.parse('https://your-domain.com/customer/api/v1/software/fcm-tokens'),
  headers: {
    'Authorization': 'Bearer $sanctumToken',
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: jsonEncode({'token': token}),
);
```

### استقبال الإشعار في الـ Background

```dart
FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);

@pragma('vm:entry-point')
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  final ticketId = message.data['ticket_id'];
  // navigate to ticket screen
}
```

---

## Firebase Realtime Database Security Rules

```json
{
    "rules": {
        "tickets": {
            "$ticketId": {
                ".read": "auth != null",
                ".write": false
            }
        }
    }
}
```

> الـ Server (Admin SDK) يكتب بـ Service Account لا يخضع للـ Security Rules.
> الـ Browser يقرأ فقط — لا يمكنه الكتابة.

---

## تشغيل الـ Queue

الـ push notifications تُرسَل عبر queued jobs. تأكد أن الـ queue worker شغّال:

```bash
php artisan queue:work --tries=3
```

أو في الـ production استخدم Supervisor.

---

## تدفق العملية (Sequence)

```
العميل يرسل رد (Flutter / Customer Portal)
    ↓
TicketService::replyToTicket()
    ↓
    ├── يحدّث قاعدة البيانات
    ├── dispatch NotifyTicketUpdate (queued)
    │       ↓
    │   FirebaseNotificationService::notifyAdmins()
    │       ↓
    │   FCM → كل الـ admin devices
    │
    └── يكتب signal في RTDB: tickets/{id}/last_event
            ↓
        Browser listener (Alpine JS)
            ↓
        $wire.$refresh() → Livewire re-render فوري
```

```
Admin يرسل رد (Admin Panel)
    ↓
TicketService::replyToTicket()
    ↓
    ├── يحدّث قاعدة البيانات
    ├── dispatch NotifyTicketUpdate (queued)
    │       ↓
    │   FirebaseNotificationService::notifyCustomer()
    │       ↓
    │   FCM → أجهزة العميل (Flutter)
    │
    └── يكتب signal في RTDB: tickets/{id}/last_event
            ↓
        Browser listener (Alpine JS) للـ admin الثاني المفتوح
            ↓
        $wire.$refresh() → Livewire re-render فوري
```
