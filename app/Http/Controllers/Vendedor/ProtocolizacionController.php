<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Protocolizacion;
use App\Models\Minuta;
use App\Models\ComprobanteIt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProtocolizacionController extends Controller
{
    /**
     * Almacena o actualiza la protocolización notarial del vendedor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_protocolo'      => 'required|string|max:255',
            'fecha_protocolizacion' => 'required|date|before_or_equal:today',
            'archivo_testimonio'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB Max
        ], [
            'numero_protocolo.required'      => 'El número de protocolo es obligatorio.',
            'fecha_protocolizacion.required' => 'La fecha de protocolización es obligatoria.',
            'fecha_protocolizacion.before_or_equal' => 'La fecha no puede ser futura.',
            'archivo_testimonio.required'    => 'El archivo de testimonio es obligatorio.',
            'archivo_testimonio.mimes'       => 'Formato inválido. Solo PDF, JPG o PNG.',
            'archivo_testimonio.max'         => 'El archivo no puede pesar más de 5MB.',
        ]);

        $user = Auth::user();
        
        // 1. Validar que exista una minuta aprobada
        $minuta = Minuta::where('vendedor_id', $user->id)
            ->where('estado', 'aprobada')
            ->latest()
            ->first();

        if (!$minuta) {
            return redirect()->back()->with('error', 'No se encontró una minuta aprobada para este trámite.');
        }

        // 2. Validar que exista un comprobante IT aprobado asociado a esta minuta
        $comprobante = ComprobanteIt::where('minuta_id', $minuta->id)
            ->where('estado', 'aprobado')
            ->first();

        if (!$comprobante) {
            return redirect()->back()->with('error', 'Debe tener el Comprobante IT aprobado antes de registrar la protocolización.');
        }

        $archivo = $request->file('archivo_testimonio');
        $path = $archivo->storeAs(
            'protocolizaciones',
            'proto_' . $user->id . '_' . time() . '.' . $archivo->getClientOriginalExtension(),
            'public'
        );

        Protocolizacion::updateOrCreate(
            [
                'minuta_id'   => $minuta->id,
                'vendedor_id' => $user->id,
            ],
            [
                'terreno_id'            => $minuta->terreno_id,
                'numero_protocolo'      => $request->numero_protocolo,
                'fecha_protocolizacion' => $request->fecha_protocolizacion,
                'archivo_testimonio'    => $path,
                'estado'                => 'en_revision',
                'observacion'           => null,
            ]
        );

        return redirect()->route('vendedor.proceso_legal')
            ->with('success', '✅ Protocolización notarial enviada. Pendiente de validación final por administración.');
    }

    /**
     * Sirve el archivo de testimonio al vendedor propietario.
     */
    public function verArchivo($id)
    {
        $user = Auth::user();
        $proto = Protocolizacion::where('id', $id)
            ->where('vendedor_id', $user->id)
            ->firstOrFail();

        if (!$proto->archivo_testimonio || !Storage::disk('public')->exists($proto->archivo_testimonio)) {
            abort(404, 'Testimonio no encontrado.');
        }

        return Storage::disk('public')->response($proto->archivo_testimonio);
    }
}
