<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Folio;
use App\Models\HistorialAcceso;
use Illuminate\Http\Request;

class FolioAccesoController extends Controller
{
    public function registrar(Request $request)
    {
        $request->validate([
            'folio_id' => 'required|exists:folios,id',
            'tipo_consulta' => 'required|in:rapida,completa'
        ]);

        $acceso = HistorialAcceso::create([
            'folio_id' => $request->folio_id,
            'user_id' => auth()->id(),
            'tipo_consulta' => $request->tipo_consulta,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'fecha_acceso' => now(),
        ]);

        return response()->json(['success' => true, 'acceso_id' => $acceso->id]);
    }
}