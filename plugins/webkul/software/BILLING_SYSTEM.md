# Software Plugin — Billing System Technical Reference

## Overview

This document describes the full billing flow for the **Software** plugin in AureusERP.
It covers every change made, the reasoning behind each decision, and the exact setup
required when deploying on a new machine or rebuilding from scratch.

---

## Architecture Decision

### Why link billing to the core Accounting system?

The `billLicense` action originally created records only in `software_license_invoices`
(a plugin-local table). This meant financial data lived outside the standard accounting
flow — no tax calculation, no journal entries, no payment tracking, no reconciliation.

The decision was made to **also create an `AccountMove` (OUT_INVOICE)** using the same
pattern that `SaleManager` and `PurchaseOrder` use in the `sales` / `purchases` plugins.
The local `LicenseInvoice` record is kept for quick software-context lookups and linked
to the `AccountMove` via `account_move_id`.

### Why add `product_id` to `ProgramEdition`?

The accounting system computes accounts, taxes, and UOM from a `Product`. Without a
product linked to an edition, the invoice line would have no account assignment.
Each edition should be linked to a **service-type Product** (`type = 'service'`).

### Why add `service_type` to `ProgramFeature`?

Features represent billable add-ons (e.g., Technical Support, Mail). Each feature that
should be included automatically on every invoice and tracked as an active subscription
must declare its `service_type` (enum value from `Webkul\Software\Enums\ServiceType`).
Features without a `service_type` are purely informational and are ignored during billing.

---

## Database Changes

All migrations live in `plugins/webkul/software/database/migrations/`.

### Migration 0021 — `2026_04_12_000021_alter_software_program_editions_add_product_id`

```sql
ALTER TABLE software_program_editions
    ADD COLUMN product_id BIGINT UNSIGNED NULL
    REFERENCES products_products(id) ON DELETE SET NULL;
```

**Purpose:** Links each program edition to a service Product for proper accounting
line item creation (account mapping, UOM, tax).

### Migration 0022 — `2026_04_12_000022_alter_software_license_invoices_add_account_move_id`

```sql
ALTER TABLE software_license_invoices
    ADD COLUMN account_move_id BIGINT UNSIGNED NULL
    REFERENCES accounts_account_moves(id) ON DELETE SET NULL;
```

**Purpose:** Links every local invoice record to the corresponding `AccountMove`,
enabling direct navigation from software invoices to the accounting system.

### Migration 0023 — `2026_04_12_000023_alter_software_program_features_add_service_type`

```sql
ALTER TABLE software_program_features
    ADD COLUMN service_type VARCHAR(50) NULL COMMENT 'Maps this feature to a subscription service type';
```

**Purpose:** Declares which `ServiceType` enum value a feature represents.
Only features with a non-null `service_type` **and** `amount > 0` participate in billing.

---

## Models Modified

### `Webkul\Software\Models\ProgramEdition`

- Added `product_id` to `$fillable`
- Added `product(): BelongsTo` → `Webkul\Product\Models\Product`

### `Webkul\Software\Models\LicenseInvoice`

- Added `account_move_id` to `$fillable`
- Added `accountMove(): BelongsTo` → `Webkul\Account\Models\Move`

### `Webkul\Software\Models\ProgramFeature`

- Added `service_type` to `$fillable`
- Added `'service_type' => ServiceType::class` to `$casts`
- Added `use Webkul\Software\Enums\ServiceType`

---

## Filament Resources Modified

### `ProgramEditionResource`

Added a **Select** field for `product_id` filtered to `type = 'service'` products.
This must be set for each edition so the accounting invoice line has a product reference.

### `ProgramFeatureResource`

- Added a **Select** field for `service_type` (nullable) using `ServiceType` enum options
- Added `service_type` badge column in the table

---

## Billing Flow — `billLicense` Action

**File:** `plugins/webkul/software/src/Filament/Admin/Resources/LicenseResource.php`

### Trigger

The `billLicense` action appears in the Licenses table row actions. The user selects
an **Edition** and a **License Plan** (Full / Monthly / Annual) and confirms.

### Steps executed inside a single DB transaction

```
1. Load ProgramEdition (with product) for the chosen edition_id
2. Compute amount from plan:
       Full    → edition.license_price
       Monthly → edition.monthly_renewal
       Annual  → edition.annual_renewal
   Throws if amount <= 0
3. Update License record:
       edition_id, license_plan, period, start_date, end_date,
       status = Approved, is_active = true, approved_by = Auth::id()
4. Generate invoice_number:  LIC-{YmdHis}-{XXXX}
5. Create AccountMove (OUT_INVOICE / DRAFT):
       move_type      = MoveType::OUT_INVOICE
       invoice_origin = license.serial_number
       date           = today
       company_id     = Auth::user()->default_company_id
       currency_id    = company.currency_id
       partner_id     = license.partner_id
       creator_id     = Auth::id()
       invoice_user_id= Auth::id()
6. Create MoveLine for the edition:
       name       = "{Program} - {Edition} ({Plan})"
       quantity   = 1
       price_unit = amount
       product_id = edition.product_id   (nullable)
       uom_id     = edition.product.uom_id  (nullable)
7. Load program features WHERE service_type IS NOT NULL AND amount > 0
8. For EACH qualifying feature:
   a. Create MoveLine:
           name       = feature.name
           quantity   = 1
           price_unit = feature.amount
           product_id = null   (features have no linked product)
   b. Upsert LicenseSubscription (updateOrCreate):
           license_id   = license.id
           service_type = feature.service_type.value
           start_date   = today
           end_date     = license.end_date (as set in step 3)
           is_active    = true
9. AccountFacade::computeAccountMove($accountMove)
   → syncs tax lines, payment-term lines, computes totals
10. Create LicenseInvoice (local record):
       account_move_id = accountMove.id
       + all other fields (invoice_number, amounts, billed_by, etc.)
```

### Result

| Record                            | Location                         |
| --------------------------------- | -------------------------------- |
| AccountMove (invoice, DRAFT)      | `accounts_account_moves`         |
| AccountMoveLines (1 + N features) | `accounts_account_move_lines`    |
| LicenseInvoice (local summary)    | `software_license_invoices`      |
| LicenseSubscription (per feature) | `software_license_subscriptions` |

The generated `AccountMove` is in **DRAFT** state. The user can go to the Invoices
section to review, confirm (post), and register payments.

---

## Initial Data Setup (Required on New Machine)

After running `php artisan migrate`, you must configure the following data via the
Admin panel before the billing action produces complete invoices:

### 1 — Create Service Products for Editions

Go to **Products** → create a product for each program edition:

| Field | Value                                     |
| ----- | ----------------------------------------- |
| Name  | e.g., "ERP License — Enterprise Edition"  |
| Type  | `service`                                 |
| Price | (informational, price comes from edition) |

### 2 — Link Products to Program Editions

Go to **Catalog → Program Editions** → edit each edition → set **Linked Product**.

### 3 — Assign `service_type` to Program Features

Go to **Catalog → Program Features** → edit each feature:

| Feature Name      | Subscription Type   |
| ----------------- | ------------------- |
| Technical Support | `technical_support` |
| Mail              | `mail`              |

> Feature IDs 1 and 2 in a fresh database are typically Technical Support and Mail,
> but **always verify via the Admin UI** — IDs are environment-specific.

### 4 — Ensure User Has a Default Company with Currency

The billing action reads `Auth::user()->default_company_id` and then
`company->currency_id`. Both must be set or the action throws a runtime exception.

---

## Key Classes & Files

| File                                                                       | Purpose                                         |
| -------------------------------------------------------------------------- | ----------------------------------------------- |
| `plugins/webkul/software/src/Filament/Admin/Resources/LicenseResource.php` | Main billing action (`billLicense`)             |
| `plugins/webkul/software/src/Models/ProgramEdition.php`                    | Edition ↔ Product link                          |
| `plugins/webkul/software/src/Models/ProgramFeature.php`                    | Feature ↔ ServiceType link                      |
| `plugins/webkul/software/src/Models/LicenseInvoice.php`                    | Local invoice ↔ AccountMove link                |
| `plugins/webkul/software/src/Models/LicenseSubscription.php`               | Subscription records                            |
| `plugins/webkul/accounts/src/AccountManager.php`                           | `computeAccountMove()` — computes taxes, totals |
| `plugins/webkul/accounts/src/Facades/Account.php`                          | `AccountFacade` facade                          |
| `plugins/webkul/accounts/src/Models/Move.php`                              | AccountMove model (`accounts_account_moves`)    |
| `plugins/webkul/accounts/src/Models/MoveLine.php`                          | AccountMoveLine model                           |
| `plugins/webkul/accounts/src/Enums/MoveType.php`                           | `OUT_INVOICE` etc.                              |
| `plugins/webkul/software/src/Enums/ServiceType.php`                        | `TechnicalSupport`, `Mail`, `Remote`            |

---

## Accounting Flow Diagram

```
billLicense action
       │
       ├─── AccountMove::create()          → DRAFT invoice header
       │         (move_type = OUT_INVOICE)
       │
       ├─── $move->lines()->create()        → Edition line
       │
       ├─── foreach (subscriptionFeatures)
       │         $move->lines()->create()   → Feature line (Technical Support, Mail…)
       │         LicenseSubscription::updateOrCreate()
       │
       ├─── AccountFacade::computeAccountMove()
       │         → auto tax lines
       │         → auto payment-term lines
       │         → totals computed
       │
       └─── LicenseInvoice::create()        → local record with account_move_id
```

---

## Commands Reference

```bash
# Run all migrations
php artisan migrate --no-interaction

# Check migration status
php artisan migrate:status | grep software

# Reset and re-run software migrations (development only)
php artisan migrate:rollback --path=plugins/webkul/software/database/migrations

# Fix code style
vendor/bin/pint --dirty plugins/webkul/software/
```
