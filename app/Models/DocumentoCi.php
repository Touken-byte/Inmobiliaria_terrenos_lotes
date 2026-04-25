<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoCi extends Model
{
    use HasFactory;

    protected $table = 'documentos_ci';

    protected $fillable = [
        'usuario_id',
        'nombre_archivo',
        'nombre_original',
        'tipo_mime',
        'tamano',
        'activo',
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
        'activo' => 'boolean',
    ];

    public $timestamps = false; // La DB controla fecha_subida

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
