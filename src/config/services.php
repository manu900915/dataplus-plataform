<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'chatwoot' => [
        'base_url' => rtrim((string) env('CHATWOOT_BASE_URL', 'https://chat.dataplus.cu/'), '/'),
        'access_token' => env('CHATWOOT_ACCESS_TOKEN', 'p9wKrE7Zt44CJWgAVtfzvjx4'),
        'account_id' => (int) env('CHATWOOT_ACCOUNT_ID', 1),
        'inbox_id' => (int) env('CHATWOOT_INBOX_ID', 1),
    ],

    'softether' => [
        'host' => env('SOFTETHER_HOST', '127.0.0.1'),
        'port' => (int) env('SOFTETHER_PORT', 5555),
        'hub' => env('SOFTETHER_HUB', 'DEFAULT'),
        'admin_password' => env('SOFTETHER_ADMIN_PASSWORD', ''),
        'rpc_url' => env('SOFTETHER_RPC_URL', ''),
        'verify_ssl' => (bool) env('SOFTETHER_VERIFY_SSL', false),
    ],

];
