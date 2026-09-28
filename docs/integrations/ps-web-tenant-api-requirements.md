# PS-Web Tenant API Integration Requirements

هذه الوثيقة هي عقد التسليم المطلوب تنفيذه في مشروع `ps-web` لكي يتكامل مع موديول الأنظمة الأونلاين في `aureuserp`.

## 1. القرارات الثابتة

- `aureuserp` هو مصدر الحقيقة للعملاء، الأسعار، الفواتير، الرصيد، الدفع، ودورية التجديد.
- `ps-web` هو المسؤول عن إنشاء وإدارة قاعدة بيانات الـ Tenant وإرسال بريد جاهزية الموقع.
- حقل `user_id` هو رقم العميل `Partner ID` داخل `aureuserp`. لا يدخله الأدمن أو العميل يدويًا؛ يرسله Backend `aureuserp` تلقائيًا.
- يتم إنشاء مستخدم واحد فقط داخل الـ Tenant لصاحب الموقع باستخدام `name` و`email` المرسلين.
- البريد المرسل في API هو نفس البريد المسجل للعميل في `aureuserp`.
- لا يوجد طلب لتغيير آلية كلمة المرور الابتدائية الحالية؛ يغيرها صاحب الـ Tenant بعد أول دخول.
- الخادمان يعملان بتوقيت UTC، وكل قيم `datetime` ترسل بصيغة ISO 8601 مع timezone.
- يجب أن تكون قيمة `APP_TIMEZONE=UTC` في بيئة الإنتاج للطرفين؛ جدول التجديد في `aureuserp` مضبوط صراحةً على UTC أيضًا.
- `ended_at` لحظة تاريخ ووقت كاملة وليست نهاية يوم افتراضية.
- اختيار وفحص أسماء الـ subdomain المحجوزة يتم في `aureuserp`. يظل على `ps-web` فرض uniqueness وحجز الدومين بصورة atomic عند الإنشاء.

## 2. المصادقة

كل طلب من `aureuserp` إلى `ps-web` يرسل:

```http
Authorization: Bearer <TENANT_PROVISIONING_API_TOKEN>
Accept: application/json
Content-Type: application/json
Idempotency-Key: <stable-unique-operation-key>
```

إعداد الربط المقابل بين المشروعين:

- قيمة `API Token` في سجل النظام داخل `aureuserp` يجب أن تكون واحدة من القيم الموجودة في `TENANT_PROVISIONING_API_TOKENS` داخل بيئة `ps-web`.
- قيمة `API Secret` في سجل النظام ذي `slug=ps-web` داخل `aureuserp` يجب أن تساوي `TENANT_WEBHOOK_SECRET` داخل بيئة `ps-web`، وتُحفظ مشفرة في قاعدة بيانات `aureuserp`.
- داخل `ps-web` تكون `AUREUSERP_BASE_URL` هي رابط `aureuserp` العام، و`TENANT_WEBHOOK_SYSTEM=ps-web`، و`SOFTWARE_ONLINE_WEBHOOK_PATH=api/webhooks/ps-web/tenant-events`.
- لا تُستخدم قيمة `TENANT_WEBHOOK_SECRET` كـ Bearer token؛ مفتاح الـ API وسر توقيع الـ Webhook قيمتان مستقلتان.

المطلوب من `ps-web`:

- عدم تخزين التوكن في logs.
- دعم تدوير التوكن دون توقف طويل للخدمة.
- تطبيق rate limiting مع عدم كسر retries المشروعة.
- دعم `Idempotency-Key` في الإنشاء والتجديد وتحديث المزايا والحذف.
- إعادة نفس النتيجة عند تكرار المفتاح ونفس payload.
- إرجاع `409 IDEMPOTENCY_KEY_REUSED` إذا استُخدم المفتاح نفسه مع payload مختلف.

## 3. إنشاء Tenant

```http
POST /api/tenants
```

### Request

```json
{
  "domain": "client-name.ps-web.example",
  "name": "Client Name",
  "email": "client@example.com",
  "started_at": "2026-09-27T10:00:00Z",
  "ended_at": "2026-10-27T10:00:00Z",
  "user_id": 105,
  "external_subscription_id": "online-instance:481",
  "auto_renew": true,
  "branches_limit": 2,
  "has_ai_subscription": true,
  "ai_requests_limit": 20
}
```

### قواعد الحقول

- `user_id`: رقم العميل في `aureuserp` للربط فقط، وليس User ID داخل Tenant DB.
- `external_subscription_id`: مرجع ثابت وفريد للاشتراك في `aureuserp` ويجب حفظه وإرجاعه في الـ webhooks.
- يمكن للعميل الواحد امتلاك أكثر من Tenant، لذلك `user_id` ليس unique.
- `external_subscription_id` يجب أن يكون unique.
- `domain` يجب حجزه داخل transaction أو قفل مناسب لمنع طلبين متزامنين من إنشائه.
- `email` هو البريد الوحيد المستخدم لإنشاء صاحب الـ Tenant وإرسال رسالة الجاهزية.

### الاستجابة المطلوبة

```http
202 Accepted
```

```json
{
  "message": "Tenant provisioning request has been queued.",
  "external_subscription_id": "online-instance:481",
  "status": "queued",
  "domain": "client-name.ps-web.example"
}
```

استجابة `202` تعني فقط أن الطلب تم التحقق منه ووُضع في الـ Queue؛ لا تعني أن الموقع أصبح جاهزًا. لا يُشترط إنشاء أو إرجاع `tenant_id` في هذه المرحلة. يحتفظ `ps-web` بقيمة `external_subscription_id` داخل بيانات الـ Job ثم يعيدها كما هي في جميع Webhooks الخاصة بالعملية.

يمكن إرجاع `provisioning_request_id` اختياريًا إذا كان `ps-web` ينشئ سجلًا لطلب التجهيز قبل dispatch، لكنه ليس شرطًا لإتمام الربط لأن المرجع الأساسي هو `external_subscription_id`.

داخل `ProvisionTenantJob` يجب الحفاظ على التسلسل التالي:

1. الاحتفاظ بـ `external_subscription_id` ضمن بيانات الـ Job وسجل الـ Tenant المركزي.
2. إنشاء قاعدة بيانات الـ Tenant وتشغيل migrations وseeders وإنشاء مستخدم صاحب الـ Tenant.
3. تثبيت `tenant_id` والدومين وحالة الجاهزية داخل transaction مناسبة.
4. بعد نجاح الـ commit فقط إرسال `tenant.provisioned` متضمنًا `tenant_id` و`external_subscription_id`.
5. عند فشل أي مرحلة إرسال `tenant.provisioning_failed` بنفس `external_subscription_id` مع كود الخطأ وقابلية إعادة المحاولة.

## 4. متابعة حالة التجهيز

```http
GET /api/tenants/{tenantId}/status
```

هذا الـ endpoint يصبح متاحًا بعد وصول `tenant_id` في Webhook النجاح. قبل ذلك يعتمد `aureuserp` على حالته المحلية `provisioning` وعلى Webhook النتيجة.

```json
{
  "tenant_id": "01J...ULID",
  "provisioning_request_id": "01K...ULID",
  "external_subscription_id": "online-instance:481",
  "status": "ready",
  "domain": "client-name.ps-web.example",
  "login_url": "https://client-name.ps-web.example/admin/login",
  "started_at": "2026-09-27T10:00:00Z",
  "ended_at": "2026-10-27T10:00:00Z",
  "error_code": null,
  "error_message": null,
  "retryable": false,
  "completed_at": "2026-09-27T10:03:12Z"
}
```

الحالات المطلوبة: `pending`, `provisioning`, `ready`, `failed`, `suspended`, `deleting`, `deleted`.

## 5. تجديد Tenant

التجديد المالي يتم بالكامل داخل `aureuserp`: يحسب إجمالي الفاتورة، يفحص الرصيد المقفول، ينشئ ويرحّل الفاتورة، يسويها من رصيد العميل، ثم يرسل التاريخ النهائي إلى `ps-web`.

```http
POST /api/tenants/{tenantId}/renew
```

```json
{
  "ended_at": "2026-11-27T10:00:00Z"
}
```

المطلوب:

- حفظ `ended_at` كما أُرسل كـ UTC datetime.
- إعادة تفعيل Tenant عند نجاح التجديد.
- السماح بإعادة نفس القيمة بأمان.
- عدم إنشاء فاتورة أو محاولة خصم أموال داخل `ps-web`.
- عدم تقصير الاشتراك الحالي.
- دعم التجديد خلال فترة السماح الحالية.

```json
{
  "tenant_id": "01J...ULID",
  "external_subscription_id": "online-instance:481",
  "ended_at": "2026-11-27T10:00:00Z",
  "is_active": true
}
```

## 6. المزايا والحذف

### الإيقاف والتفعيل

```http
POST /api/tenants/{tenantId}/suspend
POST /api/tenants/{tenantId}/activate
```

- الطلبان لا يستقبلان مبلغًا أو تاريخًا ولا ينشئان عمليات مالية داخل `ps-web`.
- الإيقاف يضبط `is_active=false` والحالة `suspended` ثم يرسل `tenant.suspended`.
- التفعيل يضبط `is_active=true` والحالة `ready` ثم يرسل `tenant.activated`.
- لا يجوز تفعيل Tenant منتهي؛ يجب تنفيذ التجديد أولًا.
- كلا الطلبين يدعمان `Idempotency-Key` وقفل دورة حياة التينانت.

### المزايا والحذف

تظل endpoints الحالية مطلوبة:

```http
PATCH /api/tenants/{tenantId}/entitlements
DELETE /api/tenants/{tenantId}
```

- تحديث المزايا يستقبل القيم النهائية، وليس مقدار الزيادة.
- الحذف غير المتزامن يرجع `202` وحالة `deleting` ثم يرسل Webhook نهائيًا.
- لا يحذف تخفيض `branches_limit` الفروع الموجودة.

## 7. Webhook من PS-Web إلى AureusERP

### URL

```http
POST <AUREUSERP_BASE_URL>/api/webhooks/ps-web/tenant-events
```

يمكن تغيير المسار في `aureuserp` عبر:

```dotenv
SOFTWARE_ONLINE_WEBHOOK_PATH=api/webhooks/ps-web/tenant-events
SOFTWARE_ONLINE_WEBHOOK_TOLERANCE=300
```

### المفتاح المشترك

يتم ضبط نفس Webhook signing secret في:

- إعداد `API Secret` للنظام الأونلاين ذي slug يساوي `ps-web` داخل `aureuserp`.
- إعداد آمن مقابل داخل `ps-web`.

هذا السر منفصل عن Bearer API token.

### Headers

```http
Content-Type: application/json
Accept: application/json
X-Online-System: ps-web
X-Webhook-Timestamp: 1790503392
X-Webhook-Signature: sha256=<hex-hmac>
```

حساب التوقيع:

```text
signed_payload = X-Webhook-Timestamp + "." + raw_request_body
signature = "sha256=" + HMAC_SHA256(signed_payload, webhook_secret)
```

يجب التوقيع على raw JSON body نفسه قبل إرساله. السماح الحالي لفارق الوقت هو 300 ثانية.

### Payload

```json
{
  "event_id": "01K...ULID",
  "event_type": "tenant.provisioned",
  "occurred_at": "2026-09-27T10:03:12Z",
  "data": {
    "tenant_id": "01J...ULID",
    "provisioning_request_id": null,
    "external_subscription_id": "online-instance:481",
    "user_id": 105,
    "domain": "client-name.ps-web.example",
    "login_url": "https://client-name.ps-web.example/admin/login",
    "started_at": "2026-09-27T10:00:00Z",
    "ended_at": "2026-10-27T10:00:00Z",
    "is_active": true,
    "error_code": null,
    "error_message": null
  }
}
```

في حدث `tenant.provisioned` يكون كل من `external_subscription_id` و`tenant_id` إلزاميين. يتم البحث عن الاشتراك المحلي أولًا باستخدام `external_subscription_id`، ثم تُحفظ قيمة `tenant_id` عليه لاستخدامها في التجديد والتحديث والحذف لاحقًا.

في حدث `tenant.provisioning_failed` يظل `external_subscription_id` إلزاميًا، بينما يمكن ألا توجد قيمة `tenant_id` لأن الفشل قد يحدث قبل إنشائه.

### أنواع الأحداث المقبولة

- `tenant.provisioning_started`
- `tenant.provisioned`
- `tenant.provisioning_failed`
- `tenant.renewed`
- `tenant.suspended`
- `tenant.activated`
- `tenant.deletion_started`
- `tenant.deleted`
- `tenant.entitlements_updated`

عند الفشل، يجب إرسال `error_code`, `error_message`, و`retryable` داخل `data`.

### التسليم وإعادة المحاولة

- `event_id` ثابت وفريد ولا يتغير عند retry.
- `occurred_at` يجب أن يمثل وقت حدوث الحدث الحقيقي؛ `aureuserp` يتجاهل أثر أي حدث أقدم من آخر حدث تمت معالجته لنفس الاشتراك.
- `aureuserp` يعيد `200` للأحداث المعالجة وللنسخة المكررة من حدث معالج.
- يعاد الإرسال عند network failure أو `5xx` باستخدام exponential backoff.
- لا يعاد الإرسال تلقائيًا عند `401` لأن التوقيع أو الإعداد غير صحيح.
- يمكن إعادة الإرسال عند `429` مع احترام `Retry-After`.
- يجب الاحتفاظ بسجل محاولات Webhook في `ps-web` للمراجعة.

## 8. عقد الأخطاء

كل خطأ API يجب أن يحتوي على `error_code` ثابت بالإضافة إلى `message` و`errors` عند وجود validation errors.

الأكواد المطلوبة على الأقل:

- `DOMAIN_ALREADY_TAKEN`
- `SUBSCRIPTION_ALREADY_PROVISIONED`
- `IDEMPOTENCY_KEY_REUSED`
- `TENANT_NOT_FOUND`
- `TENANT_BUSY`
- `TENANT_DELETION_STARTED`
- `RENEWAL_GRACE_PERIOD_ENDED`
- `INVALID_ENTITLEMENTS`
- `PROVISIONING_FAILED`
- `UNAUTHORIZED`
- `RATE_LIMIT_EXCEEDED`

## 9. اختبارات القبول المطلوبة في PS-Web

- إنشاء Tenant يرجع `external_subscription_id` وحالة `queued` مع `202`، ولا يُشترط أن يرجع `tenant_id`.
- إنشاء المستخدم الوحيد بالبريد المرسل من API وإرسال بريد الجاهزية من `ps-web`.
- طلبان متزامنان لنفس الدومين لا ينشئان Tenantين.
- تكرار طلب الإنشاء بنفس Idempotency-Key لا يكرر Tenant أو Queue Job.
- Webhook النجاح والفشل يحمل نفس مراجع الطلب الأصلية.
- Webhook `tenant.provisioned` يحمل `tenant_id` ويربطه إلزاميًا بنفس `external_subscription_id` المرسل في طلب الإنشاء.
- Webhook يعاد إرساله بنفس `event_id` عند `5xx` أو network error.
- التجديد يحفظ UTC datetime بدقته ولا يحوله إلى date فقط.
- إعادة نفس طلب التجديد آمنة.
- الحذف يرسل `tenant.deletion_started` ثم `tenant.deleted`.
- لا يظهر API token أو Webhook secret أو كلمة المرور في logs أو response payloads.

## 10. ما يتم تنفيذه داخل AureusERP ولا يُطلب من PS-Web

- قواعد subdomain المحلية: حروف إنجليزية lowercase وأرقام و`-` فقط، دون `-` في البداية أو النهاية.
- حجز أسماء البنية الأساسية مثل `admin`, `api`, `mail`, `webmail`, `mqtt`, `cpanel`, `www`, `smtp`, `ftp`, `ns1`, `ns2` وغيرها من القائمة القابلة للضبط.
- التشغيل اليومي للتجديد التلقائي الساعة `00:05 UTC` للحالات التي تنتهي خلال 24 ساعة.
- حساب السعر وإجمالي الفاتورة والضرائب وفحص الرصيد بقفل قاعدة بيانات.
- إنشاء وترحيل وتسوية الفاتورة من رصيد العميل.
- عدم التجديد وعدم إنشاء فاتورة دائمة إذا كان الرصيد لا يغطي إجمالي الفاتورة.
- إعادة محاولة مزامنة التجديد البعيد باستخدام Idempotency-Key ثابت دون خصم العميل مرة ثانية.
