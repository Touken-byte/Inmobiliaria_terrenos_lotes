<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialEstadoLote extends Model
{
    use HasFactory;

    protected $table = 'historial_estado_lotes';

    public $timestamps = false; // Solo usamos fecha_cambio

    protected $fillable = [
        'terreno_id',
        'usuario_id',
        'estado_anterior',
        'estado_nuevo',
        'fecha_cambio',
    ];

    protected $casts = [
        'fecha_cambio' => 'datetime',
    ];

    public function terreno()
    {
        return $this->belongsTo(Terreno::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
