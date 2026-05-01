<?php

namespace App\Http\Controllers;

use App\Models\Alquiler;
use Illuminate\Http\Request;

class AlquilerController extends Controller
{
    public function catalogo()
    {
        $alquileres = Alquiler::where('estado', 'disponible')->latest()->paginate(12);
        return view('Alquiler.index', compact('alquileres'));
    }

    public function detalle($id)
    {
        $alquiler = Alquiler::findOrFail($id);
        return view('Alquiler.show', compact('alquiler'));
    }

    public function index()
    {
        $alquileres = Alquiler::where('user_id', auth()->id())->paginate(10);
        return view('vendedor.alquileres.index', compact('alquileres'));
    }

    public function create()
    {
        return view('vendedor.alquileres.create');
    }

    public function store(Request $request) {}

    public function misAlquileres()
    {
        return $this->index();
    }

    public function edit($id)
    {
        $alquiler = Alquiler::where('user_id', auth()->id())->findOrFail($id);
        return view('vendedor.alquileres.edit', compact('alquiler'));
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}

    public function toggleEstado($id) {}
}