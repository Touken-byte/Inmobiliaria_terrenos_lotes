<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gravamen extends Model
{
    use HasFactory;

    protected $table = 'gravamenes';

    protected $fillable = [
        'folio_id', 'tipo', 'descripcion', 'monto', 'fecha_registro', 'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_registro' => 'date',
        'monto' => 'decimal:2',
    ];

    public function folio()
    {
        return $this->belongsTo(Folio::class);
    }
}