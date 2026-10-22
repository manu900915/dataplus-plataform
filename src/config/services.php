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
        'default_server' => env('SOFTETHER_DEFAULT_SERVER', 'vps1'),
        // Configuración servidor único o VPS 1 por defecto
        'host' => env('SOFTETHER_VPS1_HOST', env('SOFTETHER_HOST', '152.206.177.55')),
        'port' => (int) env('SOFTETHER_VPS1_PORT', env('SOFTETHER_PORT', 5555)),
        'hub' => env('SOFTETHER_VPS1_HUB', env('SOFTETHER_HUB', 'DEFAULT')),
        'admin_password' => env('SOFTETHER_VPS1_ADMIN_PASSWORD', env('SOFTETHER_ADMIN_PASSWORD', '')),
        'rpc_url' => env('SOFTETHER_VPS1_RPC_URL', env('SOFTETHER_RPC_URL', '')),
        'verify_ssl' => (bool) env('SOFTETHER_VPS1_VERIFY_SSL', env('SOFTETHER_VERIFY_SSL', false)),

        // Servidores configurados (VPS 1 y VPS 2)
        'servers' => [
            'vps1' => [
                'id' => 'vps1',
                'name' => env('SOFTETHER_VPS1_NAME', 'VPS 1 (Principal - 152.206.177.55)'),
                'host' => env('SOFTETHER_VPS1_HOST', env('SOFTETHER_HOST', '152.206.177.55')),
                'port' => (int) env('SOFTETHER_VPS1_PORT', env('SOFTETHER_PORT', 5555)),
                'hub' => env('SOFTETHER_VPS1_HUB', env('SOFTETHER_HUB', 'DEFAULT')),
                'admin_password' => env('SOFTETHER_VPS1_ADMIN_PASSWORD', env('SOFTETHER_ADMIN_PASSWORD', '')),
                'rpc_url' => env('SOFTETHER_VPS1_RPC_URL', env('SOFTETHER_RPC_URL', '')),
                'verify_ssl' => (bool) env('SOFTETHER_VPS1_VERIFY_SSL', env('SOFTETHER_VERIFY_SSL', false)),
            ],
            'vps2' => [
                'id' => 'vps2',
                'name' => env('SOFTETHER_VPS2_NAME', 'VPS 2 (Secundario)'),
                'host' => env('SOFTETHER_VPS2_HOST', ''),
                'port' => (int) env('SOFTETHER_VPS2_PORT', 5555),
                'hub' => env('SOFTETHER_VPS2_HUB', 'DEFAULT'),
                'admin_password' => env('SOFTETHER_VPS2_ADMIN_PASSWORD', ''),
                'rpc_url' => env('SOFTETHER_VPS2_RPC_URL', ''),
                'verify_ssl' => (bool) env('SOFTETHER_VPS2_VERIFY_SSL', false),
            ],
        ],
    ],

];
