<?php

namespace App\Http\Controllers;

use App\Models\Terreno;
use Illuminate\Support\Facades\Auth;

class MapaController extends Controller
{
    public function index()
    {
        // Obtener terrenos aprobados con coordenadas e imágenes
        $terrenos = Terreno::where('estado', 'aprobado')
            ->with(['imagenes' => function ($q) {
                $q->orderByDesc('es_portada');
            }])
            ->limit(200)
            ->get()
            ->map(function ($terreno) {
                return [
                    'id'              => $terreno->id,
                    'nombre_lote'     => $terreno->nombre_lote ?? null,
                    'ubicacion'       => $terreno->ubicacion,
                    'precio'          => $terreno->precio,
                    'metros_cuadrados'=> $terreno->metros_cuadrados,
                    'latitud'         => $terreno->latitud,
                    'longitud'        => $terreno->longitud,
                    'estado_lote'     => $terreno->estado_lote ?? 'disponible',
                    'imagenes'        => $terreno->imagenes->map(function ($img) {
                        return [
                            'nombre_archivo' => $img->nombre_archivo,
                            'es_portada'     => $img->es_portada,
                        ];
                    }),
                ];
            });

        return view('mapa', compact('terrenos'));
    }
}