<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DisponibilidadVendedor;
use App\Models\Usuario;

class DisponibilidadSeeder extends Seeder
{
    public function run()
    {
        $vendedores = Usuario::where('rol', 'vendedor')->where('activo', true)->get();

        if ($vendedores->isEmpty()) {
            $this->command->warn('⚠️ No hay vendedores. Ejecuta DatabaseSeeder primero.');
            return;
        }

        $diasLaborables = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];

        foreach ($vendedores as $vendedor) {
            // Limpiar disponibilidad previa (evita duplicados)
            DisponibilidadVendedor::where('vendedor_id', $vendedor->id)->delete();

            // Lunes a Viernes: 8am a 6pm
            foreach ($diasLaborables as $dia) {
                DisponibilidadVendedor::create([
                    'vendedor_id' => $vendedor->id,
                    'dia_semana' => $dia,
                    'hora_inicio' => '08:00:00',
                    'hora_fin' => '18:00:00',
                    'activo' => true,
                ]);
            }

            // Sábado: 9am a 1pm
            DisponibilidadVendedor::create([
                'vendedor_id' => $vendedor->id,
                'dia_semana' => 'sabado',
                'hora_inicio' => '09:00:00',
                'hora_fin' => '13:00:00',
                'activo' => true,
            ]);
        }

        $this->command->info("✅ Disponibilidad creada para {$vendedores->count()} vendedores.");
    }
}