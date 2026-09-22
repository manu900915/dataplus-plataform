<?php

return [
    'logging' => env('LDAP_LOGGING', true),

    'connections' => [
        'default' => [
            'hosts' => [env('LDAP_HOST', 'lldap')],
            'username' => env('LDAP_USERNAME', 'uid=admin,ou=people,dc=dataplus,dc=local'),
            'password' => env('LDAP_PASSWORD', 'Data.1234'),
            'port' => env('LDAP_PORT', 3890),
            'base_dn' => env('LDAP_BASE_DN', 'dc=dataplus,dc=local'),
            'timeout' => env('LDAP_TIMEOUT', 5),
        ],
    ],
];