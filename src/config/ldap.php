<?php

return [
    'logging' => env('LDAP_LOGGING', true),

    'connections' => [
        'default' => [
            // Normaliza: si LDAP_HOST apunta al gateway del host, usa el nombre
            // estable que define docker-compose (extra_hosts -> host.docker.internal)
            'hosts' => [env('LDAP_HOST', 'lldap') === '172.17.0.1' ? 'host.docker.internal' : env('LDAP_HOST', 'lldap')],
            'username' => env('LDAP_USERNAME', 'uid=admin,ou=people,dc=dataplus,dc=cu'),
            'password' => env('LDAP_PASSWORD', ''),
            'port' => env('LDAP_PORT', 3890),
            'base_dn' => env('LDAP_BASE_DN', 'dc=dataplus,dc=cu'),
            'timeout' => env('LDAP_TIMEOUT', 5),
        ],
    ],
];