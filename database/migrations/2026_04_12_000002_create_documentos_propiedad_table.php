<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documentos_propiedad', function (Blueprint $table) {
            $table->id();

            $table->foreignId('terreno_id')
                ->constrained('terrenos')
                ->cascadeOnDelete();

            $table->string('nombre_archivo', 255);
            $table->string('nombre_original', 255);
            $table->string('tipo_mime', 100);
            $table->integer('tamano');
            $table->enum('estado', ['en_verificacion', 'verificado', 'observado'])->default('en_verificacion');

            $table->dateTime('creado_en')->useCurrent();
            $table->dateTime('actualizado_en')->useCurrent()->useCurrentOnUpdate();

            $table->index('terreno_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_propiedad');
    }
};