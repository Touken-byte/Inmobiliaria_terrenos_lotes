<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Chat;
use App\Models\Terreno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    // Vendedor: Panel de Gestión de Leads
    public function indexVendedor(Request $request)
    {
        $user = Auth::user();
        
        $query = Lead::with(['comprador', 'terreno', 'alquiler', 'chat.ultimoMensaje'])
            ->where('vendedor_id', $user->id)
            ->latest('fecha_contacto');

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }
        if ($request->buscar) {
            $query->whereHas('comprador', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('telefono', 'like', '%' . $request->buscar . '%');
            });
        }

        $leads = $query->paginate(15);

        $stats = [
            'nuevos' => Lead::where('vendedor_id', $user->id)->where('estado', 'nuevo')->count(),
            'negociacion' => Lead::where('vendedor_id', $user->id)->where('estado', 'negociacion')->count(),
            'cerrados' => Lead::where('vendedor_id', $user->id)->where('estado', 'cerrado')->count(),
            'total' => Lead::where('vendedor_id', $user->id)->count(),
        ];

        return view('vendedor.leads.index', compact('leads', 'stats'));
    }

    // Comprador: Mis intereses
    public function indexComprador()
    {
        $user = Auth::user();
        $leads = Lead::with(['vendedor', 'terreno', 'alquiler', 'chat.ultimoMensaje'])
            ->where('comprador_id', $user->id)
            ->latest('fecha_contacto')
            ->get();

        return view('comprador.leads.index', compact('leads'));
    }

    // Comprador: Contactar Vendedor (Crear Lead para Terreno)
    public function store(Request $request, $id)
    {
        $request->validate([
            'telefono' => 'required|string|max:20',
            'mensaje' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        if ($user->rol !== 'comprador') {
            return redirect()->back()->with('error', 'Solo los compradores pueden generar leads.');
        }

        $terreno = Terreno::findOrFail($id);

        // Evitar duplicados (solo un lead activo por terreno para este comprador)
        $existingLead = Lead::where('terreno_id', $terreno->id)
            ->where('comprador_id', $user->id)
            ->whereIn('estado', ['nuevo', 'contactado', 'negociacion'])
            ->first();

        if ($existingLead) {
            return redirect()->route('chat.show', $existingLead->chat->id)->with('info', 'Ya tienes una negociación activa para este terreno.');
        }

        // Crear Lead
        $lead = Lead::create([
            'terreno_id' => $terreno->id,
            'comprador_id' => $user->id,
            'vendedor_id' => $terreno->usuario_id,
            'nombre' => $user->nombre,
            'telefono' => $request->telefono,
            'mensaje' => $request->mensaje,
            'estado' => 'nuevo',
        ]);

        // Crear Chat automáticamente
        $chat = Chat::create([
            'lead_id' => $lead->id,
            'comprador_id' => $user->id,
            'vendedor_id' => $terreno->usuario_id,
            'estado' => 'activo',
        ]);

        // Si envió un mensaje inicial, lo registramos en el chat
        if ($request->mensaje) {
            \App\Models\Mensaje::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'mensaje' => $request->mensaje,
            ]);
        }

        return redirect()->route('chat.show', $chat->id)->with('success', '¡Has contactado al vendedor exitosamente!');
    }

    // Comprador: Contactar Arrendador (Crear Lead para Alquiler)
    public function storeAlquiler(Request $request, $id)
    {
        $request->validate([
            'telefono' => 'required|string|max:20',
            'mensaje' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        if ($user->rol !== 'comprador') {
            return redirect()->back()->with('error', 'Solo los compradores pueden generar leads.');
        }

        $alquiler = \App\Models\Alquiler::findOrFail($id);

        // Evitar duplicados (solo un lead activo por alquiler para este comprador)
        $existingLead = Lead::where('alquiler_id', $alquiler->id)
            ->where('comprador_id', $user->id)
            ->whereIn('estado', ['nuevo', 'contactado', 'negociacion'])
            ->first();

        if ($existingLead) {
            return redirect()->route('chat.show', $existingLead->chat->id)->with('info', 'Ya tienes una conversación activa para este alquiler.');
        }

        // Crear Lead
        $lead = Lead::create([
            'alquiler_id' => $alquiler->id,
            'comprador_id' => $user->id,
            'vendedor_id' => $alquiler->user_id,
            'nombre' => $user->nombre,
            'telefono' => $request->telefono,
            'mensaje' => $request->mensaje,
            'estado' => 'nuevo',
        ]);

        // Crear Chat automáticamente
        $chat = Chat::create([
            'lead_id' => $lead->id,
            'comprador_id' => $user->id,
            'vendedor_id' => $alquiler->user_id,
            'estado' => 'activo',
        ]);

        // Si envió un mensaje inicial, lo registramos en el chat
        if ($request->mensaje) {
            \App\Models\Mensaje::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'mensaje' => $request->mensaje,
            ]);
        }

        return redirect()->route('chat.show', $chat->id)->with('success', '¡Has contactado al arrendador exitosamente!');
    }

    // Vendedor: Actualizar estado del Lead
    public function updateEstado(Request $request, Lead $lead)
    {
        if (Auth::id() !== $lead->vendedor_id) {
            abort(403);
        }

        $request->validate([
            'estado' => 'required|in:nuevo,contactado,negociacion,cerrado'
        ]);

        $lead->update(['estado' => $request->estado]);

        if ($request->estado === 'cerrado') {
            $lead->chat()->update(['estado' => 'cerrado']);
        }

        return redirect()->back()->with('success', 'Estado del lead actualizado correctamente.');
    }
}
