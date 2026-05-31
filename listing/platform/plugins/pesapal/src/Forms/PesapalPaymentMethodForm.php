<?php

namespace Botble\Pesapal\Forms;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Payment\Concerns\Forms\HasAvailableCountriesField;
use Botble\Payment\Forms\PaymentMethodForm;

class PesapalPaymentMethodForm extends PaymentMethodForm
{
    use HasAvailableCountriesField;

    public function setup(): void
    {
        parent::setup();

        $this
            ->paymentId(PESAPAL_PAYMENT_METHOD_NAME)
            ->paymentName('PesaPal')
            ->paymentDescription(trans('plugins/pesapal::pesapal.payment_description', ['name' => 'PesaPal']))
            ->paymentLogo(url('vendor/core/plugins/pesapal/images/pesapal.png'))
            ->paymentFeeField(PESAPAL_PAYMENT_METHOD_NAME)
            ->paymentUrl('https://www.pesapal.com')
            ->paymentInstructions(view('plugins/pesapal::instructions')->render())
            ->add(
                sprintf('payment_%s_consumer_key', PESAPAL_PAYMENT_METHOD_NAME),
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/pesapal::pesapal.consumer_key'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('consumer_key', PESAPAL_PAYMENT_METHOD_NAME))
                    ->helperText(trans('plugins/pesapal::pesapal.consumer_key_helper'))
            )
            ->add(
                sprintf('payment_%s_consumer_secret', PESAPAL_PAYMENT_METHOD_NAME),
                'password',
                TextFieldOption::make()
                    ->label(trans('plugins/pesapal::pesapal.consumer_secret'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('consumer_secret', PESAPAL_PAYMENT_METHOD_NAME))
                    ->helperText(trans('plugins/pesapal::pesapal.consumer_secret_helper'))
            )
            ->add(
                sprintf('payment_%s_mode', PESAPAL_PAYMENT_METHOD_NAME),
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/pesapal::pesapal.mode'))
                    ->choices([
                        'sandbox' => trans('plugins/pesapal::pesapal.sandbox'),
                        'live' => trans('plugins/pesapal::pesapal.live'),
                    ])
                    ->selected(get_payment_setting('mode', PESAPAL_PAYMENT_METHOD_NAME, 'sandbox'))
                    ->helperText(trans('plugins/pesapal::pesapal.mode_helper'))
            )
            ->add(
                sprintf('payment_%s_ipn_url', PESAPAL_PAYMENT_METHOD_NAME),
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/pesapal::pesapal.ipn_url'))
                    ->value(url('pesapal/payment/ipn'))
                    ->addAttribute('readonly', true)
                    ->helperText(trans('plugins/pesapal::pesapal.ipn_url_helper'))
            )
            ->addAvailableCountriesField(PESAPAL_PAYMENT_METHOD_NAME);
    }
}

