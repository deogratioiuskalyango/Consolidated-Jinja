<?php

namespace Botble\Pesapal\Providers;

use Botble\Base\Facades\Html;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Facades\PaymentMethods;
use Botble\Pesapal\Forms\PesapalPaymentMethodForm;
use Botble\Pesapal\Services\Gateways\PesapalPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerPesapalMethod'], 1, 2);

        $this->app->booted(function (): void {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithPesapal'], 1, 2);
        });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 1);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['PESAPAL'] = PESAPAL_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 1, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == PESAPAL_PAYMENT_METHOD_NAME) {
                $value = 'PesaPal';
            }

            return $value;
        }, 1, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == PESAPAL_PAYMENT_METHOD_NAME) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )
                    ->toHtml();
            }

            return $value;
        }, 1, 2);

        add_filter(PAYMENT_FILTER_GET_SERVICE_CLASS, function ($data, $value) {
            if ($value == PESAPAL_PAYMENT_METHOD_NAME) {
                $data = PesapalPaymentService::class;
            }

            return $data;
        }, 1, 2);
    }

    public function addPaymentSettings(?string $settings): string
    {
        return $settings . PesapalPaymentMethodForm::create()->renderForm();
    }

    public function registerPesapalMethod(?string $html, array $data): string
    {
        PaymentMethods::method(PESAPAL_PAYMENT_METHOD_NAME, [
            'html' => view('plugins/pesapal::methods', $data)->render(),
        ]);

        return $html;
    }

    public function checkoutWithPesapal(array $data, Request $request): array
    {
        if ($data['type'] !== PESAPAL_PAYMENT_METHOD_NAME) {
            return $data;
        }

        // Ensure order_id is in the data
        if (empty($data['order_id']) && $request->has('order_id')) {
            $data['order_id'] = $request->input('order_id');
        }

        $service = $this->app->make(PesapalPaymentService::class);
        $result = $service->execute($data);

        if ($service->getErrorMessage()) {
            $data['error'] = true;
            $data['message'] = $service->getErrorMessage();
        } elseif ($result) {
            $data['checkoutUrl'] = $result;
        } else {
            // If no result and no error message, set a generic error
            $data['error'] = true;
            $data['message'] = $service->getErrorMessage() ?: trans('plugins/pesapal::pesapal.failed_to_generate_payment_url');
        }

        return $data;
    }
}

