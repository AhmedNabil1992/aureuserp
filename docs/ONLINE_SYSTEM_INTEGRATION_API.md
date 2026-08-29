# مواصفات ووثيقة ربط واجهة برمجة التطبيقات (API Specification)
## بين نظام AureusERP ونظام المواقع السحابية / المستأجرين (Remote Tenant / Online Systems)

تهدف هذه الوثيقة إلى توجيه مطور النظام أو وكيل الذكاء الاصطناعي (AI Agent) المسؤول عن نظام المواقع/المستأجرين لبناء وتجهيز الـ **REST API Endpoints** المطلوبة لاستقبال طلبات الإنشاء، التجديد، الإيقاف، والتفعيل للمواقع والأنظمة تلقائياً وبأعلى معايير الأمان والاستقرار.

---

## 1. نظرة عامة على آلية الربط (Architecture Overview)

عند قيام العميل في نظام **AureusERP** بالاشتراك في باقة (نسخة تجريبية، اشتراك شهري، أو سنوي) أو تجديد اشتراكه، يقوم النظام تلقائياً بإرسال طلبات HTTP إلى نظام المواقع عبر الـ API Base URL المحدد مع Bearer Token.

```
┌─────────────────────────┐                     ┌───────────────────────────┐
│     AureusERP           │   HTTP / JSON       │   Remote Tenant System    │
│  (Software Online Hub)  │ ──────────────────> │  (e.g., PS-Web / SaaS Hub)│
│                         │   Bearer Token      │                           │
└─────────────────────────┘                     └───────────────────────────┘
```

---

## 2. إعدادات الأمان والمصادقة (Authentication & Headers)

- **نوع المصادقة:** Bearer Token
- **Headers المطلوبة في جميع الطلبات:**
  ```http
  Authorization: Bearer <YOUR_API_TOKEN>
  Accept: application/json
  Content-Type: application/json
  ```
- **رمز الخطأ في حال فشل المصادقة:** `401 Unauthorized`

---

## 3. قائمة الـ Endpoints المطلوبة (API Endpoints)

| الوظيفة | الطريقة (Method) | المسار الافتراضي (Endpoint) | الوصف |
| :--- | :--- | :--- | :--- |
| **فحص الاتصال** | `GET` | `/api/v1/ping` | التأكد من عمل الخادم واستجابته |
| **إنشاء موقع جديد** | `POST` | `/api/v1/tenants` | إنشاء وتجهيز المستأجر / الموقع السحابي |
| **تجديد الاشتراك** | `POST` | `/api/v1/tenants/{tenant_id}/renew` | تمديد تاريخ انتهاء الموقع وتحديث الخطة |
| **إيقاف الموقع مؤقتاً** | `POST` | `/api/v1/tenants/{tenant_id}/suspend` | تعليق الموقع لانتهاء الاشتراك |
| **إعادة تفعيل الموقع** | `POST` | `/api/v1/tenants/{tenant_id}/activate` | إعادة تنشيط الموقع بعد السداد |
| **مزامنة وفحص الحالة** | `GET` | `/api/v1/tenants/{tenant_id}/status` | جلب الحالة الحالية والبيانات الإحصائية |

---

## 4. تفاصيل الـ Endpoints والـ Payloads

### 1️⃣ فحص الاتصال (Ping / Health Check)
- **URL:** `GET /api/v1/ping`
- **الغرض:** يُستخدم للتحقق من الاتصال بين AureusERP والسيرفر البعيد.
- **الاستجابة المتوقعة (Response):**
  ```json
  {
    "status": "ok",
    "message": "Service is healthy",
    "timestamp": "2026-08-26T22:00:00Z"
  }
  ```

---

### 2️⃣ إنشاء وتجهيز موقع جديد (Provision New Tenant)
- **URL:** `POST /api/v1/tenants`
- **البيانات المرسلة (Request Body JSON):**
  ```json
  {
    "instance_number": 1001,
    "name": "متجر التقنية الحديثة",
    "subdomain": "tech-store",
    "custom_domain": null,
    "plan_slug": "pro-ecommerce",
    "admin_email": "client@example.com",
    "admin_username": "client_admin",
    "billing_cycle": "trial", // أو "monthly" أو "annual"
    "expires_at": "2026-09-09T23:59:59+00:00",
    "custom_payload": {
      "db_template": "ecommerce_v2",
      "features": ["pos", "multi_currency"]
    }
  }
  ```
- **حقول الطلب (Fields Description):**
  - `instance_number` *(integer)*: الرقم المرجعي الفريد للموقع في AureusERP.
  - `name` *(string)*: اسم الموقع أو الشركة.
  - `subdomain` *(string)*: النطاق الفرعي المطلوب (e.g. `tech-store`).
  - `custom_domain` *(string|null)*: الدومين المخصص إن وجد.
  - `plan_slug` *(string)*: كود الخطة أو الباقة.
  - `admin_email` *(string)*: البريد الإلكتروني لمدير الموقع.
  - `admin_username` *(string|null)*: اسم مستخدم المدير.
  - `billing_cycle` *(string)*: نوع الدورة (`trial` = تجريبي 14 يوم، `monthly` = شهري، `annual` = سنوي).
  - `expires_at` *(string ISO-8601)*: تاريخ ووقت انتهاء الصلاحية المحسوب.
  - `custom_payload` *(object|array)*: أي إعدادات إضافية تم تحديدها في باقة AureusERP.

- **الاستجابة الناجحة (Success Response - 200/201):**
  ```json
  {
    "success": true,
    "tenant_id": "tenant_abc12345",
    "instance_url": "https://tech-store.yoursaas.com",
    "admin_login_url": "https://tech-store.yoursaas.com/admin/login",
    "message": "Tenant created successfully"
  }
  ```
  *(ملاحظة: يقبل AureusERP كلاً من `tenant_id` أو `id`، وكلاً من `instance_url` أو `url`)*

- **الاستجابة في حال وجود خطأ (Error Response - 422/500):**
  ```json
  {
    "success": false,
    "message": "Subdomain is already taken."
  }
  ```

---

### 3️⃣ تجديد الاشتراك وتمديد الصلاحية (Renew Tenant)
- **URL:** `POST /api/v1/tenants/{tenant_id}/renew`
- **البيانات المرسلة (Request Body JSON):**
  ```json
  {
    "tenant_id": "tenant_abc12345",
    "billing_cycle": "monthly", // أو "annual"
    "expires_at": "2026-10-09T23:59:59+00:00"
  }
  ```
- **الاستجابة الناجحة (Success Response - 200):**
  ```json
  {
    "success": true,
    "message": "Subscription renewed successfully",
    "expires_at": "2026-10-09T23:59:59+00:00"
  }
  ```

---

### 4️⃣ إيقاف الموقع مؤقتاً (Suspend Tenant)
- **URL:** `POST /api/v1/tenants/{tenant_id}/suspend`
- **البيانات المرسلة (Request Body JSON):**
  ```json
  {
    "tenant_id": "tenant_abc12345"
  }
  ```
- **الاستجابة الناجحة (Success Response - 200):**
  ```json
  {
    "success": true,
    "status": "suspended",
    "message": "Tenant suspended successfully"
  }
  ```

---

### 5️⃣ إعادة تفعيل الموقع (Activate / Unsuspend Tenant)
- **URL:** `POST /api/v1/tenants/{tenant_id}/activate`
- **البيانات المرسلة (Request Body JSON):**
  ```json
  {
    "tenant_id": "tenant_abc12345"
  }
  ```
- **الاستجابة الناجحة (Success Response - 200):**
  ```json
  {
    "success": true,
    "status": "active",
    "message": "Tenant activated successfully"
  }
  ```

---

### 6️⃣ فحص ومزامنة حالة الموقع (Sync Status)
- **URL:** `GET /api/v1/tenants/{tenant_id}/status`
- **الاستجابة الناجحة (Success Response - 200):**
  ```json
  {
    "success": true,
    "tenant_id": "tenant_abc12345",
    "status": "active",
    "disk_usage_mb": 145.2,
    "database_size_mb": 18.5,
    "users_count": 5,
    "orders_count": 120,
    "expires_at": "2026-10-09T23:59:59+00:00"
  }
  ```

---

## 5. أمثلة تنفيذية سريعة بلغة Laravel (Example Controller Implementation)

يمكن للـ AI Agent استخدام هذا الهيكل البرمجي داخل مشروع الـ SaaS:

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantProvisioningController extends Controller
{
    public function ping()
    {
        return response()->json(['status' => 'ok', 'time' => now()]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subdomain'   => 'required|string|alpha_dash|unique:tenants,subdomain',
            'name'        => 'required|string|max:255',
            'admin_email' => 'required|email',
            'expires_at'  => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        // 1. Create Tenant (e.g. stancl/tenancy, custom DB, docker container, etc.)
        // $tenant = Tenant::create([...]);

        $tenantId = 'tenant_' . uniqid();
        $domain = $request->subdomain . '.' . config('app.domain', 'example.com');

        return response()->json([
            'success'      => true,
            'tenant_id'    => $tenantId,
            'instance_url' => 'https://' . $domain,
            'message'      => 'Tenant provisioned successfully',
        ], 201);
    }

    public function renew(Request $request, $tenantId)
    {
        // Extend tenant expires_at date
        return response()->json([
            'success' => true,
            'message' => 'Tenant subscription renewed',
            'expires_at' => $request->expires_at,
        ]);
    }

    public function suspend(Request $request, $tenantId)
    {
        // Mark tenant as suspended / redirect to renewal page
        return response()->json([
            'success' => true,
            'status'  => 'suspended',
        ]);
    }

    public function activate(Request $request, $tenantId)
    {
        // Remove suspension / reactivate tenant
        return response()->json([
            'success' => true,
            'status'  => 'active',
        ]);
    }

    public function status($tenantId)
    {
        return response()->json([
            'success'   => true,
            'tenant_id' => $tenantId,
            'status'    => 'active',
        ]);
    }
}
```

---

## 6. مسارات الـ Routes المقترحة في `routes/api.php`

```php
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/ping', [TenantProvisioningController::class, 'ping']);
    Route::post('/tenants', [TenantProvisioningController::class, 'store']);
    Route::post('/tenants/{tenant_id}/renew', [TenantProvisioningController::class, 'renew']);
    Route::post('/tenants/{tenant_id}/suspend', [TenantProvisioningController::class, 'suspend']);
    Route::post('/tenants/{tenant_id}/activate', [TenantProvisioningController::class, 'activate']);
    Route::get('/tenants/{tenant_id}/status', [TenantProvisioningController::class, 'status']);
});
```

---

> 💡 **ملاحظة:** تم ضبط نظام AureusERP ليدعم تعديل مسارات الـ Endpoints والـ Headers واسم التوكن من خلال شاشة إعدادات النظام (`OnlineSystem`) في لوحة تحكم الأدمن بكل مرونة وسهولة.
