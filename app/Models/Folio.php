<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folio extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_folio',
        'terreno_id',
        'superficie',
        'ubicacion',
        'colindancias',
        'estado',
        'verificado_por',
    ];

    public function scopeVerificado($query)
    {
        return $query->where('estado', 'verificado');
    }

    public function terreno()
    {
        return $this->belongsTo(Terreno::class);
    }

    public function propietarios()
    {
        return $this->hasMany(Propietario::class);
    }

    public function gravamenes()
    {
        return $this->hasMany(Gravamen::class);
    }

    public function restricciones()
    {
        return $this->hasMany(Restriccion::class);
    }

    public function tramites()
    {
        return $this->hasMany(Tramite::class);
    }

    public function historialAccesos()
    {
        return $this->hasMany(HistorialAcceso::class);
    }

    public function adminVerificador()
    {
        return $this->belongsTo(Usuario::class, 'verificado_por');
    }

    // ← NUEVO
    public function inscripcionDerechosReales()
    {
        return $this->hasOne(InscripcionDerechosReales::class);
    }

    public function propietariosVigentes()
    {
        return $this->propietarios()->where('vigente', true);
    }

    public function gravamenesActivos()
    {
        return $this->gravamenes()->where('activo', true);
    }

    public function restriccionesActivas()
    {
        return $this->restricciones()->where('activa', true);
    }

    public function tramitesPendientes()
    {
        return $this->tramites()->where('estado', 'pendiente');
    }
}