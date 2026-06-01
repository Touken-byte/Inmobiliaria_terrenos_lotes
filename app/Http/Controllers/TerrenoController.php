<?php

namespace App\Http\Controllers;

use App\Models\Terreno;
use App\Models\TerrenoImagen;
use App\Models\Alquiler;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use App\Helpers\Auditoria;

class TerrenoController extends Controller
{
    // ==========================================
    // VISTA COMPRADOR: CATÁLOGO / MARKETPLACE
    // ==========================================
    public function catalogoUnificado(Request $request)
    {
        $categorias = Categoria::where('activa', true)->orderBy('nombre')->get();

        $tipo = $request->input('tipo');
        $mostrarTerrenos = true;
        $mostrarAlquileres = true;

        if ($tipo === 'lote' || $tipo === 'terreno') {
            $mostrarAlquileres = false;
        } elseif ($tipo === 'alquiler') {
            $mostrarTerrenos = false;
        }

        $terrenosQuery = Terreno::select('terrenos.*')
            ->where('estado', 'aprobado')
            ->where('estado_lote', 'disponible')
            ->with(['imagenes', 'categoria', 'promocion'])
            ->addSelect(['has_promocion' => \App\Models\Promocion::selectRaw('count(*)')
                ->whereColumn('promotable_id', 'terrenos.id')
                ->where('promotable_type', Terreno::class)
                ->where('estado', 'aprobado')
                ->limit(1)
            ]);

        $alquileresQuery = Alquiler::select('alquileres.*')
            ->where('estado', 'disponible')
            ->where('estado_aprobacion', 'aprobado')
            ->with(['imagenes', 'categoria', 'promocion'])
            ->addSelect(['has_promocion' => \App\Models\Promocion::selectRaw('count(*)')
                ->whereColumn('promotable_id', 'alquileres.id')
                ->where('promotable_type', Alquiler::class)
                ->where('estado', 'aprobado')
                ->limit(1)
            ]);

        $mapTerrenosQuery = Terreno::select('terrenos.*')
            ->where('estado', 'aprobado')
            ->where('estado_lote', 'disponible')
            ->with(['imagenes', 'categoria', 'promocion'])
            ->addSelect(['has_promocion' => \App\Models\Promocion::selectRaw('count(*)')
                ->whereColumn('promotable_id', 'terrenos.id')
                ->where('promotable_type', Terreno::class)
                ->where('estado', 'aprobado')
                ->limit(1)
            ]);

        $mapAlquileresQuery = Alquiler::select('alquileres.*')
            ->where('estado_aprobacion', 'aprobado')
            ->with(['imagenes', 'categoria', 'promocion'])
            ->addSelect(['has_promocion' => \App\Models\Promocion::selectRaw('count(*)')
                ->whereColumn('promotable_id', 'alquileres.id')
                ->where('promotable_type', Alquiler::class)
                ->where('estado', 'aprobado')
                ->limit(1)
            ]);

        if ($mostrarTerrenos && ($tipo === 'terreno' || $tipo === 'lote')) {
            $terrenosQuery->where('tipo', $tipo);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            if ($mostrarTerrenos) {
                $terrenosQuery->where(function($q) use ($search) {
                    $q->where('descripcion', 'LIKE', "%{$search}%")
                      ->orWhere('ubicacion', 'LIKE', "%{$search}%");
                });
            }
            if ($mostrarAlquileres) {
                $alquileresQuery->where(function($q) use ($search) {
                    $q->where('titulo', 'LIKE', "%{$search}%")
                      ->orWhere('ubicacion', 'LIKE', "%{$search}%")
                      ->orWhere('descripcion', 'LIKE', "%{$search}%");
                });
            }

            $mapTerrenosQuery->where(function($q) use ($search) {
                $q->where('descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('ubicacion', 'LIKE', "%{$search}%");
            });
            $mapAlquileresQuery->where(function($q) use ($search) {
                $q->where('titulo', 'LIKE', "%{$search}%")
                  ->orWhere('ubicacion', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('precio_min')) {
            if ($mostrarTerrenos) {
                $terrenosQuery->where('precio', '>=', $request->precio_min);
            }
            if ($mostrarAlquileres) {
                $alquileresQuery->where('precio_mensual', '>=', $request->precio_min);
            }

            $mapTerrenosQuery->where('precio', '>=', $request->precio_min);
            $mapAlquileresQuery->where('precio_mensual', '>=', $request->precio_min);
        }

        if ($request->filled('precio_max')) {
            if ($mostrarTerrenos) {
                $terrenosQuery->where('precio', '<=', $request->precio_max);
            }
            if ($mostrarAlquileres) {
                $alquileresQuery->where('precio_mensual', '<=', $request->precio_max);
            }

            $mapTerrenosQuery->where('precio', '<=', $request->precio_max);
            $mapAlquileresQuery->where('precio_mensual', '<=', $request->precio_max);
        }

        if ($request->filled('metros_min')) {
            if ($mostrarTerrenos) {
                $terrenosQuery->where('metros_cuadrados', '>=', $request->metros_min);
            }
            if ($mostrarAlquileres) {
                $alquileresQuery->where('metros_cuadrados', '>=', $request->metros_min);
            }

            $mapTerrenosQuery->where('metros_cuadrados', '>=', $request->metros_min);
            $mapAlquileresQuery->where('metros_cuadrados', '>=', $request->metros_min);
        }

        if ($request->filled('metros_max')) {
            if ($mostrarTerrenos) {
                $terrenosQuery->where('metros_cuadrados', '<=', $request->metros_max);
            }
            if ($mostrarAlquileres) {
                $alquileresQuery->where('metros_cuadrados', '<=', $request->metros_max);
            }

            $mapTerrenosQuery->where('metros_cuadrados', '<=', $request->metros_max);
            $mapAlquileresQuery->where('metros_cuadrados', '<=', $request->metros_max);
        }

        if ($request->filled('categoria_id')) {
            if ($mostrarTerrenos) {
                $terrenosQuery->where('categoria_id', $request->categoria_id);
            }
            if ($mostrarAlquileres) {
                $alquileresQuery->where('categoria_id', $request->categoria_id);
            }

            $mapTerrenosQuery->where('categoria_id', $request->categoria_id);
            $mapAlquileresQuery->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('ubicacion')) {
            if ($mostrarTerrenos) {
                $terrenosQuery->where('ubicacion', 'LIKE', '%' . $request->ubicacion . '%');
            }
            if ($mostrarAlquileres) {
                $alquileresQuery->where('ubicacion', 'LIKE', '%' . $request->ubicacion . '%');
            }

            $mapTerrenosQuery->where('ubicacion', 'LIKE', '%' . $request->ubicacion . '%');
            $mapAlquileresQuery->where('ubicacion', 'LIKE', '%' . $request->ubicacion . '%');
        }

        if ($request->filled('lat') && $request->filled('lng') && $request->filled('radio')) {
            $lat   = (float) $request->lat;
            $lng   = (float) $request->lng;
            $radio = (float) $request->radio;
            if ($mostrarTerrenos) {
                $terrenosQuery->whereRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(latitud)) * cos(radians(longitud) - radians(?)) + sin(radians(?)) * sin(radians(latitud)))) <= ?",
                    [$lat, $lng, $lat, $radio]
                );
            }
            if ($mostrarAlquileres) {
                $alquileresQuery->whereRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(latitud)) * cos(radians(longitud) - radians(?)) + sin(radians(?)) * sin(radians(latitud)))) <= ?",
                    [$lat, $lng, $lat, $radio]
                );
            }

            $mapTerrenosQuery->whereRaw(
                "(6371 * acos(cos(radians(?)) * cos(radians(latitud)) * cos(radians(longitud) - radians(?)) + sin(radians(?)) * sin(radians(latitud)))) <= ?",
                [$lat, $lng, $lat, $radio]
            );
            $mapAlquileresQuery->whereRaw(
                "(6371 * acos(cos(radians(?)) * cos(radians(latitud)) * cos(radians(longitud) - radians(?)) + sin(radians(?)) * sin(radians(latitud)))) <= ?",
                [$lat, $lng, $lat, $radio]
            );
        }

        if ($request->filled('con_promocion') && $request->con_promocion == '1') {
            if ($mostrarTerrenos) {
                $terrenosQuery->whereHas('promociones', function($q) {
                    $q->where('estado', 'aprobado');
                });
            }
            if ($mostrarAlquileres) {
                $alquileresQuery->whereHas('promociones', function($q) {
                    $q->where('estado', 'aprobado');
                });
            }

            $mapTerrenosQuery->whereHas('promociones', function($q) {
                $q->where('estado', 'aprobado');
            });
            $mapAlquileresQuery->whereHas('promociones', function($q) {
                $q->where('estado', 'aprobado');
            });
        }

        $items = collect();
        $total = 0;

        if ($mostrarTerrenos) {
            $terrenosCount = (clone $terrenosQuery)->count();
            $total += $terrenosCount;
            $terrenos = $terrenosQuery->orderBy('has_promocion', 'desc')
                                      ->orderBy('creado_en', 'desc')
                                      ->get();
            $items = $items->concat($terrenos);
        }

        if ($mostrarAlquileres) {
            $alquileresCount = (clone $alquileresQuery)->count();
            $total += $alquileresCount;
            $alquileres = $alquileresQuery->orderBy('has_promocion', 'desc')
                                          ->orderBy('created_at', 'desc')
                                          ->get();
            $items = $items->concat($alquileres);
        }

        $items = $items->sort(function ($a, $b) {
            $promoA = $a->has_promocion ? 1 : 0;
            $promoB = $b->has_promocion ? 1 : 0;
            if ($promoA !== $promoB) {
                return $promoB <=> $promoA;
            }

            $fechaA = $a instanceof Alquiler ? ($a->created_at ? $a->created_at->timestamp : 0) : ($a->creado_en ? $a->creado_en->timestamp : 0);
            $fechaB = $b instanceof Alquiler ? ($b->created_at ? $b->created_at->timestamp : 0) : ($b->creado_en ? $b->creado_en->timestamp : 0);
            return $fechaB <=> $fechaA;
        })->values();

        $perPage = 8;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $sliced = $items->slice(($page - 1) * $perPage, $perPage);

        $propiedades = new LengthAwarePaginator(
            $sliced,
            $total,
            $perPage,
            $page,
            [
                'path'  => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $mapTerrenos = $mapTerrenosQuery->orderBy('has_promocion', 'desc')
                                          ->orderBy('creado_en', 'desc')
                                          ->get();

        $mapAlquileres = $mapAlquileresQuery->orderBy('has_promocion', 'desc')
                                            ->orderBy('created_at', 'desc')
                                            ->get();

        $mapPropiedades = Terreno::where('estado', 'aprobado')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get()
            ->concat(
                Alquiler::where('estado_aprobacion', 'aprobado')
                    ->whereNotNull('latitud')
                    ->whereNotNull('longitud')
                    ->get()
            );

        $catalogRouteName = 'catalogo.unificado';

        return view('comprador.catalogo', compact('propiedades', 'categorias', 'catalogRouteName', 'mapPropiedades'));
    }

    public function catalogoTerrenos(Request $request)
    {
        $query = Terreno::select('terrenos.*')
                        ->where('estado', 'aprobado')
                        ->where('estado_lote', 'disponible')
                        ->where('tipo', 'terreno')
                        ->with(['imagenes', 'categoria', 'promocion'])
                        ->addSelect(['has_promocion' => \App\Models\Promocion::selectRaw('count(*)')
                            ->whereColumn('promotable_id', 'terrenos.id')
                            ->where('promotable_type', Terreno::class)
                            ->where('estado', 'aprobado')
                            ->limit(1)
                        ]);

        if ($request->filled('tipo') && $request->tipo === 'terreno') {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('ubicacion', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', $request->precio_min);
        }

        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', $request->precio_max);
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
            $query->where('ubicacion', 'LIKE', '%' . $request->ubicacion . '%');
        }

        if ($request->filled('lat') && $request->filled('lng') && $request->filled('radio')) {
            $lat   = (float) $request->lat;
            $lng   = (float) $request->lng;
            $radio = (float) $request->radio;
            $query->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(latitud)) * cos(radians(longitud) - radians(?)) + sin(radians(?)) * sin(radians(latitud)))) <= ?", [$lat, $lng, $lat, $radio]);
        }

        if ($request->filled('con_promocion') && $request->con_promocion == '1') {
            $query->whereHas('promociones', function($q) {
                $q->where('estado', 'aprobado');
            });
        }

        $categorias = Categoria::where('activa', true)->orderBy('nombre')->get();

        $terrenos = $query->orderBy('has_promocion', 'desc')
                          ->orderBy('creado_en', 'desc')
                          ->paginate(8)
                          ->appends($request->query());

        $propiedades = $terrenos;
        $catalogRouteName = 'catalogo.terrenos';

        return view('comprador.catalogo', compact('propiedades', 'categorias', 'catalogRouteName'));
    }

    public function detalle($id)
    {
        $terreno = Terreno::with(['imagenes', 'folio', 'terrenoPadre'])->where('estado', 'aprobado')->findOrFail($id);

        $folio = null;
        if ($terreno->folio && $terreno->folio->estado === 'verificado') {
            $folio = $terreno->folio;
        }

        return view('comprador.detalle', compact('terreno', 'folio'));
    }

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
            return redirect()->route('vendedor.dashboard')->with('error', 'Debe estar verificado para poder publicar. Verifique su identidad primero.');
        }

        $categorias = \App\Models\Categoria::where('activa', true)
            ->whereIn('tipo_propiedad', ['terreno', 'lote', 'todos'])
            ->orderBy('nombre')
            ->get();
        // Terrenos padre disponibles (para asociar lotes)
        $terrenosPadre = Terreno::where('usuario_id', $user->id)
                                ->where('tipo', 'terreno')
                                ->where('estado', 'aprobado')
                                ->orderBy('ubicacion')
                                ->get();
        
        $propiedad = new Terreno();
        
        return view('vendedor.publicar_propiedad', compact(
            'categorias', 'terrenosPadre', 'propiedad'
        ));
    }

    public function misTerrenos()
    {
        $user = auth()->user();
        $terrenos = Terreno::where('usuario_id', $user->id)
            ->with(['imagenes', 'terrenoPadre'])
            ->orderBy('creado_en', 'desc')
            ->get();
        return view('vendedor.mis_terrenos', compact('terrenos'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $terreno = Terreno::where('id', $id)->where('usuario_id', $user->id)->firstOrFail();
        $categorias = \App\Models\Categoria::where('activa', true)
            ->whereIn('tipo_propiedad', [$terreno->tipo, 'todos'])
            ->orderBy('nombre')
            ->get();
        $terrenosPadre = Terreno::where(function ($query) use ($user, $terreno) {
                                    $query->where('usuario_id', $user->id)
                                          ->where('tipo', 'terreno')
                                          ->where('estado', 'aprobado');

                                    if ($terreno->parent_id) {
                                        $query->orWhere('id', $terreno->parent_id);
                                    }
                                })
                                ->orderBy('ubicacion')
                                ->get();

        $propiedad = $terreno;
        $editando = true;

        return view('vendedor.publicar_propiedad', compact('propiedad', 'categorias', 'terrenosPadre', 'editando'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $terreno = Terreno::where('id', $id)->where('usuario_id', $user->id)->firstOrFail();

        $rules = [
            'nombre'           => 'required|string|max:255',
            'precio'           => 'required|numeric|min:0|max:999999999999.99',
            'metros_cuadrados' => 'required|numeric|min:0|max:999999999999.99',
            'ubicacion'        => 'required|string|max:255',
            'descripcion'      => 'required|string|min:50',
            'categoria_id'     => 'required|exists:categorias,id',
            'estado_lote'      => 'required|in:disponible,reservado,vendido',
            'imagenes'         => 'nullable|array|max:10',
            'imagenes.*'       => 'file|mimes:jpg,jpeg,png|max:5120',
            'portada_id'       => 'nullable|exists:terreno_imagenes,id',
            'portada_index'    => 'sometimes|nullable|integer|min:0',
            // Campos opcionales
            'pais'             => 'nullable|string|max:100',
            'departamento'     => 'nullable|string|max:100',
            'provincia'        => 'nullable|string|max:100',
            'municipio'        => 'nullable|string|max:100',
            'zona_barrio'      => 'nullable|string|max:255',
            'direccion'        => 'nullable|string|max:255',
            'tipo_terreno'     => 'nullable|string|max:50',
            'topografia'       => 'nullable|string|max:50',
            'largo'            => 'nullable|numeric|min:0',
            'ancho'            => 'nullable|numeric|min:0',
            'numero_matricula' => 'nullable|string|max:100',
            'codigo_catastral' => 'nullable|string|max:100',
            'numero_lote'      => 'nullable|string|max:50',
            'codigo_lote'      => 'nullable|string|max:50',
            'manzano_bloque'   => 'nullable|string|max:50',
            'frente'           => 'nullable|numeric|min:0',
            'fondo'            => 'nullable|numeric|min:0',
            'colinda_norte'    => 'nullable|string|max:255',
            'colinda_sur'      => 'nullable|string|max:255',
            'colinda_este'     => 'nullable|string|max:255',
            'colinda_oeste'    => 'nullable|string|max:255',
            'agua_potable'     => 'nullable|boolean',
            'energia_electrica' => 'nullable|boolean',
            'alcantarillado'   => 'nullable|boolean',
            'gas_domiciliario' => 'nullable|boolean',
            'internet'         => 'nullable|boolean',
            'moneda'           => 'nullable|in:BOB,USD',
            'forma_pago'       => 'nullable|in:contado,financiamiento,ambos',
        ];

        if (!$request->has('portada_index') || $request->portada_index === '' || (int)$request->portada_index < 0) {
            $request->merge(['portada_index' => 0]);
        }

        $request->validate($rules);

        $categoria = \App\Models\Categoria::findOrFail($request->categoria_id);
        if ($categoria->tipo_propiedad !== 'todos' && $categoria->tipo_propiedad !== $terreno->tipo) {
            return back()->withErrors(['categoria_id' => 'La categoría seleccionada no aplica a este tipo de propiedad.'])->withInput();
        }

        if (($terreno->tipo ?? 'terreno') === 'lote') {
            $request->validate([
                'parent_id' => [
                    'required',
                    Rule::exists('terrenos', 'id')->where(function ($query) use ($user) {
                        $query->where('usuario_id', $user->id)
                              ->where('tipo', 'terreno')
                              ->where('estado', 'aprobado');
                    }),
                ],
            ], [
                'parent_id.required' => 'Debe seleccionar el terreno padre para el lote.',
                'parent_id.exists'   => 'El terreno padre seleccionado no es válido.',
            ]);
        }

        $terreno->moneda = $request->moneda ?? 'USD';

        $terreno->update([
            'nombre'           => $request->nombre,
            'parent_id'        => ($terreno->tipo ?? 'terreno') === 'lote' ? $request->parent_id : null,
            'precio'           => $request->precio,
            'metros_cuadrados' => $request->metros_cuadrados,
            'ubicacion'        => $request->ubicacion,
            'descripcion'      => $request->descripcion,
            'categoria_id'     => $request->categoria_id,
            'latitud'          => $request->latitud ?: null,
            'longitud'         => $request->longitud ?: null,
            'estado_lote'      => $request->estado_lote,
            'portada_id'       => $request->portada_id ?: $terreno->portada_id,
            'pais'             => $request->pais,
            'departamento'     => $request->departamento,
            'provincia'        => $request->provincia,
            'municipio'        => $request->municipio,
            'zona_barrio'      => $request->zona_barrio,
            'direccion'        => $request->direccion,
            'tipo_terreno'     => $request->tipo_terreno,
            'topografia'       => $request->topografia,
            'largo'            => $request->largo,
            'ancho'            => $request->ancho,
            'numero_matricula' => $request->numero_matricula,
            'codigo_catastral' => $request->codigo_catastral,
            'numero_lote'      => $request->numero_lote,
            'codigo_lote'      => $request->codigo_lote,
            'manzano_bloque'   => $request->manzano_bloque,
            'frente'           => $request->frente,
            'fondo'            => $request->fondo,
            'colinda_norte'    => $request->colinda_norte,
            'colinda_sur'      => $request->colinda_sur,
            'colinda_este'     => $request->colinda_este,
            'colinda_oeste'    => $request->colinda_oeste,
            'agua_potable'     => $request->boolean('agua_potable'),
            'energia_electrica' => $request->boolean('energia_electrica'),
            'alcantarillado'   => $request->boolean('alcantarillado'),
            'gas_domiciliario' => $request->boolean('gas_domiciliario'),
            'internet'         => $request->boolean('internet'),
            'moneda'           => $request->moneda ?? 'USD',
            'forma_pago'       => $request->forma_pago ?? 'contado',
            'actualizado_en'   => now(),
        ]);

        Auditoria::registrar(
            'actualizacion_terreno',
            'terreno',
            $terreno->id,
            "El vendedor {$user->nombre} actualizó el terreno/lote #{$terreno->id} (Código: {$terreno->codigo})"
        );

        if ($request->hasFile('imagenes')) {
            $orden = $terreno->imagenes->max('orden') + 1;
            $portadaIndex = $request->filled('portada_index') ? (int) $request->portada_index : 0;
            $imagenesCreadas = [];
            
            foreach ($request->file('imagenes') as $file) {
                $path = $file->store('terrenos', 'public');
                if ($path) {
                    $img = TerrenoImagen::create([
                        'terreno_id' => $terreno->id,
                        'ruta_archivo' => '/storage/' . $path,
                        'orden' => $orden++
                    ]);
                    $imagenesCreadas[] = $img;
                }
            }

            if ($portadaIndex >= 0 && $portadaIndex < count($imagenesCreadas)) {
                $terreno->portada_id = $imagenesCreadas[$portadaIndex]->id;
                $terreno->save();
            }
        }

        // Si se borró la portada y quedó en null, o no tiene portada, autoasignamos la primera
        if (!$terreno->portada_id) {
            $primera = $terreno->imagenes()->first();
            if ($primera) {
                $terreno->portada_id = $primera->id;
                $terreno->save();
            }
        }

        return redirect()->route('vendedor.propiedades_panel')->with('success', 'Terreno actualizado correctamente.');
    }

    public function eliminarImagen($id)
    {
        $imagen  = TerrenoImagen::findOrFail($id);
        $terreno = $imagen->terreno;

        if ($terreno->usuario_id !== auth()->id()) {
            abort(403);
        }

        $fuePortada = ($terreno->portada_id === $imagen->id);
        if ($fuePortada) {
            $terreno->portada_id = null;
            $terreno->save();
        }

        $ruta = str_replace('/storage/', '', $imagen->ruta_archivo);
        \Storage::disk('public')->delete($ruta);
        $imagen->delete();

        if ($fuePortada) {
            $primera = $terreno->imagenes()->first();
            if ($primera) {
                $terreno->portada_id = $primera->id;
                $terreno->save();
            }
        }

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->estado_verificacion !== 'verificado') {
            return redirect()->route('vendedor.dashboard')->with('error', 'Debe estar verificado para publicar.');
        }

        // Reglas base
        $rules = [
            'tipo'             => 'required|in:terreno,lote',
            'nombre'           => 'required|string|max:255',
            'precio'           => 'required|numeric|min:0|max:999999999999.99',
            'metros_cuadrados' => 'required|numeric|min:0|max:999999999999.99',
            'ubicacion'        => 'required|string|max:255',
            'descripcion'      => 'required|string|min:50',
            'categoria_id'     => 'required|exists:categorias,id',
            'imagenes'         => 'required|array|min:1|max:10',
            'imagenes.*'       => 'file|mimes:jpg,jpeg,png|max:5120',
            'portada_index'    => 'sometimes|nullable|integer|min:0',
            'pais'             => 'nullable|string|max:100',
            'departamento'     => 'nullable|string|max:100',
            'provincia'        => 'nullable|string|max:100',
            'municipio'        => 'nullable|string|max:100',
            'zona_barrio'      => 'nullable|string|max:255',
            'direccion'        => 'nullable|string|max:255',
            'agua_potable'     => 'nullable|boolean',
            'energia_electrica' => 'nullable|boolean',
            'alcantarillado'   => 'nullable|boolean',
            'gas_domiciliario' => 'nullable|boolean',
            'internet'         => 'nullable|boolean',
            'moneda'           => 'nullable|in:BOB,USD',
            'forma_pago'       => 'nullable|in:contado,financiamiento,ambos',
        ];

        // Si es lote, el parent_id es obligatorio
        if ($request->tipo === 'lote') {
            $rules['parent_id'] = [
                'required',
                Rule::exists('terrenos', 'id')->where(function ($query) use ($user) {
                    $query->where('usuario_id', $user->id)
                          ->where('tipo', 'terreno')
                          ->where('estado', 'aprobado');
                }),
            ];
            $rules['numero_lote']   = 'nullable|string|max:50';
            $rules['codigo_lote']   = 'nullable|string|max:50';
            $rules['manzano_bloque'] = 'nullable|string|max:50';
            $rules['frente']        = 'nullable|numeric|min:0';
            $rules['fondo']         = 'nullable|numeric|min:0';
            $rules['colinda_norte'] = 'nullable|string';
            $rules['colinda_sur']   = 'nullable|string';
            $rules['colinda_este']  = 'nullable|string';
            $rules['colinda_oeste'] = 'nullable|string';
        } else {
            $rules['tipo_terreno']      = 'nullable|in:urbano,rural,agricola,comercial,industrial';
            $rules['largo']             = 'nullable|numeric|min:0';
            $rules['ancho']             = 'nullable|numeric|min:0';
            $rules['topografia']        = 'nullable|in:plano,semiplano,inclinado';
            $rules['numero_matricula']  = 'nullable|string|max:100';
            $rules['codigo_catastral']  = 'nullable|string|max:100';
        }

        if (!$request->has('portada_index') || $request->portada_index === '') {
            $request->merge(['portada_index' => 0]);
        }

        $request->validate($rules, [
            'tipo.required'             => 'Debe seleccionar el tipo: Terreno o Lote.',
            'tipo.in'                   => 'El tipo debe ser terreno o lote.',
            'nombre.required'           => 'El nombre es obligatorio.',
            'precio.required'           => 'El precio es obligatorio.',
            'precio.numeric'            => 'El precio debe ser un número válido.',
            'metros_cuadrados.required' => 'La dimensión en metros cuadrados es obligatoria.',
            'ubicacion.required'        => 'La ubicación es obligatoria.',
            'descripcion.min'           => 'La descripción debe tener al menos 50 caracteres.',
            'imagenes.required'         => 'Debe subir al menos una imagen.',
            'imagenes.max'              => 'Máximo 10 imágenes permitidas.',
            'imagenes.*.mimes'          => 'Solo formatos JPG, JPEG o PNG.',
            'imagenes.*.max'            => 'Cada imagen no puede exceder los 5MB.',
            'parent_id.required'        => 'Debe seleccionar el terreno padre para el lote.',
            'parent_id.exists'          => 'El terreno padre seleccionado no es válido.',
        ]);

        $categoria = \App\Models\Categoria::findOrFail($request->categoria_id);
        if ($categoria->tipo_propiedad !== 'todos' && $categoria->tipo_propiedad !== $request->tipo) {
            return back()->withErrors(['categoria_id' => 'La categoría seleccionada no aplica a este tipo de propiedad.'])->withInput();
        }

        // Generar código automático
        $ultimoId = \DB::table('terrenos')->max('id') + 1;
        $codigo = $request->tipo === 'lote' 
            ? 'LOT-' . str_pad($ultimoId, 3, '0', STR_PAD_LEFT)
            : 'TER-' . str_pad($ultimoId, 3, '0', STR_PAD_LEFT);

        // Validar que el lote no sea más grande que el terreno padre
        if ($request->tipo === 'lote' && $request->filled('parent_id')) {
            $terrenoPadre = Terreno::find($request->parent_id);
            if ($terrenoPadre && (float)$request->metros_cuadrados > (float)$terrenoPadre->metros_cuadrados) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['metros_cuadrados' => 'El lote no puede tener mayor superficie que el terreno padre (' . number_format($terrenoPadre->metros_cuadrados, 2) . ' m²).']);
            }
        }

        $data = [
            'usuario_id'        => $user->id,
            'tipo'              => $request->tipo,
            'parent_id'         => ($request->tipo === 'lote') ? $request->parent_id : null,
            'nombre'            => $request->nombre,
            'codigo'            => $codigo,
            'pais'              => $request->pais,
            'departamento'      => $request->departamento,
            'provincia'         => $request->provincia,
            'municipio'         => $request->municipio,
            'zona_barrio'       => $request->zona_barrio,
            'direccion'         => $request->direccion,
            'precio'            => $request->precio,
            'metros_cuadrados'  => $request->metros_cuadrados,
            'ubicacion'         => $request->ubicacion,
            'descripcion'       => $request->descripcion,
            'agua_potable'      => $request->boolean('agua_potable'),
            'energia_electrica' => $request->boolean('energia_electrica'),
            'alcantarillado'    => $request->boolean('alcantarillado'),
            'gas_domiciliario'  => $request->boolean('gas_domiciliario'),
            'internet'          => $request->boolean('internet'),
            'moneda'            => $request->moneda ?? 'USD',
            'forma_pago'        => $request->forma_pago ?? 'ambos',
            'categoria_id'      => $request->categoria_id,
            'estado'            => 'pendiente',
            'estado_lote'       => 'disponible',
            'latitud'           => $request->latitud ?: null,
            'longitud'          => $request->longitud ?: null,
            'creado_en'         => now(),
            'actualizado_en'    => now(),
        ];

        // Campos específicos según tipo
        if ($request->tipo === 'lote') {
            $data['numero_lote'] = $request->numero_lote;
            $data['codigo_lote'] = $request->codigo_lote;
            $data['manzano_bloque'] = $request->manzano_bloque;
            $data['frente'] = $request->frente;
            $data['fondo'] = $request->fondo;
            $data['colinda_norte'] = $request->colinda_norte;
            $data['colinda_sur'] = $request->colinda_sur;
            $data['colinda_este'] = $request->colinda_este;
            $data['colinda_oeste'] = $request->colinda_oeste;
        } else {
            $data['tipo_terreno'] = $request->tipo_terreno;
            $data['largo'] = $request->largo;
            $data['ancho'] = $request->ancho;
            $data['topografia'] = $request->topografia;
            $data['numero_matricula'] = $request->numero_matricula;
            $data['codigo_catastral'] = $request->codigo_catastral;
        }

        $terreno = Terreno::create($data);

        if ($request->hasFile('imagenes')) {
            $orden = 1;
            $portadaIndex = (int) $request->input('portada_index', 0);
            $imagenesCreadas = [];
            
            foreach ($request->file('imagenes') as $i => $file) {
                $path = $file->store('terrenos', 'public');
                if ($path) {
                    $img = TerrenoImagen::create([
                        'terreno_id' => $terreno->id,
                        'ruta_archivo' => '/storage/' . $path,
                        'orden' => $orden++
                    ]);
                    $imagenesCreadas[] = $img;
                }
            }

            if ($portadaIndex >= 0 && $portadaIndex < count($imagenesCreadas)) {
                $terreno->portada_id = $imagenesCreadas[$portadaIndex]->id;
                $terreno->save();
            }
        }

        Auditoria::registrar(
            'creacion_terreno',
            'terreno',
            $terreno->id,
            "El vendedor {$user->nombre} creó el terreno/lote #{$terreno->id} (Código: {$codigo})"
        );

        $label = $request->tipo === 'lote' ? 'Lote' : 'Terreno';
        return redirect()->route('vendedor.dashboard')->with('success', "{$label} publicado con código {$codigo}. Quedó pendiente de aprobación.");
    }
/**
 * Cambiar el estado de la propiedad (disponible / reservado / vendido)
 * Soporta los 3 estados. Solo funciona si la propiedad está aprobada.
 */
    public function toggleEstado($id, Request $request)
    {
        $user = auth()->user();
        $terreno = Terreno::where('id', $id)
                        ->where('usuario_id', $user->id)
                        ->firstOrFail();

        // Solo permitir cambiar si está aprobado
        if ($terreno->estado !== 'aprobado') {
            return redirect()->back()->with('error', 'No puedes cambiar el estado de un terreno que no está aprobado.');
        }

        // Validar el estado recibido
        $request->validate([
            'estado_lote' => 'required|in:disponible,reservado,vendido'
        ]);

        $nuevoEstado = $request->input('estado_lote');
        $estadoAnterior = $terreno->estado_lote;

        // Si no hay cambio real, no hacer nada
        if ($estadoAnterior === $nuevoEstado) {
            return redirect()->back()->with('info', "El terreno ya está en estado '{$nuevoEstado}'.");
        }

        // Si se va a marcar como vendido, eliminar de todas las listas de favoritos
        if ($nuevoEstado === 'vendido') {
            \App\Models\Favorito::where('favoriteable_id', $terreno->id)
                                ->where('favoriteable_type', Terreno::class)
                                ->delete();
        }

        // Registrar historial de cambio de estado
        \App\Models\HistorialEstadoLote::create([
            'terreno_id' => $terreno->id,
            'usuario_id' => auth()->id(),
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $nuevoEstado,
            'fecha_cambio' => now(),
        ]);

        $terreno->estado_lote = $nuevoEstado;
        $terreno->actualizado_en = now();
        $terreno->save();

        Auditoria::registrar(
            'cambio_estado_terreno',
            'terreno',
            $terreno->id,
            "El vendedor {$user->nombre} cambió el estado del terreno/lote #{$terreno->id}: {$estadoAnterior} → {$nuevoEstado}"
        );

        $mensajes = [
            'disponible' => 'El terreno ahora está disponible para la venta.',
            'reservado'  => 'El terreno se ha marcado como RESERVADO.',
            'vendido'    => 'El terreno se ha marcado como VENDIDO y se eliminó de los favoritos.',
        ];

        return redirect()->back()->with('success', $mensajes[$nuevoEstado] ?? "Estado actualizado a '{$nuevoEstado}'.");
    }
}
