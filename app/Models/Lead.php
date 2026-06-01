<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'terreno_id',
        'alquiler_id',
        'comprador_id',
        'vendedor_id',
        'nombre',
        'telefono',
        'mensaje',
        'estado',
        'fecha_contacto',
    ];

    protected $casts = [
        'fecha_contacto' => 'datetime',
    ];

    public function terreno()
    {
        return $this->belongsTo(Terreno::class);
    }

    public function alquiler()
    {
        return $this->belongsTo(Alquiler::class);
    }

    public function comprador()
    {
        return $this->belongsTo(Usuario::class, 'comprador_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(Usuario::class, 'vendedor_id');
    }

    public function chat()
    {
        return $this->hasOne(Chat::class);
    }
}
