<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modificar tabla alquileres
        Schema::table('alquileres', function (Blueprint $table) {
            // Cambiar estado a string para flexibilidad total de estados (disponible, reservado, vendido)
            $table->string('estado', 50)->default('disponible')->change();
            
            // Agregar portada_id
            $table->unsignedBigInteger('portada_id')->nullable()->after('estado_aprobacion');
            $table->foreign('portada_id')->references('id')->on('imagenes')->onDelete('set null');
        });

        // 2. Modificar tabla solicitud_visitas
        Schema::table('solicitud_visitas', function (Blueprint $table) {
            // Hacer terreno_id nulable
            $table->unsignedBigInteger('terreno_id')->nullable()->change();
            
            // Agregar alquiler_id
            $table->unsignedBigInteger('alquiler_id')->nullable()->after('terreno_id');
            $table->foreign('alquiler_id')->references('id')->on('alquileres')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_visitas', function (Blueprint $table) {
            $table->dropForeign(['alquiler_id']);
            $table->dropColumn('alquiler_id');
            $table->unsignedBigInteger('terreno_id')->nullable(false)->change();
        });

        Schema::table('alquileres', function (Blueprint $table) {
            $table->dropForeign(['portada_id']);
            $table->dropColumn('portada_id');
            // Nota: Para revertir el cambio de tipo en SQL, dependería de volver a poner enum. 
            // Para mantener compatibilidad simple, no forzamos enum en rollback si puede fallar por datos existentes.
        });
    }
};
