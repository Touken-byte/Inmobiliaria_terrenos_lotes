<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tramites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folio_id')->constrained()->onDelete('cascade');
            $table->string('nombre_tramite');
            $table->string('estado'); // pendiente, en_proceso, completado
            $table->date('fecha_solicitud')->nullable();
            $table->date('fecha_resolucion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tramites');
    }
};