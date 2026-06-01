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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terreno_id')->constrained('terrenos')->onDelete('cascade');
            $table->foreignId('comprador_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('vendedor_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('nombre');
            $table->string('telefono');
            $table->text('mensaje')->nullable();
            $table->enum('estado', ['nuevo', 'contactado', 'negociacion', 'cerrado'])->default('nuevo');
            $table->timestamp('fecha_contacto')->useCurrent();
            $table->timestamps();

            // Prevent duplicate active leads for the same user and terrain
            $table->unique(['terreno_id', 'comprador_id', 'estado'], 'unique_active_lead_per_comprador_terreno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
