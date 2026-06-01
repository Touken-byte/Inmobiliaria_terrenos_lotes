<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'tipo_propiedad',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function terrenos()
    {
        return $this->hasMany(Terreno::class, 'categoria_id');
    }

    public function alquileres()
    {
        return $this->hasMany(Alquiler::class, 'categoria_id');
    }

    // Total de lotes activos (terrenos aprobados + alquileres disponibles)
    public function getLotesActivosCountAttribute(): int
    {
        return $this->terrenos()->where('estado', 'aprobado')->count()
             + $this->alquileres()->where('estado', 'disponible')->count();
    }
}