<?php

namespace Botble\YoutubeFeed\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Botble\YoutubeFeed\Supports\YoutubeFeedSettings;

class YoutubeFeedSettingRequest extends Request
{
    public function rules(): array
    {
        $k = fn (string $n) => YoutubeFeedSettings::key($n);

        return [
            $k('api_key') => ['nullable', 'string', 'max:512'],
            $k('channel_id') => ['nullable', 'string', 'max:128'],
            $k('channel_handle') => ['nullable', 'string', 'max:128'],
            $k('max_results') => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}
