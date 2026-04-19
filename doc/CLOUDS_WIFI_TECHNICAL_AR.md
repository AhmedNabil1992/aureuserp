# توثيق تقني: نظام Clouds - Wi-Fi

هذا الملف يغطي بنية الكود الكاملة لنظام الـ Cloud Radius وإدارة نقاط الوصول اللاسلكية (Access Points) كما هو مطبق فعليًا في المشروع.

---

## 1) المخطط العام للنظام

```
┌───────────────────────────────────────────────────────────┐
│                  Laravel Admin Panel                       │
│                                                           │
│  ┌─────────────┐   ┌──────────────┐   ┌────────────────┐ │
│  │  Clouds     │   │ DynamicClients│   │  WifiInvoices  │ │
│  │  (Cloud     │──▶│  (AP devices) │   │  (كروت الواي  │ │
│  │   Radius)   │   │              │   │    فاي)        │ │
│  └─────────────┘   └──────┬───────┘   └────────────────┘ │
│                           │                               │
│              ┌────────────▼─────────────┐                 │
│              │       LuciRpcService     │                 │
│              │   (HTTP → OpenWrt UBUS)  │                 │
│              └────────────┬─────────────┘                 │
│                           │  + MQTT                       │
│              ┌────────────▼─────────────┐                 │
│              │   MqttAccessPointService │                 │
│              │  (SSID / reboot / kick)  │                 │
│              └──────────────────────────┘                 │
└───────────────────────────────────────────────────────────┘
              │                        │
     ZeroTier VPN               FreeRADIUS (MariaDB)
   (IP مخصص لكل AP)            (clouds / dynamic_clients)
```

---

## 2) جدول Clouds

### الموديل: `app/Models/Clouds.php`

جدول `clouds` على قاعدة بيانات **mariadb** (ليس الـ default connection).

| الحقل  | النوع   | الوصف                           |
| ------ | ------- | ------------------------------- |
| id     | bigint  | المعرف                          |
| name   | string  | اسم الـ Cloud                   |
| IsDist | boolean | هل هو Cloud توزيع (Distributor) |

### العلاقات:

```php
Clouds → hasMany DynamicClients      // نقاط الوصول المرتبطة
Clouds → hasMany VoucherSales        // مبيعات الكروت عليه
Clouds → hasMany PermanentUsers      // مستخدمين دائمين
Clouds → hasMany Topups              // عمليات الشحن
```

### الواجهة في Filament:

- **المسار**: `app/Filament/Pages/Clouds.php`
- **Navigation Group**: Cloud Radius
- **الأعمدة المعروضة**: Cloud ID, Cloud Name, Created

---

## 3) جدول Dynamic Clients (نقاط الوصول)

### الموديل: `app/Models/DynamicClients.php`

جدول `dynamic_clients` على قاعدة بيانات **mariadb**.

| الحقل           | النوع    | الوصف                                     |
| --------------- | -------- | ----------------------------------------- |
| id              | bigint   | المعرف                                    |
| name            | string   | اسم الـ AP                                |
| nasidentifier   | string   | معرف الـ NAS الخاص بالجهاز في RADIUS      |
| last_contact    | datetime | آخر وقت اتصل فيه الجهاز بالـ RADIUS       |
| last_contact_ip | string   | آخر IP خاص بالاتصال                       |
| cloud_id        | bigint   | الـ Cloud المرتبط به                      |
| Picture         | string   | صورة الجهاز                               |
| zero_ip         | string   | IP الخاص بـ ZeroTier VPN (يُستخدم للتحكم) |

### العلاقات:

```php
DynamicClients → belongsTo Clouds            // الـ Cloud المالك
DynamicClients → hasMany DynamicClientRealms // الـ Realms المرتبطة
DynamicClients → hasMany VoucherSales        // مبيعات الكروت
DynamicClients → hasMany Radacct             // سجلات الجلسات
```

### Scope مهم:

```php
// يُرجع فقط الأجهزة التي لديها zero_ip مُعيَّن (يعني AP يمكن التحكم فيه)
DynamicClients::accessPoints()->get();
```

---

## 4) التواصل مع الـ Access Point (OpenWrt)

### 4.1 الخدمة الرئيسية: `LuciRpcService`

**الملف**: `app/Services/LuciRpcService.php`

هذه الخدمة هي الطريقة الرئيسية المعتمدة للتواصل مع الأجهزة.

#### مسار الاتصال:

```
LuciRpcService
  │
  ├── POST {ip}:{port}/ubus          ← المسار الرئيسي
  └── POST {ip}/cgi-bin/luci/admin/ubus  ← fallback
```

#### المصادقة (Authentication):

```
1. POST /ubus بـ session.login + username/password
2. الجهاز يُرجع ubus_rpc_session token
3. Token يُخزن في Cache لمدة 270 ثانية
4. كل طلب بعد كده يحمل الـ token في params[0]
```

#### الإعدادات (من `config/openwrt.php`):

| المتغير    | القيمة الافتراضية | env var            |
| ---------- | ----------------- | ------------------ |
| username   | root              | OPENWRT_USERNAME   |
| password   | Justdoitnow157#   | OPENWRT_PASSWORD   |
| scheme     | http              | OPENWRT_SCHEME     |
| port       | 80                | OPENWRT_PORT       |
| verify_ssl | false             | OPENWRT_VERIFY_SSL |
| timeout    | 8 ثانية           | OPENWRT_TIMEOUT    |
| ssh_port   | 22                | OPENWRT_SSH_PORT   |

#### المتطلبات على الجهاز (OpenWrt packages):

```
opkg install luci-mod-rpc rpcd-mod-luci rpcd-mod-iwinfo luci-mod-status
/etc/init.d/rpcd restart
```

---

### 4.2 الطرق المتاحة في `LuciRpcService`

#### معلومات النظام:

```php
$svc = new LuciRpcService($ip);

// معلومات اللوحة: model, hostname, firmware, kernel
$svc->systemBoard();

// معلومات وقت التشغيل: uptime, load, memory
$svc->systemInfo();

// ملخص مدمج جاهز للعرض
$svc->getSystemSummary();
// يُرجع: hostname, model, firmware, uptime, load_1m/5m/15m, mem_total/used/free/pct
```

#### الواي فاي:

```php
// جميع الأجهزة اللاسلكية مع الراديوز والـ interfaces
$svc->getWirelessDevices();

// قائمة واجهات الواي فاي بشكل مفهوم (SSID, channel, encryption, status...)
$svc->getWirelessInterfaces();

// قائمة الأجهزة المتصلة حالياً على interface معين
$svc->getWifiClients('wlan0');

// جميع الأجهزة المتصلة على جميع الـ interfaces
$svc->getAllWifiClients();
```

#### الشبكة:

```php
// جميع network devices مع RX/TX stats
$svc->getNetworkDevices();

// dump كامل للـ interfaces مع IPs والـ routes
$svc->getInterfaceDump();

// عقود الـ DHCP (MAC, IP, hostname, expiry)
$svc->getDhcpLeases();
```

#### الإجراءات (Commands):

```php
// إعادة تشغيل الـ WiFi
$svc->reloadWifi();

// إعادة تشغيل الجهاز كامل
$svc->reboot();

// تغيير SSID
$svc->setSsid($ucisection, $newSsid);

// تغيير كلمة مرور الواي فاي
$svc->setPassword($ucisection, $newPassword);

// فحص الاتصال بالجهاز
$svc->healthCheck();
// يُرجع: ['ok' => bool, 'message' => string, 'latency_ms' => int]
```

---

### 4.3 الخدمة القديمة: `AccessPointService`

**الملف**: `app/Services/AccessPointService.php`

خدمة أقدم تعتمد على نفس UBUS لكن بدون Guzzle وبدون cache للـ token.

- تجرب credentials متعددة تلقائيًا عند فشل الـ login
- تحتوي على fallback لقراءة البيانات من HTML الـ LuCI
- **تُستخدم في**: `app/Http/Controllers/AccessPointController.php`

---

### 4.4 الخدمة الثانية: `OpenWrtService`

**الملف**: `app/Services/OpenWrtService.php`

خدمة مختلفة في الأسلوب: تعتمد على RPC endpoint `/cgi-bin/luci/rpc/` بدلًا من `/ubus`.

- تستخدم **Guzzle** بدلًا من Laravel Http
- Token يُخزن في Cache بعنوان مختلف
- تحتوي على UCI helpers مباشرة: `uciGet`, `uciSet`, `uciAdd`, `uciDelete`, `uciCommit`
- تحتوي على `sysExec` لتنفيذ أوامر shell

**ملاحظة**: لا تخلط بين `LuciRpcService` و `OpenWrtService` في نفس السياق - الـ endpoints والـ token format مختلفان.

---

## 5) التحكم عبر MQTT

### الخدمة: `MqttAccessPointService`

**الملف**: `app/Services/MqttAccessPointService.php`

طريقة بديلة للتحكم في الـ APs بدون HTTP مباشر - عبر MQTT broker.

#### إعدادات MQTT (من `config/mqtt.php`):

| المتغير  | env var                 |
| -------- | ----------------------- |
| host     | MQTT_BROKER_HOST        |
| port     | MQTT_BROKER_PORT (1883) |
| username | MQTT_BROKER_USERNAME    |
| password | MQTT_BROKER_PASSWORD    |

#### Topics المستخدمة:

```
etech/ap/{nasidentifier}/ssid   ← تغيير اسم الشبكة
etech/ap/{nasidentifier}/cmd    ← أوامر (reboot)
etech/ap/{nasidentifier}/kick   ← طرد عميل بـ MAC address
```

#### الطرق المتاحة:

```php
$svc = new MqttAccessPointService();

// تغيير SSID
$svc->changeSsid($accessPoint, 'New-Network-Name');

// إعادة تشغيل
$svc->reboot($accessPoint);

// طرد عميل
$svc->kickClientByNasIdentifier($nasIdentifier, $macAddress);
```

#### قيود الـ SSID:

- لا يمكن أن يكون فارغًا
- يدعم العربي، لكن الحجم لا يتجاوز **32 بايت** (مش 32 حرف)

---

## 6) صفحة إدارة AP في Filament

### الملف: `app/Filament/Pages/AccessPointManagement.php`

**Navigation Group**: الشبكة  
**Navigation Sort**: 10  
**Icon**: heroicon-o-wifi

#### ما يعرضه:

1. **System Summary**: hostname, model, firmware, uptime, load averages, نسبة الـ memory
2. **Wireless Interfaces**: SSID, channel, frequency, txpower, encryption, mode, status
3. **Connected Clients**: أجهزة متصلة (MAC, IP, signal, hostname)
4. **DHCP Leases**: عقود الـ IP المُعطاة

#### الـ Actions المتاحة للأدمن:

- تحديث البيانات (Refresh)
- إعادة تشغيل WiFi
- Reboot الجهاز
- تغيير SSID
- تغيير كلمة المرور
- طرد عميل (Kick)
- تبديل حالة الراديو (Enable/Disable)

#### آلية الـ Cache:

```php
// قائمة الـ APs: 60 ثانية
Cache::remember('ap_management_list', 60, fn() => ...);

// Dashboard data: 30 ثانية (لتقليل الطلبات للجهاز)
Cache::put("ap_dashboard_{$id}", [...], 30);

// عدد الأجهزة المتصلة: 300 ثانية
Cache::put("ap_clients_count_{$id}", count($clients), 300);
```

---

## 7) نظام ZeroTier

### الملف: `app/Filament/Pages/ZeroTierNetworks.php`

**Navigation Group**: الشبكة  
**Navigation Sort**: 25

ZeroTier هو VPN overlay يُعطي كل AP عنوان IP خاص (`zero_ip`) يمكن الوصول إليه من السيرفر حتى لو الجهاز خلف NAT.

#### الشبكات المُعرَّفة (من `config/ztnet.php`):

```
Wi-Fi       → network ID: fe87fb1e83afaa91
PlayStation → network ID: fe87fb1e83032d1f
```

#### إعدادات ZTNet API:

| المتغير    | env var          | القيمة الافتراضية                 |
| ---------- | ---------------- | --------------------------------- |
| base_url   | ZTNET_BASE_URL   | http://zt.etech-valley.com/api/v1 |
| auth_token | ZTNET_AUTH_TOKEN | (token طويل في config)            |
| org_id     | ZTNET_ORG_ID     | cmmw7y6u10003hfbrw8as0r5b         |

#### الصفحة تعرض:

- قائمة شبكات ZeroTier المُعرَّفة
- Members لكل شبكة (nodeId, name, IP, status, online)

---

## 8) كروت الواي فاي (Wi-Fi Vouchers)

### الموديلات:

```
WifiInvoices → hasMany Vouchers
Vouchers     → hasMany Voucher (الكروت الفردية)
WifiInvoices → belongsTo Clouds
```

### حقول `WifiInvoices`:

| الحقل      | الوصف                 |
| ---------- | --------------------- |
| Invoice_No | رقم الفاتورة المرجعية |
| Item_Name  | اسم الباقة            |
| Type       | نوع الكرت             |
| QTY        | الكمية الإجمالية      |
| Remain     | الكمية المتبقية       |
| Cloud_ID   | الـ Cloud الخاص بها   |

### الواجهات:

- `app/Filament/Resources/WifiInvoicesResource.php` ← للأدمن
- `app/Filament/Customer/Resources/VouchersListResource.php` ← للعملاء
- `app/Filament/Customer/Resources/VoucherInvoicesResource.php` ← للعملاء
- `app/Filament/Customer/Pages/VoucherSales.php` ← صفحة المبيعات
- `app/Filament/Customer/Pages/VoucherBatch.php` ← طباعة Batch

---

## 9) خريطة الملفات

```
app/
├── Models/
│   ├── Clouds.php                  ← Cloud Radius records
│   ├── DynamicClients.php          ← APs / NAS devices
│   ├── WifiInvoices.php            ← فواتير الكروت
│   ├── Vouchers.php                ← Batches كروت
│   └── VoucherSales.php            ← مبيعات الكروت
│
├── Services/
│   ├── LuciRpcService.php          ← التحكم في APs (الرئيسي)
│   ├── OpenWrtService.php          ← تحكم بديل (RPC style)
│   ├── AccessPointService.php      ← تحكم قديم (Legacy)
│   └── MqttAccessPointService.php  ← تحكم عبر MQTT
│
├── Filament/Pages/
│   ├── Clouds.php                  ← عرض Cloud Radius
│   ├── AccessPointManagement.php   ← إدارة APs
│   └── ZeroTierNetworks.php        ← شبكات ZeroTier
│
└── Http/Controllers/
    ├── AccessPointController.php   ← API للـ APs (Web)
    └── Api/AccessPointController.php ← API خارجي

config/
├── openwrt.php    ← إعدادات الاتصال بالأجهزة
├── mqtt.php       ← إعدادات MQTT broker
└── ztnet.php      ← إعدادات ZeroTier API
```

---

## 10) تدفق البيانات: من الـ AP للـ Dashboard

```
1. Admin يختار AP من Dropdown
         ↓
2. AccessPointManagement::selectAp($id)
         ↓
3. يسحب zero_ip من DynamicClients
         ↓
4. LuciRpcService($zero_ip)
         ↓
5. healthCheck() → هل الجهاز متاح؟
         ↓
6. إذا متاح:
   ├── getSystemSummary()     → systemInfo[]
   ├── getWirelessInterfaces() → interfaces[]
   ├── getAllWifiClients()     → wifiClients[]
   └── getDhcpLeases()        → dhcpLeases[]
         ↓
7. كل البيانات تتخزن في Cache لـ 30 ثانية
         ↓
8. تُعرض في الـ Blade view
```

---

## 11) نقاط مهمة يجب مراعاتها

### التوحيد في استخدام الخدمات:

- `AccessPointManagement` تستخدم `LuciRpcService` (الأحدث)
- `AccessPointController` يستخدم `OpenWrtService`
- `MqttAccessPointService` مستقلة ومخصصة للأوامر السريعة
- لا تخلط بين الخدمات في نفس السياق لأن الـ endpoints والـ token format مختلفان

### اتصال MariaDB:

- موديلات `Clouds` و `DynamicClients` و `VoucherSales` محدَّدة صراحةً على `mariadb`
- لا تفترض أنها على الـ default connection

### حدود SSID:

- الـ MQTT validation: لا يتجاوز 32 بايت
- الحرف العربي يأخذ 2-3 بايت ← عدد الحروف الفعلي أقل من 32

### ZeroTier IP:

- الحقل `zero_ip` هو الـ IP المستخدم للتحكم في الجهاز
- الأجهزة بدون `zero_ip` لا يمكن التحكم فيها عن بُعد
- Scope: `DynamicClients::accessPoints()` يُصفي فقط الأجهزة ذات `zero_ip`

### Cache الـ Token:

- `LuciRpcService`: مفتاح `luci_rpc:session:{ip}:{username}` لمدة 270 ثانية
- `OpenWrtService`: مفتاح `openwrt:token:{ip}` لمدة 240 ثانية
- لو الجهاز أعاد تشغيله يجب مسح الـ Cache يدويًا أو الانتظار لانتهاء الـ TTL
