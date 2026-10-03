<?php

return [
    'logging' => env('LDAP_LOGGING', true),
    'connections' => [
        'default' => [
            'hosts' => [env('LDAP_HOST', 'lldap')],
            'username' => env('LDAP_USERNAME', 'uid=admin,ou=people,dc=dataplus,dc=cu'),
            'password' => env('LDAP_PASSWORD', ''),
            'port' => (int) env('LDAP_PORT', 3890),
            'base_dn' => env('LDAP_BASE_DN', 'dc=dataplus,dc=cu'),
            'timeout' => (int) env('LDAP_TIMEOUT', 5),
            'use_tls' => (bool) env('LDAP_TLS', false),
        ],
    ],
];
