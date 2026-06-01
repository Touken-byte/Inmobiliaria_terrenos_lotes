<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use App\Models\Terreno;
use App\Models\Alquiler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Auditoria;

class FavoritoController extends Controller
{
    /**
     * Página "Mis Favoritos" del comprador.
     */
    public function index()
    {
        $usuario = Auth::user();

        $favoritos = Favorito::where('usuario_id', $usuario->id)
            ->with('favoriteable')
            ->latest()
            ->get();

        // Separar por tipo y verificar que el relation favoriteable exista
        $terrenosFav = $favoritos->filter(fn($f) =>
            $f->favoriteable_type === Terreno::class && $f->favoriteable && ($f->favoriteable->tipo ?? 'terreno') === 'terreno'
        );

        $lotesFav = $favoritos->filter(fn($f) =>
            $f->favoriteable_type === Terreno::class && $f->favoriteable && ($f->favoriteable->tipo ?? '') === 'lote'
        );

        $alquileresFav = $favoritos->filter(fn($f) =>
            $f->favoriteable_type === Alquiler::class && $f->favoriteable
        );

        $total = $terrenosFav->count() + $lotesFav->count() + $alquileresFav->count();

        return view('comprador.favoritos', compact('terrenosFav', 'lotesFav', 'alquileresFav', 'total'));
    }

    /**
     * Toggle: agrega o quita un favorito (responde JSON para AJAX).
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'id'   => 'required|integer',
            'type' => 'required|in:terreno,lote,alquiler',
        ]);

        $usuario = Auth::user();
        $limite  = 50;

        // 'lote' y 'terreno' usan el mismo modelo Terreno
        $modelClass = $request->type === 'alquiler'
            ? Alquiler::class
            : Terreno::class;

        $item = $modelClass::findOrFail($request->id);

        // Validar disponibilidad y aprobación antes de agregar
        if ($modelClass === Terreno::class) {
            if ($item->estado !== 'aprobado' || $item->estado_lote !== 'disponible') {
                return response()->json([
                    'action'  => 'unavailable',
                    'message' => 'Este terreno no está disponible para favoritos'
                ], 400);
            }
        } elseif ($modelClass === Alquiler::class) {
            if ($item->estado !== 'disponible' || $item->estado_aprobacion !== 'aprobado') {
                return response()->json([
                    'action'  => 'unavailable',
                    'message' => 'Este alquiler no está disponible para favoritos'
                ], 400);
            }
        }

        $existente = Favorito::where('usuario_id', $usuario->id)
            ->where('favoriteable_id', $item->id)
            ->where('favoriteable_type', $modelClass)
            ->first();

        if ($existente) {
            $existente->delete();
            $tipoLabel = $request->type === 'alquiler' ? 'alquiler' : ($request->type === 'lote' ? 'lote' : 'terreno');
            Auditoria::registrar(
                'eliminar_favorito',
                $tipoLabel,
                $item->id,
                "El usuario {$usuario->nombre} eliminó de sus favoritos el {$tipoLabel} #{$item->id}"
            );
            return response()->json(['action' => 'removed', 'limite' => false]);
        }

        $totalActual = Favorito::where('usuario_id', $usuario->id)->count();
        if ($totalActual >= $limite) {
            return response()->json(['action' => 'limit', 'limite' => true], 200);
        }

        Favorito::create([
            'usuario_id'        => $usuario->id,
            'favoriteable_id'   => $item->id,
            'favoriteable_type' => $modelClass,
        ]);

        $tipoLabel = $request->type === 'alquiler' ? 'alquiler' : ($request->type === 'lote' ? 'lote' : 'terreno');
        Auditoria::registrar(
            'agregar_favorito',
            $tipoLabel,
            $item->id,
            "El usuario {$usuario->nombre} agregó a sus favoritos el {$tipoLabel} #{$item->id}"
        );

        return response()->json(['action' => 'added', 'limite' => false]);
    }
}