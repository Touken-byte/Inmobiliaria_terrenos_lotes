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
            $table->tinyInteger('habitaciones')->default(1);
            $table->tinyInteger('banos')->default(1);
            $table->text('descripcion');
            $table->json('servicios_incluidos')->nullable();
            $table->date('disponible_desde');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('estado', ['disponible', 'alquilado', 'inactivo'])->default('disponible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alquileres');
    }
};