<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Support\Http\Requests\Request;

class LoginRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'email' => 'required|email|max:255|min:6',
            'password' => 'required|string|max:255|min:6',
        ];

        return apply_filters('hotel_customer_login_form_validation_rules', $rules);
    }

    public function attributes(): array
    {
        return apply_filters('hotel_customer_login_form_validation_attributes', [
            'email' => __('Email'),
            'password' => __('Password'),
        ]);
    }

    public function messages(): array
    {
        return apply_filters('hotel_customer_login_form_validation_messages', []);
    }
}
