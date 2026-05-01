<?php

namespace App\Http\Controllers;

use App\Models\Alquiler;
use App\Models\Imagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlquilerController extends Controller
{
    public function catalogo()
    {
        $alquileres = Alquiler::where('estado', 'disponible')
                              ->where('estado_aprobacion', 'aprobado')
                              ->latest()
                              ->paginate(12);
        return view('Alquiler.index', compact('alquileres'));
    }

    public function detalle($id)
    {
        $alquiler = Alquiler::findOrFail($id);
        return view('Alquiler.show', compact('alquiler'));
    }

    public function index()
    {
        $alquileres = Alquiler::where('user_id', auth()->id())->latest()->paginate(10);
        return view('vendedor.alquileres.index', compact('alquileres'));
    }

    public function create()
    {
        return view('vendedor.alquileres.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'precio_mensual' => 'required|numeric|min:0',
            'metros_cuadrados' => 'required|numeric|min:0',
            'habitaciones' => 'required|integer|min:1',
            'banos' => 'required|integer|min:1',
            'descripcion' => 'required|string',
            'servicios_incluidos' => 'nullable|array',
            'disponible_desde' => 'required|date',
            'imagenes' => 'nullable|array|max:5',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $alquiler = Alquiler::create([
            'titulo' => $request->titulo,
            'ubicacion' => $request->ubicacion,
            'precio_mensual' => $request->precio_mensual,
            'metros_cuadrados' => $request->metros_cuadrados,
            'habitaciones' => $request->habitaciones,
            'banos' => $request->banos,
            'descripcion' => $request->descripcion,
            'servicios_incluidos' => $request->servicios_incluidos,
            'disponible_desde' => $request->disponible_desde,
            'user_id' => auth()->id(),
            'estado' => 'disponible',
            'estado_aprobacion' => 'pendiente',
        ]);

        if ($request->hasFile('imagenes')) {
            $orden = 1;
            foreach ($request->file('imagenes') as $file) {
                $path = $file->store('alquileres', 'public');
                $alquiler->imagenes()->create([
                    'ruta_archivo' => '/storage/' . $path,
                    'orden' => $orden++
                ]);
            }
        }

        return redirect()->route('vendedor.alquileres.index')->with('success', 'Publicación creada exitosamente.');
    }

    public function misAlquileres()
    {
        return $this->index();
    }

    public function edit($id)
    {
        $alquiler = Alquiler::where('user_id', auth()->id())->findOrFail($id);
        return view('vendedor.alquileres.edit', compact('alquiler'));
    }

    public function update(Request $request, $id)
    {
        $alquiler = Alquiler::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'precio_mensual' => 'required|numeric|min:0',
            'metros_cuadrados' => 'required|numeric|min:0',
            'habitaciones' => 'required|integer|min:1',
            'banos' => 'required|integer|min:1',
            'descripcion' => 'required|string',
            'servicios_incluidos' => 'nullable|array',
            'disponible_desde' => 'required|date',
            'imagenes' => 'nullable|array|max:5',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $alquiler->update([
            'titulo' => $request->titulo,
            'ubicacion' => $request->ubicacion,
            'precio_mensual' => $request->precio_mensual,
            'metros_cuadrados' => $request->metros_cuadrados,
            'habitaciones' => $request->habitaciones,
            'banos' => $request->banos,
            'descripcion' => $request->descripcion,
            'servicios_incluidos' => $request->servicios_incluidos,
            'disponible_desde' => $request->disponible_desde,
        ]);

        if ($request->hasFile('imagenes')) {
            $orden = $alquiler->imagenes()->max('orden') + 1;
            foreach ($request->file('imagenes') as $file) {
                $path = $file->store('alquileres', 'public');
                $alquiler->imagenes()->create([
                    'ruta_archivo' => '/storage/' . $path,
                    'orden' => $orden++
                ]);
            }
        }

        return redirect()->route('vendedor.alquileres.index')->with('success', 'Publicación actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $alquiler = Alquiler::where('user_id', auth()->id())->findOrFail($id);
        
        foreach ($alquiler->imagenes as $imagen) {
            $ruta = str_replace('/storage/', '', $imagen->ruta_archivo);
            Storage::disk('public')->delete($ruta);
            $imagen->delete();
        }

        $alquiler->delete();

        return redirect()->route('vendedor.alquileres.index')->with('success', 'Publicación eliminada correctamente.');
    }

    public function toggleEstado($id)
    {
        $alquiler = Alquiler::where('user_id', auth()->id())->findOrFail($id);
        
        $alquiler->estado = $alquiler->estado === 'disponible' ? 'alquilado' : 'disponible';
        $alquiler->save();

        return redirect()->route('vendedor.alquileres.index')->with('success', 'Estado de la publicación actualizado.');
    }
}