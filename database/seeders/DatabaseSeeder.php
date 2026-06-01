<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Categoria;
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

        // ── Categorías por defecto ──
        Categoria::create([
            'nombre'              => 'Residencial',
            'descripcion'         => 'Lotes y terrenos residenciales para viviendas y condominios.',
            'color'               => '#3d7ef5',
            'activa'              => true,
            'tipo_propiedad'      => 'todos',
        ]);

        Categoria::create([
            'nombre'              => 'Comercial',
            'descripcion'         => 'Espacios comerciales, tiendas, oficinas o avenidas principales.',
            'color'               => '#fd7e14',
            'activa'              => true,
            'tipo_propiedad'      => 'todos',
        ]);

        Categoria::create([
            'nombre'              => 'Industrial',
            'descripcion'         => 'Zonas industriales, almacenes, galpones y fábricas.',
            'color'               => '#6c757d',
            'activa'              => true,
            'tipo_propiedad'      => 'todos',
        ]);

        Categoria::create([
            'nombre'              => 'Agrícola',
            'descripcion'         => 'Terrenos agrícolas, quintas y parcelas de cultivo o crianza.',
            'color'               => '#198754',
            'activa'              => true,
            'tipo_propiedad'      => 'todos',
        ]);

        // Categorías específicas por tipo de propiedad (OBS-A03)
        Categoria::create([
            'nombre'              => 'Terreno de Playa',
            'descripcion'         => 'Terrenos exclusivos frente al mar o cuerpos de agua.',
            'color'               => '#0dcaf0',
            'activa'              => true,
            'tipo_propiedad'      => 'terreno',
        ]);

        Categoria::create([
            'nombre'              => 'Lote Urbanizado',
            'descripcion'         => 'Lotes con servicios básicos completos listos para construir.',
            'color'               => '#6f42c1',
            'activa'              => true,
            'tipo_propiedad'      => 'lote',
        ]);

        Categoria::create([
            'nombre'              => 'Habitación/Piso',
            'descripcion'         => 'Habitaciones o departamentos individuales en alquiler.',
            'color'               => '#d63384',
            'activa'              => true,
            'tipo_propiedad'      => 'alquiler',
        ]);

        // ─────────────────────────────────────────
        // 1. Administrador principal
        // ─────────────────────────────────────────
        Usuario::create([
            'nombre'              => 'Carlos Admin Principal',
            'email'               => 'admin@terrenosur.com',
            'email_verified_at'   => now(),
            'password'            => Hash::make('T3rreno$ur2026!'),
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
            'email_verified_at'   => now(),
            'password'            => Hash::make('T3rreno$ur2026!'),
            'rol'                 => 'admin',
            'estado_verificacion' => 'verificado',
            'telefono'            => '+591 70000002',
            'activo'              => true,
        ]);

        Usuario::create([
            'nombre'              => 'Roberto Secundario',
            'email'               => 'roberto.admin@terrenosur.com',
            'email_verified_at'   => now(),
            'password'            => Hash::make('T3rreno$ur2026!'),
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
                'email_verified_at'   => now(),
                'password'            => Hash::make('T3rreno$ur2026!'),
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
            'email_verified_at'   => now(),
            'password'            => Hash::make('T3rreno$ur2026!'),
            'rol'                 => 'comprador',     // ← Siempre 'comprador'
            'estado_verificacion' => 'verificado',
            'telefono'            => '+591 79999999',
            'activo'              => true,
        ]);

        $this->command->info('');
        $this->command->info('✅ Base de datos reiniciada correctamente.');
        $this->command->info('─────────────────────────────────────────');
        $this->command->info('👑 Contraseña de prueba para todos: T3rreno$ur2026!');
        $this->command->info('👑 Admin:      admin@terrenosur.com');
        $this->command->info('👑 Admin 2:    laura.admin@terrenosur.com');
        $this->command->info('👑 Admin 3:    roberto.admin@terrenosur.com');
        $this->command->info('👤 Vendedor 1: maria@terrenosur.com');
        $this->command->info('👤 Vendedor 2: juan@terrenosur.com');
        $this->command->info('🛒 Comprador:  comprador@terrenosur.com');
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
            'auditoria_accesos',
            'promociones',
            'favoritos',
            'mensajes',
            'chats',
            'leads',
            'protocolizaciones',
            'inscripciones_derechos_reales',
            'alertas_legales',
            'restricciones',
            'gravamenes',
            'tramites',
            'propietarios',
            'folios',
            'comprobante_its',
            'minutas',
            'historial_estado_lotes',
            'historial_estado_alquileres',
            'solicitud_visitas',
            'disponibilidad_vendedors',
            'documentos_propiedad',
            'terreno_imagenes',
            'imagenes',
            'terrenos',
            'alquileres',
            'categorias',
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