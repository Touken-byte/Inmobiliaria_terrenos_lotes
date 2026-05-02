<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support changing enums easily, but since we use MySQL/Laragon:
        DB::statement("ALTER TABLE minutas MODIFY COLUMN estado ENUM('pendiente', 'aprobada', 'rechazada', 'completada') DEFAULT 'pendiente'");
        DB::statement("ALTER TABLE comprobantes_it MODIFY COLUMN estado ENUM('pendiente', 'aprobado', 'rechazado', 'completado') DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE minutas MODIFY COLUMN estado ENUM('pendiente', 'aprobada', 'rechazada') DEFAULT 'pendiente'");
        DB::statement("ALTER TABLE comprobantes_it MODIFY COLUMN estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente'");
    }
};
