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
        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->morphs('promotable'); // Creates promotable_type and promotable_id
            $table->string('titulo', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('descuento_porcentaje', 5, 2);
            $table->string('estado', 20)->default('pendiente'); // pendiente, aprobado, rechazado
            $table->text('motivo_rechazo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promociones');
    }
};
