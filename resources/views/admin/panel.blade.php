@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')

{{-- ═══ Stats Cards ═══ --}}
<div class="stats-grid" id="statsSection">
    <div class="stat-card stat-total">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats->total }}</span>
            <span class="stat-label">Total Vendedores</span>
        </div>
    </div>
    <div class="stat-card stat-pending">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats->pendientes }}</span>
            <span class="stat-label">Pendientes</span>
        </div>
    </div>
    <div class="stat-card stat-verified">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats->verificados }}</span>
            <span class="stat-label">Verificados</span>
        </div>
    </div>
    <div class="stat-card stat-rejected">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats->rechazados }}</span>
            <span class="stat-label">Rechazados</span>
        </div>
    </div>
</div>

{{-- ═══ Acceso Rápido — Minutas ═══ --}}
<style>
.minutas-quick-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    position: relative;
    overflow: hidden;
}

.minutas-quick-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(100deg, rgba(37,99,235,0.07) 0%, transparent 60%);
    pointer-events: none;
}

.minutas-quick-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.minutas-quick-icon {
    width: 44px; height: 44px;
    background: rgba(37,99,235,0.2);
    border: 1px solid rgba(37,99,235,0.3);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.minutas-quick-icon svg {
    width: 20px; height: 20px;
    stroke: #60a5fa;
}

.minutas-quick-title {
    font-family: 'Syne', sans-serif;
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
    margin: 0 0 2px;
    letter-spacing: -0.01em;
}

.minutas-quick-sub {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.35);
    margin: 0;
}

.minutas-quick-actions {
    display: flex;
    gap: 0.65rem;
    flex-wrap: wrap;
}

.btn-minuta-ver {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.1rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 9px;
    color: rgba(255,255,255,0.75) !important;
    font-size: 0.83rem;
    font-weight: 500;
    text-decoration: none !important;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
}

.btn-minuta-ver svg { width: 14px; height: 14px; stroke: currentColor; flex-shrink: 0; }

.btn-minuta-ver:hover {
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.2);
    color: #fff !important;
}

.btn-minuta-nueva {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.1rem;
    background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    border: none;
    border-radius: 9px;
    color: #fff !important;
    font-size: 0.83rem;
    font-weight: 600;
    text-decoration: none !important;
    transition: filter 0.15s, transform 0.15s, box-shadow 0.15s;
    font-family: 'DM Sans', sans-serif;
    box-shadow: 0 3px 12px rgba(37,99,235,0.35);
    cursor: pointer;
}

.btn-minuta-nueva svg { width: 14px; height: 14px; stroke: currentColor; flex-shrink: 0; }

.btn-minuta-nueva:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
    box-shadow: 0 5px 18px rgba(37,99,235,0.45);
}
</style>

<div class="minutas-quick-card">
    <div class="minutas-quick-left">
        <div class="minutas-quick-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14,2 14,8 20,8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10,9 9,9 8,9"/>
            </svg>
        </div>
        <div>
            <p class="minutas-quick-title">Minutas de Compraventa</p>
            <p class="minutas-quick-sub">Gestiona y registra minutas del sistema</p>
        </div>
    </div>
    <div class="minutas-quick-actions">
        <a href="{{ route('admin.minutas.index') }}" class="btn-minuta-ver">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            Ver Minutas
        </a>
        <a href="{{ route('admin.minutas.create') }}" class="btn-minuta-nueva">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Registrar Minuta
        </a>
    </div>
</div>

{{-- ═══ Filtros y Búsqueda ═══ --}}
<div class="card" id="filtersCard">
    <div class="card-body">
        <div class="filters-row">
            <div class="search-box">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="searchVendor" class="search-input" placeholder="Buscar por nombre o email...">
            </div>
            <div class="filter-actions-group" style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
                <div class="filter-buttons">
                    <a href="{{ url('/admin/panel?filtro=todos') }}" class="btn-filter {{ $filtroActual === 'todos' ? 'active' : '' }}">Todos</a>
                    <a href="{{ url('/admin/panel?filtro=pendiente') }}" class="btn-filter {{ $filtroActual === 'pendiente' ? 'active' : '' }}">⏳ Pendientes</a>
                    <a href="{{ url('/admin/panel?filtro=verificado') }}" class="btn-filter {{ $filtroActual === 'verificado' ? 'active' : '' }}">✅ Verificados</a>
                    <a href="{{ url('/admin/panel?filtro=rechazado') }}" class="btn-filter {{ $filtroActual === 'rechazado' ? 'active' : '' }}">❌ Rechazados</a>
                </div>
                <button type="button" class="btn btn-primary" onclick="mostrarModal('modalCrearVendedor')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>Nuevo Vendedor</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Tabla de Vendedores ═══ --}}
<div class="card" id="vendorsCard">
    <div class="card-header">
        <h2 class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Vendedores
            <span class="badge badge-secondary">{{ count($vendedores) }}</span>
        </h2>
    </div>
    <div class="card-body no-padding">
        @if (count($vendedores) === 0)
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            <p>No se encontraron vendedores con el filtro seleccionado.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="data-table" id="vendorsTable">
                <thead>
                    <tr>
                        <th>Vendedor</th>
                        <th>Email</th>
                        <th>Documento CI</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vendedores as $v)
                    <tr id="vendor-row-{{ $v->id }}">
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar-sm {{ $v->doc_id ? 'has-doc' : '' }}">{{ strtoupper(substr($v->nombre, 0, 1)) }}</div>
                                <div>
                                    <span class="user-name-text">{{ $v->nombre }}</span>
                                    <span class="user-email-text">{{ $v->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($v->doc_id)
                                <span class="doc-indicator has-doc" title="{{ $v->nombre_original }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                                    Subido (Activo)
                                </span>
                            @else
                                <span class="doc-indicator no-doc">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                    Sin documento
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $v->estado_verificacion === 'verificado' ? 'success' : ($v->estado_verificacion === 'rechazado' ? 'danger' : 'warning') }}">
                                {{ $v->estado_verificacion === 'verificado' ? '✅ Verificado' : ($v->estado_verificacion === 'rechazado' ? '❌ Rechazado' : '⏳ Pendiente') }}
                            </span>
                        </td>
                        <td class="date-cell">{{ \Carbon\Carbon::parse($v->fecha_registro)->timezone('America/La_Paz')->translatedFormat('d M Y') }}</td>
                        <td>
                            <div class="action-buttons" style="display: flex; gap: 8px; flex-wrap: wrap;">
                                @if ($v->doc_id)
                                <a href="{{ url('/admin/ver-ci', $v->id) }}" class="btn btn-sm btn-outline" title="Ver documento">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Revisar
                                </a>
                                @endif
                                <a href="{{ route('admin.editar_vendedor', $v->id) }}" class="btn btn-sm btn-warning" title="Editar vendedor">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M17 3l4 4-7 7H10v-4l7-7z"/><path d="M4 20h16"/></svg>
                                    Editar
                                </a>
                                <form action="{{ route('admin.eliminar_vendedor', $v->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Eliminar permanentemente a {{ addslashes($v->nombre) }}? Esta acción no se puede deshacer.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar vendedor">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="3,6 5,6 21,6"/><path d="M19,6v14a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- ═══ Modal Crear Vendedor ═══ --}}
<div class="modal-overlay" id="modalCrearVendedor" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <h3>Nuevo Vendedor</h3>
            <button class="modal-close" type="button" onclick="cerrarModal('modalCrearVendedor')">&times;</button>
        </div>
        <form action="{{ route('admin.crear_vendedor') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nombre Completo <span class="required">*</span></label>
                    <input type="text" name="nombre" class="form-control" required placeholder="Ej. Juan Pérez">
                </div>
                <div class="form-group">
                    <label class="form-label">Correo Electrónico <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control" required placeholder="correo@ejemplo.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña Temporal <span class="required">*</span></label>
                    <input type="text" name="password" class="form-control" required placeholder="Contraseña segura">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" placeholder="+591 70000000">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalCrearVendedor')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Crear Vendedor</button>
            </div>
        </form>
    </div>
</div>

@endsection
