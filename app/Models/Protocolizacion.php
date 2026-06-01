<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Protocolizacion extends Model
{
    protected $table = 'protocolizaciones';

    protected $fillable = [
        'minuta_id',
        'terreno_id',
        'vendedor_id',
        'numero_protocolo',
        'fecha_protocolizacion',
        'archivo_testimonio',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'fecha_protocolizacion' => 'date',
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function minuta()
    {
        return $this->belongsTo(Minuta::class, 'minuta_id');
    }

    public function terreno()
    {
        return $this->belongsTo(Terreno::class, 'terreno_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(Usuario::class, 'vendedor_id');
    }
}
