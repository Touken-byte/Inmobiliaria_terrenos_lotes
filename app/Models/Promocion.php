<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use HasFactory;

    protected $table = 'promociones';

    protected $fillable = [
        'promotable_type',
        'promotable_id',
        'titulo',
        'descripcion',
        'descuento_porcentaje',
        'estado',
        'motivo_rechazo',
    ];

    protected $casts = [
        'descuento_porcentaje' => 'decimal:2',
    ];

    /**
     * Get the owning promotable model (Terreno or Alquiler).
     */
    public function promotable()
    {
        return $this->morphTo();
    }
}
