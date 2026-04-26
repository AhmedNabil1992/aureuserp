# WiFi Plugin — Backend APIs Required

Base URL: `http://192.168.1.6`
Base path: `/customer/api/v1/`

## Authentication

Every request needs:
- **Header**: `Authorization: Bearer {token}`
- **Query param / body**: `customer_id` = the logged-in customer's ID

---

## ✅ Already Implemented (Backend Done)

### 1. Get Customer Clouds
```
POST /customer/api/v1/wifi/clouds
```
**Body (form/json):**
```json
{
  "customer_id": 5,
  "token": "bearer_token_value"
}
```
**Expected Response:**
```json
{
  "data": [
    { "id": 1, "name": "Cloud Cairo", "created": "2024-01-10", "modified": "2024-03-01" },
    { "id": 2, "name": "Cloud Alex",  "created": "2024-02-05", "modified": "2024-03-10" }
  ]
}
```

---

### 2. Get Customer Access Points (Dynamic Clients)
```
POST /customer/api/v1/wifi/dynamic-clients
```
**Body (form/json):**
```json
{
  "customer_id": 5,
  "token": "bearer_token_value"
}
```
**Expected Response:**
```json
{
  "data": [
    {
      "id": 10,
      "name": "AP-Branch-1",
      "nasidentifier": "nas_001",
      "cloud_id": 1,
      "realm_id": 3,
      "realm_name": "realm_main",
      "last_contact": "2024-04-25T10:30:00",
      "last_contact_ip": "192.168.1.100",
      "active": true,
      "picture": null,
      "zero_ip": null,
      "created": "2024-01-15",
      "modified": "2024-04-20"
    }
  ]
}
```

> **Note**: `realm_id` and `realm_name` fields are new — please include them if each AP belongs to a realm.

---

## ⏳ Pending (Need to Build)

### 3. Get Realms for a Cloud
```
GET /customer/api/v1/wifi/clouds/{cloud_id}/realms?customer_id=5
```
**Expected Response:**
```json
{
  "data": [
    { "id": 3, "name": "realm_main", "cloud_id": 1 },
    { "id": 4, "name": "realm_guest", "cloud_id": 1 }
  ]
}
```

---

### 4. WiFi Dashboard Stats
```
GET /customer/api/v1/wifi/dashboard?customer_id=5
```
**Expected Response:**
```json
{
  "data": {
    "total_clouds": 2,
    "total_access_points": 15,
    "active_access_points": 12,
    "last_contact_at": "2024-04-25T10:45:00",
    "total_vouchers": 500,
    "used_vouchers": 320,
    "remaining_vouchers": 180,
    "total_sales_revenue": 9600.00
  }
}
```

---

### 5. Get Voucher Batches
```
GET /customer/api/v1/wifi/voucher-batches?customer_id=5
```
**Expected Response:**
```json
{
  "data": [
    {
      "id": 1,
      "batch_code": "BATCH-2024-001",
      "quantity": 100,
      "status": "active",
      "created_at": "2024-03-01T08:00:00",
      "download_url": null
    }
  ]
}
```

---

### 6. Get Voucher Batch PDF Download URL
```
GET /customer/api/v1/wifi/voucher-batches/{batch_id}/download-url?customer_id=5
```
**Expected Response:**
```json
{
  "data": {
    "url": "https://example.com/storage/batches/BATCH-2024-001.pdf"
  }
}
```

---

### 7. Get Sales List
```
GET /customer/api/v1/wifi/sales?customer_id=5&cloud_id=1&realm_id=3
```
- `cloud_id` → optional filter
- `realm_id` → optional filter (requires `cloud_id`)

**Expected Response:**
```json
{
  "data": [
    {
      "id": 201,
      "voucher_code": "ABC123",
      "cloud_id": 1,
      "cloud_name": "Cloud Cairo",
      "realm_id": 3,
      "realm_name": "realm_main",
      "amount": 30.00,
      "sold_at": "2024-04-24T14:22:00"
    }
  ]
}
```

---

### 8. Sales Summary
```
GET /customer/api/v1/wifi/sales-summary?customer_id=5
```
**Expected Response:**
```json
{
  "data": {
    "total_sales": 320,
    "total_revenue": 9600.00,
    "currency": "EGP",
    "remaining_vouchers": 180
  }
}
```

---

## Summary Table

| # | Endpoint | Method | Status | Priority |
|---|----------|--------|--------|----------|
| 1 | `/wifi/clouds` | POST | ✅ Done | - |
| 2 | `/wifi/dynamic-clients` | POST | ✅ Done | - |
| 3 | `/wifi/clouds/{id}/realms` | GET | ⏳ Pending | 🔴 High |
| 4 | `/wifi/dashboard` | GET | ⏳ Pending | 🔴 High |
| 5 | `/wifi/voucher-batches` | GET | ⏳ Pending | 🔴 High |
| 6 | `/wifi/voucher-batches/{id}/download-url` | GET | ⏳ Pending | 🟡 Medium |
| 7 | `/wifi/sales` | GET | ⏳ Pending | 🔴 High |
| 8 | `/wifi/sales-summary` | GET | ⏳ Pending | 🟡 Medium |

---

## Notes for Backend Developer

1. **Standard response wrapper** — all endpoints should return `{ "data": ... }`.
2. **Realm filter on APs** — the `dynamic-clients` endpoint should ideally return `realm_id` + `realm_name` per AP row (join with realms table).
3. **Sales filters** — `cloud_id` and `realm_id` are optional GET params; if omitted, return all sales for the customer.
4. **Auth** — the customer is identified by `customer_id` in the query/body; the Bearer token is validated server-side from the Authorization header.
5. **Dates** — use ISO-8601 format (`2024-04-25T10:30:00`) for all datetime fields so the Flutter app can parse them with `DateTime.parse()`.
