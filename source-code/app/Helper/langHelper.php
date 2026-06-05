<?php

if (!function_exists('__')) {
    function __($key = null, $replace = [], $locale = null)
    {
        if (session()->get('local') != null) {
            $path = resource_path() . "/lang/" . session()->get('local') . ".json";
            if (!file_exists($path)) {
                // fopen(resource_path() . "/lang/" . session()->get('local') . ".json", "w");
                file_put_contents(resource_path() . "/lang/" . session()->get('local') . ".json", '{}');
            }
            $website = json_decode(file_get_contents(resource_path("/lang/" . session()->get('local') . ".json")), true);
            if (!is_array($website)) {
                // File was empty or contained invalid JSON (e.g. from a partial write) — reset it
                $website = [];
                file_put_contents(resource_path("/lang/" . session()->get('local') . ".json"), '{}');
            }

            $key = preg_replace('/\s+/S', " ", $key);

            if (array_key_exists($key, $website)) {
                if (session()->get('local') == null) {
                    return $key;
                }
                return $website[$key];
            }

            $website[$key] = $key;
            $encoded = json_encode($website, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            if ($encoded !== false) {
                file_put_contents(resource_path("/lang/" . session()->get('local') . ".json"), $encoded, LOCK_EX);
            }
            if (session()->get('local') == null) {
                return $key;
            }
        }
        if (is_null($key)) {
            return $key;
        }
        return trans($key, $replace, $locale);
    }
}
