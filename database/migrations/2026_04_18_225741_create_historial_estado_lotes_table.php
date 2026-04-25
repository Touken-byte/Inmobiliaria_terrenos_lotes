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
        Schema::create('historial_estado_lotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('terreno_id');
            $table->unsignedBigInteger('usuario_id'); // Usuario que hizo el cambio
            $table->string('estado_anterior');
            $table->string('estado_nuevo');
            $table->timestamp('fecha_cambio')->useCurrent();
            
            // Si quieres definir las llaves foráneas, descomenta:
            $table->foreign('terreno_id')->references('id')->on('terrenos')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_estado_lotes');
    }
};
