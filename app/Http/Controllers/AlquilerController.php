<?php

namespace App\Http\Controllers;

use App\Models\Alquiler;
use App\Models\Categoria;
use App\Models\Imagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Auditoria;

class AlquilerController extends Controller
{
    public function catalogo(Request $request)
    {
        $query = Alquiler::select('alquileres.*')
                         ->where('estado', 'disponible')
                         ->where('estado_aprobacion', 'aprobado')
                         ->with(['imagenes', 'categoria', 'promocion'])
                         ->addSelect(['has_promocion' => \App\Models\Promocion::selectRaw('count(*)')
                             ->whereColumn('promotable_id', 'alquileres.id')
                             ->where('promotable_type', Alquiler::class)
                             ->where('estado', 'aprobado')
                             ->limit(1)
                         ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('ubicacion', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->filled('precio_min')) {
            $query->where('precio_mensual', '>=', $request->precio_min);
        }

        if ($request->filled('precio_max')) {
            $query->where('precio_mensual', '<=', $request->precio_max);
        }

        if ($request->filled('metros_min')) {
            $query->where('metros_cuadrados', '>=', $request->metros_min);
        }

        if ($request->filled('metros_max')) {
            $query->where('metros_cuadrados', '<=', $request->metros_max);
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('ubicacion')) {
            $query->where('ubicacion', 'like', '%' . $request->ubicacion . '%');
        }

        if ($request->filled('con_promocion') && $request->con_promocion == '1') {
            $query->whereHas('promociones', function($q) {
                $q->where('estado', 'aprobado');
            });
        }

        $categorias = Categoria::where('activa', true)->orderBy('nombre')->get();

        $alquileres = $query->orderBy('has_promocion', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(12)
                            ->withQueryString();

        return view('Alquiler.index', compact('alquileres', 'categorias'));
    }

    public function detalle($id)
    {
        $alquiler = Alquiler::with(['imagenes', 'categoria'])->findOrFail($id);
        return view('Alquiler.show', compact('alquiler'));
    }

    public function index()
    {
        $alquileres = Alquiler::where('user_id', auth()->id())->with('historialEstados.usuario')->latest()->paginate(10);
        return view('vendedor.alquileres.index', compact('alquileres'));
    }

    public function create()
    {
        $categorias = \App\Models\Categoria::where('activa', true)
            ->whereIn('tipo_propiedad', ['alquiler', 'todos'])
            ->orderBy('nombre')
            ->get();
        
        $terrenosPadre = \App\Models\Terreno::where('usuario_id', auth()->id())
            ->where('tipo', 'terreno')
            ->where('estado', 'aprobado')
            ->orderBy('ubicacion')
            ->get();

        $propiedad = new Alquiler();

        return view('vendedor.publicar_propiedad', compact('categorias', 'terrenosPadre', 'propiedad'));
    }

    public function store(Request $request)
    {
        // Mapear campos del formulario unificado: el form envía 'nombre' como título
        $request->merge([
            'titulo' => $request->input('nombre', $request->input('titulo', '')),
            'precio_mensual' => $request->input('precio_mensual', $request->input('precio', 0)),
        ]);

        if (!$request->has('portada_index') || $request->portada_index === '') {
            $request->merge(['portada_index' => 0]);
        }

        $request->validate([
            'titulo'              => 'required|string|max:255',
            'ubicacion'           => 'required|string|max:255',
            'precio_mensual'      => 'required|numeric|min:0',
            'metros_cuadrados'    => 'nullable|numeric|min:0',
            'habitaciones'        => 'required|integer|min:1',
            'banos'               => 'required|integer|min:1',
            'descripcion'         => 'required|string',
            'categoria_id'        => 'required|exists:categorias,id',
            'servicios_incluidos' => 'nullable|array',
            'disponible_desde'    => 'required|date',
            'imagenes'            => 'nullable|array|max:10',
            'imagenes.*'          => 'image|mimes:jpeg,png,jpg|max:5120',
            'portada_index'       => 'sometimes|nullable|integer|min:0',
            'latitud'             => 'nullable|numeric',
            'longitud'            => 'nullable|numeric',
        ]);

        $categoria = \App\Models\Categoria::findOrFail($request->categoria_id);
        if ($categoria->tipo_propiedad !== 'todos' && $categoria->tipo_propiedad !== 'alquiler') {
            return back()->withErrors(['categoria_id' => 'La categoría seleccionada no aplica a alquileres.'])->withInput();
        }

        $alquiler = Alquiler::create([
            'titulo'              => $request->titulo,
            'ubicacion'           => $request->ubicacion,
            'precio_mensual'      => $request->precio_mensual,
            'metros_cuadrados'    => $request->metros_cuadrados,
            'habitaciones'        => $request->habitaciones,
            'banos'               => $request->banos,
            'descripcion'         => $request->descripcion,
            'categoria_id'        => $request->categoria_id,
            'servicios_incluidos' => $request->servicios_incluidos,
            'disponible_desde'    => $request->disponible_desde,
            'latitud'             => $request->latitud ?: null,
            'longitud'            => $request->longitud ?: null,
            'user_id'             => auth()->id(),
            'estado'              => 'disponible',
            'estado_aprobacion'   => 'pendiente',
        ]);

        if ($request->hasFile('imagenes')) {
            $orden = 1;
            $portadaIndex = (int) $request->input('portada_index', 0);
            $imagenesCreadas = [];
            
            foreach ($request->file('imagenes') as $file) {
                $path = $file->store('alquileres', 'public');
                $img = $alquiler->imagenes()->create([
                    'ruta_archivo' => '/storage/' . $path,
                    'orden'        => $orden++,
                ]);
                $imagenesCreadas[] = $img;
            }

            if ($portadaIndex >= 0 && $portadaIndex < count($imagenesCreadas)) {
                $alquiler->portada_id = $imagenesCreadas[$portadaIndex]->id;
                $alquiler->save();
            }
        }

        // Autoasignar portada si no se especificó o quedó en null
        if (!$alquiler->portada_id) {
            $primera = $alquiler->imagenes()->first();
            if ($primera) {
                $alquiler->portada_id = $primera->id;
                $alquiler->save();
            }
        }

        Auditoria::registrar(
            'creacion_alquiler',
            'alquiler',
            $alquiler->id,
            "El vendedor " . auth()->user()->nombre . " creó la publicación de alquiler #{$alquiler->id} (Título: {$alquiler->titulo})"
        );

        return redirect()->route('vendedor.alquileres.index')
                         ->with('success', 'Publicación de alquiler creada exitosamente. Quedó pendiente de aprobación por un administrador.');
    }

    public function misAlquileres()
    {
        return $this->index();
    }

    public function edit($id)
    {
        $alquiler = Alquiler::where('user_id', auth()->id())->findOrFail($id);
        $categorias = \App\Models\Categoria::where('activa', true)
            ->whereIn('tipo_propiedad', ['alquiler', 'todos'])
            ->orderBy('nombre')
            ->get();
        
        $terrenosPadre = \App\Models\Terreno::where('usuario_id', auth()->id())
            ->where('tipo', 'terreno')
            ->where('estado', 'aprobado')
            ->orderBy('ubicacion')
            ->get();

        $propiedad = $alquiler;
        $propiedad->tipo = 'alquiler'; // Forzar tipo alquiler para la vista
        $editando = true;

        return view('vendedor.publicar_propiedad', compact('propiedad', 'categorias', 'terrenosPadre', 'editando'));
    }

    public function update(Request $request, $id)
    {
        $alquiler = Alquiler::where('user_id', auth()->id())->findOrFail($id);

        // Mapear campos del formulario unificado
        $request->merge([
            'titulo' => $request->input('nombre', $request->input('titulo', '')),
            'precio_mensual' => $request->input('precio_mensual', $request->input('precio', 0)),
        ]);

        if (!$request->has('portada_index') || $request->portada_index === '' || (int) $request->portada_index < 0) {
            $request->merge(['portada_index' => 0]);
        }

        $request->validate([
            'titulo'              => 'required|string|max:255',
            'ubicacion'           => 'required|string|max:255',
            'precio_mensual'      => 'required|numeric|min:0',
            'metros_cuadrados'    => 'nullable|numeric|min:0',
            'habitaciones'        => 'required|integer|min:1',
            'banos'               => 'required|integer|min:1',
            'descripcion'         => 'required|string',
            'categoria_id'        => 'required|exists:categorias,id',
            'servicios_incluidos' => 'nullable|array',
            'disponible_desde'    => 'required|date',
            'imagenes'            => 'nullable|array|max:10',
            'imagenes.*'          => 'image|mimes:jpeg,png,jpg|max:5120',
            'portada_id'          => 'nullable|exists:imagenes,id',
            'portada_index'       => 'sometimes|nullable|integer|min:0',
            'estado_lote'         => 'required|in:disponible,reservado,vendido',
        ]);

        $categoria = \App\Models\Categoria::findOrFail($request->categoria_id);
        if ($categoria->tipo_propiedad !== 'todos' && $categoria->tipo_propiedad !== 'alquiler') {
            return back()->withErrors(['categoria_id' => 'La categoría seleccionada no aplica a alquileres.'])->withInput();
        }

        $estado_anterior = $alquiler->estado;
        $estado_nuevo = $request->input('estado_lote');

        if ($estado_nuevo === 'vendido' && $estado_anterior !== 'vendido') {
            \App\Models\Favorito::where('favoriteable_id', $alquiler->id)
                                ->where('favoriteable_type', Alquiler::class)
                                ->delete();
        }

        if ($estado_anterior !== $estado_nuevo) {
            \App\Models\HistorialEstadoAlquiler::create([
                'alquiler_id' => $alquiler->id,
                'user_id' => auth()->id(),
                'estado_anterior' => $estado_anterior,
                'estado_nuevo' => $estado_nuevo,
            ]);
        }

        $alquiler->update([
            'titulo'              => $request->titulo,
            'ubicacion'           => $request->ubicacion,
            'precio_mensual'      => $request->precio_mensual,
            'metros_cuadrados'    => $request->metros_cuadrados,
            'habitaciones'        => $request->habitaciones,
            'banos'               => $request->banos,
            'descripcion'         => $request->descripcion,
            'categoria_id'        => $request->categoria_id,
            'servicios_incluidos' => $request->servicios_incluidos,
            'disponible_desde'    => $request->disponible_desde,
            'latitud'             => $request->latitud ?: null,
            'longitud'            => $request->longitud ?: null,
            'estado'              => $estado_nuevo,
            'portada_id'          => $request->portada_id ?: $alquiler->portada_id,
            'portada_index'       => $request->portada_index ?? 0,
        ]);

        if ($request->hasFile('imagenes')) {
            $orden = $alquiler->imagenes()->max('orden') + 1;
            $portadaIndex = $request->filled('portada_index') ? (int) $request->portada_index : 0;
            $imagenesCreadas = [];

            foreach ($request->file('imagenes') as $file) {
                $path = $file->store('alquileres', 'public');
                $img = $alquiler->imagenes()->create([
                    'ruta_archivo' => '/storage/' . $path,
                    'orden'        => $orden++,
                ]);
                $imagenesCreadas[] = $img;
            }

            if ($portadaIndex >= 0 && $portadaIndex < count($imagenesCreadas)) {
                $alquiler->portada_id = $imagenesCreadas[$portadaIndex]->id;
                $alquiler->save();
            }
        }

        // Autoasignar portada si no tiene
        if (!$alquiler->portada_id) {
            $primera = $alquiler->imagenes()->first();
            if ($primera) {
                $alquiler->portada_id = $primera->id;
                $alquiler->save();
            }
        }

        Auditoria::registrar(
            'actualizacion_alquiler',
            'alquiler',
            $alquiler->id,
            "El vendedor " . auth()->user()->nombre . " actualizó la publicación de alquiler #{$alquiler->id} (Título: {$alquiler->titulo})"
        );

        return redirect()->route('vendedor.alquileres.index')
                         ->with('success', 'Publicación actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $alquiler = Alquiler::where('user_id', auth()->id())->findOrFail($id);

        foreach ($alquiler->imagenes as $imagen) {
            $ruta = str_replace('/storage/', '', $imagen->ruta_archivo);
            Storage::disk('public')->delete($ruta);
            $imagen->delete();
        }

        Auditoria::registrar(
            'eliminacion_alquiler',
            'alquiler',
            $id,
            "El vendedor " . auth()->user()->nombre . " eliminó la publicación de alquiler #{$id} (Título: {$alquiler->titulo})"
        );

        $alquiler->delete();

        return redirect()->route('vendedor.alquileres.index')
                         ->with('success', 'Publicación eliminada correctamente.');
    }

    public function toggleEstado($id, Request $request)
    {
        $alquiler = Alquiler::where('user_id', auth()->id())->findOrFail($id);

        $estado_anterior = $alquiler->estado;
        
        if ($request->has('estado')) {
            $estado_nuevo = $request->input('estado');
        } elseif ($request->has('estado_lote')) {
            $estado_nuevo = $request->input('estado_lote');
        } else {
            $estado_nuevo = $alquiler->estado === 'disponible' ? 'vendido' : 'disponible';
        }

        if (!in_array($estado_nuevo, ['disponible', 'reservado', 'vendido', 'alquilado'])) {
            $estado_nuevo = 'disponible';
        }

        if ($estado_anterior === $estado_nuevo) {
            return redirect()->back()->with('info', 'La publicación de alquiler ya se encuentra en ese estado.');
        }

        if ($estado_nuevo === 'vendido') {
            \App\Models\Favorito::where('favoriteable_id', $alquiler->id)
                                ->where('favoriteable_type', Alquiler::class)
                                ->delete();
        }

        $alquiler->estado = $estado_nuevo;
        $alquiler->save();

        \App\Models\HistorialEstadoAlquiler::create([
            'alquiler_id' => $alquiler->id,
            'user_id' => auth()->id(),
            'estado_anterior' => $estado_anterior,
            'estado_nuevo' => $estado_nuevo,
        ]);

        Auditoria::registrar(
            'cambio_estado_alquiler',
            'alquiler',
            $alquiler->id,
            "El vendedor " . auth()->user()->nombre . " cambió el estado del alquiler #{$alquiler->id}: {$estado_anterior} → {$estado_nuevo}"
        );

        $mensajes = [
            'disponible' => 'El alquiler ahora está disponible para la venta/renta.',
            'reservado'  => 'El alquiler se ha marcado como RESERVADO.',
            'vendido'    => 'El alquiler se ha marcado como VENDIDO y se eliminó de los favoritos.',
        ];

        return redirect()->back()->with('success', $mensajes[$estado_nuevo] ?? "Estado actualizado a '{$estado_nuevo}'.");
    }

    public function eliminarImagen($id)
    {
        $imagen = Imagen::findOrFail($id);
        $alquiler = $imagen->imageable;

        if ($alquiler->user_id !== auth()->id()) {
            abort(403);
        }

        $fuePortada = ($alquiler->portada_id === $imagen->id);
        if ($fuePortada) {
            $alquiler->portada_id = null;
            $alquiler->save();
        }

        $ruta = str_replace('/storage/', '', $imagen->ruta_archivo);
        Storage::disk('public')->delete($ruta);
        $imagen->delete();

        if ($fuePortada) {
            $primera = $alquiler->imagenes()->first();
            if ($primera) {
                $alquiler->portada_id = $primera->id;
                $alquiler->save();
            }
        }

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }
}