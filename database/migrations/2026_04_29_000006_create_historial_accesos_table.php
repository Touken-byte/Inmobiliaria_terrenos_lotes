<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('historial_accesos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folio_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->string('tipo_consulta'); // rapida o completa
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('fecha_acceso')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('historial_accesos');
    }
};