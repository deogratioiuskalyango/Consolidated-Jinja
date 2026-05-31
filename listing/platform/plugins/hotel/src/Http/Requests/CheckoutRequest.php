<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Captcha\Facades\Captcha;
use Botble\Support\Http\Requests\Request;

class CheckoutRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'room_id' => 'required|exists:ht_rooms,id',
            'start_date' => 'date|required:date_format:d-m-Y',
            'end_date' => 'date|required:date_format:d-m-Y',
            'first_name' => 'required|string|max:120',
            'last_name' => 'required|string|max:120',
            'email' => 'required|email|max:120',
            'phone' => 'required|' . BaseHelper::getPhoneValidationRule(),
            'number_of_guests' => 'nullable|integer|min:1',
            'zip' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:400',
            'arrival_time' => 'nullable|string',
            'city' => 'nullable|string|max:60',
            'state' => 'nullable|string|max:60',
            'country' => 'nullable|string|max:60',
            'requests' => 'nullable|string|max:10000',
            'services' => 'nullable|array',
            'terms_conditions' => 'accepted:1',
            'register_customer' => 'nullable',
            'password' => 'nullable|required_if:register_customer,1|min:6',
            'password_confirm' => 'nullable|required_if:register_customer,1|same:password',
        ];

        if (is_plugin_active('captcha')) {
            $rules += Captcha::rules();
        }

        return $rules;
    }

    public function messages(): array
    {
        return is_plugin_active('captcha') ? Captcha::attributes() : [];
    }
}
