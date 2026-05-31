<?php

namespace Botble\Hotel\Facades;

use Botble\Hotel\Supports\InvoiceHelper as BaseInvoiceHelper;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\Hotel\Models\Invoice store(\Botble\Hotel\Models\Booking $booking)
 * @method static \Barryvdh\DomPDF\PDF|\Dompdf\Dompdf makeInvoicePDF(\Botble\Hotel\Models\Invoice $invoice)
 * @method static string|null getInvoiceTemplate()
 * @method static array getVariables()
 * @method static \Botble\Hotel\Models\Invoice getDataForPreview()
 * @method static \Illuminate\Http\Response downloadInvoice($invoice)
 * @method static \Illuminate\Http\Response streamInvoice(\Botble\Hotel\Models\Invoice $invoice)
 * @method static \Barryvdh\DomPDF\PDF|\Dompdf\Dompdf makeInvoice(\Botble\Hotel\Models\Invoice $invoice)
 *
 * @see \Botble\Hotel\Supports\InvoiceHelper
 */
class InvoiceHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseInvoiceHelper::class;
    }
}
