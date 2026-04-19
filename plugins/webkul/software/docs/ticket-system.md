# Ticket System — Implementation Notes

## Overview

A complete ticket support system was built inside the `software` plugin. It supports:
- **Admin side**: create, view, edit, manage tickets with full conversation thread
- **Customer portal**: open tickets, view replies, respond
- **REST API**: for future mobile app integration (auth:sanctum protected)

---

## Database

### New Table: `software_ticket_attachments`

Migration: `2026_04_19_000029_create_software_ticket_attachments_table.php`

```
software_ticket_attachments
├── id
├── attachable_type   (morphs — Ticket or TicketEvent)
├── attachable_id
├── file_path
├── original_name
├── mime_type
├── file_size
└── timestamps
```

Polymorphic design allows attachments on both the original ticket message and on each reply event.

---

## New Files

### Models

| File | Purpose |
|------|---------|
| `src/Models/TicketAttachment.php` | Polymorphic attachment model with `url` appended attribute and `isImage()` helper |

Modified existing models:
- `Ticket` — added `attachments(): MorphMany`
- `TicketEvent` — added `attachments(): MorphMany`

---

### Services

| File | Purpose |
|------|---------|
| `src/Services/TicketService.php` | Shared service used by admin, customer, and API |

**Methods:**
- `generateTicketNumber()` — auto-increments from max existing ticket_number (safe with soft-deleted)
- `createTicket(array $data, array $filePaths): Ticket` — creates ticket + saves attachments
- `replyToTicket(Ticket, array $data, array $filePaths): TicketEvent` — adds reply + updates unread flags
- `saveAttachments(Ticket|TicketEvent, array $filePaths)` — persists file paths to `software_ticket_attachments`
- `storeUploadedFile(UploadedFile): string` — stores to `public/software/tickets` disk

Registered as singleton in `SoftwareServiceProvider`.

---

### Livewire

| File | Purpose |
|------|---------|
| `src/Livewire/TicketConversationPanel.php` | Livewire component for ticket conversation thread |
| `resources/views/livewire/ticket-conversation-panel.blade.php` | Blade view |

**Props:**
- `ticket: Ticket`
- `senderType: 'admin' | 'customer'`
- `canReply: bool`

**Behavior:**
- Shows replies newest-first, original message at the bottom
- Reply via modal with RichEditor + FileUpload
- Admin replies shown in blue with "Staff" badge
- Customer replies shown in green with "Customer" badge
- Reply button hidden when ticket is closed

Registered in `SoftwareServiceProvider::packageBooted()`:
```php
Livewire::component('software-ticket-conversation-panel', TicketConversationPanel::class);
```

---

### Admin Filament (under `Filament/Admin/Resources/TicketResource/`)

| File | Purpose |
|------|---------|
| `TicketResource.php` | Full rebuild — form + table + filters |
| `Pages/ListTickets.php` | List page with Create button |
| `Pages/CreateTicket.php` | Create page — calls TicketService |
| `Pages/ViewTicket.php` | View page — shows infolist + TicketConversationPanel |
| `Pages/EditTicket.php` | Edit page for status/priority/assignment changes |

**Key features:**
- `partner_id` select → live → filters `license_id` options to that customer's licenses
- `license_id` select → live → auto-fills `program_id`
- `ticket_number` is read-only, auto-generated via TicketService
- `content` uses RichEditor (not Textarea)
- `attachments` uses FileUpload (multiple, any type, max 10 MB)
- Status/Priority badges with color coding in table

---

### Customer Filament (under `Filament/Customer/Clusters/Account/Resources/TicketResource/`)

| File | Purpose |
|------|---------|
| `TicketResource.php` | Customer-facing resource — filtered to `Auth::guard('customer')->id()` |
| `Pages/ListTickets.php` | Customer's ticket list |
| `Pages/CreateTicket.php` | Create ticket — auto-sets `partner_id` from auth guard |
| `Pages/ViewTicket.php` | View + conversation panel with `senderType='customer'` |

**Key differences from admin:**
- Cluster: `Webkul\Website\Filament\Customer\Clusters\Account`
- `partner_id` never shown to customer — set automatically from auth
- `license_id` options filtered to the logged-in customer's licenses
- `program_id` auto-filled when license is selected

Appears in sidebar under "Account" cluster as "Support Tickets".

---

### Plugin Registration

`SoftwarePlugin.php` — added customer panel discovery:
```php
->when($panel->getId() == 'customer', function (Panel $panel): void {
    $panel->discoverResources(
        in: __DIR__.'/Filament/Customer/Clusters',
        for: 'Webkul\\Software\\Filament\\Customer\\Clusters'
    );
});
```

`SoftwareServiceProvider.php` changes:
- Added migration `2026_04_19_000029_create_software_ticket_attachments_table`
- Registered `TicketService::class` singleton
- Registered `TicketConversationPanel` Livewire component in `packageBooted()`

---

### API Endpoints (`routes/api.php`)

All routes under `auth:sanctum` middleware, prefix `admin/api/v1/software/`.

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/tickets` | `admin.api.v1.software.tickets.index` | List tickets (filterable, sortable, paginated) |
| POST | `/tickets` | `admin.api.v1.software.tickets.store` | Create ticket |
| GET | `/tickets/{ticket}` | `admin.api.v1.software.tickets.show` | View single ticket |
| PUT/PATCH | `/tickets/{ticket}` | `admin.api.v1.software.tickets.update` | Update ticket |
| DELETE | `/tickets/{ticket}` | `admin.api.v1.software.tickets.destroy` | Delete ticket |
| GET | `/tickets/{ticket}/replies` | `admin.api.v1.software.tickets.replies.index` | List replies |
| POST | `/tickets/{ticket}/replies` | `admin.api.v1.software.tickets.replies.store` | Add reply |

**Controller:** `src/Http/Controllers/API/V1/TicketController.php`

**Allowed filters:** `status`, `priority`, `partner_id`, `assigned_to`  
**Allowed sorts:** `ticket_number`, `created_at`, `updated_at`, `status`, `priority`  
**Allowed includes:** `partner`, `program`, `license`, `assignedTo`, `attachments`

---

### API Form Requests

| File | Covers |
|------|--------|
| `src/Http/Requests/TicketRequest.php` | Create + Update ticket (partial update safe) |
| `src/Http/Requests/TicketReplyRequest.php` | Reply to ticket |

---

### API Resources

| File | Covers |
|------|--------|
| `src/Http/Resources/V1/TicketResource.php` | Full ticket with conditional relations |
| `src/Http/Resources/V1/TicketEventResource.php` | Reply event with sender info (staff vs customer) |

---

## Design Decisions

| Decision | Reason |
|----------|--------|
| Custom Livewire conversation panel instead of Chatter | Chatter is for internal admin communication (logs, activities, followers). Not suitable for customer-facing ticket threads. |
| Polymorphic `TicketAttachment` | Attachments belong to both Ticket (original message) and TicketEvent (replies) without duplicating upload logic |
| `TicketService` shared | Admin Filament, Customer Filament, and API all call the same service — no duplicate creation logic |
| `ticket_number` auto-generated | Uses `max(ticket_number) + 1` on the base Ticket query including soft-deleted records to prevent gaps being reused |
| License select filters by partner | Admin selects customer first → only that customer's licenses appear → program auto-fills |
| Customer `partner_id` from auth | Customer never sees or sets their own ID — it's always taken from `Auth::guard('customer')->id()` |
