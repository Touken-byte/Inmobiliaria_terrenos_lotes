<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('restricciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folio_id')->constrained()->onDelete('cascade');
            $table->string('tipo'); // ej: servidumbre, uso de suelo, etc.
            $table->text('descripcion');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('restricciones');
    }
};