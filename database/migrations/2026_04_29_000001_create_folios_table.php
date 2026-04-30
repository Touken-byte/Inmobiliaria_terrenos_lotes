<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('folios', function (Blueprint $table) {
            $table->id();
            $table->string('numero_folio', 50)->unique();
            $table->foreignId('terreno_id')->constrained('terrenos')->onDelete('cascade');
            $table->decimal('superficie', 10, 2);
            $table->text('ubicacion');
            $table->text('colindancias')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('folios');
    }
};