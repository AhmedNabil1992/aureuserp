# AureusERP User Guide — Reference Summary

> Source: https://docs.aureuserp.com/
> Last updated: April 2026

## 📋 Available Modules (User-Facing)

| Module                 | Documentation URL                                                       |
| ---------------------- | ----------------------------------------------------------------------- |
| Invoices               | https://docs.aureuserp.com/master/invoice/customers/invoices.html       |
| Customer Payments      | https://docs.aureuserp.com/master/invoice/customers/payments.html       |
| Vendor Bills           | https://docs.aureuserp.com/master/invoice/vendors/bills.html            |
| Vendor Payments        | https://docs.aureuserp.com/master/invoice/vendors/payments.html         |
| Invoice Configurations | https://docs.aureuserp.com/master/invoice/configurations.html           |
| Invoice Settings       | https://docs.aureuserp.com/master/invoice/settings.html                 |
| Sales Quotations       | https://docs.aureuserp.com/master/sales/orders/quotations.html          |
| Sales Orders           | https://docs.aureuserp.com/master/sales/orders/orders.html              |
| Purchase RFQs          | https://docs.aureuserp.com/master/purchase/orders/quotations.html       |
| Purchase Orders        | https://docs.aureuserp.com/master/purchase/orders/purchase-orders.html  |
| Inventory Transfers    | https://docs.aureuserp.com/master/inventories/operations/transfers.html |
| Contacts               | https://docs.aureuserp.com/master/contact/contacts.html                 |
| Projects               | https://docs.aureuserp.com/master/project/projects.html                 |
| Employees              | https://docs.aureuserp.com/master/employees/employees.html              |
| Recruitment            | https://docs.aureuserp.com/master/recruitment/applications.html         |
| Time Off               | https://docs.aureuserp.com/master/timeOff/my-time.html                  |

---

## 💳 Payments Module (Invoices Plugin)

**Path:** `Invoices → Customers → Payments`

### Payment Form Fields

- **Payment Type**: Send / Receive
- **Customer**: From registered customers
- **Amount**: Total amount
- **Customer Bank Account**: Linked bank account
- **Payment Method**: Bank, Cash, Cheque, etc.
- **Date**: Transaction date
- **Memo**: Optional reference notes

### Payment States

| State      | Description                           |
| ---------- | ------------------------------------- |
| Draft      | Initial state when payment is created |
| In Process | After Confirm action                  |
| Paid       | Payment matched to invoice            |
| Not Paid   | Payment failed or marked unpaid       |
| Cancelled  | Manually cancelled                    |
| Rejected   | Declined or failed validation         |

### Invoice Settlement Logic

- **Full Payment**: Invoice marked as Paid
- **Partial Payment**: Invoice shows Partially Paid; settle the rest with another payment
- Reconciliation is **automatic** based on customer + open invoices

---

## 🏦 Bank Accounts Configuration

**Path:** `Invoices → Configurations → Bank Accounts`

- Supports **multiple bank accounts** per company
- Fields: Account Number, Bank Name, BIC, Email, Phone, Address, Account Holder
- Used for **payment reconciliation** during invoice/bill processing
- Linked to Journals (type: Bank)

---

## 📄 Invoice Configurations Summary

| Config        | Description                                       |
| ------------- | ------------------------------------------------- |
| Bank Accounts | Multiple bank accounts for payment reconciliation |
| Incoterms     | International shipment terms (e.g., FOB, CIF)     |
| Payment Terms | Due dates, early discount rules                   |
| Categories    | Product/service categories for invoices           |
| Attributes    | Product variants affecting pricing                |
| Tax Groups    | Grouped tax rates                                 |
| Taxes         | Individual tax rules (%, fixed, group, formula)   |
