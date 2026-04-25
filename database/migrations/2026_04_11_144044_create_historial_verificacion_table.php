<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('historial_verificacion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->foreignId('admin_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->enum('accion', ['aprobado', 'rechazado']);
            $table->text('comentario')->nullable();

            $table->foreignId('documento_id')
                ->nullable()
                ->constrained('documentos_ci')
                ->nullOnDelete();

            $table->dateTime('fecha')->useCurrent();

            $table->index('usuario_id');
            $table->index('admin_id');
            $table->index('fecha');
            $table->index('accion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_verificacion');
    }
};