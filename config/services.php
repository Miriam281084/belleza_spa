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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'mercadopago' => [
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
    ],

    'whatsapp' => [
        'provider' => env('WHATSAPP_PROVIDER', 'twilio'), // 'twilio' (sandbox gratis) o 'callmebot' (no funciona en Argentina)
        'enabled' => env('WHATSAPP_ENABLED', false),

        // CallMeBot (alternativa - no funciona en Argentina)
        'callmebot_api_key' => env('CALLMEBOT_API_KEY'),
        'callmebot_phone' => env('CALLMEBOT_PHONE'), // Tu número sin + ni espacios (ej: 5491123456789)
    ],

];
