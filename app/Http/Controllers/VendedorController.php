<?php

namespace App\Http\Controllers;

use App\Models\DocumentoCi;
use App\Models\HistorialVerificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Terreno;
use App\Helpers\Auditoria;
use File;

class VendedorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Obtener documento activo actual
        $documento = DocumentoCi::where('usuario_id', $user->id)
            ->where('activo', 1)
            ->orderBy('fecha_subida', 'desc')
            ->first();

        // Formatear tamaño del documento si existe
        if ($documento) {
            $documento->tamano_formateado = $this->formatFileSize($documento->tamano);
        }

        // Obtener historial de verificaciones
        $historial = HistorialVerificacion::with('admin')
            ->where('usuario_id', $user->id)
            ->orderBy('fecha', 'desc')
            ->get();

        return view('vendedor.dashboard', [
            'estado' => $user->estado_verificacion,
            'documento' => $documento,
            'historial' => $historial
        ]);
    }

    public function subirCI(Request $request)
    {
        $request->validate([
            'documento_ci' => 'required|file|mimes:jpg,jpeg,png,pdf,application/pdf|max:10240',
        ], [
            'documento_ci.required' => 'Debe seleccionar un archivo para subir.',
            'documento_ci.mimes' => 'El archivo no es válido. Solo se aceptan JPG, PNG o PDF.',
            'documento_ci.max' => 'El archivo supera el límite de 10MB.',
        ]);

        $user = Auth::user();
        $file = $request->file('documento_ci');

        // Log para depuración
        \Log::info('Subiendo CI', [
            'nombre' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        // Desactivar documentos anteriores
        DocumentoCi::where('usuario_id', $user->id)->where('activo', 1)->update(['activo' => 0]);

        $path = $file->store('documentos_ci', 'private');

        DocumentoCi::create([
            'usuario_id' => $user->id,
            'nombre_archivo' => $path,
            'nombre_original' => $file->getClientOriginalName(),
            'tipo_mime' => $file->getMimeType(),
            'tamano' => $file->getSize(),
            'activo' => 1
        ]);

        $user->estado_verificacion = 'pendiente';
        $user->save();

        Auditoria::registrar(
            'subida_documento_ci',
            'vendedor',
            $user->id,
            "Vendedor subió nuevo documento CI: {$file->getClientOriginalName()}"
        );

        return redirect()->route('vendedor.dashboard')->with('success', 'Documento subido exitosamente. Será revisado por un administrador.');
    }

    public function servirMiCI()
    {
        $user = Auth::user();

        $documento = DocumentoCi::where('usuario_id', $user->id)
            ->where('activo', 1)
            ->orderBy('fecha_subida', 'desc')
            ->first();

        if (!$documento) {
            abort(404, 'No tiene un documento CI activo.');
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('private');

        // Verificar si el archivo existe usando el disco private
        if (!$disk->exists($documento->nombre_archivo)) {
            abort(404, 'Archivo no encontrado en el servidor.');
        }

        return $disk->response($documento->nombre_archivo, $documento->nombre_original);
    }

    public function historialPropio()
    {
        $user = Auth::user();
        $historial = HistorialVerificacion::with('admin')
            ->where('usuario_id', $user->id)
            ->orderBy('fecha', 'desc')
            ->get();
        return view('vendedor.historial', compact('historial'));
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
    // CONTROL DE LOTES
    // ═══════════════════════════════════════════════════════

    public function controlLotes()
    {
        $user = Auth::user();
        $terrenos = Terreno::where('usuario_id', $user->id)
            ->orderBy('actualizado_en', 'DESC')
            ->orderBy('creado_en', 'DESC')
            ->get();
                    
        return view('shared.lotes', compact('terrenos'));
    }

    public function updateLoteEstado(Request $request, $id)
    {
        $request->validate([
            'estado_lote' => 'required|in:disponible,reservado,vendido'
        ]);

        $terreno = Terreno::findOrFail($id);

        // Seguridad: Asegurar que el terreno pertenece al vendedor actual
        if ($terreno->usuario_id !== Auth::id()) {
            abort(403, 'No tienes permiso para modificar este lote.');
        }

        $estadoAnterior = $terreno->estado_lote;
        $estadoNuevo = $request->input('estado_lote');

        if ($estadoAnterior !== $estadoNuevo) {
            \App\Models\HistorialEstadoLote::create([
                'terreno_id' => $terreno->id,
                'usuario_id' => Auth::id(),
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
                'fecha_cambio' => now(),
            ]);
        }

        $terreno->estado_lote = $estadoNuevo;
        $terreno->actualizado_en = now();
        $terreno->save();

        Auditoria::registrar(
            'cambio_estado_lote',
            'terreno',
            $terreno->id,
            "Vendedor cambió estado del lote #{$terreno->id}: {$estadoAnterior} → {$estadoNuevo}"
        );

        return redirect()->back()->with('success', "El estado de tu lote #{$terreno->id} se ha actualizado a '{$terreno->estado_lote}'.");
    }

    public function eliminarCI()
    {
        $user = Auth::user();
        $documento = DocumentoCi::where('usuario_id', $user->id)->where('activo', 1)->first();

        if (!$documento) {
            return redirect()->route('vendedor.dashboard')->with('error', 'No tienes ningún documento activo para eliminar.');
        }

        // Eliminar archivo físico
        Storage::disk('private')->delete($documento->nombre_archivo);

        // Desactivar documento
        $documento->activo = 0;
        $documento->save();

        // Resetear estado de verificación
        $user->estado_verificacion = 'pendiente';
        $user->save();

        return redirect()->route('vendedor.dashboard')->with('success', 'Documento eliminado correctamente. Puedes subir uno nuevo.');
    }
}