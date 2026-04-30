<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Esta migración agrega las columnas necesarias para el mapa interactivo.
 * Si ya tienes la migración 2026_04_29_200314_add_coordinates_to_terrenos_table.php
 * verifica que incluya 'latitud', 'longitud' y 'nombre_lote'.
 * Si ya los tienes, NO corras esta migración.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            // Nombre del lote para mostrarlo en el mapa
            if (!Schema::hasColumn('terrenos', 'nombre_lote')) {
                $table->string('nombre_lote', 100)->nullable()->after('ubicacion');
            }

            // Coordenadas para el mapa
            if (!Schema::hasColumn('terrenos', 'latitud')) {
                $table->decimal('latitud', 10, 8)->nullable()->after('nombre_lote');
            }

            if (!Schema::hasColumn('terrenos', 'longitud')) {
                $table->decimal('longitud', 11, 8)->nullable()->after('latitud');
            }

            // Estado del lote (diferente al estado de aprobación del admin)
            // estado = pendiente/aprobado/rechazado (flujo admin)
            // estado_lote = disponible/reservado/vendido (estado comercial)
            if (!Schema::hasColumn('terrenos', 'estado_lote')) {
                $table->enum('estado_lote', ['disponible', 'reservado', 'vendido'])
                      ->default('disponible')
                      ->after('estado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            $table->dropColumn(['nombre_lote', 'latitud', 'longitud', 'estado_lote']);
        });
    }
};