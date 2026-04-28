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
            'monto'         => 'required|numeric|min:0.01',
            'archivo'       => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB Max
        ], [
            'numero_recibo.required' => 'El número de recibo es obligatorio.',
            'fecha_pago.required'    => 'La fecha de pago es obligatoria.',
            'fecha_pago.before_or_equal' => 'La fecha no puede ser futura.',
            'monto.required'         => 'El monto es obligatorio.',
            'monto.min'              => 'El monto debe ser mayor a 0.',
            'archivo.required'       => 'El comprobante es obligatorio.',
            'archivo.mimes'          => 'Formato inválido. Solo PDF, JPG o PNG.',
            'archivo.max'            => 'El archivo no puede pesar más de 5MB.',
        ]);

        $user = Auth::user();
        $archivo = $request->file('archivo');

        $path = $archivo->storeAs(
            'comprobantes_it', 
            'it_' . $user->id . '_' . time() . '.' . $archivo->getClientOriginalExtension(), 
            'local' // Private storage
        );

        ComprobanteIt::create([
            'user_id'       => $user->id,
            'numero_recibo' => $request->numero_recibo,
            'fecha_pago'    => $request->fecha_pago,
            'monto'         => $request->monto,
            'archivo'       => $path,
            'estado'        => 'pendiente',
        ]);

        return redirect()->route('vendedor.comprobante_it')->with('success', 'Comprobante de Impuesto de Transferencia enviado correctamente.');
    }
}
