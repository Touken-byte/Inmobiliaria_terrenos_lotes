<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialVerificacion extends Model
{
    use HasFactory;

    protected $table = 'historial_verificacion';

    protected $fillable = [
        'usuario_id',
        'admin_id',
        'accion',
        'comentario',
        'documento_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public $timestamps = false; // La DB controla fecha

    // Relación con el vendedor (usuario)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Relación con el administrador
    public function admin()
    {
        return $this->belongsTo(Usuario::class, 'admin_id');
    }

    // Relación con el documento revisado
    public function documento()
    {
        return $this->belongsTo(DocumentoCi::class, 'documento_id');
    }
}
