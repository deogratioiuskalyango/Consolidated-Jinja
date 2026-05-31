<?php

namespace Botble\YoutubeFeed\Forms\Settings;

use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\TextField;
use Botble\YoutubeFeed\Http\Requests\YoutubeFeedSettingRequest;
use Botble\YoutubeFeed\Supports\YoutubeFeedSettings;
use Botble\Setting\Forms\SettingForm;

class YoutubeFeedSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $k = fn (string $n) => YoutubeFeedSettings::key($n);
        $hasKey = YoutubeFeedSettings::getApiKey() !== null;

        $this
            ->setSectionTitle(trans('plugins/youtube-feed::youtube-feed.settings.title'))
            ->setSectionDescription(trans('plugins/youtube-feed::youtube-feed.settings.description'))
            ->setValidatorClass(YoutubeFeedSettingRequest::class)
            ->add($k('api_key'), TextField::class, [
                'label' => trans('plugins/youtube-feed::youtube-feed.settings.api_key'),
                'value' => '',
                'attr' => [
                    'type' => 'password',
                    'placeholder' => $hasKey
                        ? trans('plugins/youtube-feed::youtube-feed.settings.api_key_placeholder_saved')
                        : '',
                    'autocomplete' => 'new-password',
                ],
                'help_block' => [
                    'text' => trans('plugins/youtube-feed::youtube-feed.settings.api_key_help'),
                ],
            ])
            ->add($k('channel_id'), TextField::class, [
                'label' => trans('plugins/youtube-feed::youtube-feed.settings.channel_id'),
                'value' => setting($k('channel_id')),
                'help_block' => [
                    'text' => trans('plugins/youtube-feed::youtube-feed.settings.channel_id_help'),
                ],
            ])
            ->add($k('channel_handle'), TextField::class, [
                'label' => trans('plugins/youtube-feed::youtube-feed.settings.channel_handle'),
                'value' => setting($k('channel_handle')),
                'help_block' => [
                    'text' => trans('plugins/youtube-feed::youtube-feed.settings.channel_handle_help'),
                ],
            ])
            ->add($k('max_results'), NumberField::class, [
                'label' => trans('plugins/youtube-feed::youtube-feed.settings.max_results'),
                'value' => (int) setting($k('max_results'), YoutubeFeedSettings::defaults()['max_results']),
                'attr' => [
                    'min' => 1,
                    'max' => 50,
                ],
            ]);
    }
}
