<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Minuta;
use App\Models\ComprobanteIt;
use App\Models\Terreno;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MinutaController extends Controller
{
    // ─── Admin: formulario de creación (ruta original intacta) ───────────────
    public function create()
    {
        $terrenos   = Terreno::all();
        $compradores = Usuario::where('rol', 'comprador')->get();
        $vendedores  = Usuario::where('rol', 'vendedor')->get();

        return view('minutas.create', compact('terrenos', 'compradores', 'vendedores'));
    }

    // ─── Admin: guardar minuta (ruta original intacta) ────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'terreno_id'   => 'required|exists:terrenos,id',
            'comprador_id' => 'required|exists:usuarios,id',
            'vendedor_id'  => 'required|exists:usuarios,id|different:comprador_id',
            'monto'        => 'required|numeric|min:0',
            'fecha'        => 'required|date',
            'archivo'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $ruta = null;
        if ($request->hasFile('archivo')) {
            $ruta = $request->file('archivo')->store('minutas', 'public');
        }

        Minuta::create([
            'terreno_id'   => $request->terreno_id,
            'comprador_id' => $request->comprador_id,
            'vendedor_id'  => $request->vendedor_id,
            'monto'        => $request->monto,
            'fecha'        => $request->fecha,
            'archivo'      => $ruta,
            'estado'       => 'pendiente',
        ]);

        return redirect()->back()->with('success', 'Minuta registrada correctamente');
    }

    // ─── Admin: listado (ruta original intacta) ───────────────────────────────
    public function index()
    {
        $minutas = Minuta::with(['terreno', 'comprador', 'vendedor'])->latest()->get();
        return view('minutas.index', compact('minutas'));
    }

    // ─── VENDEDOR: Vista unificada Proceso Legal ──────────────────────────────
    public function tramiteLegal()
    {
        $user = Auth::user();

        // Minuta más reciente del vendedor
        $minuta = Minuta::with(['terreno', 'comprador'])
            ->where('vendedor_id', $user->id)
            ->latest()
            ->first();

        // Comprobante IT más reciente del vendedor vinculado a ESTA minuta
        $comprobante = null;
        if ($minuta) {
            $comprobante = ComprobanteIt::where('minuta_id', $minuta->id)
                ->latest()
                ->first();
        }

        // SI EL TRÁMITE ESTÁ COMPLETADO: "Limpiamos" las variables activas para 
        // que el vendedor pueda iniciar un NUEVO trámite desde cero.
        if ($minuta && $minuta->estado === 'completada') {
            $minuta = null;
            $comprobante = null;
        }

        // Lista de terrenos del vendedor para el formulario de minuta
        // Filtrar terrenos que ya tienen un proceso legal activo de cualquier vendedor
        $terrenosOcupados = Minuta::whereIn('estado', ['pendiente', 'aprobada', 'completada'])
            ->pluck('terreno_id')
            ->toArray();

        // Terrenos del vendedor que están libres O que pertenecen a su trámite actual (si está editando)
        $terrenos = Terreno::where('usuario_id', $user->id)
            ->where(function($query) use ($terrenosOcupados, $minuta) {
                $query->whereNotIn('id', $terrenosOcupados);
                if ($minuta) {
                    $query->orWhere('id', $minuta->terreno_id);
                }
            })->get();

        $compradores = Usuario::where('rol', 'comprador')->get();

        // Calcular paso activo para la UI
        $paso = 1;
        if ($minuta) $paso = 2;
        if ($minuta && $comprobante) $paso = 3;

        return view('vendedor.proceso_legal', compact(
            'minuta', 'comprobante', 'terrenos', 'compradores', 'paso'
        ));
    }

    /**
     * VENDEDOR: Vista del historial de trámites legales.
     */
    public function historialLegal()
    {
        $user = Auth::user();

        $historial = Minuta::with(['terreno', 'comprador'])
            ->where('vendedor_id', $user->id)
            ->latest()
            ->get();

        // Para el historial, adjuntamos el IT de cada una si existe
        $historial->transform(function ($m) {
            $m->it = ComprobanteIt::where('minuta_id', $m->id)->first();
            return $m;
        });

        return view('vendedor.historial_legal', compact('historial'));
    }

    // ─── VENDEDOR: Registrar su propia minuta desde el flujo legal ───────────
    public function storeVendedor(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'terreno_id'   => 'required|exists:terrenos,id',
            'comprador_id' => 'required|exists:usuarios,id',
            'monto'        => 'required|numeric|min:0.01',
            'fecha'        => 'required|date|before_or_equal:today',
            'archivo'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'terreno_id.required'   => 'Seleccione un terreno.',
            'comprador_id.required' => 'Seleccione un comprador.',
            'monto.min'             => 'El monto debe ser mayor a 0.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'archivo.mimes'         => 'Solo se aceptan PDF, JPG o PNG.',
            'archivo.max'           => 'El archivo no puede pesar más de 5MB.',
        ]);

        $ruta = null;
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $ruta = $archivo->storeAs(
                'minutas',
                'minuta_' . $user->id . '_' . time() . '.' . $archivo->getClientOriginalExtension(),
                'public'
            );
        }

        // Lógica para decidir si actualizar la minuta actual o crear una nueva
        $minutaActual = Minuta::where('vendedor_id', $user->id)
            ->where('estado', '!=', 'completada')
            ->latest()
            ->first();

        $datos = [
            'terreno_id'   => $request->terreno_id,
            'comprador_id' => $request->comprador_id,
            'vendedor_id'  => $user->id,
            'monto'        => $request->monto,
            'fecha'        => $request->fecha,
            'archivo'      => $ruta ?? ($minutaActual?->archivo),
            'estado'       => 'pendiente',
            'observacion'  => null,
        ];

        if ($minutaActual) {
            $minutaActual->update($datos);
        } else {
            Minuta::create($datos);
        }

        return redirect()->route('vendedor.proceso_legal')
            ->with('success', '✅ Minuta registrada. Ahora suba el comprobante del Impuesto de Transferencia.');
    }

    // Helper privado
    private function minutaActual(int $userId): ?Minuta
    {
        return Minuta::where('vendedor_id', $userId)->latest()->first();
    }

    public function verArchivo($id)
    {
        $user = Auth::check() ? Auth::user() : null;
        if (!$user) abort(403);

        $minuta = Minuta::where('id', $id)
            ->where('vendedor_id', $user->id)
            ->firstOrFail();

        if (!$minuta->archivo || !\Illuminate\Support\Facades\Storage::disk('public')->exists($minuta->archivo)) {
            abort(404, 'Archivo no encontrado.');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->response($minuta->archivo);
    }
}
