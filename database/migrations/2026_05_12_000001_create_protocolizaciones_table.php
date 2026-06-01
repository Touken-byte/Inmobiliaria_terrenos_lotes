<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de protocolizaciones notariales.
     * Tercer paso del proceso legal: Minuta → Comprobante IT → Protocolización → VENDIDO
     */
    public function up(): void
    {
        Schema::create('protocolizaciones', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('minuta_id')->constrained('minutas')->onDelete('cascade');
            $table->foreignId('terreno_id')->constrained('terrenos')->onDelete('cascade');
            $table->foreignId('vendedor_id')->constrained('usuarios')->onDelete('cascade');

            // Datos del protocolo notarial
            $table->string('numero_protocolo');
            $table->date('fecha_protocolizacion');
            $table->string('archivo_testimonio');   // PDF/JPG/PNG — obligatorio

            // Estado del proceso
            $table->enum('estado', ['en_revision', 'aprobado', 'rechazado'])->default('en_revision');
            $table->text('observacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('protocolizaciones');
    }
};
