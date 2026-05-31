<?php

namespace Botble\YoutubeFeed\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Breadcrumb;
use Botble\Setting\Http\Controllers\SettingController;
use Botble\YoutubeFeed\Forms\Settings\YoutubeFeedSettingForm;
use Botble\YoutubeFeed\Http\Requests\YoutubeFeedSettingRequest;
use Botble\YoutubeFeed\Supports\YoutubeFeedSettings;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class YoutubeFeedSettingsController extends SettingController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/youtube-feed::youtube-feed.name'), route('youtube-feed.settings'));
    }

    public function edit()
    {
        $this->pageTitle(trans('plugins/youtube-feed::youtube-feed.settings.title'));

        return YoutubeFeedSettingForm::create()->renderForm();
    }

    public function update(YoutubeFeedSettingRequest $request): BaseHttpResponse
    {
        $data = $request->validated();

        $apiInput = $data[YoutubeFeedSettings::key('api_key')] ?? '';
        unset($data[YoutubeFeedSettings::key('api_key')]);

        if (is_string($apiInput) && $apiInput !== '') {
            $data[YoutubeFeedSettings::key('api_key_encrypted')] = Crypt::encryptString($apiInput);
        }

        $maxKey = YoutubeFeedSettings::key('max_results');
        if (array_key_exists($maxKey, $data)) {
            $data[$maxKey] = (string) max(1, min(50, (int) $data[$maxKey]));
        }

        $data = Arr::where(
            $data,
            fn ($value, $key) => is_string($key) && Str::startsWith($key, YoutubeFeedSettings::PREFIX)
        );

        return $this->performUpdate($data);
    }
}
