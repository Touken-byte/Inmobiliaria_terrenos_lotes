<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('folios', function (Blueprint $table) {
            if (!Schema::hasColumn('folios', 'estado')) {
                $table->enum('estado', ['pendiente', 'verificado'])
                      ->default('pendiente')
                      ->after('colindancias');
            }
            if (!Schema::hasColumn('folios', 'verificado_por')) {
                $table->foreignId('verificado_por')
                      ->nullable()
                      ->after('estado')
                      ->constrained('usuarios')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('folios', function (Blueprint $table) {
            if (Schema::hasColumn('folios', 'verificado_por')) {
                $table->dropForeign(['verificado_por']);
                $table->dropColumn('verificado_por');
            }
            if (Schema::hasColumn('folios', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};