<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Chat;
use App\Models\Mensaje;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Auditoria;

class ChatController extends Controller
{
    public function show(Chat $chat)
    {
        $user = Auth::user();

        // Validar acceso: comprador, vendedor, o admin
        if ($user->id !== $chat->comprador_id && $user->id !== $chat->vendedor_id && $user->rol !== 'admin') {
            abort(403, 'Acceso denegado a este chat.');
        }

        // Marcar mensajes como leídos si no es el remitente y no es admin
        if ($user->rol !== 'admin') {
            Mensaje::where('chat_id', $chat->id)
                ->where('user_id', '!=', $user->id)
                ->where('leido', false)
                ->update(['leido' => true]);

            // Si el vendedor abre el chat por primera vez, el lead pasa a 'contactado' o 'negociacion'
            if ($user->id === $chat->vendedor_id) {
                $lead = $chat->lead;
                if (in_array($lead->estado, ['nuevo', 'contactado'])) {
                    $lead->update(['estado' => 'negociacion']);
                }
            }
        }

        $mensajes = $chat->mensajes()->with('usuario')->get();
        return view('chat.show', compact('chat', 'mensajes'));
    }

    public function getNewMessages(Request $request, Chat $chat)
    {
        $user = Auth::user();
        if ($user->id !== $chat->comprador_id && $user->id !== $chat->vendedor_id && $user->rol !== 'admin') {
            abort(403);
        }

        $lastId = $request->query('last_id', 0);
        $mensajes = $chat->mensajes()
            ->where('id', '>', $lastId)
            ->with('usuario')
            ->get();

        return response()->json(['mensajes' => $mensajes]);
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        $user = Auth::user();

        // Admin no puede enviar mensajes
        if ($user->id !== $chat->comprador_id && $user->id !== $chat->vendedor_id) {
            abort(403);
        }

        if ($chat->estado === 'cerrado') {
            return response()->json(['error' => 'El chat está cerrado.'], 403);
        }

        $request->validate([
            'mensaje' => 'nullable|string',
            'archivo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        ]);

        if (!$request->mensaje && !$request->hasFile('archivo')) {
            return response()->json(['error' => 'Mensaje vacío.'], 422);
        }

        $path = null;
        $nombreOriginal = null;

        if ($request->hasFile('archivo')) {
            // Guardar en disco privado
            $path = $request->file('archivo')->store('chats/' . $chat->id, 'local');
            $nombreOriginal = $request->file('archivo')->getClientOriginalName();
        }

        try {
            $mensaje = Mensaje::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'mensaje' => $request->mensaje,
                'archivo' => $path,
                'nombre_archivo' => $nombreOriginal,
                'leido' => false
            ]);

            Auditoria::registrar(
                'mensaje_enviado',
                'chat',
                $chat->id,
                "El usuario {$user->nombre} ({$user->rol}) envió un mensaje en el chat #{$chat->id}" . ($path ? " con archivo adjunto '{$nombreOriginal}'" : "")
            );

            // Intentar transmitir el mensaje, pero no fallar si el servidor de WebSockets está caído
            try {
                broadcast(new MessageSent($mensaje))->toOthers();
            } catch (\Exception $e) {
                // Silenciar error de broadcasting para permitir que el mensaje se guarde
                \Log::warning("Error de broadcasting: " . $e->getMessage());
            }

            return response()->json(['status' => 'success', 'mensaje' => $mensaje->load('usuario')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'error' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
    }

    public function downloadArchivo(Mensaje $mensaje)
    {
        $user = Auth::user();
        $chat = $mensaje->chat;

        if ($user->id !== $chat->comprador_id && $user->id !== $chat->vendedor_id && $user->rol !== 'admin') {
            abort(403);
        }

        if (!$mensaje->archivo) {
            abort(404);
        }

        if (!Storage::disk('local')->exists($mensaje->archivo)) {
            abort(404);
        }

        return Storage::disk('local')->download($mensaje->archivo, $mensaje->nombre_archivo);
    }
}
