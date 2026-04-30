<?php

namespace App\Http\Controllers;

use App\Models\Terreno;
use App\Models\TerrenoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TerrenoController extends Controller
{
    // ==========================================
    // VISTA COMPRADOR: CATÁLOGO / MARKETPLACE
    // ==========================================
    public function catalogo(Request $request)
    {
        $query = Terreno::where('estado', 'aprobado')->with('imagenes');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('ubicacion', 'LIKE', "%{$search}%");
            });
        }

        $terrenos = $query->orderBy('creado_en', 'desc')
                          ->paginate(8)
                          ->appends($request->query());

        return view('comprador.catalogo', compact('terrenos'));
    }

    public function detalle($id)
    {
        // Buscar el terreno asegurando que esté aprobado
        $terreno = Terreno::with('imagenes')->where('estado', 'aprobado')->findOrFail($id);

        return view('comprador.detalle', compact('terreno'));
    }

    // Mostrar y buscar terrenos disponibles (Público)
    public function index(Request $request)
    {
        $query = Terreno::where('estado', 'aprobado')->with('imagenes');

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

        return view('terrenos.index', compact('terrenos'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->estado_verificacion !== 'verificado') {
            return redirect()->route('vendedor.dashboard')->with('error', 'Debe estar verificado para poder publicar un terreno. Verifique su identidad primero.');
        }

        return view('vendedor.terrenos.create');
    }

    // Listar terrenos del vendedor
    public function misTerrenos()
    {
        $user = auth()->user();
        $terrenos = Terreno::where('usuario_id', $user->id)
            ->with('imagenes')
            ->orderBy('creado_en', 'desc')
            ->get();
        return view('vendedor.mis_terrenos', compact('terrenos'));
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $user = auth()->user();
        $terreno = Terreno::where('id', $id)->where('usuario_id', $user->id)->firstOrFail();
        return view('vendedor.editar_terreno', compact('terreno'));
    }

    // Actualizar terreno (solo si está pendiente o rechazado)
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $terreno = Terreno::where('id', $id)->where('usuario_id', $user->id)->firstOrFail();

        // Solo permitir editar si está pendiente o rechazado
        if (!in_array($terreno->estado, ['pendiente', 'rechazado'])) {
            return redirect()->route('vendedor.terrenos.mis')->with('error', 'No puedes editar un terreno ya aprobado.');
        }

        $request->validate([
            'precio' => 'required|numeric|min:0',
            'metros_cuadrados' => 'required|numeric|min:0',
            'ubicacion' => 'required|string|max:255',
            'descripcion' => 'required|string|min:50',
            'imagenes' => 'nullable|array|max:10',
            'imagenes.*' => 'file|mimes:jpg,jpeg,png|max:5120'
        ]);

        $terreno->update([
            'precio' => $request->precio,
            'metros_cuadrados' => $request->metros_cuadrados,
            'ubicacion' => $request->ubicacion,
            'descripcion' => $request->descripcion,
            'latitud' => $request->latitud ?: null,
            'longitud' => $request->longitud ?: null,
            'actualizado_en' => now(),
        ]);

        // Si se suben nuevas imágenes, las agregamos (sin eliminar las existentes)
        if ($request->hasFile('imagenes')) {
            $orden = $terreno->imagenes->max('orden') + 1;
            foreach ($request->file('imagenes') as $file) {
                $path = $file->store('terrenos', 'public');
                TerrenoImagen::create([
                    'terreno_id' => $terreno->id,
                    'ruta_archivo' => '/storage/' . $path,
                    'orden' => $orden++
                ]);
            }
        }

        return redirect()->route('vendedor.terrenos.mis')->with('success', 'Terreno actualizado correctamente.');
    }

    public function eliminarImagen($id)
    {
        $imagen = TerrenoImagen::findOrFail($id);
        $terreno = $imagen->terreno;

        // Verificar que el terreno pertenezca al vendedor autenticado
        if ($terreno->usuario_id !== auth()->id()) {
            abort(403);
        }

        // Si la imagen a eliminar es la portada, actualizar el terreno
        if ($terreno->portada_id === $imagen->id) {
            $terreno->portada_id = null;
            $terreno->save();
        }

        // Eliminar archivo físico del disco public
        $ruta = str_replace('/storage/', '', $imagen->ruta_archivo);
        \Storage::disk('public')->delete($ruta);

        // Eliminar registro
        $imagen->delete();

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->estado_verificacion !== 'verificado') {
            return redirect()->route('vendedor.dashboard')->with('error', 'Debe estar verificado para publicar un terreno.');
        }

        $request->validate([
            'precio' => 'required|numeric|min:0',
            'metros_cuadrados' => 'required|numeric|min:0',
            'ubicacion' => 'required|string|max:255',
            'descripcion' => 'required|string|min:50',
            'imagenes' => 'required|array|min:1|max:10',
            'imagenes.*' => 'file|mimes:jpg,jpeg,png|max:5120'
        ], [
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'metros_cuadrados.required' => 'La dimensión en metros cuadrados es obligatoria.',
            'ubicacion.required' => 'La ubicación es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 50 caracteres.',
            'imagenes.required' => 'Debe subir al menos una imagen.',
            'imagenes.max' => 'Máximo 10 imágenes permitidas.',
            'imagenes.*.mimes' => 'Solo formatos JPG, JPEG o PNG.',
            'imagenes.*.max' => 'Cada imagen no puede exceder los 5MB.'
        ]);

        $terreno = Terreno::create([
            'usuario_id' => $user->id,
            'precio' => $request->precio,
            'metros_cuadrados' => $request->metros_cuadrados,
            'ubicacion' => $request->ubicacion,
            'descripcion' => $request->descripcion,
            'estado' => 'pendiente',
            'latitud' => $request->latitud ?: null,
            'longitud' => $request->longitud ?: null,
            'creado_en' => now(),
            'actualizado_en' => now(),
        ]);

        if ($request->hasFile('imagenes')) {
            $orden = 1;
            foreach ($request->file('imagenes') as $file) {
                $path = $file->store('terrenos', 'public');

                TerrenoImagen::create([
                    'terreno_id' => $terreno->id,
                    'ruta_archivo' => '/storage/' . $path,
                    'orden' => $orden++
                ]);
            }
        }

        return redirect()->route('vendedor.dashboard')->with('success', 'Terreno publicado. Quedó pendiente de aprobación.');
    }
}