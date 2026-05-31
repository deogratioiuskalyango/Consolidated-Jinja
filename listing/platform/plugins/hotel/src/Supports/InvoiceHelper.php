<?php

namespace Botble\Hotel\Supports;

use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PDFHelper;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\TwigCompiler;
use Botble\Hotel\Enums\InvoiceStatusEnum;
use Botble\Hotel\Models\Booking;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\Invoice;
use Botble\Hotel\Models\InvoiceItem;
use Botble\Media\Facades\RvMedia;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Image\Cache;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Throwable;

class InvoiceHelper
{
    public function store(Booking $booking): Invoice
    {
        if ($booking->invoice()->exists()) {
            return $booking->invoice;
        }

        $bookingInformation = $booking->address;

        $invoiceData = [
            'reference_id' => $booking->getKey(),
            'reference_type' => Booking::class,
            'customer_id' => $booking->customer_id,
            'customer_name' => $bookingInformation->first_name . $bookingInformation->last_name,
            'customer_email' => $bookingInformation->email,
            'customer_phone' => $bookingInformation->phone,
            'customer_address' => $bookingInformation->full_address,
            'payment_id' => null,
            'status' => InvoiceStatusEnum::COMPLETED,
            'paid_at' => Carbon::now(),
            'sub_total' => $booking->sub_total,
            'tax_amount' => $booking->tax_amount,
            'discount_amount' => $booking->coupon_amount ?: 0,
            'amount' => $booking->amount,
            'description' => $booking->requests,
        ];

        if (is_plugin_active('payment')) {
            switch ($booking->payment->status) {
                case PaymentStatusEnum::COMPLETED:
                    $invoiceData['status'] = InvoiceStatusEnum::COMPLETED;

                    break;
                case PaymentStatusEnum::PENDING:
                    $invoiceData['status'] = InvoiceStatusEnum::PENDING;

                    break;
                case PaymentStatusEnum::FAILED:
                case PaymentStatusEnum::FRAUD:
                case PaymentStatusEnum::REFUNDED:
                case PaymentStatusEnum::REFUNDING:
                    $invoiceData['status'] = InvoiceStatusEnum::CANCELED;

                    break;
            }

            $invoiceData = array_merge($invoiceData, [
                'payment_id' => $booking->payment->id,
                'paid_at' => $booking->payment->status == PaymentStatusEnum::COMPLETED ? Carbon::now() : null,
            ]);
        }

        $invoice = new Invoice($invoiceData);
        $invoice->created_at = $booking->created_at;

        $invoice->save();

        if ($room = $booking->room) {
            $invoice->items()->create([
                'name' => $room->room_name,
                'description' => null,
                'qty' => 1,
                'sub_total' => $room->price,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'amount' => $room->price,
            ]);
        }

        if ($services = $booking->services) {
            foreach ($services as $service) {
                $invoice->items()->create([
                    'name' => sprintf('%s (extra service)', $service->name),
                    'description' => null,
                    'qty' => 1,
                    'sub_total' => $service->price,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'amount' => $service->price,
                ]);
            }
        }

        return $invoice;
    }

    public function makeInvoicePDF(Invoice $invoice): PDFHelper|Dompdf
    {
        $fontsPath = storage_path('fonts');

        if (! File::isDirectory($fontsPath)) {
            File::makeDirectory($fontsPath);
        }

        $content = $this->getInvoiceTemplate();

        if ($content) {
            $twigCompiler = (new TwigCompiler())->addExtension(new TwigExtension());
            $content = $twigCompiler->compile($content, $this->getDataForInvoiceTemplate($invoice));

            if ((int)setting('invoice_support_arabic_language', 0) == 1) {
                $arabic = new Arabic();
                $p = $arabic->arIdentify($content);

                for ($i = count($p) - 1; $i >= 0; $i -= 2) {
                    try {
                        $utf8ar = $arabic->utf8Glyphs(substr($content, $p[$i - 1], $p[$i] - $p[$i - 1]));
                        $content = substr_replace($content, $utf8ar, $p[$i - 1], $p[$i] - $p[$i - 1]);
                    } catch (Throwable) {
                        continue;
                    }
                }
            }
        }

        Cache::$error_message = null;

        return Pdf::setWarnings(false)
            ->setOption('chroot', [public_path(), base_path()])
            ->setOption('tempDir', storage_path('app'))
            ->setOption('logOutputFile', storage_path('logs/pdf.log'))
            ->setOption('isRemoteEnabled', true)
            ->loadHTML($content, 'UTF-8')
            ->setPaper('a4');
    }

    public function getInvoiceTemplate(): ?string
    {
        $defaultPath = platform_path('plugins/hotel/resources/templates/invoice.tpl');
        $storagePath = storage_path('app/templates/invoice.tpl');

        if ($storagePath && File::exists($storagePath)) {
            $templateHtml = BaseHelper::getFileData($storagePath, false);
        } else {
            $templateHtml = File::exists($defaultPath) ? BaseHelper::getFileData($defaultPath, false) : '';
        }

        return $templateHtml;
    }

    public function getVariables(): array
    {
        return [
            'invoice.*' => __('Invoice information from database, ex: invoice.code, invoice.amount, ...'),
            'account.*' => __('Bill payment user account information, ex: account.name, account.email, ...'),
            'payment_method' => __('Payment method'),
            'payment_status' => __('Payment status'),
            'payment_description' => __('Payment description'),
            'settings.using_custom_font_for_invoice' => __('Check site is using custom font for invoice or not'),
            'settings.font_family' => __('The font family of invoice template'),
            'settings.enable_invoice_stamp' => __('Check have enabled the invoice stamp'),
            'settings.company_name_for_invoicing' => __('The company name of invoice'),
            'settings.company_address_for_invoicing' => __('The company address of invoice'),
            'settings.company_email_for_invoicing' => __('The company email of invoice'),
            'settings.company_phone_for_invoicing' => __('The company phone number of invoice'),
        ];
    }

    protected function getDataForInvoiceTemplate(Invoice $invoice): array
    {
        return [
            'invoice' => $invoice,
            'logo_full_path' => RvMedia::getImageUrl(setting('hotel_company_logo_for_invoicing') ?: theme_option('logo')),
            'site_title' => theme_option('site_title'),
            'customer' => $invoice->customer,
            'payment_method' => $invoice->payment->payment_channel->label(),
            'payment_status' => $invoice->payment->status->label(),
            'payment_description' => ($invoice->payment->payment_channel == PaymentMethodEnum::BANK_TRANSFER && $invoice->payment->status == PaymentStatusEnum::PENDING)
                ? BaseHelper::clean(get_payment_setting('description', $invoice->payment->payment_channel))
                : null,
            'settings' => [
                'using_custom_font_for_invoice' => setting('hotel_using_custom_font_for_invoice', false),
                'font_family' => setting('hotel_using_custom_font_for_invoice', 0) == 1
                    ? setting('hotel_invoice_font_family', '')
                    : 'DejaVu Sans',
                'enable_invoice_stamp' => setting('hotel_enable_invoice_stamp', true),
                'company_name_for_invoicing' => setting('hotel_company_name_for_invoicing') ?: theme_option('site_title'),
                'company_address_for_invoicing' => setting('hotel_company_address_for_invoicing'),
                'company_email_for_invoicing' => setting('hotel_company_email_for_invoicing'),
                'company_phone_for_invoicing' => setting('hotel_company_phone_for_invoicing'),
                'hotel_invoice_footer' => apply_filters('hotel_invoice_footer', null, $invoice),
            ],
        ];
    }

    public function getDataForPreview(): Invoice
    {
        $invoice = new Invoice([
            'code' => 'INV-1',
            'status' => InvoiceStatusEnum::PENDING,
        ]);

        $items = [];

        foreach (range(1, 5) as $i) {
            $amount = rand(10, 1000);
            $qty = rand(1, 10);

            $items[] = new InvoiceItem([
                'name' => "Item $i",
                'description' => "Description of item $i",
                'amount' => $amount,
                'qty' => $qty,
            ]);

            $invoice->amount += $amount * $qty;
            $invoice->sub_total = $invoice->amount;
        }

        $payment = new Payment([
            'payment_channel' => PaymentMethodEnum::BANK_TRANSFER,
            'status' => PaymentStatusEnum::PENDING,
        ]);

        $account = new Customer([
            'company' => 'My Company',
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'example@mail.com',
            'phone' => '0123456789',
        ]);

        $invoice->setRelation('payment', $payment);
        $invoice->setRelation('items', $items);
        $invoice->setRelation('account', $account);

        return $invoice;
    }

    public function downloadInvoice($invoice): Response
    {
        return $this->makeInvoice($invoice)->download('invoice-' . $invoice->code . '.pdf');
    }

    public function streamInvoice(Invoice $invoice): Response
    {
        return $this->makeInvoice($invoice)->stream();
    }

    public function makeInvoice(Invoice $invoice): PDFHelper|Dompdf
    {
        $fontsPath = storage_path('fonts');

        if (! File::isDirectory($fontsPath)) {
            File::makeDirectory($fontsPath);
        }

        $content = null;
        $templateHtml = $this->getInvoiceTemplate();

        if ($templateHtml) {
            $twigCompiler = (new TwigCompiler())->addExtension(new TwigExtension());
            $content = $twigCompiler->compile($templateHtml, $this->getDataForInvoiceTemplate($invoice));

            if (setting('hotel_invoice_support_arabic_language', 0) == 1) {
                $arabic = new Arabic();
                $p = $arabic->arIdentify($content);

                for ($i = count($p) - 1; $i >= 0; $i -= 2) {
                    try {
                        $utf8ar = $arabic->utf8Glyphs(substr($content, $p[$i - 1], $p[$i] - $p[$i - 1]));
                        $content = substr_replace($content, $utf8ar, $p[$i - 1], $p[$i] - $p[$i - 1]);
                    } catch (Throwable) {
                        continue;
                    }
                }
            }
        }

        Cache::$error_message = null;

        return Pdf::setWarnings(false)
            ->setOption('chroot', [public_path(), base_path()])
            ->setOption('tempDir', storage_path('app'))
            ->setOption('logOutputFile', storage_path('logs/pdf.log'))
            ->setOption('isRemoteEnabled', true)
            ->loadHTML($content, 'UTF-8')
            ->setPaper('a4');
    }
}
