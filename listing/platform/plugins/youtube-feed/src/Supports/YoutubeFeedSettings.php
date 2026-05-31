<?php

namespace Botble\YoutubeFeed\Supports;

use Illuminate\Support\Facades\Crypt;

class YoutubeFeedSettings
{
    public const PREFIX = 'youtube_feed_';

    public static function defaults(): array
    {
        return [
            'max_results' => 12,
            'channel_id' => '',
            'channel_handle' => '',
        ];
    }

    public static function key(string $name): string
    {
        return self::PREFIX . $name;
    }

    public static function get(string $name, mixed $default = null): mixed
    {
        $defaults = self::defaults();
        $fallback = array_key_exists($name, $defaults) ? $defaults[$name] : $default;

        return setting(self::key($name), $fallback);
    }

    public static function getInt(string $name): int
    {
        return (int) self::get($name);
    }

    /**
     * Decrypted Data API key, or null if unset.
     */
    public static function getApiKey(): ?string
    {
        $raw = setting(self::key('api_key_encrypted'));
        if (! is_string($raw) || $raw === '') {
            return null;
        }

        try {
            return Crypt::decryptString($raw);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function maxResultsForRequest(): int
    {
        $n = self::getInt('max_results');

        return min(50, max(1, $n > 0 ? $n : 12));
    }
}
