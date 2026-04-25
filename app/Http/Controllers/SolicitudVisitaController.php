<?php

namespace App\Http\Controllers;

use App\Models\SolicitudVisita;
use App\Models\Terreno;
use App\Models\Usuario;
use App\Models\DisponibilidadVendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\SolicitudCreada;
use App\Mail\SolicitudEstadoActualizado;

class SolicitudVisitaController extends Controller
{
    /**
     * Muestra la vista del calendario.
     */
    public function calendario()
    {
        $terrenos = Terreno::where('estado', 'aprobado')->get();
        $vendedores = Usuario::where('rol', 'vendedor')->where('activo', 1)->get();

        return view('solicitudes.calendario', compact('terrenos', 'vendedores'));
    }

    /**
     * Muestra el listado de solicitudes.
     */
    public function index()
    {
        $user = Auth::user();
        $query = SolicitudVisita::with(['usuario', 'terreno', 'vendedor']);

        if ($user->rol === 'admin') {
            $solicitudes = $query->orderByDesc('id')->paginate(10);
        } elseif ($user->rol === 'vendedor') {
            $solicitudes = $query->where('vendedor_id', $user->id)->orderByDesc('id')->paginate(10);
        } else {
            $solicitudes = $query->where('user_id', $user->id)->orderByDesc('id')->paginate(10);
        }

        return view('solicitudes.index', compact('solicitudes'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        $terrenos = Terreno::where('estado', 'aprobado')->get();
        $vendedores = Usuario::where('rol', 'vendedor')->where('activo', 1)->get();
        
        return view('solicitudes.create', compact('terrenos', 'vendedores'));
    }

    /**
     * Guardar solicitud.
     */
    public function store(Request $request)
    {
        $request->validate([
            'terreno_id' => 'required|exists:terrenos,id',
            'vendedor_id' => 'required|exists:usuarios,id',
            'fecha_visita' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
        ]);

        $conflicto = $this->verificarConflicto(
            $request->vendedor_id,
            $request->fecha_visita,
            $request->hora_inicio,
            $request->hora_fin
        );

        if ($conflicto) {
            return back()->withErrors(['horario' => 'El horario seleccionado tiene conflicto con la agenda del vendedor.'])->withInput();
        }

        $solicitud = SolicitudVisita::create([
            'user_id' => Auth::id(),
            'terreno_id' => $request->terreno_id,
            'vendedor_id' => $request->vendedor_id,
            'fecha_visita' => $request->fecha_visita,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'estado' => 'pendiente',
        ]);

        try {
            $solicitud->load(['usuario', 'vendedor', 'terreno']);
            Mail::to($solicitud->usuario->email)->send(new SolicitudCreada($solicitud, 'cliente'));
            Mail::to($solicitud->vendedor->email)->send(new SolicitudCreada($solicitud, 'vendedor'));
        } catch (\Exception $e) {
            Log::error('Error enviando mail: ' . $e->getMessage());
        }

        return redirect()->route('vendedor.solicitudes.index')->with('success', 'Solicitud creada con éxito.');
    }

    /**
     * API Verificar disponibilidad AJAX.
     */
    public function verificarDisponibilidad(Request $request)
    {
        try {
            $request->validate([
                'vendedor_id' => 'required|exists:usuarios,id',
                'fecha' => 'required|date',
                'hora_inicio' => 'required',
                'hora_fin' => 'required',
            ]);

            $conflicto = $this->verificarConflicto(
                $request->vendedor_id,
                $request->fecha,
                $request->hora_inicio,
                $request->hora_fin
            );

            return response()->json([
                'disponible' => !$conflicto,
                'mensaje' => !$conflicto ? 'Horario disponible' : 'Horario no permitido o vendedor ocupado.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * LOGICA FLEXIBLE: No bloquea si no hay registros en disponibilidad_vendedors.
     */
    private function verificarConflicto($vendedorId, $fecha, $horaInicio, $horaFin)
    {
        $diaSemana = $this->obtenerDiaSemanaEspanol($fecha);
        
        $disponibilidad = DisponibilidadVendedor::where('vendedor_id', $vendedorId)
            ->where('dia_semana', $diaSemana)
            ->where('activo', 1)
            ->first();

        // --- CAMBIO CLAVE: Si no hay horario fijo, NO BLOQUEAMOS el botón ---
        if ($disponibilidad) {
            // Si el admin puso horarios, respetamos el rango
            if (!$disponibilidad->contieneHorario($horaInicio, $horaFin)) {
                return true; 
            }
        }

        // Comprobamos si choca con OTRA visita ya aprobada o pendiente
        return SolicitudVisita::where('vendedor_id', $vendedorId)
            ->where('fecha_visita', $fecha)
            ->whereNotIn('estado', ['cancelada', 'rechazada'])
            ->where(function($query) use ($horaInicio, $horaFin) {
                $query->where('hora_inicio', '<', $horaFin)
                      ->where('hora_fin', '>', $horaInicio);
            })
            ->exists();
    }

    private function obtenerDiaSemanaEspanol($fecha): string
    {
        $dias = [1 => 'lunes', 2 => 'martes', 3 => 'miercoles', 4 => 'jueves', 5 => 'viernes', 6 => 'sabado', 7 => 'domingo'];
        return $dias[Carbon::parse($fecha)->dayOfWeekIso] ?? 'lunes';
    }

    /**
     * Eventos para el FullCalendar.
     */
    public function eventos(Request $request)
    {
        $user = Auth::user();
        $query = SolicitudVisita::with(['usuario', 'terreno', 'vendedor']);

        if ($user->rol === 'vendedor') {
            $query->where('vendedor_id', $user->id);
        } elseif ($user->rol !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return response()->json($query->get()->map(function($s) {
            return [
                'id' => $s->id,
                'title' => ($s->terreno->ubicacion ?? 'Terreno') . ' - ' . $s->usuario->nombre,
                'start' => $s->fecha_visita . 'T' . $s->hora_inicio,
                'end' => $s->fecha_visita . 'T' . $s->hora_fin,
                'color' => $this->getColorPorEstado($s->estado),
                'url' => route('vendedor.solicitudes.show', $s->id)
            ];
        }));
    }

    private function getColorPorEstado($estado)
    {
        return ['pendiente' => '#ffc107', 'aprobada' => '#28a745', 'rechazada' => '#dc3545', 'cancelada' => '#6c757d'][$estado] ?? '#007bff';
    }

    public function show($id) { return view('solicitudes.show', ['solicitud' => SolicitudVisita::with(['usuario', 'terreno', 'vendedor'])->findOrFail($id)]); }
    public function aprobar($id) { SolicitudVisita::findOrFail($id)->aprobar(Auth::id()); return back()->with('success', 'Aprobada.'); }
    public function rechazar(Request $request, $id) { SolicitudVisita::findOrFail($id)->rechazar($request->motivo, Auth::id()); return back()->with('success', 'Rechazada.'); }
    public function cancelar($id) { SolicitudVisita::findOrFail($id)->cancelar(Auth::id()); return back()->with('success', 'Cancelada.'); }
}