<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialEstadoAlquiler extends Model
{
    use HasFactory;

    protected $table = 'historial_estado_alquileres';

    protected $fillable = [
        'alquiler_id',
        'user_id',
        'estado_anterior',
        'estado_nuevo',
    ];

    public function alquiler()
    {
        return $this->belongsTo(Alquiler::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}
