<?php

namespace App\Helpers;

use App\Models\AuditoriaAcceso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Auditoria
{
    /**
     * Registra una acción en la auditoría.
     *
     * Uso: Auditoria::registrar('login')
     * Uso: Auditoria::registrar('aprobacion_terreno', 'terreno', $terreno->id, 'Terreno #5 aprobado')
     */
    public static function registrar(
        string $accion,
        string $entidad = null,
        int $entidadId = null,
        string $descripcion = null,
        int $usuarioId = null
    ): void {
        try {
            AuditoriaAcceso::create([
                'usuario_id'  => $usuarioId ?? Auth::id(),
                'accion'      => $accion,
                'entidad'     => $entidad,
                'entidad_id'  => $entidadId,
                'descripcion' => $descripcion,
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // No interrumpir el flujo si falla el log
            \Log::error('Error al registrar auditoría: ' . $e->getMessage());
        }
    }
}