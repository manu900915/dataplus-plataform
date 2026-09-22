<?php

namespace App\Ldap;

use LdapRecord\Models\Model;

class User extends Model
{
    public static array $objectClasses = [
        'inetOrgPerson',
        'posixAccount',
        'shadowAccount',
    ];

    protected array $dates = [
        'passwordExpirationTime',
    ];
}