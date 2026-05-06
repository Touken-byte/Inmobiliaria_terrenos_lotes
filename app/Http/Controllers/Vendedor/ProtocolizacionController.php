<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Minuta;
use App\Models\Protocolizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProtocolizacionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'numero_protocolo'    => 'required|string|max:255',
            'fecha_protocolizacion' => 'required|date|before_or_equal:today',
            'archivo_testimonio'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'numero_protocolo.required'    => 'El número de protocolo es obligatorio.',
            'fecha_protocolizacion.required' => 'La fecha de protocolización es obligatoria.',
            'archivo_testimonio.required'  => 'El testimonio notarial es obligatorio.',
            'archivo_testimonio.mimes'     => 'Formato inválido. Solo PDF, JPG o PNG.',
            'archivo_testimonio.max'       => 'El archivo no puede pesar más de 5MB.',
        ]);

        $user = Auth::user();
        
        // Buscamos la minuta activa
        $minuta = Minuta::where('vendedor_id', $user->id)
            ->where('estado', 'aprobada')
            ->latest()
            ->first();

        if (!$minuta) {
            return redirect()->back()->with('error', 'No se encontró una minuta aprobada para este trámite.');
        }

        $archivo = $request->file('archivo_testimonio');
        $path = $archivo->storeAs(
            'testimonios', 
            'testimonio_' . $user->id . '_' . time() . '.' . $archivo->getClientOriginalExtension(), 
            'public' 
        );

        Protocolizacion::updateOrCreate(
            [
                'minuta_id' => $minuta->id,
                'user_id'   => $user->id
            ],
            [
                'terreno_id'           => $minuta->terreno_id,
                'numero_protocolo'     => $request->numero_protocolo,
                'fecha_protocolizacion' => $request->fecha_protocolizacion,
                'archivo_testimonio'   => $path,
                'estado'               => 'pendiente',
                'observacion'          => null,
            ]
        );

        return redirect()->route('vendedor.proceso_legal')
            ->with('success', '✅ Protocolización enviada. El trámite legal está ahora en revisión final por el administrador.');
    }

    public function verArchivo($id)
    {
        $user = Auth::user();
        $prot = Protocolizacion::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!$prot->archivo_testimonio || !Storage::disk('public')->exists($prot->archivo_testimonio)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::disk('public')->response($prot->archivo_testimonio);
    }
}
