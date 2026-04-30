<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Limpiar TODAS las tablas relacionadas en orden correcto ──
        // (para evitar errores de foreign key)
        $this->limpiarTablas();

        // ─────────────────────────────────────────
        // 1. Administrador principal
        // ─────────────────────────────────────────
        Usuario::create([
            'nombre'              => 'Carlos Admin Principal',
            'email'               => 'admin@terrenosur.com',
            'password'            => Hash::make('admin123'),
            'rol'                 => 'admin',        // ← Siempre 'admin'
            'estado_verificacion' => 'verificado',
            'telefono'            => '+591 70000001',
            'activo'              => true,
        ]);

        // ─────────────────────────────────────────
        // 2. Administradores secundarios
        // ─────────────────────────────────────────
        Usuario::create([
            'nombre'              => 'Laura Secundaria',
            'email'               => 'laura.admin@terrenosur.com',
            'password'            => Hash::make('admin123'),
            'rol'                 => 'admin',
            'estado_verificacion' => 'verificado',
            'telefono'            => '+591 70000002',
            'activo'              => true,
        ]);

        Usuario::create([
            'nombre'              => 'Roberto Secundario',
            'email'               => 'roberto.admin@terrenosur.com',
            'password'            => Hash::make('admin123'),
            'rol'                 => 'admin',
            'estado_verificacion' => 'verificado',
            'telefono'            => '+591 70000003',
            'activo'              => true,
        ]);

        // ─────────────────────────────────────────
        // 3. Vendedores de prueba
        // ─────────────────────────────────────────
        $vendedores = [
            [
                'nombre'              => 'María Vendedora',
                'email'               => 'maria@terrenosur.com',
                'telefono'            => '+591 71111111',
                'estado_verificacion' => 'verificado',
            ],
            [
                'nombre'              => 'Juan Pérez',
                'email'               => 'juan@terrenosur.com',
                'telefono'            => '+591 72222222',
                'estado_verificacion' => 'verificado',
            ],
            [
                'nombre'              => 'Ana Gómez',
                'email'               => 'ana@terrenosur.com',
                'telefono'            => '+591 73333333',
                'estado_verificacion' => 'pendiente',
            ],
            [
                'nombre'              => 'Carlos López',
                'email'               => 'carlos@terrenosur.com',
                'telefono'            => '+591 74444444',
                'estado_verificacion' => 'verificado',
            ],
            [
                'nombre'              => 'Sofía Ramírez',
                'email'               => 'sofia@terrenosur.com',
                'telefono'            => '+591 75555555',
                'estado_verificacion' => 'pendiente',
            ],
        ];

        foreach ($vendedores as $v) {
            Usuario::create([
                'nombre'              => $v['nombre'],
                'email'               => $v['email'],
                'password'            => Hash::make('vendedor123'),
                'rol'                 => 'vendedor',   // ← Siempre 'vendedor'
                'estado_verificacion' => $v['estado_verificacion'],
                'telefono'            => $v['telefono'],
                'activo'              => true,
            ]);
        }

        // ─────────────────────────────────────────
        // 4. Compradores de prueba
        // ─────────────────────────────────────────
        Usuario::create([
            'nombre'              => 'Comprador Prueba',
            'email'               => 'comprador@terrenosur.com',
            'password'            => Hash::make('comprador123'),
            'rol'                 => 'comprador',     // ← Siempre 'comprador'
            'estado_verificacion' => 'verificado',
            'telefono'            => '+591 79999999',
            'activo'              => true,
        ]);

        $this->command->info('');
        $this->command->info('✅ Base de datos reiniciada correctamente.');
        $this->command->info('─────────────────────────────────────────');
        $this->command->info('👑 Admin:      admin@terrenosur.com       / admin123');
        $this->command->info('👑 Admin 2:    laura.admin@terrenosur.com / admin123');
        $this->command->info('👑 Admin 3:    roberto.admin@terrenosur.com / admin123');
        $this->command->info('👤 Vendedor 1: maria@terrenosur.com       / vendedor123');
        $this->command->info('👤 Vendedor 2: juan@terrenosur.com        / vendedor123');
        $this->command->info('🛒 Comprador:  comprador@terrenosur.com   / comprador123');
        $this->command->info('─────────────────────────────────────────');
    }

    /**
     * Limpia las tablas en orden correcto respetando foreign keys.
     */
    private function limpiarTablas(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Orden: primero las tablas hijas, luego las padres
        $tablas = [
            'historial_accesos',
            'restricciones',
            'gravamenes',
            'tramites',
            'propietarios',
            'folios',
            'comprobante_its',
            'minutas',
            'historial_estado_lotes',
            'solicitud_visitas',
            'disponibilidad_vendedors',
            'documentos_propiedad',
            'terreno_imagenes',
            'terrenos',
            'historial_verificacion',
            'documentos_ci',
            'usuarios',
        ];

        foreach ($tablas as $tabla) {
            // Verificar que la tabla exista antes de truncar
            if (DB::getSchemaBuilder()->hasTable($tabla)) {
                DB::table($tabla)->truncate();
            }
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}