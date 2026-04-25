@extends('layouts.app')

@section('title', 'Historial de Verificaciones')

@section('content')
<div class="page-actions">
    <a href="{{ url('/admin/panel') }}" class="btn btn-secondary" id="backToPanelFromHistory">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12,19 5,12 12,5"/></svg>
        Volver al Panel
    </a>
</div>

<!-- ═══ Filtros ═══ -->
<div class="card" id="historyFiltersCard">
    <div class="card-header">
        <h2 class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/></svg>
            Filtros de Búsqueda
        </h2>
    </div>
    <div class="card-body">
        <form action="{{ url('/admin/historial') }}" method="GET" class="filters-form" id="historyFilterForm">
            <div class="filters-grid">
                <div class="form-group">
                    <label for="vendedor_id" class="form-label">Vendedor</label>
                    <select name="vendedor_id" id="vendedor_id" class="form-control">
                        <option value="">— Todos —</option>
                        @foreach ($vendedores as $v)
                        <option value="{{ $v->id }}" {{ $filtros['vendedor_id'] == $v->id ? 'selected' : '' }}>
                            {{ $v->nombre }} ({{ $v->email }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="accion" class="form-label">Acción</label>
                    <select name="accion" id="accion" class="form-control">
                        <option value="">— Todas —</option>
                        <option value="aprobado" {{ $filtros['accion'] === 'aprobado' ? 'selected' : '' }}>✅ Aprobado</option>
                        <option value="rechazado" {{ $filtros['accion'] === 'rechazado' ? 'selected' : '' }}>❌ Rechazado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ $filtros['fecha_desde'] }}">
                </div>
                <div class="form-group">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ $filtros['fecha_hasta'] }}">
                </div>
            </div>
            <div class="filters-actions">
                <button type="submit" class="btn btn-primary" id="applyFilters">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Buscar
                </button>
                <a href="{{ url('/admin/historial') }}" class="btn btn-secondary" id="clearFilters">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1,4 1,10 7,10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- ═══ Tabla de Historial ═══ -->
<div class="card" id="historyTableCard">
    <div class="card-header">
        <h2 class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            Registro de Auditoría
            <span class="badge badge-secondary">{{ count($registros) }} registros</span>
        </h2>
    </div>
    <div class="card-body no-padding">
        @if (count($registros) === 0)
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            <p>No se encontraron registros con los filtros seleccionados.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="data-table" id="historyTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha / Hora</th>
                        <th>Vendedor</th>
                        <th>Acción</th>
                        <th>Admin</th>
                        <th>Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registros as $r)
                    <tr>
                        <td class="id-cell">{{ $r->id }}</td>
                        <td class="date-cell">
                            <div class="date-multi">
                                <span class="date-main">{{ \Carbon\Carbon::parse($r->fecha)->timezone('America/La_Paz')->translatedFormat('d M Y') }}</span>
                                <span class="date-time">{{ \Carbon\Carbon::parse($r->fecha)->timezone('America/La_Paz')->translatedFormat('H:i') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="user-cell">
                                <span class="user-name-text">{{ $r->vendedor_nombre }}</span>
                                <span class="user-email-text">{{ $r->vendedor_email }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $r->accion === 'aprobado' ? 'success' : 'danger' }}">
                                {{ $r->accion === 'aprobado' ? '✅ Aprobado' : '❌ Rechazado' }}
                            </span>
                        </td>
                        <td>{{ $r->admin_nombre }}</td>
                        <td class="comment-cell">
                            @if ($r->comentario)
                                <span class="comment-text" title="{{ $r->comentario }}">{{ Str::limit($r->comentario, 80) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
