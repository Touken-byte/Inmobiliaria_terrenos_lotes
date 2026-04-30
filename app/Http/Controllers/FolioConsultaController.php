<?php

namespace App\Http\Controllers;

use App\Models\Folio;
use App\Models\HistorialAcceso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FolioConsultaController extends Controller
{
    public function form()
    {
        return view('consulta-folio.form');
    }

    public function consultar(Request $request)
    {
        $request->validate([
            'numero_folio' => 'required|string|max:50',
            'tipo_consulta' => 'required|in:rapida,completa'
        ]);

        $folio = Folio::where('numero_folio', $request->numero_folio)->first();

        if (!$folio) {
            return back()->with('error', 'Número de folio no encontrado.');
        }

        // Registrar acceso
        HistorialAcceso::create([
            'folio_id' => $folio->id,
            'user_id' => auth()->id(),
            'tipo_consulta' => $request->tipo_consulta,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'fecha_acceso' => now(),
        ]);

        if ($request->tipo_consulta === 'rapida') {
            return view('consulta-folio.resultado-rapido', compact('folio'));
        } else {
            return redirect()->route('folio.completo', $folio->id);
        }
    }

    public function completo($id)
    {
        $folio = Folio::with([
            'propietarios',
            'gravamenes',
            'restricciones',
            'tramites'
        ])->findOrFail($id);

        // Registrar siempre el acceso — cumple requisito IN-L06
        HistorialAcceso::create([
            'folio_id' => $folio->id,
            'user_id' => auth()->id(),
            'tipo_consulta' => 'completa',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'fecha_acceso' => now(),
        ]);

        return view('consulta-folio.completo', compact('folio'));
    }
}