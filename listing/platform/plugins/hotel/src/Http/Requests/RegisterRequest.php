<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Captcha\Facades\Captcha;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Support\Http\Requests\Request;

class RegisterRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:60|min:2',
            'last_name' => 'required|string|max:60|min:2',
            'email' => 'required|max:120|min:6|email|unique:ht_customers',
            'password' => 'required|string|min:6|confirmed',
            'agree_terms_and_policy' => 'sometimes|accepted:1',
        ];

        if (is_plugin_active('captcha')) {
            if (HotelHelper::getSetting('enable_recaptcha_in_register_page', 0)) {
                $rules += Captcha::rules();
            }

            if (HotelHelper::getSetting('enable_math_captcha_in_register_page', 0)) {
                $rules += Captcha::mathCaptchaRules();
            }
        }

        return apply_filters('hotel_customer_registration_form_validation_rules', $rules);
    }

    public function attributes(): array
    {
        return apply_filters('hotel_customer_registration_form_validation_attributes', [
            'first_name' => __('First name'),
            'last_name' => __('Last name'),
            'email' => __('Email'),
            'password' => __('Password'),
            'agree_terms_and_policy' => __('Term and Policy'),
        ] + (is_plugin_active('captcha') ? Captcha::attributes() : []));
    }

    public function messages(): array
    {
        return apply_filters('hotel_customer_registration_form_validation_messages', []);
    }
}
