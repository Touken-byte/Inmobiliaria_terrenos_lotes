<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solo crear si no existe para evitar errores de duplicidad
        if (!Schema::hasTable('protocolizaciones')) {
            Schema::create('protocolizaciones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('minuta_id')->constrained('minutas')->onDelete('cascade');
                $table->foreignId('terreno_id')->constrained('terrenos')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('usuarios')->onDelete('cascade'); // Vendedor
                
                $table->string('numero_protocolo');
                $table->date('fecha_protocolizacion');
                $table->string('archivo_testimonio');
                
                // Nota: El fix_protocolizaciones_enum.php ampliará esto a 'completado'
                $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'completado'])->default('pendiente');
                $table->text('observacion')->nullable();
                
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('protocolizaciones');
    }
};
