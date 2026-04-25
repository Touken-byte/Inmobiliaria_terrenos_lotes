<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoPropiedad extends Model
{
    use HasFactory;

    protected $table = 'documentos_propiedad';

    protected $fillable = [
        'terreno_id',
        'nombre_archivo',
        'nombre_original',
        'tipo_mime',
        'tamano',
        'estado',
    ];

    public $timestamps = false;

    protected $casts = [
        'tamano' => 'integer',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime',
    ];

    /**
     * Relación con el terreno (lote) al que pertenece el documento.
     */
    public function terreno()
    {
        return $this->belongsTo(Terreno::class, 'terreno_id');
    }
}