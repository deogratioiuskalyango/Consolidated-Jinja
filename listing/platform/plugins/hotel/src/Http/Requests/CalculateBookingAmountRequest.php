<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CalculateBookingAmountRequest extends Request
{
    public function rules(): array
    {
        return [
            'room_id' => 'required|exists:ht_rooms,id',
            'start_date' => 'date|required:date_format:d-m-Y',
            'end_date' => 'date|required:date_format:d-m-Y',
            'services' => 'nullable|array',
        ];
    }
}
