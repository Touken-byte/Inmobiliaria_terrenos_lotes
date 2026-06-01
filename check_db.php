<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$alertas = \App\Models\AlertaLegal::all();
echo "Total Alertas en DB: " . $alertas->count() . "\n";
foreach($alertas as $a) {
    echo "ID: {$a->id} | Alertable: {$a->alertable_type} #{$a->alertable_id} | Tipo: {$a->tipo} | Estado: {$a->estado}\n";
}
