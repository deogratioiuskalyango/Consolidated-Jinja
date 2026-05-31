<?php

namespace Botble\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'room_id' => 'required|exists:ht_rooms,id',
            'star' => 'required|int|min:1|max:5',
            'content' => 'required|string|min:4|max:10000',
        ];
    }
}
