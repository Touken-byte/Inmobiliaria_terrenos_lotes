<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documentos_ci', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->string('nombre_archivo', 255);
            $table->string('nombre_original', 255);
            $table->string('tipo_mime', 100);
            $table->integer('tamano');
            $table->dateTime('fecha_subida')->useCurrent();
            $table->boolean('activo')->default(true);

            $table->index('usuario_id');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_ci');
    }
};