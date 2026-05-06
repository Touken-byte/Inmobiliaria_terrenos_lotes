<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaAcceso extends Model
{
    use HasFactory;

    protected $table = 'auditoria_accesos';

    protected $fillable = [
        'usuario_id',
        'accion',
        'entidad',
        'entidad_id',
        'descripcion',
        'ip_address',
        'user_agent',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}