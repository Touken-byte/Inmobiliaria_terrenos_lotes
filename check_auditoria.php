<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = \App\Models\AuditoriaAcceso::with('usuario')->latest()->limit(20)->get();
echo "Total logs: " . $logs->count() . "\n";
foreach($logs as $log) {
    echo "ID: {$log->id} | Usuario ID: {$log->usuario_id} | Nombre: " . ($log->usuario->nombre ?? 'N/A') . " | Accion: {$log->accion} | Descripcion: {$log->descripcion}\n";
}
