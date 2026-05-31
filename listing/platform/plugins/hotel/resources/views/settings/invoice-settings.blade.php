@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    {!! Form::open(['url' => route('hotel.invoice.settings.post'), 'class' => 'main-setting-form']) !!}
    <div class='max-width-1200'>
        <x-core-setting::section
            :title="trans('plugins/hotel::settings.invoice_settings')"
            :description="trans('plugins/hotel::settings.invoice_settings_description')"
        >
            <x-core-setting::text-input
                name="hotel_company_name_for_invoicing"
                :label="trans('plugins/hotel::settings.invoicing.company_name')"
                :value="setting('hotel_company_name_for_invoicing', theme_option('site_title'))"
            />

            <x-core-setting::text-input
                name="hotel_company_address_for_invoicing"
                :label="trans('plugins/hotel::settings.invoicing.company_address')"
                :value="setting('hotel_company_address_for_invoicing')"
            />

            <x-core-setting::text-input
                name="hotel_company_email_for_invoicing"
                :label="trans('plugins/hotel::settings.invoicing.company_email')"
                :value="setting('hotel_company_email_for_invoicing', get_admin_email()->first())"
            />

            <x-core-setting::text-input
                name="hotel_company_phone_for_invoicing"
                :label="trans('plugins/hotel::settings.invoicing.company_phone')"
                :value="setting('hotel_company_phone_for_invoicing')"
            />

            <x-core-setting::form-group>
                <label
                    class="text-title-field"
                    for="hotel_company_logo_for_invoicing"
                >{{ trans('plugins/hotel::settings.invoicing.company_logo') }}</label>
                {!! Form::mediaImage(
                    'hotel_company_logo_for_invoicing',
                    setting('hotel_company_logo_for_invoicing') ?: theme_option('logo'),
                    ['allow_thumb' => false],
                ) !!}
            </x-core-setting::form-group>

            <x-core-setting::on-off
                name="hotel_using_custom_font_for_invoice"
                :label="trans('plugins/hotel::settings.using_custom_font_for_invoice')"
                :value="setting('hotel_using_custom_font_for_invoice', false)"
            />

            <x-core-setting::form-group>
                <label
                    class="text-title-field"
                    for="hotel_invoice_font_family"
                >{{ trans('plugins/hotel::settings.invoice_font_family') }}</label>
                {!! Form::googleFonts('hotel_invoice_font_family', setting('hotel_invoice_font_family')) !!}
            </x-core-setting::form-group>

            <x-core-setting::on-off
                name="hotel_invoice_support_arabic_language"
                :label="trans('plugins/hotel::settings.invoice_support_arabic_language')"
                :value="setting('hotel_invoice_support_arabic_language', false)"
            />

            <x-core-setting::on-off
                name="hotel_enable_invoice_stamp"
                :label="trans('plugins/hotel::settings.enable_invoice_stamp')"
                :value="setting('hotel_enable_invoice_stamp', true)"
            />

            <x-core-setting::text-input
                name="hotel_invoice_code_prefix"
                :label="trans('plugins/hotel::settings.invoice_code_prefix')"
                :value="setting('hotel_invoice_code_prefix', 'INV-')"
            />
        </x-core-setting::section>

        <div
            class="flexbox-annotated-section"
            style="border: none"
        >
            <div class="flexbox-annotated-section-annotation">
                &nbsp;
            </div>
            <div class="flexbox-annotated-section-content">
                <button
                    class="btn btn-info"
                    type="submit"
                >{{ trans('plugins/hotel::currency.save_settings') }}</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
