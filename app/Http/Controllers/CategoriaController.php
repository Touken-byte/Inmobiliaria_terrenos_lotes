<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    // Solo admin puede acceder — protegido por middleware en rutas

    public function index()
    {
        $categorias = Categoria::withCount([
            'terrenos as terrenos_activos' => fn($q) => $q->where('estado', 'aprobado'),
            'alquileres as alquileres_activos' => fn($q) => $q->where('estado', 'disponible'),
            'terrenos as terrenos_vendidos' => fn($q) => $q->where('estado', 'vendido'),
            'alquileres as alquileres_cerrados' => fn($q) => $q->where('estado', 'alquilado'),
        ])->latest()->get();

        return view('admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('admin.categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:50|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:255',
            'color'       => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'tipo_propiedad' => 'required|in:terreno,lote,alquiler,todos',
            'activa'      => 'boolean',
        ], [
            'nombre.unique'    => 'Ya existe una categoría con ese nombre.',
            'nombre.max'       => 'El nombre no puede superar los 50 caracteres.',
            'color.regex'      => 'El color debe ser un código hexadecimal válido (ej: #3d7ef5).',
            'tipo_propiedad.required' => 'El tipo de propiedad es obligatorio.',
            'tipo_propiedad.in' => 'El tipo de propiedad seleccionado no es válido.',
        ]);

        Categoria::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'color'       => $request->color,
            'tipo_propiedad' => $request->tipo_propiedad,
            'activa'      => $request->boolean('activa', true),
        ]);

        return redirect()->route('admin.categorias.index')
                         ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Categoria $categoria)
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre'      => 'required|string|max:50|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string|max:255',
            'color'       => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'tipo_propiedad' => 'required|in:terreno,lote,alquiler,todos',
            'activa'      => 'boolean',
        ], [
            'nombre.unique' => 'Ya existe otra categoría con ese nombre.',
            'color.regex'   => 'El color debe ser un código hexadecimal válido (ej: #3d7ef5).',
            'tipo_propiedad.required' => 'El tipo de propiedad es obligatorio.',
            'tipo_propiedad.in' => 'El tipo de propiedad seleccionado no es válido.',
        ]);

        // Bloquear desactivación si tiene lotes activos
        $activando = $request->boolean('activa', false);
        if (!$activando && $categoria->lotes_activos_count > 0) {
            return back()->withErrors([
                'activa' => 'No se puede desactivar: la categoría tiene lotes activos asociados.'
            ])->withInput();
        }

        $categoria->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'color'       => $request->color,
            'tipo_propiedad' => $request->tipo_propiedad,
            'activa'      => $activando,
        ]);

        return redirect()->route('admin.categorias.index')
                         ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        // Bloquear eliminación si tiene lotes activos
        if ($categoria->lotes_activos_count > 0) {
            return back()->with('error', 'No se puede eliminar: la categoría tiene lotes activos. Desasócialos primero.');
        }

        $categoria->delete();

        return redirect()->route('admin.categorias.index')
                         ->with('success', 'Categoría eliminada correctamente.');
    }
}