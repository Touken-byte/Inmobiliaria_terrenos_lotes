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
        // Limpiar tabla usuarios (resetea IDs)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Usuario::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Administrador principal
        Usuario::create([
            'nombre' => 'Carlos Admin Principal',
            'email' => 'admin@terrenosur.com',
            'password' => Hash::make('admin123'),
            'rol' => 'admin',
            'estado_verificacion' => 'verificado',
            'telefono' => '+591 70000001',
            'fecha_registro' => now(),
            'ultimo_login' => null,
            'activo' => true,
        ]);

        // 2. Administradores secundarios (2)
        Usuario::create([
            'nombre' => 'Laura Secundaria',
            'email' => 'laura.admin@terrenosur.com',
            'password' => Hash::make('admin123'),
            'rol' => 'admin',
            'estado_verificacion' => 'verificado',
            'telefono' => '+591 70000002',
            'fecha_registro' => now(),
            'activo' => true,
        ]);

        Usuario::create([
            'nombre' => 'Roberto Secundario',
            'email' => 'roberto.admin@terrenosur.com',
            'password' => Hash::make('admin123'),
            'rol' => 'admin',
            'estado_verificacion' => 'verificado',
            'telefono' => '+591 70000003',
            'fecha_registro' => now(),
            'activo' => true,
        ]);

        // 3. Vendedores de prueba (5)
        $vendedores = [
            ['nombre' => 'María Vendedora', 'email' => 'maria@terrenosur.com', 'telefono' => '+591 71111111', 'estado_verificacion' => 'verificado'],
            ['nombre' => 'Juan Pérez', 'email' => 'juan@terrenosur.com', 'telefono' => '+591 72222222', 'estado_verificacion' => 'verificado'],
            ['nombre' => 'Ana Gómez', 'email' => 'ana@terrenosur.com', 'telefono' => '+591 73333333', 'estado_verificacion' => 'pendiente'],
            ['nombre' => 'Carlos López', 'email' => 'carlos@terrenosur.com', 'telefono' => '+591 74444444', 'estado_verificacion' => 'verificado'],
            ['nombre' => 'Sofia Ramírez', 'email' => 'sofia@terrenosur.com', 'telefono' => '+591 75555555', 'estado_verificacion' => 'pendiente'],
        ];

        foreach ($vendedores as $v) {
            Usuario::create([
                'nombre' => $v['nombre'],
                'email' => $v['email'],
                'password' => Hash::make('vendedor123'),
                'rol' => 'vendedor',
                'estado_verificacion' => $v['estado_verificacion'],
                'telefono' => $v['telefono'],
                'fecha_registro' => now(),
                'activo' => true,
            ]);
        }

        // 4. Comprador de prueba (1)
        Usuario::create([
            'nombre' => 'Comprador Prueba',
            'email' => 'comprador@terrenosur.com',
            'password' => Hash::make('comprador123'),
            'rol' => 'comprador',
            'estado_verificacion' => 'verificado',
            'telefono' => '+591 79999999',
            'fecha_registro' => now(),
            'activo' => true,
        ]);

        $this->command->info('✅ Base de datos de usuarios reiniciada correctamente.');
        $this->command->info('👑 Admin principal: admin@terrenosur.com / admin123');
        $this->command->info('👥 Admins secundarios: laura..., roberto... (pass: admin123)');
        $this->command->info('👤 Vendedores: 5 creados (pass: vendedor123)');
        $this->command->info('🛒 Comprador: comprador@terrenosur.com / comprador123');
    }
}