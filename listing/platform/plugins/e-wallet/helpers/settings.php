<?php

use Botble\Payment\Enums\PaymentMethodEnum;

if (! function_exists('get_wallet_setting')) {
    function get_wallet_setting(string $key, mixed $default = null): mixed
    {
        return setting('e_wallet_' . $key, $default);
    }
}

if (! function_exists('set_wallet_setting')) {
    function set_wallet_setting(string $key, mixed $value): void
    {
        setting()->set('e_wallet_' . $key, $value)->save();
    }
}

if (! function_exists('get_allowed_topup_payment_methods')) {
    function get_allowed_topup_payment_methods(): array
    {
        $saved = get_wallet_setting('topup_payment_methods');

        if (empty($saved)) {
            $methods = [];
            foreach (PaymentMethodEnum::toArray() as $value) {
                if ($value === E_WALLET_PAYMENT_METHOD_NAME) {
                    continue;
                }

                if (get_payment_setting('status', $value)) {
                    $methods[] = $value;
                }
            }

            return $methods;
        }

        if (is_string($saved)) {
            $methods = json_decode($saved, true) ?: [];
        } else {
            $methods = (array) $saved;
        }

        return array_values(array_filter($methods, function ($method) {
            return $method !== E_WALLET_PAYMENT_METHOD_NAME && get_payment_setting('status', $method);
        }));
    }
}

if (! function_exists('is_topup_payment_method_allowed')) {
    function is_topup_payment_method_allowed(string $method): bool
    {
        return in_array($method, get_allowed_topup_payment_methods());
    }
}
