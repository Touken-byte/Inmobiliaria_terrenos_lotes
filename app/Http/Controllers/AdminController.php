<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\DocumentoCi;
use App\Models\HistorialVerificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Terreno;
use App\Models\Alquiler;
use App\Helpers\Auditoria;
use App\Models\TerrenoImagen;
use App\Mail\DocumentApproval;
use App\Mail\DocumentRejection;
use App\Mail\TerrenoAprobado;
use App\Mail\TerrenoRechazado;
use File;

class AdminController extends Controller
{
    public function panel(Request $request)
    {
        $filtroActual = $request->query('filtro', 'todos');

        // Estadísticas
        $stats = (object) [
            'total' => Usuario::where('rol', 'vendedor')->count(),
            'pendientes' => Usuario::where('rol', 'vendedor')->where('estado_verificacion', 'pendiente')->count(),
            'verificados' => Usuario::where('rol', 'vendedor')->where('estado_verificacion', 'verificado')->count(),
            'rechazados' => Usuario::where('rol', 'vendedor')->where('estado_verificacion', 'rechazado')->count(),
        ];

        // Consulta de vendedores
        $query = Usuario::where('rol', 'vendedor')
            ->select('usuarios.*', 'd.id as doc_id', 'd.nombre_archivo', 'd.tipo_mime', 'd.fecha_subida', 'd.nombre_original', 'd.tamano')
            ->leftJoin('documentos_ci as d', function ($join) {
                $join->on('usuarios.id', '=', 'd.usuario_id')
                    ->where('d.activo', 1);
            });

        if ($filtroActual !== 'todos') {
            $query->where('usuarios.estado_verificacion', $filtroActual);
        }

        $query->orderByRaw("CASE usuarios.estado_verificacion WHEN 'pendiente' THEN 1 WHEN 'rechazado' THEN 2 WHEN 'verificado' THEN 3 ELSE 4 END")
            ->orderBy('usuarios.fecha_registro', 'DESC');

        $vendedores = $query->get();

        return view('admin.panel', compact('stats', 'vendedores', 'filtroActual'));
    }

    public function verCI($id)
    {
        $vendedor = Usuario::where('id', $id)->where('rol', 'vendedor')->firstOrFail();

        $documento = DocumentoCi::where('usuario_id', $id)
            ->where('activo', 1)
            ->orderBy('fecha_subida', 'desc')
            ->first();

        // Formatear tamaño del documento si existe
        if ($documento) {
            $documento->tamano_formateado = $this->formatFileSize($documento->tamano);
        }

        $historial = HistorialVerificacion::with('admin')
            ->where('usuario_id', $id)
            ->orderBy('fecha', 'desc')
            ->get();

        return view('admin.ver_ci', compact('vendedor', 'documento', 'historial'));
    }

    public function editVendedor($id)
    {
        $vendedor = Usuario::where('id', $id)->where('rol', 'vendedor')->firstOrFail();
        return view('admin.editar_vendedor', compact('vendedor'));
    }

    public function updateVendedor(Request $request, $id)
    {
        $vendedor = Usuario::where('id', $id)->where('rol', 'vendedor')->firstOrFail();

        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $id,
            'telefono' => 'nullable|string|max:20',
            'estado_verificacion' => 'required|in:pendiente,verificado,rechazado',
            'activo' => 'boolean',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'estado_verificacion' => $request->estado_verificacion,
            'activo' => $request->has('activo'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $vendedor->update($data);

        Auditoria::registrar(
            'edicion_vendedor',
            'vendedor',
            $vendedor->id,
            "Admin editó datos del vendedor: {$vendedor->nombre}"
        );

        return redirect()->route('admin.panel')->with('success', "Vendedor {$vendedor->nombre} actualizado correctamente.");
    }

    public function deleteVendedor($id)
    {
        $vendedor = Usuario::where('id', $id)->where('rol', 'vendedor')->firstOrFail();
        $nombre = $vendedor->nombre;

        Auditoria::registrar(
            'eliminacion_vendedor',
            'vendedor',
            $id,
            "Admin eliminó al vendedor: {$nombre}"
        );

        $vendedor->delete();

        return redirect()->route('admin.panel')->with('success', "Vendedor {$nombre} eliminado permanentemente.");
    }

    public function servirCI($id)
    {
        $doc = DocumentoCi::findOrFail($id);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('private');

        // Verificar si el archivo existe usando el disco private
        if (!$disk->exists($doc->nombre_archivo)) {
            abort(404, 'El archivo no fue encontrado en el servidor.');
        }

        return $disk->response($doc->nombre_archivo, $doc->nombre_original);
    }

    public function procesarVerificacion(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|integer',
            'accion' => 'required|in:aprobado,rechazado',
            'comentario' => 'required_if:accion,rechazado'
        ]);

        $adminId = Auth::id();
        $vendedorId = $request->input('usuario_id');
        $accion = $request->input('accion');
        $comentario = $request->input('comentario');

        $vendedor = Usuario::where('id', $vendedorId)->where('rol', 'vendedor')->firstOrFail();

        $doc = DocumentoCi::where('usuario_id', $vendedorId)->where('activo', 1)->orderBy('fecha_subida', 'desc')->first();

        $nuevoEstado = $accion === 'aprobado' ? 'verificado' : 'rechazado';

        $vendedor->estado_verificacion = $nuevoEstado;
        $vendedor->save();

        HistorialVerificacion::create([
            'usuario_id' => $vendedorId,
            'admin_id' => $adminId,
            'accion' => $accion,
            'comentario' => trim($comentario),
            'documento_id' => $doc ? $doc->id : null,
        ]);

        try {
            if ($accion === 'aprobado') {
                Mail::to($vendedor->email)->send(new DocumentApproval($vendedor));
            } else {
                Mail::to($vendedor->email)->send(new DocumentRejection($vendedor, $comentario));
            }
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de notificación: ' . $e->getMessage());
        }

        // Registrar auditoría
        Auditoria::registrar(
            $accion === 'aprobado' ? 'aprobacion_vendedor' : 'rechazo_vendedor',
            'vendedor',
            $vendedorId,
            ($accion === 'aprobado' ? 'Vendedor aprobado' : 'Vendedor rechazado') . ": {$vendedor->nombre}" . ($comentario ? " — Motivo: {$comentario}" : '')
        );

        $accionTexto = $accion === 'aprobado' ? 'aprobado ✅' : 'rechazado ❌';
        return redirect()->route('admin.panel')->with('success', "Vendedor \"{$vendedor->nombre}\" {$accionTexto} exitosamente.");
    }

    public function historial(Request $request)
    {
        $filtros = [
            'vendedor_id' => $request->query('vendedor_id', ''),
            'accion' => $request->query('accion', ''),
            'fecha_desde' => $request->query('fecha_desde', ''),
            'fecha_hasta' => $request->query('fecha_hasta', '')
        ];

        $query = HistorialVerificacion::select('historial_verificacion.*', 'u.nombre as vendedor_nombre', 'u.email as vendedor_email', 'a.nombre as admin_nombre')
            ->join('usuarios as u', 'historial_verificacion.usuario_id', '=', 'u.id')
            ->join('usuarios as a', 'historial_verificacion.admin_id', '=', 'a.id');

        if (!empty($filtros['vendedor_id'])) {
            $query->where('historial_verificacion.usuario_id', $filtros['vendedor_id']);
        }
        if (!empty($filtros['accion'])) {
            $query->where('historial_verificacion.accion', $filtros['accion']);
        }
        if (!empty($filtros['fecha_desde'])) {
            $query->whereDate('historial_verificacion.fecha', '>=', $filtros['fecha_desde']);
        }
        if (!empty($filtros['fecha_hasta'])) {
            $query->whereDate('historial_verificacion.fecha', '<=', $filtros['fecha_hasta']);
        }

        $registros = $query->orderBy('historial_verificacion.fecha', 'desc')->limit(200)->get();

        $vendedores = Usuario::where('rol', 'vendedor')->orderBy('nombre', 'asc')->get();

        return view('admin.historial', compact('registros', 'vendedores', 'filtros'));
    }

    public function crearVendedor(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string',
            'telefono' => 'nullable|string|max:20'
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'rol' => 'vendedor',
            'estado_verificacion' => 'pendiente',
            'activo' => 1
        ]);

        return redirect()->route('admin.panel')->with('success', "Vendedor {$request->nombre} creado exitosamente.");
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
    // GESTIÓN DE TERRENOS Y MODERACIÓN (IN-U01)
    // ═══════════════════════════════════════════════════════

    public function moderacionPanel()
    {
        $stats = (object) [
            'pendientes' => Terreno::where('estado', 'pendiente')->count() + Alquiler::where('estado_aprobacion', 'pendiente')->count(),
            'total_aprobados' => Terreno::where('estado', 'aprobado')->count() + Alquiler::where('estado_aprobacion', 'aprobado')->count(),
        ];

        $terrenos = Terreno::with(['vendedor', 'imagenes'])
            ->where('estado', 'pendiente')
            ->orderBy('creado_en', 'ASC')
            ->get();
                    
        $alquileres = Alquiler::with(['usuario', 'imagenes'])
            ->where('estado_aprobacion', 'pendiente')
            ->orderBy('created_at', 'ASC')
            ->get();

        return view('admin.moderacion', compact('stats', 'terrenos', 'alquileres'));
    }

    public function terrenosPanel(Request $request)
    {
        $filtroActual = $request->query('filtro', 'todos');

        $stats = (object) [
            'total' => Terreno::count(),
            'pendientes' => Terreno::where('estado', 'pendiente')->count(),
            'aprobados' => Terreno::where('estado', 'aprobado')->count(),
            'rechazados' => Terreno::where('estado', 'rechazado')->count(),
        ];

        $query = Terreno::with(['vendedor', 'imagenes'])
            ->select('terrenos.*');

        if ($filtroActual !== 'todos') {
            $query->where('terrenos.estado', $filtroActual);
        }

        $query->orderByRaw("CASE terrenos.estado WHEN 'pendiente' THEN 1 WHEN 'rechazado' THEN 2 WHEN 'aprobado' THEN 3 ELSE 4 END")
            ->orderBy('terrenos.creado_en', 'DESC');

        $terrenos = $query->get();

        return view('admin.terrenos', compact('stats', 'terrenos', 'filtroActual'));
    }

    public function verTerreno($id)
    {
        $terreno = Terreno::with(['vendedor', 'imagenes', 'adminAprobador', 'folio.adminVerificador'])->findOrFail($id);

        return view('admin.ver_terreno', compact('terreno'));
    }

    public function verAlquiler($id)
    {
        $alquiler = Alquiler::with(['usuario', 'imagenes'])->findOrFail($id);
                
        return view('admin.ver_alquiler', compact('alquiler'));
    }

    public function procesarTerreno(Request $request)
    {
        $request->validate([
            'terreno_id' => 'required|integer',
            'accion' => 'required|in:aprobado,rechazado',
            'observacion' => 'required_if:accion,rechazado'
        ]);

        $adminId = Auth::id();
        $terrenoId = $request->input('terreno_id');
        $accion = $request->input('accion');
        $observacion = $request->input('observacion');

        $terreno = Terreno::findOrFail($terrenoId);

        $terreno->estado = $accion;
        $terreno->id_admin_aprobador = $adminId;
        $terreno->actualizado_en = now();
                
        if ($accion === 'rechazado') {
            $terreno->motivo_rechazo = $observacion;
        } else {
            $terreno->motivo_rechazo = null; // Limpiar motivo si fue aprobado
        }
                
        $terreno->save();

        // Enviar correos
        try {
            if ($accion === 'aprobado') {
                Mail::to($terreno->vendedor->email)->send(new TerrenoAprobado($terreno, $terreno->vendedor));
            } else {
                Mail::to($terreno->vendedor->email)->send(new TerrenoRechazado($terreno, $terreno->vendedor, $observacion));
            }
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de terreno: ' . $e->getMessage());
        }

        // Registrar auditoría
        Auditoria::registrar(
            $accion === 'aprobado' ? 'aprobacion_terreno' : 'rechazo_terreno',
            'terreno',
            $terrenoId,
            ($accion === 'aprobado' ? 'Terreno aprobado' : 'Terreno rechazado') . ": #{$terreno->id}" . ($observacion ? " — Observación: {$observacion}" : '')
        );

        $accionTexto = $accion === 'aprobado' ? 'aprobado ✅' : 'rechazado ❌';
        $msgExtra = $observacion ? " Observación: {$observacion}" : '';
                
        $previousUrl = url()->previous();
        $isModeracion = str_contains($previousUrl, route('admin.moderacion_panel'));
        $rutaDestino = $isModeracion ? 'admin.moderacion_panel' : 'admin.terrenos_panel';
                
        return redirect()->route($rutaDestino)->with('success', "Terreno #{$terreno->id} {$accionTexto} exitosamente.{$msgExtra}");
    }

    public function procesarAlquiler(Request $request)
    {
        $request->validate([
            'alquiler_id' => 'required|integer',
            'accion' => 'required|in:aprobado,rechazado'
        ]);

        $alquilerId = $request->input('alquiler_id');
        $accion = $request->input('accion');

        $alquiler = Alquiler::findOrFail($alquilerId);
        $alquiler->estado_aprobacion = $accion;
        $alquiler->save();

        $accionTexto = $accion === 'aprobado' ? 'aprobado ✅' : 'rechazado ❌';
                
        $previousUrl = url()->previous();
        $isModeracion = str_contains($previousUrl, route('admin.moderacion_panel'));
        $rutaDestino = $isModeracion ? 'admin.moderacion_panel' : 'admin.panel';
                
        return redirect()->route($rutaDestino)->with('success', "Alquiler #{$alquiler->id} {$accionTexto} exitosamente.");
    }

    // ═══════════════════════════════════════════════════════
    // CONTROL DE LOTES (LECTURA)
    // ═══════════════════════════════════════════════════════

    public function controlLotes()
    {
        $terrenos = Terreno::orderBy('actualizado_en', 'DESC')->orderBy('creado_en', 'DESC')->get();
        // 👇 NUEVO: Obtener vendedores
        $vendedores = \App\Models\Usuario::where('rol', 'vendedor')->get();
        
        return view('shared.lotes', compact('terrenos', 'vendedores'));
    }

    public function getInventarioStats(Request $request)
    {
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');
        $ubicacion = $request->query('ubicacion');
        $vendedorId = $request->query('vendedor_id');

        $query = Terreno::query();

        if ($fechaDesde) {
            $query->whereDate('creado_en', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->whereDate('creado_en', '<=', $fechaHasta);
        }
        if ($ubicacion) {
            $query->where('ubicacion', 'like', '%' . $ubicacion . '%');
        }
        if ($vendedorId) {
            $query->where('vendedor_id', $vendedorId);
        }

        $terrenos = $query->select('id', 'creado_en', 'actualizado_en', 'estado_lote', 'precio')->get();

        $data = [];
        $meses = [];
        
        // Variables para Distribución (Dona) y KPIs
        $globalCounts = ['disponible' => 0, 'vendido' => 0, 'reservado' => 0];
        $financials = ['disponible' => 0, 'vendido' => 0, 'reservado' => 0];
        
        $ventasEsteMes = 0;
        $ventasMesPasado = 0;
        $tiempoVentaTotal = 0; // en días
        
        $hoy = \Carbon\Carbon::now();
        $mesActual = $hoy->format('Y-m');
        $mesPasado = $hoy->copy()->subMonth()->format('Y-m');

        foreach ($terrenos as $t) {
            // Totales globales y financieros
            $estado = strtolower($t->estado_lote ?? 'disponible');
            $precio = (float)($t->precio ?? 0);
            
            if (isset($globalCounts[$estado])) {
                $globalCounts[$estado]++;
                $financials[$estado] += $precio;
            } else {
                $globalCounts['disponible']++;
                $financials['disponible'] += $precio;
            }

            // Agrupación por meses para barras
            if ($t->creado_en) {
                $mes = \Carbon\Carbon::parse($t->creado_en)->format('Y-m');
                
                if (!in_array($mes, $meses)) {
                    $meses[] = $mes;
                    $data[$mes] = ['disponible' => 0, 'vendido' => 0, 'reservado' => 0, 'dinero_vendido' => 0];
                }
                
                if (isset($data[$mes][$estado])) {
                    $data[$mes][$estado]++;
                    if ($estado === 'vendido') {
                        $data[$mes]['dinero_vendido'] += $precio;
                    }
                } else {
                    $data[$mes]['disponible']++;
                }
            }
            
            // Lógica para KPIs
            if ($estado === 'vendido') {
                $fechaVenta = $t->actualizado_en ? \Carbon\Carbon::parse($t->actualizado_en) : null;
                $fechaCreacion = $t->creado_en ? \Carbon\Carbon::parse($t->creado_en) : null;
                
                if ($fechaVenta && $fechaCreacion) {
                    $dias = $fechaCreacion->diffInDays($fechaVenta);
                    $tiempoVentaTotal += $dias;
                }
                
                if ($fechaVenta) {
                    $mesVenta = $fechaVenta->format('Y-m');
                    if ($mesVenta === $mesActual) {
                        $ventasEsteMes++;
                    } elseif ($mesVenta === $mesPasado) {
                        $ventasMesPasado++;
                    }
                }
            }
        }

        sort($meses);

        $labels = [];
        $disponibles = [];
        $vendidos = [];
        $reservados = [];
        $dinero_vendido = [];

        foreach ($meses as $mes) {
            $labelFormateado = \Carbon\Carbon::createFromFormat('Y-m', $mes)->locale('es')->translatedFormat('M Y');
            $labels[] = ucfirst($labelFormateado);
            $disponibles[] = $data[$mes]['disponible'];
            $vendidos[] = $data[$mes]['vendido'];
            $reservados[] = $data[$mes]['reservado'];
            $dinero_vendido[] = $data[$mes]['dinero_vendido'];
        }
        
        // Calcular crecimiento
        $crecimientoPorcentaje = 0;
        if ($ventasMesPasado > 0) {
            $crecimientoPorcentaje = (($ventasEsteMes - $ventasMesPasado) / $ventasMesPasado) * 100;
        } elseif ($ventasEsteMes > 0) {
            $crecimientoPorcentaje = 100; // Si antes era 0 y ahora vendió algo, crecimiento del 100%
        }
        
        // Calcular promedio venta
        $promedioDiasVenta = $globalCounts['vendido'] > 0 ? round($tiempoVentaTotal / $globalCounts['vendido']) : 0;

        return response()->json([
            'labels' => $labels,
            'disponibles' => $disponibles,
            'vendidos' => $vendidos,
            'reservados' => $reservados,
            'dinero_vendido' => $dinero_vendido,
            'global' => $globalCounts,
            'financials' => $financials,
            'kpis' => [
                'ventas_mes' => $ventasEsteMes,
                'crecimiento' => round($crecimientoPorcentaje),
                'disponibles_total' => $globalCounts['disponible'],
                'promedio_dias' => $promedioDiasVenta
            ]
        ]);
    }

    // ═══════════════════════════════════════════════════════
    // GESTIÓN DE FOLIOS
    // ═══════════════════════════════════════════════════════

    public function foliosPanel()
    {
        $folios = \App\Models\Folio::with(['terreno.vendedor'])
            ->orderByRaw("CASE estado WHEN 'pendiente' THEN 1 WHEN 'verificado' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('admin.folios', compact('folios'));
    }

    public function verificarFolio(Request $request)
    {
        $request->validate([
            'folio_id' => 'required|integer',
            'accion'   => 'required|in:verificado,rechazado',
        ]);

        $folio = \App\Models\Folio::findOrFail($request->folio_id);

        $folio->update([
            'estado'         => $request->accion,
            'verificado_por' => Auth::id(),
        ]);

        // Registrar auditoría
        Auditoria::registrar(
            $request->accion === 'verificado' ? 'verificacion_folio' : 'rechazo_folio',
            'folio',
            $folio->id,
            ($request->accion === 'verificado' ? 'Folio verificado' : 'Folio rechazado') . ": {$folio->numero_folio}"
        );

        $accionTexto = $request->accion === 'verificado' ? 'verificado ✅' : 'rechazado ❌';

        return redirect()->route('admin.folios_panel')
            ->with('success', "Folio #{$folio->numero_folio} {$accionTexto} exitosamente.");
    }

    public function actualizarCoordenadas(Request $request, $id)
    {
        $request->validate([
            'latitud'  => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
        ], [
            'latitud.between'  => 'La latitud debe estar entre -90 y 90.',
            'longitud.between' => 'La longitud debe estar entre -180 y 180.',
        ]);

        $terreno = Terreno::findOrFail($id);
        $terreno->latitud     = $request->latitud;
        $terreno->longitud    = $request->longitud;
        $terreno->actualizado_en = now();
        $terreno->save();

        Auditoria::registrar(
            'edicion_coordenadas',
            'terreno',
            $terreno->id,
            "Admin actualizó coordenadas del terreno #{$terreno->id}: lat {$request->latitud}, lng {$request->longitud}"
        );

        return redirect()->route('admin.ver_terreno', $terreno->id)
            ->with('success_mapa', '✅ Coordenadas actualizadas correctamente.');
    }
}