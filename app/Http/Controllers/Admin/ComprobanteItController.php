<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComprobanteIt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComprobanteItController extends Controller
{
    public function index()
    {
        $comprobantes = ComprobanteIt::with('usuario')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.comprobantes_it.index', compact('comprobantes'));
    }

    public function aprobar($id)
    {
        $comprobante = ComprobanteIt::findOrFail($id);
        $comprobante->update(['estado' => 'aprobado', 'observacion' => null]);

        return redirect()->route('admin.comprobantes_it.index')->with('success', 'Comprobante #'.$comprobante->numero_recibo.' aprobado correctamente.');
    }

    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'observacion' => 'required|string|min:5|max:1000'
        ], [
            'observacion.required' => 'Debe indicar un motivo de rechazo.'
        ]);

        $comprobante = ComprobanteIt::findOrFail($id);
        $comprobante->update([
            'estado' => 'rechazado',
            'observacion' => $request->observacion
        ]);

        return redirect()->route('admin.comprobantes_it.index')->with('success', 'Comprobante rechazado correctamente.');
    }

    public function verArchivo($id)
    {
        $comprobante = ComprobanteIt::findOrFail($id);

        if (!Storage::disk('local')->exists($comprobante->archivo)) {
            abort(404, 'El archivo no fue encontrado en el servidor.');
        }

        return Storage::disk('local')->response($comprobante->archivo);
    }
}
