<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brigadas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->foreignId('jefe_id')->constrained('users');
            $table->text('notas')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brigadas');
    }
};