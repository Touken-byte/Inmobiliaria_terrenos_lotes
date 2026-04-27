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

        /* Order by custom condition like in Express: 
           ORDER BY CASE u.estado_verificacion WHEN 'pendiente' THEN 1 WHEN 'rechazado' THEN 2 WHEN 'verificado' THEN 3 ELSE 4 END
        */
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

        return redirect()->route('admin.panel')->with('success', "Vendedor {$vendedor->nombre} actualizado correctamente.");
    }

    public function deleteVendedor($id)
    {
        $vendedor = Usuario::where('id', $id)->where('rol', 'vendedor')->firstOrFail();
        $nombre = $vendedor->nombre;

        // Opcional: eliminar también documentos, terrenos, etc.
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
            'pendientes' => Terreno::where('estado', 'pendiente')->count(),
            'total_aprobados' => Terreno::where('estado', 'aprobado')->count(),
        ];

        $terrenos = Terreno::with(['vendedor', 'imagenes'])
            ->where('estado', 'pendiente')
            ->orderBy('creado_en', 'ASC')
            ->get();

        return view('admin.moderacion', compact('stats', 'terrenos'));
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
        $terreno = Terreno::with(['vendedor', 'imagenes', 'adminAprobador'])->findOrFail($id);

        return view('admin.ver_terreno', compact('terreno'));
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

        $accionTexto = $accion === 'aprobado' ? 'aprobado ✅' : 'rechazado ❌';
        $msgExtra = $observacion ? " Observación: {$observacion}" : '';
        
        // Si venimos de moderacion, volver a moderacion, si no, a terrenos_panel
        // Note: back()->getTargetUrl() is tricky, let's just use url()->previous()
        $previousUrl = url()->previous();
        $isModeracion = str_contains($previousUrl, route('admin.moderacion_panel'));
        $rutaDestino = $isModeracion ? 'admin.moderacion_panel' : 'admin.terrenos_panel';
        
        return redirect()->route($rutaDestino)->with('success', "Terreno #{$terreno->id} {$accionTexto} exitosamente.{$msgExtra}");
    }

    // ═══════════════════════════════════════════════════════
    // CONTROL DE LOTES (LECTURA)
    // ═══════════════════════════════════════════════════════

    public function controlLotes()
    {
        $terrenos = Terreno::orderBy('actualizado_en', 'DESC')->orderBy('creado_en', 'DESC')->get();
        return view('shared.lotes', compact('terrenos'));
    }
}

