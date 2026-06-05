<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWhatsAppRentReminders extends Command
{
    protected $signature   = 'whatsapp:rent-reminders';
    protected $description = 'Send WhatsApp rent-due reminders to tenants with pending invoices';

    public function handle(): int
    {
        $wa = app(WhatsAppService::class);

        // Only run when WhatsApp is enabled
        if (!config('services.openwa.enabled')) {
            $this->info('WhatsApp disabled — skipping rent reminders.');
            return 0;
        }

        $reminderDays = [7, 3, 1]; // days before due date to send a reminder

        $invoices = Invoice::query()
            ->where('status', INVOICE_STATUS_PENDING)
            ->with(['tenant.user'])
            ->get();

        $sent = 0;

        foreach ($invoices as $invoice) {
            try {
                $dueDate  = Carbon::parse($invoice->due_date);
                $daysLeft = (int) now()->startOfDay()->diffInDays($dueDate->startOfDay(), false);

                if (!in_array($daysLeft, $reminderDays) || $daysLeft < 0) {
                    continue;
                }

                $phone = $invoice->tenant?->user?->whatsapp_number ?? null;
                if (!$phone) {
                    continue;
                }

                $currency = getOption('currency_symbol', '');
                $msg  = "💸 *Rent Reminder*\n\n";
                $msg .= "Invoice *#{$invoice->invoice_no}* is due in *{$daysLeft} day(s)* on {$invoice->due_date}.\n";
                $msg .= "Amount: {$currency}" . number_format($invoice->amount, 2) . "\n";
                $msg .= "Month: {$invoice->month}\n\n";
                $msg .= "Please ensure timely payment to avoid late fees.";

                if ($wa->send($phone, $msg)) {
                    $sent++;
                }
            } catch (\Throwable $e) {
                Log::warning('WhatsApp rent reminder error for invoice ' . ($invoice->invoice_no ?? '?') . ': ' . $e->getMessage());
            }
        }

        $this->info("WhatsApp rent reminders sent: {$sent}");
        Log::info("WhatsApp rent reminders sent: {$sent}");

        return 0;
    }
}
