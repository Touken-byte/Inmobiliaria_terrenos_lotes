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
        Schema::create('alertas_legales', function (Blueprint $table) {
            $table->id();
            $table->morphs('alertable'); // Para Minuta, ComprobanteIt, etc.
            $table->string('tipo'); // e.g., 'fecha_futura', 'monto_bajo', 'fuera_de_plazo'
            $table->text('mensaje');
            $table->string('estado')->default('activa'); // activa, resuelta, ignorada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertas_legales');
    }
};
