<?php

namespace App\Http\Controllers;

use App\Models\DocumentoPropiedad;
use App\Models\Terreno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentoPropiedadController extends Controller
{
    /**
     * Muestra el formulario para subir el título de propiedad.
     */
    public function mostrarFormularioSubida($terrenoId)
    {
        $user = Auth::user();

        // Solo el dueño del terreno puede subir documentos
        $terreno = Terreno::where('id', $terrenoId)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        $documento = $terreno->documentoPropiedad;

        return view('vendedor.documentos.subir', compact('terreno', 'documento'));
    }

    /**
     * Procesa la subida del archivo PDF.
     */
    public function subirDocumento(Request $request, $terrenoId)
    {
        $user = Auth::user();

        // Solo el dueño del terreno puede subir documentos
        $terreno = Terreno::where('id', $terrenoId)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        // Validación del archivo
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:10240' // Máx 10MB, solo PDF
        ], [
            'archivo.required' => 'Debe seleccionar un archivo PDF.',
            'archivo.file' => 'El archivo no es válido.',
            'archivo.mimes' => 'Solo se permiten archivos PDF.',
            'archivo.max' => 'El archivo no puede exceder los 10MB.'
        ]);

        $archivo = $request->file('archivo');

        // Guardar archivo en almacenamiento privado
        $nombreArchivo = 'titulo_' . $terrenoId . '_' . time() . '.pdf';
        $path = $archivo->storeAs('documentos_propiedad', $nombreArchivo, 'local');

        // Crear o actualizar el documento
        DocumentoPropiedad::updateOrCreate(
            ['terreno_id' => $terreno->id],
            [
                'nombre_archivo' => $path,
                'nombre_original' => $archivo->getClientOriginalName(),
                'tipo_mime' => $archivo->getMimeType(),
                'tamano' => $archivo->getSize(),
                'estado' => 'en_verificacion',
                'creado_en' => now(),
                'actualizado_en' => now(),
            ]
        );

        return redirect()
            ->route('vendedor.documentos.subir', $terrenoId)
            ->with('success', 'Título de propiedad subido correctamente. Está pendiente de verificación.');
    }

    /**
     * Muestra/descarga el documento PDF con validación de permisos.
     * Solo el dueño del terreno o un admin pueden acceder.
     */
    public function verDocumento($documentoId)
    {
        $user = Auth::user();
        $documento = DocumentoPropiedad::with('terreno')->findOrFail($documentoId);

        // Verificar permisos: solo el dueño del terreno o un admin pueden ver el documento
        $esDueno = $documento->terreno->usuario_id === $user->id;
        $esAdmin = $user->rol === 'admin';

        if (!$esDueno && !$esAdmin) {
            abort(403, 'No tienes permiso para acceder a este documento.');
        }

        // Verificar que el archivo existe
        if (!Storage::disk('local')->exists($documento->nombre_archivo)) {
            abort(404, 'El archivo no existe en el servidor.');
        }

        // Retornar el archivo con los headers apropiados
        return Storage::disk('local')->response($documento->nombre_archivo, $documento->nombre_original);
    }

    /**
     * Elimina un documento de propiedad (solo si está en verificación o observado).
     */
    public function eliminarDocumento($terrenoId)
    {
        $user = Auth::user();

        $terreno = Terreno::where('id', $terrenoId)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        $documento = DocumentoPropiedad::where('terreno_id', $terreno->id)->first();

        if (!$documento) {
            return redirect()
                ->route('vendedor.documentos.subir', $terrenoId)
                ->with('error', 'No hay ningún documento para eliminar.');
        }

        // Solo se puede eliminar si está en verificación o observado
        if (!in_array($documento->estado, ['en_verificacion', 'observado'])) {
            return redirect()
                ->route('vendedor.documentos.subir', $terrenoId)
                ->with('error', 'No se puede eliminar un documento verificado.');
        }

        // Eliminar archivo físico
        if (Storage::disk('local')->exists($documento->nombre_archivo)) {
            Storage::disk('local')->delete($documento->nombre_archivo);
        }

        $documento->delete();

        return redirect()
            ->route('vendedor.documentos.subir', $terrenoId)
            ->with('success', 'Documento eliminado correctamente.');
    }
}