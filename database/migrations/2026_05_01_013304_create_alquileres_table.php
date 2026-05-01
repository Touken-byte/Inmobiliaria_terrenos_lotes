<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alquileres', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('ubicacion');
            $table->decimal('precio_mensual', 10, 2);
            $table->integer('metros_cuadrados')->nullable();
            $table->tinyInteger('habitaciones');
            $table->tinyInteger('banos');
            $table->text('descripcion');
            $table->json('servicios_incluidos')->nullable();
            $table->date('disponible_desde');
            $table->foreignId('user_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->enum('estado', ['disponible', 'alquilado', 'inactivo'])->default('disponible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alquileres');
    }
};