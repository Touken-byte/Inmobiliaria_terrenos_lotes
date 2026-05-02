<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Terreno;
use App\Models\Usuario;

class Minuta extends Model
{
    protected $fillable = [
        'terreno_id',
        'comprador_id',
        'vendedor_id',
        'monto',
        'fecha',
        'archivo',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    public function terreno()
    {
        return $this->belongsTo(Terreno::class);
    }

    public function comprador()
    {
        return $this->belongsTo(Usuario::class, 'comprador_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(Usuario::class, 'vendedor_id');
    }
}
