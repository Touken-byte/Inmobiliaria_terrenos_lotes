<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertaLegal extends Model
{
    use HasFactory;

    protected $table = 'alertas_legales';

    protected $fillable = [
        'alertable_id',
        'alertable_type',
        'tipo',
        'mensaje',
        'estado',
    ];

    public function alertable()
    {
        return $this->morphTo();
    }
}
