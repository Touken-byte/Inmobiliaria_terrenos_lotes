<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\ComprobanteIt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComprobanteItController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $comprobantes = ComprobanteIt::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vendedor.comprobante_it', compact('comprobantes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_recibo' => 'required|string|max:255',
            'fecha_pago'    => 'required|date|before_or_equal:today',
            'archivo'       => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB Max
        ], [
            'numero_recibo.required' => 'El número de recibo es obligatorio.',
            'fecha_pago.required'    => 'La fecha de pago es obligatoria.',
            'fecha_pago.before_or_equal' => 'La fecha no puede ser futura.',
            'archivo.required'       => 'El comprobante es obligatorio.',
            'archivo.mimes'          => 'Formato inválido. Solo PDF, JPG o PNG.',
            'archivo.max'            => 'El archivo no puede pesar más de 5MB.',
        ]);

        $user = Auth::user();
        $archivo = $request->file('archivo');

        // Buscamos la minuta activa para vincular el IT y obtener el monto
        $minuta = \App\Models\Minuta::where('vendedor_id', $user->id)
            ->where('estado', 'aprobada') // REGLA: Solo se puede subir IT si la minuta está APROBADA
            ->latest()
            ->first();

        if (!$minuta) {
            return redirect()->back()->with('error', 'No puede subir el comprobante IT hasta que su minuta sea aprobada por el administrador.');
        }

        // CÁLCULO AUTOMÁTICO: 3% del monto de la minuta
        $montoCalculado = $minuta->monto * 0.03;

        $path = $archivo->storeAs(
            'comprobantes_it', 
            'it_' . $user->id . '_' . time() . '.' . $archivo->getClientOriginalExtension(), 
            'public' 
        );

        ComprobanteIt::updateOrCreate(
            [
                'user_id' => $user->id,
                'minuta_id' => $minuta->id 
            ],
            [
                'numero_recibo' => $request->numero_recibo,
                'fecha_pago'    => $request->fecha_pago,
                'monto'         => $montoCalculado, // Usamos el monto calculado automáticamente
                'archivo'       => $path,
                'estado'        => 'pendiente',
                'observacion'   => null,
            ]
        );

        return redirect()->route('vendedor.proceso_legal')
            ->with('success', '✅ Comprobante IT enviado correctamente. El trámite está ahora en revisión.');
    }

    public function verArchivo($id)
    {
        $user = Auth::user();
        $comp = ComprobanteIt::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!$comp->archivo || !\Illuminate\Support\Facades\Storage::disk('public')->exists($comp->archivo)) {
            abort(404, 'Comprobante no encontrado.');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->response($comp->archivo);
    }
}
