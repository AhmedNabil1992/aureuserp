# AureusERP Dev Docs — Reference Summary

> Source: https://devdocs.aureuserp.com/
> GitHub: https://github.com/aureuserp/dev-docs
> Last updated: April 2026

## 🏗️ Architecture Overview

- **Framework**: Laravel (enterprise-grade)
- **UI**: Filament v5 (SDUI — Server-Driven UI on top of Livewire + Alpine.js)
- **Design**: Plugin-based — each feature is a self-contained plugin under `plugins/webkul/`
- **License**: MIT

### Key Documentation Links

| Topic               | URL                                                         |
| ------------------- | ----------------------------------------------------------- |
| Introduction        | https://devdocs.aureuserp.com/master/prologue/introduction/ |
| Plugin Architecture | https://devdocs.aureuserp.com/master/architecture/plugins   |
| GitHub Source       | https://github.com/aureuserp/aureuserp                      |

---

## 🔌 Plugin Structure (`plugins/webkul/`)

Each plugin follows this structure:

```
plugins/webkul/{plugin-name}/
  src/
    {Plugin}ServiceProvider.php     ← registers migrations, views, translations
    {Plugin}Plugin.php              ← Filament panel integration
    Filament/
      Admin/Resources/              ← Filament resources (CRUD pages)
    Models/                         ← Eloquent models
    Enums/                          ← PHP enums
    Services/                       ← Business logic services
  database/
    migrations/                     ← Plugin-specific migrations
    factories/
    seeders/
```

---

## 💰 Accounts Plugin — Key Models & Resources

**Location:** `plugins/webkul/accounts/src/`

### Models

| Model                           | Description                                         |
| ------------------------------- | --------------------------------------------------- |
| `Journal`                       | Bank/Cash/Sale/Purchase/General journal (type enum) |
| `Move`                          | Account move (invoice, bill, payment, etc.)         |
| `MoveLine`                      | Line item on a move                                 |
| `Payment`                       | Payment record linked to a journal/move             |
| `PaymentRegister`               | Batch payment registration                          |
| `BankStatement`                 | Imported bank statement                             |
| `BankStatementLine`             | Single bank statement line                          |
| `PartialReconcile`              | Partial reconciliation between move lines           |
| `FullReconcile`                 | Full reconciliation record                          |
| `Account`                       | Chart of account entry                              |
| `AccountJournal`                | Journal-Account link                                |
| `Tax`, `TaxGroup`               | Tax rules and groups                                |
| `PaymentTerm`, `PaymentDueTerm` | Payment term rules                                  |

### Journal Types (Enum)

| Value      | Description                |
| ---------- | -------------------------- |
| `sale`     | Sales journal              |
| `purchase` | Purchase journal           |
| `cash`     | Cash register / petty cash |
| `bank`     | Bank account journal       |
| `credit`   | Credit card journal        |
| `general`  | General/miscellaneous      |

### Filament Resources

| Resource              | Description                     |
| --------------------- | ------------------------------- |
| `JournalResource`     | Create/manage journals          |
| `PaymentResource`     | Record customer/vendor payments |
| `BankAccountResource` | Manage bank accounts            |
| `InvoiceResource`     | Customer invoices               |
| `BillResource`        | Vendor bills                    |
| `AccountResource`     | Chart of accounts               |

---

## 🔄 Payment Flow

```
Create Payment (Draft)
    ↓ Confirm
In Process
    ↓ Auto-reconcile with open invoices
Paid (if matched) / Not Paid
```

- Each payment is linked to a **Journal** (determines if Bank or Cash)
- Multiple journals of type `bank` or `cash` supported
- Reconciliation: automatic via `AccountFacade::reconcile()`
- Pivot table `accounts_accounts_move_payment` links payments ↔ invoices

---

## 🏦 Multiple Bank/Cash Accounts

**Currently supported:**

- Multiple Journals of type `bank` → each linked to a `BankAccount` record
- Multiple Journals of type `cash` → acts as separate cash registers
- Each journal has its own default account, suspense account, currency

**To restrict a journal to a user:** Not natively supported — needs custom field.

---

## 📦 Related Plugins

| Plugin       | Path                        | Purpose                    |
| ------------ | --------------------------- | -------------------------- |
| `accounts`   | `plugins/webkul/accounts`   | Core accounting engine     |
| `accounting` | `plugins/webkul/accounting` | Extended accounting UI     |
| `invoices`   | `plugins/webkul/invoices`   | Invoice management         |
| `payments`   | `plugins/webkul/payments`   | Payment gateway tokens     |
| `partners`   | `plugins/webkul/partners`   | Customer/vendor management |
| `wifi`       | `plugins/webkul/wifi`       | Wi-Fi voucher billing      |
| `software`   | `plugins/webkul/software`   | License billing            |
