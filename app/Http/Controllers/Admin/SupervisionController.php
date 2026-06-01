<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Chat;

class SupervisionController extends Controller
{
    public function indexLeads(Request $request)
    {
        $query = Lead::with(['comprador', 'vendedor', 'terreno', 'chat.ultimoMensaje'])
            ->latest('fecha_contacto');

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        $leads = $query->paginate(20);

        return view('admin.supervision.leads', compact('leads'));
    }

    public function showChat(Chat $chat)
    {
        $mensajes = $chat->mensajes()->with('usuario')->get();

        return view('admin.supervision.chat', compact('chat', 'mensajes'));
    }
}
