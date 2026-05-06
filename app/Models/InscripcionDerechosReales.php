<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InscripcionDerechosReales extends Model
{
    use HasFactory;

    protected $table = 'inscripciones_derechos_reales';

    protected $fillable = [
        'folio_id',
        'numero_matricula',
        'comprobante_archivo',
        'comprobante_nombre_original',
        'fecha_entrada',
        'fecha_salida',
        'tasa_pagada',
        'estado',
        'observacion_admin',
        'revisado_por',
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
        'fecha_salida'  => 'date',
    ];

    public function folio()
    {
        return $this->belongsTo(Folio::class);
    }

    public function revisor()
    {
        return $this->belongsTo(Usuario::class, 'revisado_por');
    }
}