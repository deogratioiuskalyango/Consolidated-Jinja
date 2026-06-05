# Zaiproty v4.6 — JCP Property Management System

## Project Overview
Multi-role property management SaaS for **Jinja Consolidated Properties (JCP), Uganda**.
Roles: Admin, Owner, Team Member, Tenant. Primary payment gateway: **Pesapal** (live mode, UGX).

## Stack
- **Backend:** Laravel 9 / PHP 8.2 (XAMPP on Windows)
- **Database:** MySQL via XAMPP
- **Frontend:** Blade templates, Bootstrap 5, jQuery, DataTables, Dropzone, Swiper
- **Storage:** Laravel `public` disk — files in `storage/app/public/`, served via `public/storage/`
- **Auth:** Laravel Passport (API) + session (web)
- **Payments:** Pesapal, PayPal, Stripe, Mollie, Razorpay, Mercado Pago, Authorize.net, bank/cash
- **E-signatures:** DocuSign

## Local Dev URLs
- App: `https://localhost/kintu/`
- Admin: `.../admin/dashboard`
- Owner: `.../owner/dashboard`
- Tenant: `.../tenant/dashboard`

## Key Paths
```
app/
  Console/Commands/GenerateInvoice.php   # Daily invoice auto-generation (Windows Task Scheduler)
  Http/Controllers/
    ProfileController.php
    PaymentController.php
    Owner/PropertyController.php
  Models/
    User.php / Owner.php / Tenant.php / TenantDetails.php
    Property.php / PropertyUnit.php / PropertyImage.php
    Invoice.php / InvoiceRecurringSetting.php / InvoiceRecurringSettingItem.php
    FileManager.php                      # All file uploads
    Gateway.php / GatewayCurrency.php
  Services/Payment/
    PesapalService.php                   # Primary gateway — mode: GATEWAY_MODE_LIVE=1
    BasePaymentService.php
  Helper/
    const.php                            # All constants (GATEWAY_MODE_LIVE=1, GATEWAY_MODE_SANDBOX=2)
    const_array.php                      # allowMimes(), allowExtensions(), month() helper
    helper.php                           # assetUrl(), currencyPrice(), getLayout()

resources/views/
  common/profile/my-profile.blade.php   # Shared profile page (all roles)
  owner/
    invoice/index.blade.php             # One-off invoices
    invoice/recurring.blade.php         # Recurring invoice settings
    property/show.blade.php
  tenant/invoices/pay.blade.php         # Payment page

public/assets/js/
  custom.js                             # Global JS — Swiper guarded with typeof check
  custom/invoice-recurring.js           # Recurring invoice UI logic
```

## Invoice System
- **One-off invoices:** `invoices` table — `months_covered` (1/2/3/6/12), amount × months_covered
- **Recurring settings:** `invoice_recurring_settings` — `recurring_type` (1=monthly, 2=yearly, 3=custom days), `months_covered`, `installments_per_year`
- **Auto-generation:** `php artisan generate:invoice` runs daily via `run-scheduler.bat`
- Month labels auto-format: "January", "January - March", "Full Year 2026", "May (Installment 1/4)"

## Constants (app/Helper/const.php)
```php
GATEWAY_MODE_LIVE = 1
GATEWAY_MODE_SANDBOX = 2
INVOICE_RECURRING_TYPE_MONTHLY = 1
INVOICE_RECURRING_TYPE_YEARLY = 2
INVOICE_RECURRING_TYPE_CUSTOM = 3
USER_ROLE_ADMIN / USER_ROLE_OWNER / USER_ROLE_TEAM_MEMBER / USER_ROLE_TENANT
```

## File Uploads
- `FileManager::upload('ModelName', $file)` — saves to `storage/app/public/{folder}/{file}`
- URL: `asset('storage/' . $folder . '/' . $file)` or `assetUrl($folder . '/' . $file)`
- `FileUrl` accessor on FileManager returns correct URL
- Extended MIME types: webp, heic, bmp, tiff, avif, jfif accepted

## Common Patterns
```php
// Always use firstOrCreate for Owner / TenantDetails — never assume they exist
$owner = Owner::firstOrCreate(['user_id' => auth()->id()], ['user_id' => auth()->id()]);
$details = TenantDetails::firstOrCreate(['tenant_id' => $tenant->id], ['tenant_id' => $tenant->id]);

// Error redirect — always include withInput()
return redirect()->back()->withInput()->with('error', $e->getMessage());

// Image URL in views — use accessor, not raw fields
{{ $propertyImage->image_url }}          // string, never null
{{ $user->image }}                        // via getImageAttribute() → FileUrl
```

## Artisan Commands
```bash
php artisan generate:invoice    # Manual trigger of daily invoice generation
php artisan migrate
php artisan cache:clear && php artisan view:clear && php artisan config:clear
php artisan tinker
```

## SSL / Local HTTPS
- mkcert certificate installed: `C:\xampp\apache\conf\ssl.crt\localhost+2.pem`
- Valid until August 2028 for localhost, 127.0.0.1, ::1
- httpd-ssl.conf updated to use mkcert cert

## Known Issues / Watch Out For
- `GATEWAY_MODE_LIVE = 1` (integer) — never compare mode against string `'live'`
- `public/storage` must be a symlink or copy of `storage/app/public` — run `php artisan storage:link` if images 404
- Swiper.js only loaded on public layout (`layouts/app.blade.php`) — guarded in custom.js
- `$('#payBtn').click()` causes infinite loop — always use `form[0].submit()` instead
