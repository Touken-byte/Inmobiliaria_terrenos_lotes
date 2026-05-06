@extends('layouts.app')

@section('title', 'Auditoría del Sistema')

@section('content')
<style>
    .audit-badge {
        padding: .25rem .7rem;
        border-radius: 100px;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
    }
    .audit-login       { background:#d1fae5; color:#065f46; }
    .audit-logout      { background:#f3f4f6; color:#374151; }
    .audit-aprobacion  { background:#dbeafe; color:#1e40af; }
    .audit-rechazo     { background:#fee2e2; color:#991b1b; }
    .audit-verificacion{ background:#ede9fe; color:#5b21b6; }
    .audit-otro        { background:#fef9c3; color:#854d0e; }

    .audit-table { width:100%; border-collapse:collapse; font-size:.85rem; }
    .audit-table th {
        padding:.75rem 1rem;
        text-align:left;
        font-size:.68rem;
        font-weight:700;
        letter-spacing:.1em;
        text-transform:uppercase;
        color:var(--text-muted);
        border-bottom:2px solid var(--border-color);
        white-space:nowrap;
    }
    .audit-table td {
        padding:.75rem 1rem;
        border-bottom:1px solid var(--border-color);
        vertical-align:middle;
    }
    .audit-table tr:hover td { background:var(--bg-light); }
</style>

<div class="card">
    <div class="card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <h2 class="card-title">🔍 Auditoría del Sistema</h2>
            <p style="margin:0; color:var(--text-muted); font-size:.9rem;">
                Registro de todas las acciones importantes realizadas en el sistema
            </p>
        </div>
        {{-- Botón exportar CSV --}}
        <a href="{{ route('admin.auditoria.exportar') }}?{{ http_build_query($filtros) }}"
           class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:.5rem;">
            📥 Exportar CSV
        </a>
    </div>

    {{-- Filtros --}}
    <div class="card-body" style="border-bottom:1px solid var(--border-color);">
        <form method="GET" action="{{ route('admin.auditoria') }}"
              style="display:flex; gap:1rem; flex-wrap:wrap; align-items:flex-end;">

            <div style="flex:1; min-width:160px;">
                <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted);">
                    USUARIO
                </label>
                <select name="usuario_id" class="form-control" style="width:100%;">
                    <option value="">Todos</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ $filtros['usuario_id'] == $u->id ? 'selected' : '' }}>
                            {{ $u->nombre }} ({{ $u->rol }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="flex:1; min-width:160px;">
                <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted);">
                    ACCIÓN
                </label>
                <select name="accion" class="form-control" style="width:100%;">
                    <option value="">Todas</option>
                    @foreach($acciones as $acc)
                        <option value="{{ $acc }}" {{ $filtros['accion'] === $acc ? 'selected' : '' }}>
                            {{ $acc }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="min-width:140px;">
                <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted);">
                    DESDE
                </label>
                <input type="date" name="fecha_desde" class="form-control"
                       value="{{ $filtros['fecha_desde'] }}">
            </div>

            <div style="min-width:140px;">
                <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted);">
                    HASTA
                </label>
                <input type="date" name="fecha_hasta" class="form-control"
                       value="{{ $filtros['fecha_hasta'] }}">
            </div>

            <div style="display:flex; gap:.5rem;">
                <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
                <a href="{{ route('admin.auditoria') }}" class="btn btn-secondary">✖ Limpiar</a>
            </div>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="card-body" style="padding:0; overflow-x:auto;">
        @if($registros->isEmpty())
            <div style="padding:3rem; text-align:center; color:var(--text-muted);">
                <p style="font-size:2rem;">📭</p>
                <p>No hay registros de auditoría con los filtros aplicados.</p>
            </div>
        @else
            <table class="audit-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Entidad</th>
                        <th>Descripción</th>
                        <th>IP</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registros as $r)
                    <tr>
                        <td style="color:var(--text-muted); font-size:.78rem;">{{ $r->id }}</td>
                        <td>
                            @if($r->usuario)
                                <strong>{{ $r->usuario->nombre }}</strong>
                                <span style="display:block; font-size:.73rem; color:var(--text-muted);">
                                    {{ $r->usuario->rol }}
                                </span>
                            @else
                                <span style="color:var(--text-muted);">Sistema</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $tipo = match(true) {
                                    str_contains($r->accion, 'login')        => 'login',
                                    str_contains($r->accion, 'logout')       => 'logout',
                                    str_contains($r->accion, 'aprobacion')   => 'aprobacion',
                                    str_contains($r->accion, 'rechazo')      => 'rechazo',
                                    str_contains($r->accion, 'verificacion') => 'verificacion',
                                    default                                  => 'otro',
                                };
                            @endphp
                            <span class="audit-badge audit-{{ $tipo }}">
                                {{ str_replace('_', ' ', $r->accion) }}
                            </span>
                        </td>
                        <td>
                            @if($r->entidad)
                                <span style="font-size:.8rem;">{{ $r->entidad }}</span>
                                @if($r->entidad_id)
                                    <span style="color:var(--text-muted); font-size:.75rem;"> #{{ $r->entidad_id }}</span>
                                @endif
                            @else
                                <span style="color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td style="font-size:.82rem; color:var(--text-muted); max-width:260px;">
                            {{ $r->descripcion ?? '—' }}
                        </td>
                        <td style="font-size:.78rem; color:var(--text-muted);">
                            {{ $r->ip_address ?? '—' }}
                        </td>
                        <td style="font-size:.78rem; white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($r->created_at)->timezone('America/La_Paz')->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@endsection