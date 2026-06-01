<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            // Campos comunes (tanto para terreno como lote)
            $table->string('nombre', 255)->nullable()->after('tipo');
            $table->string('codigo', 50)->nullable()->after('nombre');
            $table->string('pais', 100)->nullable()->after('codigo');
            $table->string('departamento', 100)->nullable()->after('pais');
            $table->string('provincia', 100)->nullable()->after('departamento');
            $table->string('municipio', 100)->nullable()->after('provincia');
            $table->string('zona_barrio', 255)->nullable()->after('municipio');
            $table->string('direccion', 255)->nullable()->after('zona_barrio');
            
            // Servicios (compartidos)
            $table->boolean('agua_potable')->default(false)->after('descripcion');
            $table->boolean('energia_electrica')->default(false)->after('agua_potable');
            $table->boolean('alcantarillado')->default(false)->after('energia_electrica');
            $table->boolean('gas_domiciliario')->default(false)->after('alcantarillado');
            $table->boolean('internet')->default(false)->after('gas_domiciliario');
            
            // Moneda y forma de pago (compartidos)
            $table->enum('moneda', ['BOB', 'USD'])->default('USD')->after('internet');
            $table->enum('forma_pago', ['contado', 'financiamiento', 'ambos'])->default('ambos')->after('moneda');
        });

        // Campos específicos de TERRENO
        Schema::table('terrenos', function (Blueprint $table) {
            $table->enum('tipo_terreno', ['urbano', 'rural', 'agricola', 'comercial', 'industrial'])->nullable()->after('direccion');
            $table->decimal('largo', 10, 2)->nullable()->after('metros_cuadrados');
            $table->decimal('ancho', 10, 2)->nullable()->after('largo');
            $table->enum('topografia', ['plano', 'semiplano', 'inclinado'])->nullable()->after('ancho');
            $table->string('numero_matricula', 100)->nullable()->after('topografia');
            $table->string('codigo_catastral', 100)->nullable()->after('numero_matricula');
        });

        // Campos específicos de LOTE
        Schema::table('terrenos', function (Blueprint $table) {
            $table->string('numero_lote', 50)->nullable()->after('codigo_catastral');
            $table->string('codigo_lote', 50)->nullable()->after('numero_lote');
            $table->string('manzano_bloque', 50)->nullable()->after('codigo_lote');
            $table->decimal('frente', 10, 2)->nullable()->after('manzano_bloque');
            $table->decimal('fondo', 10, 2)->nullable()->after('frente');
            $table->text('colinda_norte')->nullable()->after('fondo');
            $table->text('colinda_sur')->nullable()->after('colinda_norte');
            $table->text('colinda_este')->nullable()->after('colinda_sur');
            $table->text('colinda_oeste')->nullable()->after('colinda_este');
        });
    }

    public function down(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            $table->dropColumn([
                'nombre', 'codigo', 'pais', 'departamento', 'provincia', 'municipio',
                'zona_barrio', 'direccion', 'tipo_terreno', 'largo', 'ancho', 'topografia',
                'agua_potable', 'energia_electrica', 'alcantarillado', 'gas_domiciliario', 'internet',
                'moneda', 'forma_pago', 'numero_matricula', 'codigo_catastral',
                'numero_lote', 'codigo_lote', 'manzano_bloque', 'frente', 'fondo',
                'colinda_norte', 'colinda_sur', 'colinda_este', 'colinda_oeste',
            ]);
        });
    }
};