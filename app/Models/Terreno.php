<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terreno extends Model
{
    use HasFactory;

    protected $table = 'terrenos';

    protected $fillable = [
        'usuario_id',
        'precio',
        'metros_cuadrados',
        'ubicacion',
        'descripcion',
        'estado',
        'motivo_rechazo',
        'id_admin_aprobador',
        'estado_lote',
        'portada_id',
        'latitud',
        'longitud',
    ];

    public $timestamps = false;

    protected $casts = [
        'precio'          => 'decimal:2',
        'metros_cuadrados'=> 'decimal:2',
        'creado_en'       => 'datetime',
        'actualizado_en'  => 'datetime',
        'latitud'         => 'float',   // ← NUEVO: fuerza número flotante
        'longitud'        => 'float',   // ← NUEVO: fuerza número flotante
    ];

    public function vendedor()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function adminAprobador()
    {
        return $this->belongsTo(Usuario::class, 'id_admin_aprobador');
    }

    public function imagenes()
    {
        return $this->hasMany(TerrenoImagen::class, 'terreno_id')->orderBy('orden');
    }

    public function portada()
    {
        return $this->belongsTo(TerrenoImagen::class, 'portada_id');
    }

    public function documentoPropiedad()
    {
        return $this->hasOne(DocumentoPropiedad::class, 'terreno_id');
    }

    public function folio()
    {
        return $this->hasOne(Folio::class, 'terreno_id');
    }
}