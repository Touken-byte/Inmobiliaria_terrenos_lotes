<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Corrige el nombre de la tabla si hubo un typo y amplía el ENUM de estados.
     */
    public function up(): void
    {
        // 1. Corregir posible typo en el nombre de la tabla (protocolaciones -> protocolizaciones)
        if (Schema::hasTable('protocolaciones') && !Schema::hasTable('protocolizaciones')) {
            Schema::rename('protocolaciones', 'protocolizaciones');
        }

        // 2. Ampliar el ENUM para incluir 'completado' (Necesario para el cierre de venta)
        // Usamos DB::statement porque Schema::table no maneja bien cambios de ENUM en MySQL sin dependencias extra
        if (Schema::hasTable('protocolizaciones')) {
            DB::statement("ALTER TABLE protocolizaciones MODIFY COLUMN estado ENUM('pendiente', 'aprobado', 'rechazado', 'completado') DEFAULT 'pendiente'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('protocolizaciones')) {
            DB::statement("ALTER TABLE protocolizaciones MODIFY COLUMN estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente'");
        }
    }
};
