<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'google_meet' => [
        'service_account_json' => env('GOOGLE_MEET_SERVICE_ACCOUNT_JSON', storage_path('app/google-service-account.json')),
        'impersonate_email'    => env('GOOGLE_MEET_IMPERSONATE_EMAIL', ''),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenWA — Self-hosted WhatsApp API Gateway
    | https://github.com/rmyndharis/OpenWA
    |--------------------------------------------------------------------------
    */
    'openwa' => [
        'enabled'      => env('OPENWA_ENABLED', false),
        'url'          => env('OPENWA_URL', 'http://localhost:2785'),
        'key'          => env('OPENWA_API_KEY', ''),
        'session'      => env('OPENWA_SESSION_ID', 'default'),
        'country_code' => env('OPENWA_COUNTRY_CODE', '62'),
    ],

];
