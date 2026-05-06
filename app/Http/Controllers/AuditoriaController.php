<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaAcceso;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $filtros = [
            'usuario_id'  => $request->query('usuario_id', ''),
            'accion'      => $request->query('accion', ''),
            'fecha_desde' => $request->query('fecha_desde', ''),
            'fecha_hasta' => $request->query('fecha_hasta', ''),
        ];

        $query = AuditoriaAcceso::with('usuario')
            ->orderBy('created_at', 'desc');

        if (!empty($filtros['usuario_id'])) {
            $query->where('usuario_id', $filtros['usuario_id']);
        }
        if (!empty($filtros['accion'])) {
            $query->where('accion', $filtros['accion'])->orderBy('created_at', 'desc');
        }
        if (!empty($filtros['fecha_desde'])) {
            $query->whereDate('created_at', '>=', $filtros['fecha_desde']);
        }
        if (!empty($filtros['fecha_hasta'])) {
            $query->whereDate('created_at', '<=', $filtros['fecha_hasta']);
        }

        $registros = $query->limit(500)->get();

        $usuarios = Usuario::orderBy('nombre')->get();

        // Lista de acciones únicas para el filtro
        $acciones = AuditoriaAcceso::select('accion')
            ->distinct()
            ->orderBy('accion')
            ->pluck('accion');

        return view('admin.auditoria', compact('registros', 'usuarios', 'acciones', 'filtros'));
    }

    public function exportarCsv(Request $request)
    {
        $query = AuditoriaAcceso::with('usuario')->orderBy('created_at', 'desc');

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $registros = $query->limit(5000)->get();

        $csv = "ID,Usuario,Rol,Acción,Entidad,ID Entidad,Descripción,IP,Fecha\n";

        foreach ($registros as $r) {
            $csv .= implode(',', [
                $r->id,
                '"' . ($r->usuario->nombre ?? 'Sistema') . '"',
                '"' . ($r->usuario->rol ?? '—') . '"',
                '"' . $r->accion . '"',
                '"' . ($r->entidad ?? '—') . '"',
                $r->entidad_id ?? '—',
                '"' . str_replace('"', '""', $r->descripcion ?? '') . '"',
                $r->ip_address ?? '—',
                '"' . $r->created_at->format('d/m/Y H:i:s') . '"',
            ]) . "\n";
        }

        return Response::make($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="auditoria_' . now()->format('Ymd_His') . '.csv"',
        ]);
    }
}