<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TerrenoImagen extends Model
{
    use HasFactory;

    protected $table = 'terreno_imagenes';

    protected $fillable = [
        'terreno_id',
        'ruta_archivo',
        'orden',
    ];

    public $timestamps = false;

    protected $casts = [
        'fecha_subida' => 'datetime',
    ];

    public function terreno()
    {
        return $this->belongsTo(Terreno::class, 'terreno_id');
    }

    public function getUrlAttribute()
    {
        if (str_starts_with($this->ruta_archivo, '/storage/')) {
            $relativePath = substr($this->ruta_archivo, 9);
            return Storage::url($relativePath);
        }
        if (Storage::disk('public')->exists($this->ruta_archivo)) {
            return Storage::url($this->ruta_archivo);
        }
        return asset($this->ruta_archivo);
    }
}