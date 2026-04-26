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
    Schema::create('minutas', function (Blueprint $table) {
        $table->id();

        // Relaciones
        $table->foreignId('terreno_id')->constrained()->onDelete('cascade');
        $table->foreignId('comprador_id')->constrained('usuarios');
        $table->foreignId('vendedor_id')->constrained('usuarios');

        // Datos de la minuta
        $table->decimal('monto', 10, 2);
        $table->date('fecha');

        // Archivo (PDF o imagen)
        $table->string('archivo')->nullable();

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minutas');
    }
};
