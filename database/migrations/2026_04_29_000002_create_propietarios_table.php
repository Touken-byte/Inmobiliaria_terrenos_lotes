<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('propietarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folio_id')->constrained()->onDelete('cascade');
            $table->string('nombre_completo');
            $table->string('tipo_documento', 20)->default('DNI');
            $table->string('numero_documento', 20);
            $table->boolean('vigente')->default(true);
            $table->date('fecha_desde')->nullable();
            $table->date('fecha_hasta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('propietarios');
    }
};