<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->morphs('favoriteable'); // crea favoriteable_id + favoriteable_type
            $table->timestamps();

            // Un usuario no puede marcar el mismo item dos veces
            $table->unique(['usuario_id', 'favoriteable_id', 'favoriteable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favoritos');
    }
};