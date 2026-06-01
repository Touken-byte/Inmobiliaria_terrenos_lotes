<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            $table->decimal('precio', 18, 2)->change();
            $table->decimal('metros_cuadrados', 18, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            $table->decimal('precio', 15, 2)->change();
            $table->decimal('metros_cuadrados', 10, 2)->change();
        });
    }
};