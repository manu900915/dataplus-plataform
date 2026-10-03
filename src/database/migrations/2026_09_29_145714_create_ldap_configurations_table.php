<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ldap_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('host')->default('lldap');
            $table->integer('port')->default(3890);
            $table->string('base_dn')->default('dc=dataplus,dc=cu');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->boolean('ssl')->default(false);
            $table->boolean('tls')->default(false);
            $table->integer('timeout')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ldap_configurations');
    }
};
