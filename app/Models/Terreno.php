<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Terreno extends Model
{
    use HasFactory;

    protected $table = 'terrenos';

    protected $fillable = [
        'usuario_id',
        'tipo',
        'parent_id',
        'nombre',
        'codigo',
        'pais',
        'departamento',
        'provincia',
        'municipio',
        'zona_barrio',
        'direccion',
        'tipo_terreno',
        'precio',
        'metros_cuadrados',
        'largo',
        'ancho',
        'topografia',
        'ubicacion',
        'descripcion',
        'agua_potable',
        'energia_electrica',
        'alcantarillado',
        'gas_domiciliario',
        'internet',
        'moneda',
        'forma_pago',
        'numero_matricula',
        'codigo_catastral',
        'numero_lote',
        'codigo_lote',
        'manzano_bloque',
        'frente',
        'fondo',
        'colinda_norte',
        'colinda_sur',
        'colinda_este',
        'colinda_oeste',
        'categoria_id',
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
        'precio'           => 'decimal:2',
        'metros_cuadrados' => 'decimal:2',
        'creado_en'        => 'datetime',
        'actualizado_en'   => 'datetime',
        'latitud'          => 'float',
        'longitud'         => 'float',
    ];

    public function lotes()
    {
        return $this->hasMany(Terreno::class, 'parent_id')->where('tipo', 'lote');
    }

    public function terrenoPadre()
    {
        return $this->belongsTo(Terreno::class, 'parent_id')->where('tipo', 'terreno');
    }

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

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function promociones()
    {
        return $this->morphMany(Promocion::class, 'promotable');
    }

    public function promocion()
    {
        return $this->morphOne(Promocion::class, 'promotable')->where('estado', 'aprobado');
    }

    public function esFavorito($userId = null)
    {
        $userId = $userId ?? Auth::id();
        return \App\Models\Favorito::where('usuario_id', $userId)
            ->where('favoriteable_id', $this->id)
            ->where('favoriteable_type', self::class)
            ->exists();
    }
}