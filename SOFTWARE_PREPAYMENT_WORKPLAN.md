# Software Prepayment Workflow Plan

Last updated: 2026-04-14
Owner: Software + Accounts integration
Status: Draft (editable)

## 1) Goal

Implement customer prepayment (under-account) flow so a customer can:

- Submit or pay a prepayment from customer panel.
- Hold available credit.
- Automatically deduct credit on future purchases (including software license billing).
- Keep full traceability from invoice consumption back to original prepayment request.

## 2) Business Requirements (Current)

- Prepayment is made before specific future purchases.
- Future sales should consume available balance automatically.
- Resulting sales should be marked paid/partially paid based on consumed credit.
- Every deduction must be linked to the original payment request.

## 3) Current System State (Verified)

- Accounting supports posted invoices, payment registration, and reconciliation.
- Outstanding customer credits are discoverable and can be reconciled to invoices.
- Software license billing currently creates invoices, but does not auto-consume prior credits.
- No dedicated customer-facing prepayment request/ledger flow is fully implemented yet.

## 4) Implementation Scope

In scope:

- Data model for prepayment request and allocation tracking.
- Customer panel prepayment request/payment entry.
- Auto-allocation engine during invoice billing.
- Traceability and reporting (request -> payment -> invoice allocation).

Out of scope (for now):

- External payment gateway expansion.
- Multi-company advanced routing rules unless required by current company setup.

## 5) Work Breakdown

### Phase A - Data Model

- [ ] Create prepayment request table (status lifecycle, partner, amounts, currency, company, refs).
- [ ] Create allocation table linking request/payment source to invoice and amount consumed.
- [ ] Add model relations and policies.
- [ ] Add indexes for partner_id, status, created_at.

### Phase B - Customer Flow

- [ ] Add customer panel page/form to create prepayment request.
- [ ] Add customer view for available balance and prepayment history.
- [ ] Add validation rules (minimum amount, currency consistency, partner ownership).

### Phase C - Accounting Integration

- [ ] On request payment/approval, create accounting payment entry as customer credit.
- [ ] Ensure generated entries are consistent with receivable reconciliation rules.
- [ ] Store references between prepayment request and accounting payment/move lines.

### Phase D - Auto Deduction on Billing

- [ ] Hook into invoice creation/confirmation flow used by software license billing.
- [ ] Fetch available credit for same customer/partner (oldest first by default).
- [ ] Reconcile/apply credit automatically until invoice residual is zero or credit exhausted.
- [ ] Persist allocation rows for every consumed amount.
- [ ] Update invoice payment state accordingly (paid/partial).

### Phase E - Traceability + UI

- [ ] Add admin/customer views: allocations by invoice and by prepayment request.
- [ ] Add quick links from license invoice records to allocation details.
- [ ] Add audit fields (created_by, timestamps, optional notes).

### Phase F - Testing

- [ ] Feature tests: create prepayment request, payment posting, auto-consume full amount.
- [ ] Feature tests: partial consume and remaining credit behavior.
- [ ] Feature tests: no credit available fallback.
- [ ] Feature tests: exact traceability integrity (request -> allocation -> invoice).
- [ ] Regression tests for current software billing behavior.

## 6) Open Questions (To Fill Later)

- [ ] Should prepayment requests require manual approval before usable balance?
- [ ] Should credit expiration be supported?
- [ ] Allocation priority rule: oldest request first, or custom priority?
- [ ] Can credit be restricted by product/program/category?
- [ ] Should customers be allowed to refund unused credit?

## 7) Execution Notes

- Prefer accounting ledger as source of truth for available credit.
- Keep allocation records explicit even if reconciliation exists, for clear business traceability.
- Keep implementation backward-compatible with existing license billing action.

## 8) Change Log

- 2026-04-14: Initial draft created.
- [Add next updates here]
