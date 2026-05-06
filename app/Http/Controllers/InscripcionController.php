<?php

namespace App\Http\Controllers;

use App\Models\Folio;
use App\Models\InscripcionDerechosReales;
use App\Helpers\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InscripcionController extends Controller
{
    // ── VENDEDOR: ver formulario de carga ──
    public function create($folioId)
    {
        $folio = Folio::with('terreno')
            ->where('id', $folioId)
            ->where('estado', 'verificado')
            ->firstOrFail();

        // Solo el vendedor dueño del terreno puede acceder
        if ($folio->terreno->usuario_id !== Auth::id()) {
            abort(403);
        }

        $inscripcion = $folio->inscripcionDerechosReales;

        return view('vendedor.inscripcion.create', compact('folio', 'inscripcion'));
    }

    // ── VENDEDOR: guardar o actualizar inscripción ──
    public function store(Request $request, $folioId)
    {
        $folio = Folio::with('terreno')
            ->where('id', $folioId)
            ->where('estado', 'verificado')
            ->firstOrFail();

        if ($folio->terreno->usuario_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'numero_matricula' => 'nullable|string|max:100',
            'fecha_entrada'    => 'nullable|date',
            'fecha_salida'     => 'nullable|date|after_or_equal:fecha_entrada',
            'tasa_pagada'      => 'nullable|numeric|min:0',
            'comprobante'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'comprobante.mimes' => 'El comprobante debe ser PDF, JPG o PNG.',
            'comprobante.max'   => 'El comprobante no debe superar 5MB.',
            'fecha_salida.after_or_equal' => 'La fecha de salida debe ser igual o posterior a la de entrada.',
        ]);

        $inscripcion = $folio->inscripcionDerechosReales;

        // Procesar archivo si se subió uno
        $archivoPath = $inscripcion->comprobante_archivo ?? null;
        $archivoNombre = $inscripcion->comprobante_nombre_original ?? null;

        if ($request->hasFile('comprobante')) {
            // Eliminar archivo anterior si existe
            if ($archivoPath && Storage::disk('public')->exists($archivoPath)) {
                Storage::disk('public')->delete($archivoPath);
            }
            $archivo = $request->file('comprobante');
            $archivoPath = $archivo->store('inscripciones', 'public');
            $archivoNombre = $archivo->getClientOriginalName();
        }

        $datos = [
            'folio_id'                    => $folio->id,
            'numero_matricula'            => $request->numero_matricula,
            'fecha_entrada'               => $request->fecha_entrada,
            'fecha_salida'                => $request->fecha_salida,
            'tasa_pagada'                 => $request->tasa_pagada,
            'comprobante_archivo'         => $archivoPath,
            'comprobante_nombre_original' => $archivoNombre,
            'estado'                      => 'pendiente',
        ];

        if ($inscripcion) {
            // Si ya existe y estaba rechazada, permitir re-envío
            $inscripcion->update($datos);
        } else {
            InscripcionDerechosReales::create($datos);
        }

        Auditoria::registrar(
            'subida_inscripcion',
            'folio',
            $folio->id,
            "Vendedor subió inscripción de Derechos Reales para folio {$folio->numero_folio}"
        );

        return redirect()->route('vendedor.terrenos.mis')
            ->with('success', 'Inscripción enviada correctamente. El administrador la revisará pronto.');
    }

    // ── ADMIN: panel de inscripciones ──
    public function adminIndex()
    {
        $inscripciones = InscripcionDerechosReales::with(['folio.terreno.vendedor'])
            ->orderByRaw("CASE estado
                WHEN 'pendiente'   THEN 1
                WHEN 'en_revision' THEN 2
                WHEN 'inscrito'    THEN 3
                WHEN 'rechazado'   THEN 4
                ELSE 5 END")
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('admin.inscripciones', compact('inscripciones'));
    }

    // ── ADMIN: cambiar estado ──
    public function adminProcesar(Request $request)
    {
        $request->validate([
            'inscripcion_id' => 'required|integer',
            'estado'         => 'required|in:en_revision,inscrito,rechazado',
            'observacion'    => 'nullable|string|max:500',
        ]);

        $inscripcion = InscripcionDerechosReales::findOrFail($request->inscripcion_id);

        $inscripcion->update([
            'estado'           => $request->estado,
            'observacion_admin'=> $request->observacion,
            'revisado_por'     => Auth::id(),
        ]);

        Auditoria::registrar(
            'revision_inscripcion',
            'inscripcion_derechos_reales',
            $inscripcion->id,
            "Admin cambió estado a '{$request->estado}'" . ($request->observacion ? " — Obs: {$request->observacion}" : '')
        );

        $textos = [
            'en_revision' => 'marcada en revisión 🔍',
            'inscrito'    => 'marcada como Inscrita ✅',
            'rechazado'   => 'rechazada ❌',
        ];

        return redirect()->route('admin.inscripciones')
            ->with('success', "Inscripción #{$inscripcion->id} {$textos[$request->estado]} exitosamente.");
    }

    // ── ADMIN/VENDEDOR: ver archivo comprobante ──
    public function verArchivo($id)
    {
        $inscripcion = InscripcionDerechosReales::findOrFail($id);

        if (!$inscripcion->comprobante_archivo) {
            abort(404, 'No hay archivo adjunto.');
        }

        return response()->file(storage_path('app/public/' . $inscripcion->comprobante_archivo));
    }
}