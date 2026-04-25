<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudVisita extends Model
{
    use HasFactory;

    protected $table = 'solicitud_visitas';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'terreno_id',
        'vendedor_id',
        'fecha_visita',
        'hora_inicio',
        'hora_fin',
        'estado',
        'motivo_rechazo',
        'fecha_aprobacion',
        'fecha_cancelacion',
        'aprobado_por',
        'cancelado_por',
    ];

    protected $casts = [
        'fecha_visita' => 'date',
        'fecha_aprobacion' => 'datetime',
        'fecha_cancelacion' => 'datetime',
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function terreno(): BelongsTo
    {
        return $this->belongsTo(Terreno::class, 'terreno_id');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'vendedor_id');
    }

    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'aprobado_por');
    }

    public function canceladoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cancelado_por');
    }

    // Scopes
    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobada($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopeRechazada($query)
    {
        return $query->where('estado', 'rechazada');
    }

    public function scopeCancelada($query)
    {
        return $query->where('estado', 'cancelada');
    }

    public function scopeDeVendedor($query, $vendedorId)
    {
        return $query->where('vendedor_id', $vendedorId);
    }

    public function scopeDeFecha($query, $fecha)
    {
        return $query->where('fecha_visita', $fecha);
    }

    // Métodos para cambiar estados
    public function aprobar($userId = null)
    {
        $this->estado = 'aprobada';
        $this->fecha_aprobacion = now();
        $this->aprobado_por = $userId;
        return $this->save();
    }

    public function rechazar($motivo, $userId = null)
    {
        $this->estado = 'rechazada';
        $this->motivo_rechazo = $motivo;
        $this->fecha_aprobacion = now();
        $this->aprobado_por = $userId;
        return $this->save();
    }

    public function cancelar($userId = null)
    {
        $this->estado = 'cancelada';
        $this->fecha_cancelacion = now();
        $this->cancelado_por = $userId;
        return $this->save();
    }

    // Verificar si se puede cancelar (más de 24 horas antes)
    public function puedeCancelar(): bool
    {
        $fechaHoraVisita = $this->fecha_visita->copy()->setTimeFromTimeString($this->hora_inicio);
        $fechaLimiteCancelacion = $fechaHoraVisita->copy()->subHours(24);
        
        return $this->estado === 'aprobada' && now()->isBefore($fechaLimiteCancelacion);
    }

    // Obtener el tiempo restante para la visita
    public function tiempoRestante()
    {
        $fechaHoraVisita = $this->fecha_visita->copy()->setTimeFromTimeString($this->hora_inicio);
        return now()->diffForHumans($fechaHoraVisita, ['syntax' => 1]);
    }
}
