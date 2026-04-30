<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tramite extends Model
{
    use HasFactory;
    protected $table = 'tramites';
    protected $fillable = [
        'folio_id', 'nombre_tramite', 'estado', 'fecha_solicitud', 'fecha_resolucion', 'observaciones'
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_resolucion' => 'date',
    ];

    public function folio()
    {
        return $this->belongsTo(Folio::class);
    }
}