<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Support\Http\Requests\Request;

class UpdateInvoiceSettingsRequest extends Request
{
    public function rules(): array
    {
        return [
            'hotel_company_name_for_invoicing' => 'nullable|string|max:255',
            'hotel_company_address_for_invoicing' => 'nullable|string|max:255',
            'hotel_company_email_for_invoicing' => 'nullable|max:60|min:6|email',
            'hotel_company_phone_for_invoicing' => 'nullable|' . BaseHelper::getPhoneValidationRule(),
            'hotel_company_logo_for_invoicing' => 'nullable|string|max:255',
            'hotel_using_custom_font_for_invoice' => 'nullable|bool',
            'hotel_invoice_support_arabic_language' => 'nullable|bool',
            'hotel_invoice_code_prefix' => 'nullable|string|max:255',
            'hotel_invoice_font_family' => 'nullable|string|max:255',
            'hotel_enable_invoice_stamp' => 'nullable|bool',
        ];
    }
}
