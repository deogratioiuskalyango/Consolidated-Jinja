<?php

return [
    'name' => 'YouTube Feed',
    'settings' => [
        'title' => 'YouTube Feed settings',
        'description' => 'Connect the YouTube Data API v3 so the site can serve /api/videos (same JSON as Smash Balloon–style feeds) without exposing your API key in the browser.',
        'api_key' => 'Google API key',
        'api_key_help' => 'Create a key in Google Cloud Console, enable YouTube Data API v3, and restrict it by IP or leave unrestricted for server use (browser-restricted keys will not work for this endpoint).',
        'api_key_placeholder_saved' => '•••••••• (saved — type a new key to replace)',
        'channel_id' => 'Channel ID',
        'channel_id_help' => 'Example: UCxxxxxxxx. You can use either Channel ID or handle.',
        'channel_handle' => 'Channel handle',
        'channel_handle_help' => 'Without @. Example: HaftUganda. Used if Channel ID is empty.',
        'max_results' => 'Videos per page (load more)',
    ],
    'shortcode' => [
        'name' => 'YouTube channel feed',
        'description' => 'Renders the HAFT YouTube grid (requires Haft theme assets). Uses /api/videos when the plugin is configured.',
    ],
];
