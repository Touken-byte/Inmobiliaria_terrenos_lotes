<?php

namespace App\Http\Controllers;

use App\Models\DocumentoCi;
use App\Models\HistorialVerificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Terreno;
use App\Models\Alquiler;
use App\Helpers\Auditoria;
use File;

class VendedorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Obtener documento activo actual
        $documento = DocumentoCi::where('usuario_id', $user->id)
            ->where('activo', 1)
            ->orderBy('fecha_subida', 'desc')
            ->first();

        // Formatear tamaño del documento si existe
        if ($documento) {
            $documento->tamano_formateado = $this->formatFileSize($documento->tamano);
        }

        // Obtener historial de verificaciones
        $historial = HistorialVerificacion::with('admin')
            ->where('usuario_id', $user->id)
            ->orderBy('fecha', 'desc')
            ->get();

        return view('vendedor.dashboard', [
            'estado' => $user->estado_verificacion,
            'documento' => $documento,
            'historial' => $historial
        ]);
    }

    public function subirCI(Request $request)
    {
        $request->validate([
            'documento_ci' => 'required|file|mimes:jpg,jpeg,png,pdf,application/pdf|max:10240',
        ], [
            'documento_ci.required' => 'Debe seleccionar un archivo para subir.',
            'documento_ci.mimes' => 'El archivo no es válido. Solo se aceptan JPG, PNG o PDF.',
            'documento_ci.max' => 'El archivo supera el límite de 10MB.',
        ]);

        $user = Auth::user();
        $file = $request->file('documento_ci');

        // Log para depuración
        \Log::info('Subiendo CI', [
            'nombre' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        // Desactivar documentos anteriores
        DocumentoCi::where('usuario_id', $user->id)->where('activo', 1)->update(['activo' => 0]);

        $path = $file->store('documentos_ci', 'private');

        DocumentoCi::create([
            'usuario_id' => $user->id,
            'nombre_archivo' => $path,
            'nombre_original' => $file->getClientOriginalName(),
            'tipo_mime' => $file->getMimeType(),
            'tamano' => $file->getSize(),
            'activo' => 1
        ]);

        $user->estado_verificacion = 'pendiente';
        $user->save();

        Auditoria::registrar(
            'subida_documento_ci',
            'vendedor',
            $user->id,
            "Vendedor subió nuevo documento CI: {$file->getClientOriginalName()}"
        );

        return redirect()->route('vendedor.dashboard')->with('success', 'Documento subido exitosamente. Será revisado por un administrador.');
    }

    public function servirMiCI()
    {
        $user = Auth::user();

        $documento = DocumentoCi::where('usuario_id', $user->id)
            ->where('activo', 1)
            ->orderBy('fecha_subida', 'desc')
            ->first();

        if (!$documento) {
            abort(404, 'No tiene un documento CI activo.');
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('private');

        // Verificar si el archivo existe usando el disco private
        if (!$disk->exists($documento->nombre_archivo)) {
            abort(404, 'Archivo no encontrado en el servidor.');
        }

        return $disk->response($documento->nombre_archivo, $documento->nombre_original);
    }

    public function historialPropio()
    {
        $user = Auth::user();
        $historial = HistorialVerificacion::with('admin')
            ->where('usuario_id', $user->id)
            ->orderBy('fecha', 'desc')
            ->get();
        return view('vendedor.historial', compact('historial'));
    }

    private function formatFileSize($bytes)
    {
        if ($bytes === 0)
            return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return floatval(sprintf("%.2f", $bytes / pow($k, $i))) . ' ' . $sizes[$i];
    }

    // ═══════════════════════════════════════════════════════
    // CONTROL DE LOTES
    // ═══════════════════════════════════════════════════════

    public function controlLotes()
    {
        $user = Auth::user();
        $terrenos = Terreno::where('usuario_id', $user->id)
            ->where('tipo', 'lote')
            ->orderBy('actualizado_en', 'DESC')
            ->orderBy('creado_en', 'DESC')
            ->get();
                    
        return view('shared.lotes', compact('terrenos'));
    }

    public function updateLoteEstado(Request $request, $id)
    {
        $request->validate([
            'estado_lote' => 'required|in:disponible,reservado,vendido'
        ]);

        $terreno = Terreno::where('id', $id)->where('tipo', 'lote')->firstOrFail();

        // Seguridad: Asegurar que el terreno pertenece al vendedor actual
        if ($terreno->usuario_id !== Auth::id()) {
            abort(403, 'No tienes permiso para modificar este lote.');
        }

        if ($terreno->estado !== 'aprobado') {
            return redirect()->back()->with('error', 'Solo puedes cambiar el estado de lotes aprobados.');
        }

        $estadoAnterior = $terreno->estado_lote;
        $estadoNuevo = $request->input('estado_lote');

        if ($estadoAnterior !== $estadoNuevo) {
            \App\Models\HistorialEstadoLote::create([
                'terreno_id' => $terreno->id,
                'usuario_id' => Auth::id(),
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
                'fecha_cambio' => now(),
            ]);
        }

        $terreno->estado_lote = $estadoNuevo;
        $terreno->actualizado_en = now();
        $terreno->save();

        Auditoria::registrar(
            'cambio_estado_lote',
            'terreno',
            $terreno->id,
            "Vendedor cambió estado del lote #{$terreno->id}: {$estadoAnterior} → {$estadoNuevo}"
        );

        return redirect()->back()->with('success', "El estado de tu lote #{$terreno->id} se ha actualizado a '{$terreno->estado_lote}'.");
    }

    public function eliminarCI()
    {
        $user = Auth::user();
        $documento = DocumentoCi::where('usuario_id', $user->id)->where('activo', 1)->first();

        if (!$documento) {
            return redirect()->route('vendedor.dashboard')->with('error', 'No tienes ningún documento activo para eliminar.');
        }

        // Eliminar archivo físico
        Storage::disk('private')->delete($documento->nombre_archivo);

        // Desactivar documento
        $documento->activo = 0;
        $documento->save();

        // Resetear estado de verificación
        $user->estado_verificacion = 'pendiente';
        $user->save();

        return redirect()->route('vendedor.dashboard')->with('success', 'Documento eliminado correctamente. Puedes subir uno nuevo.');
    }

    public function propiedadesPanel(Request $request)
    {
        $user = Auth::user();
        
        $tipoActual   = $request->query('tipo', 'todos');
        $filtroActual = $request->query('filtro', 'todos'); // estado de aprobación
        $estadoActual = $request->query('estado_disponibilidad', 'todos'); // disponible, reservado, vendido, alquilado
        $search       = $request->query('search', '');

        // Calcular estadísticas consolidadas
        $stats = (object) [
            'total' => Terreno::where('usuario_id', $user->id)->count() + Alquiler::where('user_id', $user->id)->count(),
            'pendientes' => Terreno::where('usuario_id', $user->id)->where('estado', 'pendiente')->count() + Alquiler::where('user_id', $user->id)->where('estado_aprobacion', 'pendiente')->count(),
            'aprobados' => Terreno::where('usuario_id', $user->id)->where('estado', 'aprobado')->count() + Alquiler::where('user_id', $user->id)->where('estado_aprobacion', 'aprobado')->count(),
            'rechazados' => Terreno::where('usuario_id', $user->id)->where('estado', 'rechazado')->count() + Alquiler::where('user_id', $user->id)->where('estado_aprobacion', 'rechazado')->count(),
        ];

        $properties = collect();
        $terrenoIds = [];
        $alquilerIds = [];

        // 1. Obtener Terrenos (Terrenos y Lotes)
        if ($tipoActual === 'todos' || $tipoActual === 'terreno' || $tipoActual === 'lote') {
            $query = Terreno::where('usuario_id', $user->id)->with(['imagenes', 'portada', 'folio.inscripcionDerechosReales']);

            if ($tipoActual === 'terreno') {
                $query->where('tipo', 'terreno');
            } elseif ($tipoActual === 'lote') {
                $query->where('tipo', 'lote');
            }

            if ($filtroActual !== 'todos') {
                $query->where('estado', $filtroActual);
            }

            if ($estadoActual !== 'todos') {
                if (in_array($estadoActual, ['disponible', 'reservado', 'vendido'])) {
                    $query->where('estado_lote', $estadoActual);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'LIKE', "%{$search}%")
                      ->orWhere('ubicacion', 'LIKE', "%{$search}%")
                      ->orWhere('descripcion', 'LIKE', "%{$search}%")
                      ->orWhere('codigo', 'LIKE', "%{$search}%");
                });
            }

            $properties = $properties->merge($query->get()->map(function($t) use (&$terrenoIds) {
                $terrenoIds[] = $t->id;

                return (object)[
                    'id' => $t->id,
                    'tipo' => $t->tipo ?? 'terreno',
                    'nombre' => $t->nombre,
                    'codigo' => $t->codigo,
                    'ubicacion' => $t->ubicacion,
                    'precio' => $t->precio,
                    'moneda' => $t->moneda ?? 'USD',
                    'metros_cuadrados' => $t->metros_cuadrados,
                    'imagen' => $t->portada ? $t->portada->ruta_archivo : ($t->imagenes->first() ? $t->imagenes->first()->ruta_archivo : null),
                    'estado_aprobacion' => $t->estado, // pendiente, aprobado, rechazado
                    'estado_disponibilidad' => $t->estado_lote, // disponible, reservado, vendido
                    'motivo_rechazo' => $t->motivo_rechazo,
                    'fecha' => $t->creado_en,
                    'edit_route' => route('vendedor.terrenos.edit', $t->id),
                    'catalog_route' => route('catalogo.detalle', $t->id),
                    'documentos_route' => route('vendedor.documentos.subir', $t->id),
                    'folio' => $t->folio,
                    'chat_id' => null,
                    'chat_route' => null,
                    'unread_count' => 0,
                ];
            }));
        }

        // 2. Obtener Alquileres
        if ($tipoActual === 'todos' || $tipoActual === 'alquiler') {
            $query = Alquiler::where('user_id', $user->id)->with(['imagenes', 'portada']);

            if ($filtroActual !== 'todos') {
                $query->where('estado_aprobacion', $filtroActual);
            }

            if ($estadoActual !== 'todos') {
                if (in_array($estadoActual, ['disponible', 'reservado', 'vendido'])) {
                    $query->where('estado', $estadoActual);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('titulo', 'LIKE', "%{$search}%")
                      ->orWhere('ubicacion', 'LIKE', "%{$search}%")
                      ->orWhere('descripcion', 'LIKE', "%{$search}%");
                });
            }

            $properties = $properties->merge($query->get()->map(function($a) use (&$alquilerIds) {
                $alquilerIds[] = $a->id;

                return (object)[
                    'id' => $a->id,
                    'tipo' => 'alquiler',
                    'nombre' => $a->titulo,
                    'codigo' => 'ALQ-' . str_pad($a->id, 3, '0', STR_PAD_LEFT),
                    'ubicacion' => $a->ubicacion,
                    'precio' => $a->precio_mensual,
                    'moneda' => 'BOB',
                    'metros_cuadrados' => $a->metros_cuadrados,
                    'imagen' => $a->portada ? $a->portada->ruta_archivo : ($a->imagenes->first() ? $a->imagenes->first()->ruta_archivo : null),
                    'estado_aprobacion' => $a->estado_aprobacion, // pendiente, aprobado, rechazado
                    'estado_disponibilidad' => $a->estado, // disponible, reservado, vendido
                    'motivo_rechazo' => $a->motivo_rechazo,
                    'fecha' => $a->created_at,
                    'edit_route' => route('vendedor.alquileres.edit', $a->id),
                    'catalog_route' => route('catalogo.detalle.alquiler', $a->id),
                    'documentos_route' => null,
                    'folio' => null,
                    'chat_id' => null,
                    'chat_route' => null,
                    'unread_count' => 0,
                ];
            }));
        }

        $chatQuery = \App\Models\Chat::with(['lead'])
            ->withCount(['mensajes as unread_count' => function ($q) use ($user) {
                $q->where('leido', false)
                  ->where('user_id', '!=', $user->id)
                  ->where('user_id', '!=', $user->id);
            }]);

        if (!empty($terrenoIds) && !empty($alquilerIds)) {
            $chatQuery->whereHas('lead', function ($q) use ($terrenoIds, $alquilerIds) {
                $q->whereIn('terreno_id', $terrenoIds)
                  ->orWhereIn('alquiler_id', $alquilerIds);
            });
        } elseif (!empty($terrenoIds)) {
            $chatQuery->whereHas('lead', function ($q) use ($terrenoIds) {
                $q->whereIn('terreno_id', $terrenoIds);
            });
        } elseif (!empty($alquilerIds)) {
            $chatQuery->whereHas('lead', function ($q) use ($alquilerIds) {
                $q->whereIn('alquiler_id', $alquilerIds);
            });
        }

        $chats = $chatQuery->get();
        $chatByTerreno = [];
        $chatByAlquiler = [];

        foreach ($chats as $chat) {
            if ($chat->lead && $chat->lead->terreno_id) {
                $chatByTerreno[$chat->lead->terreno_id] = $chat;
            }
            if ($chat->lead && $chat->lead->alquiler_id) {
                $chatByAlquiler[$chat->lead->alquiler_id] = $chat;
            }
        }

        $properties = $properties->map(function ($p) use ($chatByTerreno, $chatByAlquiler) {
            $chat = $p->tipo === 'alquiler'
                ? ($chatByAlquiler[$p->id] ?? null)
                : ($chatByTerreno[$p->id] ?? null);

            $p->chat_id = $chat->id ?? null;
            $p->chat_route = $chat ? route('chat.show', $chat->id) : null;
            $p->unread_count = $chat ? ($chat->unread_count ?? 0) : 0;

            return $p;
        });

        // Ordenar la colección unificada por fecha descendente
        $properties = $properties->sortByDesc('fecha');

        // Paginación manual de la colección
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $properties->slice(($currentPage - 1) * $perPage, $perPage)->values()->all();
        $paginatedProperties = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $properties->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
        );
        $paginatedProperties->appends($request->query());

        return view('vendedor.propiedades', compact('stats', 'paginatedProperties', 'tipoActual', 'filtroActual', 'estadoActual', 'search'));
    }
}
