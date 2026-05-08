<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('auditoria_accesos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->string('accion');           // login, aprobacion_terreno, rechazo_terreno, etc.
            $table->string('entidad')->nullable(); // terreno, folio, vendedor, documento, etc.
            $table->unsignedBigInteger('entidad_id')->nullable(); // ID del registro afectado
            $table->text('descripcion')->nullable(); // Detalle legible para humanos
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('auditoria_accesos');
    }
};