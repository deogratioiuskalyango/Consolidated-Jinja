<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Hotel\Enums\ServicePriceTypeEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ServiceRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'status' => Rule::in(BaseStatusEnum::values()),
            'price' => 'required|numeric|min:0',
            'price_type' => Rule::in(ServicePriceTypeEnum::values()),
        ];
    }
}
