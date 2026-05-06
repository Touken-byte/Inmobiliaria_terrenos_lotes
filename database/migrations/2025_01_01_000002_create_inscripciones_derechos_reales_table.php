<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('inscripciones_derechos_reales')) {
            Schema::create('inscripciones_derechos_reales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('folio_id')->constrained('folios')->onDelete('cascade');
                $table->string('numero_matricula')->nullable();
                $table->string('comprobante_archivo')->nullable();
                $table->string('comprobante_nombre_original')->nullable();
                $table->date('fecha_entrada')->nullable();
                $table->date('fecha_salida')->nullable();
                $table->decimal('tasa_pagada', 10, 2)->nullable();
                $table->enum('estado', ['pendiente', 'en_revision', 'inscrito', 'rechazado'])->default('pendiente');
                $table->text('observacion_admin')->nullable();
                $table->foreignId('revisado_por')->nullable()->constrained('usuarios')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('inscripciones_derechos_reales');
    }
};