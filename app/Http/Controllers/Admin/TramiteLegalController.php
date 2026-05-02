<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Minuta;
use App\Models\ComprobanteIt;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TramiteLegalController extends Controller
{
    /**
     * Lista todos los trámites legales (minuta + IT) agrupados por vendedor.
     */
    public function index()
    {
        // Traemos todas las minutas con sus relaciones
        $minutas = Minuta::with(['vendedor', 'comprador', 'terreno'])
            ->latest()
            ->paginate(15);

        // Para cada minuta, buscamos su comprobante IT vinculado
        $minutas->getCollection()->transform(function ($minuta) {
            $minuta->comprobante = ComprobanteIt::where('minuta_id', $minuta->id)
                ->first();
            return $minuta;
        });

        return view('admin.tramites_legales.index', compact('minutas'));
    }

    /**
     * Aprobar la minuta.
     */
    public function aprobarMinuta($id)
    {
        $minuta = Minuta::findOrFail($id);
        $minuta->update(['estado' => 'aprobada', 'observacion' => null]);

        return redirect()->route('admin.tramites_legales.index')
            ->with('success', 'Minuta del vendedor ' . $minuta->vendedor->nombre . ' aprobada correctamente.');
    }

    /**
     * Rechazar la minuta con observación obligatoria.
     */
    public function rechazarMinuta(Request $request, $id)
    {
        $request->validate([
            'observacion' => 'required|string|min:5|max:1000',
        ], [
            'observacion.required' => 'Debe indicar el motivo del rechazo.',
        ]);

        $minuta = Minuta::findOrFail($id);
        $minuta->update([
            'estado'      => 'rechazada',
            'observacion' => $request->observacion,
        ]);

        return redirect()->route('admin.tramites_legales.index')
            ->with('success', 'Minuta rechazada. El vendedor fue notificado.');
    }

    /**
     * Aprobar el comprobante IT.
     */
    public function aprobarIT($id)
    {
        $comp = ComprobanteIt::findOrFail($id);
        $comp->update(['estado' => 'aprobado', 'observacion' => null]);

        return redirect()->route('admin.tramites_legales.index')
            ->with('success', 'Comprobante IT del vendedor ' . ($comp->usuario->nombre ?? 'N/D') . ' aprobado.');
    }

    /**
     * Rechazar el comprobante IT con observación.
     */
    public function rechazarIT(Request $request, $id)
    {
        $request->validate([
            'observacion' => 'required|string|min:5|max:1000',
        ]);

        $comp = ComprobanteIt::findOrFail($id);
        $comp->update([
            'estado'      => 'rechazado',
            'observacion' => $request->observacion,
        ]);

        return redirect()->route('admin.tramites_legales.index')
            ->with('success', 'Comprobante IT rechazado. Se solicitó corrección al vendedor.');
    }

    /**
     * Marcar el trámite completo (cuando Minuta e IT están aprobados).
     */
    public function finalizarTramite($id)
    {
        $minuta = Minuta::with('terreno')->findOrFail($id);
        $terreno = $minuta->terreno;
        $adminId = Auth::id();
        
        // 1. Marcamos ambos documentos como completados oficialmente
        $minuta->update(['estado' => 'completada']);
        
        $comp = ComprobanteIt::where('minuta_id', $minuta->id)->first();
        if ($comp) {
            $comp->update(['estado' => 'completado']);
        }

        // 2. REGISTRO OFICIAL DE VENTA: Actualizamos el estado del lote a 'vendido'
        if ($terreno) {
            $estadoAnterior = $terreno->estado_lote;
            $terreno->update([
                'estado_lote' => 'vendido',
                'actualizado_en' => now()
            ]);

            // Guardamos en el historial de estados
            \App\Models\HistorialEstadoLote::create([
                'terreno_id' => $terreno->id,
                'usuario_id' => $adminId,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => 'vendido',
                'fecha_cambio' => now(),
            ]);
        }
        
        return redirect()->route('admin.tramites_legales.index')
            ->with('success', '✅ Trámite finalizado. El lote ha sido marcado como VENDIDO oficialmente.');
    }

    public function verMinuta($id)
    {
        $minuta = Minuta::findOrFail($id);

        if (!$minuta->archivo || !Storage::disk('public')->exists($minuta->archivo)) {
            abort(404, 'El archivo de la minuta no fue encontrado.');
        }

        return Storage::disk('public')->response($minuta->archivo);
    }
}
