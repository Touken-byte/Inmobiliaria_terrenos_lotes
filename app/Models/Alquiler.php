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
        'categoria_id',
        'latitud',
        'longitud',
        'portada_id',
    ];

    protected $casts = [
        'servicios_incluidos' => 'array',
        'disponible_desde' => 'date',
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    public function imagenes()
    {
        return $this->morphMany(\App\Models\Imagen::class, 'imageable');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Usuario::class, 'user_id');
    }

    public function categoria()
    {
        return $this->belongsTo(\App\Models\Categoria::class, 'categoria_id');
    }

    public function esFavorito($userId = null)
    {
        $userId = $userId ?? \Illuminate\Support\Facades\Auth::id();
        return \App\Models\Favorito::where('usuario_id', $userId)
            ->where('favoriteable_id', $this->id)
            ->where('favoriteable_type', self::class)
            ->exists();
    }

    public function promociones()
    {
        return $this->morphMany(\App\Models\Promocion::class, 'promotable');
    }

    public function promocion()
    {
        return $this->morphOne(\App\Models\Promocion::class, 'promotable')->where('estado', 'aprobado');
    }

    public function historialEstados()
    {
        return $this->hasMany(\App\Models\HistorialEstadoAlquiler::class)->orderBy('created_at', 'desc');
    }

    public function portada()
    {
        return $this->belongsTo(\App\Models\Imagen::class, 'portada_id');
    }

    public function getPortadaUrlAttribute()
    {
        if ($this->portada) {
            return asset($this->portada->ruta_archivo);
        }
        $primera = $this->imagenes()->orderBy('orden')->first();
        if ($primera) {
            return asset($primera->ruta_archivo);
        }
        return null;
    }
}