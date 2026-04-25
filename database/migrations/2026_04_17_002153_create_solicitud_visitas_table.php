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
        Schema::create('solicitud_visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('terreno_id')->constrained('terrenos')->onDelete('cascade');
            $table->foreignId('vendedor_id')->constrained('usuarios')->onDelete('cascade');
            $table->date('fecha_visita');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'cancelada'])->default('pendiente');
            $table->text('motivo_rechazo')->nullable();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamp('fecha_cancelacion')->nullable();
            $table->foreignId('aprobado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('cancelado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            
            // Índices para búsquedas rápidas
            $table->index(['fecha_visita', 'hora_inicio', 'vendedor_id']);
            $table->index('estado');
            $table->index('user_id');
            $table->index('terreno_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_visitas');
    }
};
