<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'kyc_config_id' => 'required',
            'front'         => 'required|file|mimes:png,jpg,jpeg,pdf|max:10240',
            'back'          => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:10240',
        ];
    }

    public function messages()
    {
        return [
            'front.required' => __('The front side document is required.'),
            'front.mimes'    => __('Front side must be a JPG, PNG, or PDF.'),
            'front.max'      => __('Front side file may not be greater than 10 MB.'),
            'back.mimes'     => __('Back side must be a JPG, PNG, or PDF.'),
            'back.max'       => __('Back side file may not be greater than 10 MB.'),
        ];
    }
}
