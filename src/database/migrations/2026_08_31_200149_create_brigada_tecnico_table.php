<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brigada_tecnico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brigada_id')->constrained('brigadas')->cascadeOnDelete();
            $table->foreignId('tecnico_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['brigada_id', 'tecnico_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brigada_tecnico');
    }
};