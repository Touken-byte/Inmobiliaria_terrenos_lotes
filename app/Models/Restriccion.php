<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restriccion extends Model
{
    use HasFactory;

    protected $table = 'restricciones';

    protected $fillable = [
        'folio_id', 'tipo', 'descripcion', 'fecha_inicio', 'fecha_fin', 'activa'
    ];

    protected $casts = [
        'activa' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function folio()
    {
        return $this->belongsTo(Folio::class);
    }
}