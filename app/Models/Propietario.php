<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propietario extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio_id', 'nombre_completo', 'tipo_documento', 'numero_documento',
        'vigente', 'fecha_desde', 'fecha_hasta'
    ];

    protected $casts = [
        'vigente' => 'boolean',
        'fecha_desde' => 'date',
        'fecha_hasta' => 'date',
    ];

    public function folio()
    {
        return $this->belongsTo(Folio::class);
    }
}