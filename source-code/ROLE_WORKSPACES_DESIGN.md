# Role Workspaces — Feature Design & Implementation Plan

**Project:** Kintu Property & Governance Management  
**Stack:** Laravel 10, Bootstrap 5, shareholder-ui.css, ApexCharts, XAMPP / Cloudflare tunnel  
**Last updated:** 2025  

---

## Overview

The system has **7 role workspaces**. Each role is assigned to a team member by the owner and gets a scoped portal that shows only data belonging to that owner (`owner_user_id`). Currently the dashboards display read-only data. This document defines every **action, form, upload, and workflow** that each role should be able to perform, plus all missing features that need to be built.

> **Design rule:** All pages must follow the existing `shareholder-ui.css` theme — `sh-stat-grid`, `sh-stat-card`, `card border-0 shadow-sm`, `table table-hover`, Bootstrap 5 grid. No emojis. Mobile-first, responsive. Back-to-dashboard button on every section page. Pagination on all listing tables.

---

## Current Architecture

```
app/Http/Controllers/RoleWorkspaces/RoleWorkspaceController.php
  dashboard($role)         → loads role-specific dashboard view
  workbench($role, $section) → dispatches to one of 17 section views

resources/views/role-workspaces/
  layouts/
    app.blade.php           (shareholder-ui.css, layout wrapper)
    sidebar.blade.php       (metismenu, navItems dynamic)
    header.blade.php        (logo, role badge, dark mode, user dropdown)
    bottom-nav.blade.php    (mobile fixed nav)
  director.blade.php
  finance-manager.blade.php
  landlord.blade.php
  tenant-manager.blade.php
  auditor.blade.php
  compliance-officer.blade.php
  secretary.blade.php
  sections/
    invoices.blade.php, expenses.blade.php, properties.blade.php,
    units.blade.php, tenants.blade.php, tickets.blade.php,
    maintenance.blade.php, payments.blade.php, reports.blade.php,
    noticeboard.blade.php, approvals.blade.php, meetings.blade.php,
    resolutions.blade.php, documents.blade.php, audit-logs.blade.php,
    large-transactions.blade.php, alerts.blade.php
```

### Data Scoping

Every query must use `getOwnerUserId()` and filter by `owner_user_id`. Models that lack a direct `owner_user_id` (e.g. `MaintenanceRequest`, `PropertyUnit`) must be scoped via a JOIN through `properties`.

---

## Role 1 — Director

**Purpose:** Board-level governance. Oversees financial approvals, board calendar, resolutions, and strategic reports.

### Dashboard (current)
- KPI cards: Pending Approvals, Open Resolutions, Upcoming Meetings, Total Revenue YTD, Total Expenses YTD, Net Profit, Total Properties, Active Tenants
- ApexCharts revenue area chart (monthly)
- Upcoming meetings list with calendar date badges
- Recent approvals table

### Missing / Needs Building

#### 1. Approvals — Full Workflow
**Current:** Read-only table  
**Needs:**
- **Approve / Reject** a pending financial approval with a comment (modal)
- **View approval detail** — amount, description, requested by, deadline, supporting documents attached
- **Add a supporting document** to an existing approval
- **Create new approval request** (Director can request an approval on behalf of the board)
- Filter by: pending / approved / rejected / expired / priority (urgent, normal, low)
- Approval threshold display — show % votes required vs % received

**Models:** `FinancialApproval`, `FinancialApprovalAction`, `GovernanceDocument`  
**Actions needed in controller:**
```
approvalDetail($id)
approvalAction(Request $request, $id)   // approve or reject
approvalStore(Request $request)         // create new
approvalDocumentUpload(Request $request, $id)
```

#### 2. Meetings — Full Workflow
**Current:** Read-only list  
**Needs:**
- **Schedule a new meeting** (form: title, type AGM/Board/EGM/Other, mode In-Person/Virtual/Hybrid, scheduled_at, location/link, agenda, recurrence)
- **Edit / Cancel** a scheduled meeting
- **Mark meeting as completed** (sets status = 2, records ended_at)
- **Attach minutes document** to a completed meeting
- **View attendees list** — who was invited, RSVP status
- **Send meeting notification** to all relevant users

**Models:** `GovernanceMeeting`, `MeetingAttendee`, `GovernanceDocument`  
**Actions needed:**
```
meetingStore(Request $request)
meetingUpdate(Request $request, $id)
meetingComplete(Request $request, $id)
meetingCancel($id)
meetingAttachMinutes(Request $request, $id)
```

#### 3. Resolutions — Create & Manage
**Current:** Read-only table  
**Needs:**
- **Create a new resolution** (title, type, description, linked meeting, voting opens/closes dates, approval threshold %)
- **Open / Close** voting on a resolution
- **View vote breakdown** — for / against / abstain counts and percentages with visual bar
- **Mark as passed or failed** manually (or auto-compute based on threshold)
- **Attach supporting documents** to a resolution
- **Export resolution as PDF**

**Models:** `Resolution`, `ResolutionVote`, `GovernanceDocument`  
**Actions needed:**
```
resolutionStore(Request $request)
resolutionUpdate(Request $request, $id)
resolutionStatusChange(Request $request, $id)
resolutionVoteResults($id)
resolutionDocumentUpload(Request $request, $id)
```

#### 4. Reports — Financial Overview
**Current:** Section exists with charts  
**Needs:**
- **Date range filter** (custom from/to, or presets: this month, this quarter, this year)
- **Downloadable PDF report** (monthly summary table)
- **Expense breakdown by category** (pie chart)
- **Revenue vs Expense** bar chart (monthly comparison)

#### 5. Governance Documents — View & Download
**Current:** Linked via compliance-officer  
**Needs:** Director should also see governance documents relevant to board decisions, with download links

---

## Role 2 — Finance Manager

**Purpose:** Day-to-day financial operations — invoices, expenses, approvals, reports, and reconciliation.

### Dashboard (current)
- KPI cards: Total Revenue, Total Expenses, Paid Invoices, Unpaid Invoices, Pending Approvals, Monthly Income
- ApexCharts revenue + expense dual-axis chart
- Recent invoices table
- Recent expenses list

### Missing / Needs Building

#### 1. Invoices — Full CRUD
**Current:** Read-only table  
**Needs:**
- **Create invoice** (tenant, property, unit, type, amount, due date, month, tax, note)
- **Mark as paid** (record payment method, date, reference)
- **Send invoice notification** to tenant by email
- **View invoice detail** — line items, payment history
- **Download / Print invoice** as PDF
- **Filter by:** status (paid, unpaid, overdue), property, date range
- **Bulk invoice generation** for all active tenants of a property

**Models:** `Invoice`, `InvoiceItem`, `Tenant`, `Property`  
**Actions:**
```
invoiceStore(InvoiceRequest $request)
invoiceDetail($id)
invoicePay(Request $request, $id)
invoicePrint($id)
invoiceSendNotification(Request $request)
```

#### 2. Expenses — Full CRUD
**Current:** Read-only table  
**Needs:**
- **Add new expense** (name, type, amount, property, unit optional, responsibility: tenant/owner, date, receipt upload)
- **Edit / Delete** expense
- **Approve expense** (if it requires approval based on amount threshold)
- **Attach receipt** (file upload to `FileManager`)
- **Filter by:** property, expense type, date range, responsibility

**Models:** `Expense`, `ExpenseType`, `FileManager`  
**Actions:**
```
expenseStore(Request $request)
expenseUpdate(Request $request, $id)
expenseDelete($id)
expenseReceiptUpload(Request $request, $id)
```

#### 3. Approvals — Review & Action
**Current:** Read-only  
**Needs:**
- **Approve / Reject** with comment
- **View full detail** of approval request
- **Set approval threshold** per request (if Director has delegated this)

#### 4. Payments — Record & Track
**Current:** Read-only list of paid invoices  
**Needs:**
- **Record a manual payment** (cash, bank, mobile money) against an invoice
- **Reverse / void** a recorded payment
- **Filter by payment method, date range, property**
- **Export payment summary** CSV

**Models:** `Invoice`, `RentCollection`

#### 5. Reports — Generate & Export
**Current:** Charts only  
**Needs:**
- **Generate monthly P&L report** and save as `FinancialReport` record
- **Export to PDF / CSV**
- **Filter by property and date range**
- **Expense breakdown chart by category**
- **Occupancy vs Revenue** correlation chart

#### 6. Expense Types — Manage
- List of expense categories (maintenance, utilities, contractor, etc.)
- Add / edit custom expense types

---

## Role 3 — Landlord

**Purpose:** Property ownership view — portfolio performance, unit management, maintenance oversight, income tracking.

### Dashboard (current)
- 8 KPI cards: Properties, Units, Active Tenants, Monthly Income, Yearly Income, Pending Payments, Maintenance Cost, Net Earnings YTD
- Property portfolio card grid with occupancy progress bars

### Missing / Needs Building

#### 1. Properties — View & Manage
**Current:** Card grid (read-only)  
**Needs:**
- **Add new property** (multi-step form: info → location → units → rent charge → images)
- **Edit property** details
- **View property detail** — units, tenants, income, maintenance history
- **Upload property images**
- **Set property status** (active / deactivate)

**Models:** `Property`, `PropertyDetail`, `PropertyUnit`, `PropertyImage`

#### 2. Units — Manage
**Current:** Table (read-only)  
**Needs:**
- **Add unit** to a property (unit number, type, rent amount, rent type monthly/yearly)
- **Edit unit** rent and details
- **View unit detail** — current tenant, payment history, maintenance requests
- **Mark unit as vacant** when tenant leaves

**Models:** `PropertyUnit`, `Tenant`

#### 3. Maintenance — Track & Assign
**Current:** Read-only table  
**Needs:**
- **Create maintenance request** (property, issue type, description, estimated cost, attach photo)
- **Assign to maintainer** (select from maintainer list)
- **Update status** (pending → in progress → complete)
- **Record actual cost** on completion
- **View attached photos and invoices**
- **Filter by property, status, date**

**Models:** `MaintenanceRequest`, `MaintenanceIssue`, `Maintainer`  
**Actions:**
```
maintenanceStore(Request $request)
maintenanceStatusChange(Request $request, $id)
maintenanceAssign(Request $request, $id)
maintenanceCostUpdate(Request $request, $id)
```

#### 4. Payments — Income Tracking
**Current:** Paid invoices list  
**Needs:**
- **Record rent collection** (cash, bank, mobile money)
- **View payment history per unit / tenant**
- **Flag overdue payments**
- **Export rent roll** CSV

#### 5. Notice Board — Post Notices
**Current:** Not in landlord sidebar  
**Needs:**
- **Post a notice** to all tenants, specific property, or specific unit
- **Edit / delete** own notices
- Attach an image to a notice

---

## Role 4 — Tenant Manager

**Purpose:** Manage tenant lifecycle — onboarding, lease management, tickets, notices.

### Dashboard (current)
- 5 KPI cards: Total Tenants, Active Tenants, Vacant Units, Open Tickets, Pending Invoices
- Occupancy progress bar
- Expiring leases list (60 days)
- Recently added tenants list

### Missing / Needs Building

#### 1. Tenants — Full Lifecycle
**Current:** Read-only table  
**Needs:**
- **Add new tenant** (user details, property, unit assignment, lease start/end date, rent amount, security deposit)
- **Edit tenant** details and lease dates
- **Close tenancy** (record vacated date, deposit refund status)
- **Upload tenant documents** (ID copy, lease agreement, references)
- **View tenant detail** — invoice history, tickets, maintenance requests, documents
- **Send welcome/reminder** email to tenant
- **Lease renewal** — extend lease end date

**Models:** `Tenant`, `TenantDetails`, `TenancyContract`, `FileManager`  
**Actions:**
```
tenantStore(TenantRequest $request)
tenantEdit($id)
tenantUpdate(Request $request, $id)
tenantClose(TenantCloseRequest $request, $id)
tenantDocumentUpload(Request $request, $id)
tenantDocumentDelete($id)
tenantDetail($id)
```

#### 2. Tickets — Manage & Reply
**Current:** Read-only table  
**Needs:**
- **View ticket detail** with full conversation thread
- **Reply to ticket** (text + optional file attachment)
- **Change ticket status** (open → in progress → resolved → closed)
- **Assign ticket priority** (low / medium / high / urgent)
- **Filter by:** status, priority, property, topic, date
- **Create ticket** on behalf of tenant (if raised by phone/in person)

**Models:** `Ticket`, `TicketReply`, `TicketTopic`  
**Actions:**
```
ticketDetail($id)
ticketReply(TicketReplyRequest $request)
ticketStatusChange(Request $request)
ticketStore(Request $request)      // create on behalf of tenant
```

#### 3. Notice Board — Post & Manage
**Current:** Read-only card grid  
**Needs:**
- **Create notice** (title, details, scope: all properties / specific property / specific unit, attach image)
- **Edit / delete** own notices
- **View who received** the notice (user list)

**Models:** `NoticeBoard`, `FileManager`

#### 4. Invoices — View & Generate
**Current:** Read-only table  
**Needs:**
- **Generate invoice** for a tenant (or trigger from unit)
- **Send invoice reminder** to tenant
- View invoice and payment status per tenant

#### 5. KYC / Documents — Verify Tenants
**Current:** Not present  
**Needs:**
- **List tenant KYC verification requests** (ID documents uploaded by tenants)
- **Approve / reject** KYC document
- **View uploaded document** image/PDF

**Models:** `KycVerification`, `FileManager`

---

## Role 5 — Auditor

**Purpose:** Financial and operational audit — read-only access across all financial data with export capability.

### Dashboard (current)
- 6 KPI cards: Total Audit Entries, Today, This Week, Revenue YTD, Expenses YTD, Pending Approvals
- Recent audit logs table (scrollable)
- Activity breakdown sidebar
- Largest transactions table

### Missing / Needs Building

#### 1. Audit Logs — Full Search & Export
**Current:** Scrollable table (read-only)  
**Needs:**
- **Search by:** user name, action type, date range, IP address
- **Filter by action category** (login, payment, expense, report, etc.)
- **Export to CSV / PDF**
- **View full detail** of an audit entry (old values, new values, metadata)

**Models:** `FinancialAuditLog`, `GovernanceAuditLog`

#### 2. Large Transactions — Threshold Monitoring
**Current:** Table (read-only)  
**Needs:**
- **Configurable threshold** — filter invoices/payments above a set amount
- **Flag suspicious transaction** (add a note / mark for review)
- **Export flagged transactions** report
- **Cross-reference** with corresponding expense/approval record

#### 3. Financial Reports — View & Generate
**Current:** Not in auditor sidebar  
**Needs:**
- **View all published financial reports** (P&L, balance sheet, expense reports)
- **Download report PDF**
- **Filter by type, period, property**
- Auditor should **not** be able to create or modify reports — view/download only

**Models:** `FinancialReport`

#### 4. Reconciliation Logs — Review
**Current:** Not present  
**Needs:**
- **View reconciliation records** (MTN MoMo, Airtel Money, bank, cash)
- **See matched / unmatched / suspicious entries**
- **Flag unmatched item** for investigation

**Models:** `ReconciliationLog`

#### 5. Expense Audit Trail
**Current:** Not present  
**Needs:**
- Full list of all expenses with their approval chain
- Who created, who approved/rejected, when, what document was attached

#### 6. Audit Settings (read-only)
- View approval thresholds currently set
- View governance rules in effect

---

## Role 6 — Compliance Officer

**Purpose:** Governance risk and compliance — policies, document lifecycle, resolution compliance, regulatory alerts.

### Dashboard (current)
- 6 KPI cards: Governance Documents, Total Resolutions, Passed, Failed, Open Resolutions, Active Tenants
- Compliance alerts (color-coded)
- Governance documents table
- Resolution health + pass rate progress bar
- Governance audit trail table

### Missing / Needs Building

#### 1. Governance Documents — Full CRUD
**Current:** Read-only table  
**Needs:**
- **Upload new governance document** (title, document type: policy/bylaw/regulation/contract/minutes/other, status, expiry date, linked meeting/resolution, file upload)
- **Edit document metadata** (title, type, status, expiry)
- **Set document status** (draft → active → expired)
- **Download document** (link to file)
- **Delete document**
- **Expiry alerts** — documents expiring within 30 days flagged in red
- **Filter by:** type, status, meeting, expiry range

**Models:** `GovernanceDocument`, `FileManager`  
**Actions:**
```
documentStore(Request $request)
documentUpdate(Request $request, $id)
documentStatusChange($id, $status)
documentDelete($id)
documentDownload($id)
```

#### 2. Compliance Alerts — Configure & Manage
**Current:** Read from database — logic to be defined  
**Needs:**
- **System-generated alerts** for: documents expiring soon, unresolved open resolutions past closing date, failed resolutions requiring action, governance rules violated
- **Manual alert creation** — Compliance Officer can post a compliance notice
- **Dismiss / acknowledge** an alert
- **Alert categories:** warning, danger, info, success

**Models:** New `ComplianceAlert` model or use existing `SystemNotification`

#### 3. Resolutions — Compliance View
**Current:** Not directly linked  
**Needs:**
- **View all resolutions** with compliance status
- **Check if resolution had quorum** (minimum attendees required)
- **Check if resolution followed governance rules** (correct approval threshold, correct notice period)
- **Flag non-compliant resolution** with a note

#### 4. Governance Audit Trail — Full Export
**Current:** Read-only scrollable table  
**Needs:**
- **Full-text search** on action and description
- **Date range filter**
- **Export to PDF / CSV**
- **View detail** of each audit entry

**Models:** `GovernanceAuditLog`

#### 5. Governance Rules — View
**Current:** Not present  
**Needs:**
- **View active governance rules** (quorum requirements, notice periods, approval thresholds)
- Read-only — only owner/admin can modify rules
- Flag rules that have been violated in a resolution

**Models:** `GovernanceRule`

#### 6. Meetings — Compliance Review
**Current:** Not in compliance sidebar  
**Needs:**
- **View all past meetings** with compliance checklist
  - Was quorum met?
  - Were minutes uploaded within 48 hours?
  - Were attendees notified in time?
- **Attach compliance notes** to a meeting

---

## Role 7 — Secretary

**Purpose:** Board administration — meeting scheduling, agenda management, minutes, resolutions, document filing.

### Dashboard (current)
- 4 KPI cards: Upcoming Meetings, Total Meetings, Open Resolutions, Governance Documents
- Upcoming meetings list (calendar badges)
- Open resolutions list (with voting countdown)
- This month's calendar of meetings

### Missing / Needs Building

#### 1. Meetings — Full Lifecycle Management
**Current:** Read-only list  
**Needs:**
- **Schedule new meeting** (title, type AGM/Board/EGM/Other, mode In-Person/Virtual/Hybrid, scheduled_at, location or video link, agenda, recurrence option)
- **Edit meeting** details before it occurs
- **Cancel meeting** with reason
- **Mark as completed** — record ended_at
- **Add/edit agenda items** per meeting
- **Record attendees** — who attended, who sent apologies
- **Upload minutes** document after meeting
- **Send meeting invitations / reminders** to attendees

**Models:** `GovernanceMeeting`, `MeetingAttendee`, `GovernanceDocument`  
**Actions:**
```
meetingStore(Request $request)
meetingUpdate(Request $request, $id)
meetingCancel($id)
meetingComplete(Request $request, $id)
meetingAttachMinutes(Request $request, $id)
meetingAttendeeUpdate(Request $request, $id)
meetingSendNotification(Request $request, $id)
```

#### 2. Resolutions — Draft & Manage
**Current:** Read-only list  
**Needs:**
- **Draft new resolution** (title, type, description, linked meeting, voting opens/closes, threshold)
- **Open voting** (change status to open, set dates)
- **Close voting** and trigger result computation
- **View vote tally** — for / against / abstain with visual breakdown
- **Attach supporting document** to resolution
- **Export resolution** as PDF (for the record)

**Models:** `Resolution`, `ResolutionVote`, `GovernanceDocument`

#### 3. Documents — File & Manage
**Current:** Not in secretary sidebar  
**Needs:**
- **Upload governance document** (meeting minutes, agenda, policy, contract, etc.)
- **Link document to a meeting or resolution**
- **View all documents** with filters by type, meeting, status
- **Download document**
- **Set document status** (draft / active / archived)

**Models:** `GovernanceDocument`, `FileManager`

#### 4. Meeting Calendar — Interactive
**Current:** Static list of this month's meetings  
**Needs:**
- **Month / week / list toggle view**
- Click a day to see meetings
- Click a meeting to see detail and perform actions
- Color-code by meeting type (AGM = green, Board = blue, EGM = red)

#### 5. Notices / Correspondence
**Current:** Not present  
**Needs:**
- **Draft and send formal notice** to shareholders or tenants
- Record outgoing correspondence for compliance record

---

## Cross-Role Features (Shared Infrastructure)

### File Upload System
- All uploads must go through the existing `FileManager` model
- Store in `public/uploads/{owner_user_id}/{model_type}/`
- Accept: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, WEBP
- Max size: 10MB
- Generate a signed temporary URL for download

### Role-Scoped Notifications
- When an action is taken in a role workspace (approval submitted, meeting scheduled, notice posted), a `Notification` record should be created for relevant users
- Show notification count badge in the header

### Export / Print
- Every listing table should have an **Export CSV** button
- Key documents (invoices, reports, resolutions) should have **Print / PDF** buttons
- PDF generation via existing `barryvdh/laravel-dompdf` or similar

### Audit Logging
- Every create / update / delete action performed in a role workspace must create a `FinancialAuditLog` or `GovernanceAuditLog` record
- Log: `action`, `description`, `user_id`, `owner_user_id`, `ip_address`, `old_values` (JSON), `new_values` (JSON)

### In-line Modals vs Full Pages
- Simple status changes, quick approvals, and confirmations → **modal dialog**
- Multi-field creation forms (new meeting, new tenant, new invoice) → **dedicated page or multi-step modal**
- Detail views → **slide-out panel or dedicated detail page**

---

## Controller Architecture

Create a dedicated controller per feature group inside `RoleWorkspaces/`:

```
app/Http/Controllers/RoleWorkspaces/
  RoleWorkspaceController.php       (existing — dashboard + section dispatch)
  RW_InvoiceController.php          (invoices CRUD for all roles)
  RW_ExpenseController.php          (expenses CRUD)
  RW_MeetingController.php          (meetings full lifecycle)
  RW_ResolutionController.php       (resolutions + voting)
  RW_DocumentController.php         (governance document upload/manage)
  RW_MaintenanceController.php      (maintenance requests + assign)
  RW_TenantController.php           (tenant onboarding + lifecycle)
  RW_TicketController.php           (ticket replies + status)
  RW_NoticeBoardController.php      (notice create/manage)
  RW_ApprovalController.php         (financial approval actions)
  RW_ReportController.php           (report generation + export)
```

### Route Naming Convention
```
role.{role}.invoices.store
role.{role}.invoices.pay
role.{role}.meetings.store
role.{role}.meetings.complete
role.{role}.resolutions.store
role.{role}.resolutions.vote-results
role.{role}.documents.store
role.{role}.documents.download
role.{role}.tickets.reply
role.{role}.tenants.store
...
```

All routes must be prefixed with:
```php
Route::prefix('role-workspaces/{role}')
    ->middleware(['auth', 'role.workspace', 'role.access:{role}'])
    ->name('role.')
```

---

## Data That Is Not Pulling (Fix Required)

The following section data methods in `RoleWorkspaceController::sectionDataFor()` need fixing or verification:

| Section | Issue | Fix |
|---------|-------|-----|
| `maintenance` | JOIN through properties may miss the `property_name` alias | Verify `select('maintenance_requests.*', 'properties.name as property_name')` is working |
| `units` | Same JOIN pattern — verify `UNIT_STATUS_VACANT` constant is used correctly | Check `propertyUnits()` relationship is not `units()` |
| `approvals` | `FinancialApproval` may not have `owner_user_id` directly — check migration | Add `owner_user_id` column if missing, or scope via `requested_by` → `users.owner_user_id` |
| `meetings` | `GovernanceMeeting` may not have `owner_user_id` — check migration | Scope via `owner_user_id` on the model or via `created_by` join |
| `resolutions` | Same as meetings | Same fix |
| `documents` | `GovernanceDocument` may not have `owner_user_id` | Scope via `uploaded_by` → user → owner, or direct column |
| `audit-logs` | `FinancialAuditLog` vs `GovernanceAuditLog` — check which table exists | Confirm table name in migration, update queries |
| `large-transactions` | Currently queries `Invoice` — confirm `amount` field name and ordering | Use `orderByDesc('amount')` and ensure `amount` is the correct column |
| `alerts` | Currently no `alerts` table — generate from business logic | Build alerts dynamically from: expiring leases, overdue invoices, expiring docs, open resolutions past deadline |

---

## UI / Design Guidelines (Maintain Existing Theme)

### Keep Intact
- `shareholder-ui.css` — do not create a new CSS file for role workspaces
- `sh-stat-grid` + `sh-stat-card` + `sh-stat-icon` + `sh-stat-body` for all KPI rows
- `card border-0 shadow-sm` for all content panels
- `table table-hover` + `table-light` thead for all tables
- `badge` + status colors: success (paid/active), warning (pending/open), danger (failed/overdue/vacant), secondary (draft/inactive)
- Bottom-nav `sh-bottom-nav d-lg-none` for mobile
- Sidebar with `metismenu` + active state highlighting
- Dark mode compatible (Bootstrap `data-bs-theme="dark"`)

### Improve
- **Action buttons in tables:** Add `btn btn-sm btn-outline-primary` for View, `btn btn-sm btn-outline-success` for Approve, `btn btn-sm btn-outline-danger` for Reject — right-aligned in a dedicated Actions column
- **Empty state illustrations:** When a table has no records, show an icon (Remix Icon) + short message instead of just a text row
- **Toast notifications:** On success/error of any form submission, show a Bootstrap toast (top-right) instead of a full page alert
- **Loading state:** Add a spinner overlay on form submission buttons (`btn-loading` pattern)
- **Sticky table headers:** For long tables use `position: sticky; top: 0` on `thead`
- **Mobile table:** On xs screens, either use horizontal scroll or card-based row layout
- **Modals:** Use `modal-lg` for forms, `modal-sm` for confirmations. All modals must close on ESC and backdrop click
- **Filter bar:** Add a collapsible filter row above each table (`btn-outline-secondary` toggle button)
- **Breadcrumb:** Show role → section on all section pages (e.g. "Director / Meetings")

### Colors (Consistent with shareholder-ui.css)
| Use | Class / Color |
|-----|---------------|
| Primary action | `btn-primary` (#6366f1) |
| Success / Paid / Active | `text-success` / `bg-success` (#10b981) |
| Warning / Pending / Expiring | `text-warning` / `bg-warning` (#f59e0b) |
| Danger / Overdue / Failed | `text-danger` / `bg-danger` (#ef4444) |
| Muted info | `text-muted` / `bg-secondary` |
| KPI icon backgrounds | match existing palette per role |

---

## Priority Implementation Order

### Phase 1 — Critical (Fixes data display first)
1. Fix all `sectionDataFor()` queries that return empty data
2. Add `Approve / Reject` action to **approvals** section (Director + Finance Manager)
3. Add **ticket reply** and status change (Tenant Manager)
4. Add **maintenance status change** (Landlord)
5. Add **meeting schedule form** (Secretary + Director)

### Phase 2 — Core Workflows
6. Full **invoice CRUD** (Finance Manager)
7. Full **expense CRUD** (Finance Manager)
8. Full **tenant onboarding form** (Tenant Manager)
9. **Resolution create + vote results** (Secretary + Director)
10. **Governance document upload** (Compliance Officer + Secretary)

### Phase 3 — Advanced Features
11. **Audit log export** (Auditor)
12. **Report generation + PDF export** (Finance Manager + Auditor)
13. **KYC document review** (Tenant Manager)
14. **Notice board create/edit** (Tenant Manager + Landlord)
15. **Compliance alerts engine** (Compliance Officer)
16. **Reconciliation log view** (Auditor)
17. **Meeting calendar view** (Secretary)

### Phase 4 — Polish
18. Toast notifications on all actions
19. Export CSV on all listing tables
20. Empty state illustrations
21. Filter bars on all sections
22. Breadcrumb navigation
23. Role-scoped notification bell

---

## File Structure After Full Build

```
app/Http/Controllers/RoleWorkspaces/
  RoleWorkspaceController.php
  RW_InvoiceController.php
  RW_ExpenseController.php
  RW_MeetingController.php
  RW_ResolutionController.php
  RW_DocumentController.php
  RW_MaintenanceController.php
  RW_TenantController.php
  RW_TicketController.php
  RW_NoticeBoardController.php
  RW_ApprovalController.php
  RW_ReportController.php

resources/views/role-workspaces/
  layouts/
    app.blade.php
    sidebar.blade.php
    header.blade.php
    bottom-nav.blade.php
  dashboards/
    director.blade.php
    finance-manager.blade.php
    landlord.blade.php
    tenant-manager.blade.php
    auditor.blade.php
    compliance-officer.blade.php
    secretary.blade.php
  sections/
    invoices.blade.php          (list + filters)
    invoices/
      create.blade.php
      detail.blade.php
      pay.blade.php
    expenses.blade.php
    expenses/
      create.blade.php
    properties.blade.php
    units.blade.php
    tenants.blade.php
    tenants/
      create.blade.php
      detail.blade.php
    tickets.blade.php
    tickets/
      detail.blade.php
    maintenance.blade.php
    maintenance/
      create.blade.php
    payments.blade.php
    reports.blade.php
    noticeboard.blade.php
    noticeboard/
      create.blade.php
    approvals.blade.php
    approvals/
      detail.blade.php
    meetings.blade.php
    meetings/
      create.blade.php
      detail.blade.php
    resolutions.blade.php
    resolutions/
      create.blade.php
      votes.blade.php
    documents.blade.php
    documents/
      upload.blade.php
    audit-logs.blade.php
    large-transactions.blade.php
    alerts.blade.php

routes/
  role-workspaces.php           (existing wildcard + new resource routes)
```

---

## Notes on Data Integrity

- **Never** bypass `getOwnerUserId()` scoping. Every query in every RW controller must filter by the owner's user ID.
- **Soft deletes** are active on all key models — always use `->whereNull('deleted_at')` or rely on the global scope.
- **File uploads** go through `FileManager` — do not store file paths directly on models. Create a `FileManager` record and store the `file_id` foreign key.
- **Audit every mutation** — every store, update, statusChange, delete in a role workspace must call `FinancialAuditLog::create([...])` or `GovernanceAuditLog::create([...])` with the action, description, user_id, owner_user_id, and the old/new values JSON.
- **Role access control** — before any action, verify the authenticated user's `active_role` matches the `{role}` route parameter. The `RoleWorkspace` middleware must enforce this.

---

*End of design document. Use this as the master reference when building role workspace features.*
