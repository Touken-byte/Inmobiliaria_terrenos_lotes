<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('terrenos', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();
                
            $table->decimal('precio', 15, 2);
            $table->decimal('metros_cuadrados', 10, 2);
            $table->string('ubicacion', 255);
            $table->text('descripcion');
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            
            $table->foreignId('id_admin_aprobador')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();

            $table->dateTime('creado_en')->useCurrent();
            $table->dateTime('actualizado_en')->useCurrent()->useCurrentOnUpdate();
            
            $table->index('usuario_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terrenos');
    }
};
