<?php

namespace App\Services;

use App\Models\AlertaLegal;
use Carbon\Carbon;

class LegalValidatorService
{
    /**
     * Valida una Minuta y genera alertas si hay inconsistencias.
     *
     * @param \App\Models\Minuta $minuta
     * @return void
     */
    public function validarMinuta($minuta)
    {
        // 0) Limpiar alertas previas de esta minuta para que desaparezcan si el problema se solucionó
        AlertaLegal::where('alertable_id', $minuta->id)
            ->where('alertable_type', get_class($minuta))
            ->where('estado', 'activa')
            ->delete();

        // 1) Validar si la fecha del documento es futura
        if (Carbon::parse($minuta->fecha)->isFuture()) {
            AlertaLegal::create([
                'alertable_id' => $minuta->id,
                'alertable_type' => get_class($minuta),
                'tipo' => 'fecha_futura',
                'mensaje' => 'La fecha de la minuta (' . Carbon::parse($minuta->fecha)->format('d/m/Y') . ') es una fecha en el futuro.',
                'estado' => 'activa',
            ]);
        }

        // 2) Validar si el monto es atípicamente bajo (< $1000)
        if ($minuta->monto < 1000) {
            AlertaLegal::create([
                'alertable_id' => $minuta->id,
                'alertable_type' => get_class($minuta),
                'tipo' => 'monto_bajo',
                'mensaje' => 'El monto de transacción ($' . number_format($minuta->monto, 2) . ') es inusualmente bajo para la venta de un terreno.',
                'estado' => 'activa',
            ]);
        }

        // 3) Validar ausencia de documentos obligatorios (Folio e Inscripción DDRR)
        $folio = $minuta->terreno->folio;
        if (!$folio || $folio->estado !== 'verificado') {
            AlertaLegal::create([
                'alertable_id' => $minuta->id,
                'alertable_type' => get_class($minuta),
                'tipo' => 'documentacion_incompleta',
                'mensaje' => 'El terreno no tiene un Folio Real verificado en el sistema.',
                'estado' => 'activa',
            ]);
        } else {
            $inscripcion = $folio->inscripcionDerechosReales;
            if (!$inscripcion || $inscripcion->estado !== 'inscrito') {
                AlertaLegal::create([
                    'alertable_id' => $minuta->id,
                    'alertable_type' => get_class($minuta),
                    'tipo' => 'documentacion_incompleta',
                    'mensaje' => 'El Folio del terreno no cuenta con una Inscripción en Derechos Reales (DDRR) finalizada.',
                    'estado' => 'activa',
                ]);
            }
        }

        // 4) Diferencias entre Folio Real y datos catastrales (superficie)
        if ($folio) {
            $superficieFolio = (float) $folio->superficie;
            $superficieSistema = (float) $minuta->terreno->metros_cuadrados;
            
            // Margen de error de 0.5 metros cuadrados
            if (abs($superficieFolio - $superficieSistema) > 0.5) {
                AlertaLegal::create([
                    'alertable_id' => $minuta->id,
                    'alertable_type' => get_class($minuta),
                    'tipo' => 'inconsistencia_superficie',
                    'mensaje' => "Inconsistencia detectada: La superficie registrada en el sistema ({$superficieSistema} m²) no coincide con la del Folio Real ({$superficieFolio} m²).",
                    'estado' => 'activa',
                ]);
            }

            // 5) Gravámenes vigentes
            $gravamenesActivos = \App\Models\Gravamen::where('folio_id', $folio->id)->where('activo', true)->count();
            if ($gravamenesActivos > 0) {
                AlertaLegal::create([
                    'alertable_id' => $minuta->id,
                    'alertable_type' => get_class($minuta),
                    'tipo' => 'gravamen_vigente',
                    'mensaje' => 'Atención Crítica: El inmueble registra gravámenes vigentes (hipotecas, embargos). No es apto para transferencia libre.',
                    'estado' => 'activa',
                ]);
            }

            // 6) Trámites pendientes
            $tramitesPendientes = \App\Models\Tramite::where('folio_id', $folio->id)->whereIn('estado', ['pendiente', 'en_proceso'])->count();
            if ($tramitesPendientes > 0) {
                AlertaLegal::create([
                    'alertable_id' => $minuta->id,
                    'alertable_type' => get_class($minuta),
                    'tipo' => 'tramite_pendiente',
                    'mensaje' => 'Atención: El inmueble tiene trámites legales pendientes en curso que podrían afectar la venta.',
                    'estado' => 'activa',
                ]);
            }
        }
    }
}
