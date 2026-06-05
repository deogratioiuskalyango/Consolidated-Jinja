<?php

namespace App\Console\Commands;

use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceRecurringSetting;
use App\Models\Tenant;
use App\Services\SmsMail\MailService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateInvoice extends Command
{
    protected $signature = 'generate:invoice';
    protected $description = 'Generate invoice by invoice recurring setting and notify tenants';

    public function handle()
    {
        $invoiceRecurringSettings = InvoiceRecurringSetting::query()
            ->with('items')
            ->where('status', ACTIVE)
            ->get();

        foreach ($invoiceRecurringSettings as $invoiceRecurring) {
            $tenant = Tenant::where('unit_id', $invoiceRecurring->property_unit_id)
                ->where('status', TENANT_STATUS_ACTIVE)
                ->first();

            if (is_null($tenant)) continue;

            $created = false;

            if ($invoiceRecurring->recurring_type == INVOICE_RECURRING_TYPE_MONTHLY) {
                $monthsCovered = max(1, (int) ($invoiceRecurring->months_covered ?? 1));

                if ($monthsCovered === 1) {
                    $exists = Invoice::query()
                        ->where('invoice_recurring_setting_id', $invoiceRecurring->id)
                        ->where('month', month(now()->format('n')))
                        ->whereYear('created_at', now()->format('Y'))
                        ->exists();
                } else {
                    $exists = Invoice::query()
                        ->where('invoice_recurring_setting_id', $invoiceRecurring->id)
                        ->where('created_at', '>', now()->subMonths($monthsCovered)->startOfDay())
                        ->exists();
                }

                if (!$exists) {
                    $invoice = $this->generateInvoice($invoiceRecurring);
                    $created = (bool) $invoice;
                }

            } elseif ($invoiceRecurring->recurring_type == INVOICE_RECURRING_TYPE_YEARLY) {
                $installments = max(1, (int) ($invoiceRecurring->installments_per_year ?? 1));

                if ($installments === 1) {
                    $exists = Invoice::query()
                        ->where('invoice_recurring_setting_id', $invoiceRecurring->id)
                        ->whereYear('created_at', now()->format('Y'))
                        ->exists();
                } else {
                    $invoicesThisYear = Invoice::query()
                        ->where('invoice_recurring_setting_id', $invoiceRecurring->id)
                        ->whereYear('created_at', now()->format('Y'))
                        ->count();

                    $monthsPerInstallment = 12 / $installments;
                    $expectedByNow = min($installments, (int) ceil(now()->month / $monthsPerInstallment));

                    $exists = $invoicesThisYear >= $expectedByNow;
                }

                if (!$exists) {
                    $invoice = $this->generateInvoice($invoiceRecurring);
                    $created = (bool) $invoice;
                }

            } elseif ($invoiceRecurring->recurring_type == INVOICE_RECURRING_TYPE_CUSTOM) {
                $exists = Invoice::query()
                    ->where('invoice_recurring_setting_id', $invoiceRecurring->id)
                    ->where('created_at', '>', now()->subDays($invoiceRecurring->cycle_day))
                    ->exists();

                if (!$exists) {
                    $invoice = $this->generateInvoice($invoiceRecurring);
                    $created = (bool) $invoice;
                }
            }

            if ($created && isset($invoice) && $invoice) {
                $this->notifyTenant($invoice);
                $this->line("Invoice {$invoice->invoice_no} created and tenant notified.");
            }
        }
    }

    public function generateInvoice(InvoiceRecurringSetting $invoiceRecurring): ?Invoice
    {
        DB::beginTransaction();
        try {
            $now = now();
            $monthsCovered = max(1, (int) ($invoiceRecurring->months_covered ?? 1));
            $installments  = max(1, (int) ($invoiceRecurring->installments_per_year ?? 1));

            $invoice = new Invoice();
            $invoice->name             = $invoiceRecurring->invoice_prefix;
            $invoice->tenant_id        = $invoiceRecurring->tenant_id;
            $invoice->owner_user_id    = $invoiceRecurring->owner_user_id;
            $invoice->invoice_recurring_setting_id = $invoiceRecurring->id;
            $invoice->property_id      = $invoiceRecurring->property_id;
            $invoice->property_unit_id = $invoiceRecurring->property_unit_id;
            $invoice->month            = $this->buildMonthLabel($invoiceRecurring, $now);
            $invoice->months_covered   = $monthsCovered;
            $invoice->due_date         = $now->copy()->addDays($invoiceRecurring->due_day_after)->endOfDay()->toDateString();
            $invoice->tax_amount       = 0;
            $invoice->late_fee         = 0;
            $invoice->status           = INVOICE_STATUS_PENDING;
            $invoice->save();

            $baseAmount = 0;
            foreach ($invoiceRecurring->items as $item) {
                $invoiceItem = new InvoiceItem();
                $invoiceItem->invoice_id      = $invoice->id;
                $invoiceItem->invoice_type_id = $item->invoice_type_id;
                $invoiceItem->amount          = $item->amount;
                $invoiceItem->description     = $item->description;
                $invoiceItem->save();
                $baseAmount += $item->amount;
            }

            // For monthly type: multiply by months covered
            // For yearly installments: base amount is per installment (owner configures items accordingly)
            if ($invoiceRecurring->recurring_type == INVOICE_RECURRING_TYPE_MONTHLY) {
                $invoice->amount = $baseAmount * $monthsCovered;
            } else {
                $invoice->amount = $baseAmount;
            }
            $invoice->save();

            DB::commit();
            return $invoice;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('GenerateInvoice error: ' . $e->getMessage());
            return null;
        }
    }

    private function buildMonthLabel(InvoiceRecurringSetting $invoiceRecurring, $now): string
    {
        $monthsCovered = max(1, (int) ($invoiceRecurring->months_covered ?? 1));
        $installments  = max(1, (int) ($invoiceRecurring->installments_per_year ?? 1));
        $currentMonthNum = (int) $now->format('n');
        $startName = month($currentMonthNum);

        if ($invoiceRecurring->recurring_type == INVOICE_RECURRING_TYPE_YEARLY && $installments > 1) {
            $invoicesThisYear = Invoice::where('invoice_recurring_setting_id', $invoiceRecurring->id)
                ->whereYear('created_at', $now->format('Y'))
                ->count();
            return $startName . ' (Installment ' . ($invoicesThisYear + 1) . '/' . $installments . ')';
        }

        if ($monthsCovered === 12) {
            return 'Full Year ' . $now->format('Y');
        }

        if ($monthsCovered > 1) {
            $endMonthNum = (($currentMonthNum + $monthsCovered - 2) % 12) + 1;
            return $startName . ' - ' . month($endMonthNum);
        }

        return $startName;
    }

    private function notifyTenant(Invoice $invoice): void
    {
        try {
            if (getOption('send_email_status', 0) != ACTIVE) return;

            $invoice->load('tenant.user');
            $tenantEmail = optional(optional($invoice->tenant)->user)->email;
            if (!$tenantEmail) return;

            $mailService = new MailService();
            $ownerUserId = $invoice->owner_user_id;

            $template = EmailTemplate::where('owner_user_id', $ownerUserId)
                ->where('category', EMAIL_TEMPLATE_INVOICE)
                ->where('status', ACTIVE)
                ->first();

            if ($template) {
                $placeholders = [
                    '{{amount}}'     => currencyPrice($invoice->amount),
                    '{{due_date}}'   => $invoice->due_date,
                    '{{month}}'      => $invoice->month,
                    '{{invoice_no}}' => $invoice->invoice_no,
                    '{{app_name}}'   => getOption('app_name', config('app.name')),
                ];
                $body    = getEmailTemplate($template->body, $placeholders);
                $subject = str_replace(array_keys($placeholders), array_values($placeholders), $template->subject);
                $mailService->sendCustomizeMail([$tenantEmail], $subject, $body);
            } else {
                $subject = __('New Invoice') . ': ' . $invoice->invoice_no . ' — ' . __('Due') . ' ' . $invoice->due_date;
                $mailService->sendInvoiceMail(
                    [$tenantEmail],
                    $subject,
                    __('A new invoice has been generated for you. Please log in to your tenant portal to view and pay.'),
                    $ownerUserId,
                    __('New Invoice'),
                    $invoice->amount,
                    $invoice->due_date,
                    $invoice->month,
                    $invoice->invoice_no,
                    __('Pending')
                );
            }
        } catch (Exception $e) {
            Log::error('Invoice notification error: ' . $e->getMessage());
        }
    }
}
