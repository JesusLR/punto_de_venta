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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'openwa' => [
        'url' => env('OPENWA_URL', env('OPENWA_API_URL', env('APP_ENV') === 'local' ? 'http://127.0.0.1:2785/api' : 'http://74.208.53.13:2886/api')),
        'system_key' => env('OPENWA_SYSTEM_KEY'),
        'source_system' => 'punto_de_venta',
        'automatic_replies' => false,
        'session_id' => env('OPENWA_SESSION_ID', ''),
    ],
    'google' => [
        'analytics_id' => env('GOOGLE_ANALYTICS_ID'),
        'report_url' => env('ANALYTICS_REPORT_URL'),
    ],

];
