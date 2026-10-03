<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LdapConfiguration extends Model
{
    protected $fillable = [
        'host', 'port', 'base_dn', 'username', 'password', 'ssl', 'tls', 'timeout', 'is_active'
    ];

    protected $casts = [
        'ssl' => 'boolean',
        'tls' => 'boolean',
        'is_active' => 'boolean',
        'port' => 'integer',
        'timeout' => 'integer',
    ];
}
