<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Terreno;
use App\Models\Minuta;
use App\Models\AlertaLegal;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Folio;

class VendedorExpedienteController extends Controller
{
    public function show($id)
    {
        $user = Auth::user();

        $terreno = Terreno::with(['folio.inscripcionDerechosReales', 'folio.gravamenes', 'folio.restricciones'])
            ->where('id', $id)
            ->where('usuario_id', $user->id)
            ->whereIn('tipo', ['terreno', 'lote'])
            ->firstOrFail();

        // Minuta más reciente vinculada al terreno
        $minuta = Minuta::with(['comprobante', 'protocolizacion', 'alertasLegales'])
            ->where('terreno_id', $terreno->id)
            ->latest()
            ->first();

        // Alertas directamente asociadas al terreno (si las hubiera)
        $alertasTerreno = AlertaLegal::where('alertable_type', get_class($terreno))
            ->where('alertable_id', $terreno->id)
            ->get();

        // Calcular secciones completadas
        $completadas = 0;

        if ($minuta) $completadas++;
        if ($minuta && $minuta->comprobante) $completadas++;

        // Plazo IT: días hábiles entre minuta.fecha y comprobante.fecha_pago
        $plazoOk = false;
        if ($minuta && $minuta->comprobante && $minuta->fecha && $minuta->comprobante->fecha_pago) {
            $inicio = Carbon::parse($minuta->fecha);
            $fin = Carbon::parse($minuta->comprobante->fecha_pago);
            $diasHabiles = $fin->diffInDaysFiltered(function ($date) { return $date->isWeekday(); }, $inicio, $fin);
            if ($diasHabiles <= 10) {
                $plazoOk = true;
                $completadas++;
            }
        }

        if ($minuta && $minuta->protocolizacion) $completadas++;
        if ($terreno->folio && $terreno->folio->inscripcionDerechosReales) $completadas++;
        if ($terreno->folio) $completadas++;

        // Alertas pendientes: si no hay alertas pendientes, cuenta como completada
        $pendingAlerts = 0;
        if ($terreno->folio) {
            $pendingAlerts += AlertaLegal::where('alertable_type', Folio::class)
                ->where('alertable_id', $terreno->folio->id)
                ->where('estado', 'pendiente')
                ->count();
        }
        $pendingAlerts += $alertasTerreno->where('estado', 'pendiente')->count();

        if ($pendingAlerts === 0) $completadas++;

        return view('vendedor.expediente', compact('terreno', 'minuta', 'alertasTerreno', 'completadas'));
    }
}
