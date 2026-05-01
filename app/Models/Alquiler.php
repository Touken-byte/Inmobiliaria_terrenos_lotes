<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alquiler extends Model
{
    use HasFactory;

    protected $table = 'alquileres';

    protected $fillable = [
        'titulo',
        'ubicacion',
        'precio_mensual',
        'metros_cuadrados',
        'habitaciones',
        'banos',
        'descripcion',
        'servicios_incluidos',
        'disponible_desde',
        'user_id',
        'estado',
        'estado_aprobacion',
    ];

    protected $casts = [
        'servicios_incluidos' => 'array',
        'disponible_desde'    => 'date',
    ];

    public function imagenes()
    {
        return $this->morphMany(\App\Models\Imagen::class, 'imageable');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Usuario::class, 'user_id');
    }
}