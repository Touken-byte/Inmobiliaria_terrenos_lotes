<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Protocolizacion extends Model
{
    use HasFactory;

    protected $table = 'protocolizaciones';

    protected $fillable = [
        'minuta_id',
        'terreno_id',
        'user_id',
        'numero_protocolo',
        'fecha_protocolizacion',
        'archivo_testimonio',
        'estado',
        'observacion'
    ];

    protected $casts = [
        'fecha_protocolizacion' => 'date',
    ];

    public function minuta()
    {
        return $this->belongsTo(Minuta::class);
    }

    public function terreno()
    {
        return $this->belongsTo(Terreno::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}
