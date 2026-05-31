<?php

namespace Botble\YoutubeFeed;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\YoutubeFeed\Supports\YoutubeFeedSettings;

class Plugin extends PluginOperationAbstract
{
    public static function activate(): void
    {
        foreach (YoutubeFeedSettings::defaults() as $key => $value) {
            $full = YoutubeFeedSettings::key($key);
            if (setting($full) === null) {
                setting()->set($full, $value);
            }
        }

        setting()->save();
    }
}
