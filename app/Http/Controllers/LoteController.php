<?php

namespace App\Http\Controllers;

use App\Models\Terreno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoteController extends Controller
{
    /**
     * Listado público de lotes (comprador).
     */
    public function index(Request $request)
    {
        $query = Terreno::where('estado', 'aprobado')
                        ->where('tipo', 'lote')
                        ->with('imagenes');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('ubicacion', 'LIKE', "%{$search}%");
            });
        }

        $terrenos = $query->orderBy('creado_en', 'desc')
                          ->paginate(10)
                          ->appends($request->query());

        // Reutilizamos la vista del catálogo/listado pero filtrada a lotes.
        return view('lotes.index', compact('terrenos'));
    }

    /**
     * Detalle público de un lote (reutiliza la vista de detalle de comprador).
     */
    public function detalle($id)
    {
        $terreno = Terreno::with(['imagenes', 'folio', 'terrenoPadre'])
            ->where('estado', 'aprobado')
            ->where('tipo', 'lote')
            ->findOrFail($id);

        $folio = null;
        if ($terreno->folio && $terreno->folio->estado === 'verificado') {
            $folio = $terreno->folio;
        }

        return view('comprador.detalle', compact('terreno', 'folio'));
    }
}
