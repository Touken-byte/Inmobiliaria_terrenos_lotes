<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Terreno;
use Illuminate\Http\Request;

class LoteGeoController extends Controller
{
    public function geojson(Request $request)
    {
        // Cargar hasta 200 lotes aprobados, con sus coordenadas.
        // Asumo que tienes columnas 'latitud' y 'longitud' en terrenos.
        // Si no las tienes, deberás agregarlas. Por ahora, simulo con datos de ejemplo.
        $terrenos = Terreno::where('estado', 'aprobado')
            ->select('id', 'precio', 'metros_cuadrados', 'ubicacion', 'estado_lote', 'latitud', 'longitud')
            ->limit(200)
            ->get();

        $features = [];

        foreach ($terrenos as $terreno) {
            if (!$terreno->latitud || !$terreno->longitud) {
                continue; // Saltar si no tiene coordenadas
            }

            $color = ($terreno->estado_lote === 'vendido') ? '#808080' : '#4CAF50';

            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$terreno->longitud, $terreno->latitud]
                ],
                'properties' => [
                    'id' => $terreno->id,
                    'nombre' => $terreno->ubicacion,
                    'precio' => $terreno->precio,
                    'tamaño' => $terreno->metros_cuadrados,
                    'estado' => $terreno->estado_lote ?? 'disponible',
                    'color' => $color,
                ]
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    public function show($id)
    {
        $terreno = Terreno::with('imagenes')->where('estado', 'aprobado')->findOrFail($id);
        return response()->json($terreno);
    }
}