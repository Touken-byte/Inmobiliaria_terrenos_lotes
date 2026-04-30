<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gravamenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folio_id')->constrained()->onDelete('cascade');
            $table->string('tipo'); // ej: hipoteca, embargo, etc.
            $table->text('descripcion')->nullable();
            $table->decimal('monto', 12, 2)->nullable();
            $table->date('fecha_registro')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gravamenes');
    }
};