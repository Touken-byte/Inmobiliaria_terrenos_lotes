<?php

namespace App\Http\Controllers;

use App\Models\Folio;
use App\Models\Terreno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolioVendedorController extends Controller
{
    public function create($terrenoId)
    {
        $terreno = Terreno::where('id', $terrenoId)
            ->where('usuario_id', Auth::id())
            ->where('estado', 'aprobado')
            ->firstOrFail();

        if ($terreno->folio) {
            return redirect()->route('vendedor.folio.edit', $terrenoId)
                ->with('info', 'Este terreno ya tiene un folio registrado. Puedes editarlo aquí.');
        }

        return view('vendedor.folio.create', compact('terreno'));
    }

    public function store(Request $request, $terrenoId)
    {
        $terreno = Terreno::where('id', $terrenoId)
            ->where('usuario_id', Auth::id())
            ->where('estado', 'aprobado')
            ->firstOrFail();

        $request->validate([
            'numero_folio'  => 'required|string|max:50|unique:folios,numero_folio',
            'superficie'    => 'required|numeric|min:0',
            'ubicacion'     => 'required|string|max:500',
            'colindancias'  => 'nullable|string|max:1000',
        ], [
            'numero_folio.required'  => 'El número de folio es obligatorio.',
            'numero_folio.unique'    => 'Este número de folio ya existe en el sistema.',
            'superficie.required'    => 'La superficie es obligatoria.',
            'ubicacion.required'     => 'La ubicación es obligatoria.',
        ]);

        Folio::create([
            'numero_folio' => strtoupper(trim($request->numero_folio)),
            'terreno_id'   => $terreno->id,
            'superficie'   => $request->superficie,
            'ubicacion'    => $request->ubicacion,
            'colindancias' => $request->colindancias,
        ]);

        return redirect()->route('vendedor.terrenos.mis')
            ->with('success', 'Datos del folio registrados correctamente.');
    }

    public function edit($terrenoId)
    {
        $terreno = Terreno::where('id', $terrenoId)
            ->where('usuario_id', Auth::id())
            ->where('estado', 'aprobado')
            ->firstOrFail();

        $folio = Folio::where('terreno_id', $terreno->id)->firstOrFail();

        return view('vendedor.folio.edit', compact('terreno', 'folio'));
    }

    public function update(Request $request, $terrenoId)
    {
        $terreno = Terreno::where('id', $terrenoId)
            ->where('usuario_id', Auth::id())
            ->where('estado', 'aprobado')
            ->firstOrFail();

        $folio = Folio::where('terreno_id', $terreno->id)->firstOrFail();

        $request->validate([
            'numero_folio' => 'required|string|max:50|unique:folios,numero_folio,' . $folio->id,
            'superficie'   => 'required|numeric|min:0',
            'ubicacion'    => 'required|string|max:500',
            'colindancias' => 'nullable|string|max:1000',
        ]);

        $folio->update([
            'numero_folio' => strtoupper(trim($request->numero_folio)),
            'superficie'   => $request->superficie,
            'ubicacion'    => $request->ubicacion,
            'colindancias' => $request->colindancias,
        ]);

        return redirect()->route('vendedor.terrenos.mis')
            ->with('success', 'Datos del folio actualizados correctamente.');
    }
}