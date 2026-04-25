<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisponibilidadVendedor extends Model
{
    use HasFactory;

    protected $table = 'disponibilidad_vendedors';

    protected $fillable = [
        'vendedor_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'vendedor_id');
    }

    public function scopeDeVendedor($query, $vendedorId)
    {
        return $query->where('vendedor_id', $vendedorId);
    }

    public function scopePorDia($query, $dia)
    {
        return $query->where('dia_semana', $dia);
    }

    public function scopeActiva($query)
    {
        return $query->where('activo', true);
    }

    public function contieneHorario($horaInicio, $horaFin)
    {
        $inicio = strtotime($this->hora_inicio);
        $fin = strtotime($this->hora_fin);
        $inicioSolicitud = strtotime($horaInicio);
        $finSolicitud = strtotime($horaFin);

        return $inicioSolicitud >= $inicio && $finSolicitud <= $fin;
    }
}