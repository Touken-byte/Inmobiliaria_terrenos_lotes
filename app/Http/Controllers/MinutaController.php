<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Minuta;
use App\Models\Terreno;
use App\Models\Usuario;

class MinutaController extends Controller
{
    public function create()
    {
        $terrenos = Terreno::all();
        $compradores = Usuario::where('rol', 'comprador')->get();
        $vendedores = Usuario::where('rol', 'vendedor')->get();

        return view('minutas.create', compact('terrenos', 'compradores', 'vendedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'terreno_id' => 'required|exists:terrenos,id',
            'comprador_id' => 'required|exists:usuarios,id',
            'vendedor_id' => 'required|exists:usuarios,id|different:comprador_id',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $ruta = null;

        if ($request->hasFile('archivo')) {
            $ruta = $request->file('archivo')->store('minutas', 'public');
        }

        Minuta::create([
            'terreno_id' => $request->terreno_id,
            'comprador_id' => $request->comprador_id,
            'vendedor_id' => $request->vendedor_id,
            'monto' => $request->monto,
            'fecha' => $request->fecha,
            'archivo' => $ruta,
        ]);

        return redirect()->back()->with('success', 'Minuta registrada correctamente');
    }
     public function index()
    {
        $minutas = Minuta::with(['terreno', 'comprador', 'vendedor'])->latest()->get();

        return view('minutas.index', compact('minutas'));
    }
}
