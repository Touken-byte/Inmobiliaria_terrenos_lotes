<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialAcceso extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio_id', 'user_id', 'tipo_consulta', 'ip_address', 'user_agent', 'fecha_acceso'
    ];

    protected $casts = [
        'fecha_acceso' => 'datetime',
    ];

    public function folio()
    {
        return $this->belongsTo(Folio::class);
    }

    public function user()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}