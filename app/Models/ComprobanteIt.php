<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobanteIt extends Model
{
    protected $table = 'comprobantes_it';

    protected $fillable = [
        'user_id',
        'numero_recibo',
        'fecha_pago',
        'monto',
        'archivo',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}
