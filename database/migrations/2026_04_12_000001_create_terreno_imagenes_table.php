<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('terreno_imagenes', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('terreno_id')
                ->constrained('terrenos')
                ->cascadeOnDelete();
                
            $table->string('ruta_archivo', 255);
            $table->integer('orden')->default(0);
            
            $table->dateTime('fecha_subida')->useCurrent();
            
            $table->index('terreno_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terreno_imagenes');
    }
};
