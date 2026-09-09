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
        'url' => env('OPENWA_API_URL', 'http://74.208.53.13:2785'),
        'key' => env('OPENWA_API_KEY', 'owa_k1_4c2631a0321cb61e1d266787912fe331eef088fd850cfff50326b63cad9d9585'),
        'session_id' => env('OPENWA_SESSION_ID', '581655e7-d546-4e9c-88da-f1f8843bc8f6'),
    ],
    'google' => [
        'analytics_id' => env('GOOGLE_ANALYTICS_ID'),
        'report_url' => env('ANALYTICS_REPORT_URL'),
    ],

];
