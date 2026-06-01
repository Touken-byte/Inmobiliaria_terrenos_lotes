@extends('layouts.app')

@section('title', 'Gestor de Propiedades')

@section('content')
<!-- ═══ Stats Cards ═══ -->
<div class="stats-grid" id="propertiesStatsSection">
    <div class="stat-card stat-total">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                <polyline points="9,22 9,12 15,12 15,22" />
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats->total }}</span>
            <span class="stat-label">Total Propiedades</span>
        </div>
    </div>
    <div class="stat-card stat-pending">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12,6 12,12 16,14" />
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats->pendientes }}</span>
            <span class="stat-label">Pendientes</span>
        </div>
    </div>
    <div class="stat-card stat-verified">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                <polyline points="22,4 12,14.01 9,11.01" />
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats->aprobados }}</span>
            <span class="stat-label">Aprobadas</span>
        </div>
    </div>
    <div class="stat-card stat-rejected">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <line x1="15" y1="9" x2="9" y2="15" />
                <line x1="9" y1="9" x2="15" y2="15" />
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats->rechazados }}</span>
            <span class="stat-label">Rechazadas</span>
        </div>
    </div>
</div>

<!-- ═══ Filtros Consolidados ═══ -->
<div class="card" id="propertiesFiltersCard" style="margin-bottom: 1.5rem;">
    <div class="card-body" style="padding: 1.5rem;">
        <form method="GET" action="{{ route('admin.propiedades_panel') }}" id="filterForm">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                
                <!-- Buscador -->
                <div style="flex: 2; min-width: 250px;">
                    <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted); text-transform: uppercase;">Buscar</label>
                    <div class="search-box" style="width: 100%; margin: 0; display: flex; align-items: center; background: var(--bg-light); border: 1px solid var(--border-color); border-radius: 8px; padding: 0 10px; height: 42px;">
                        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; color: var(--text-muted); margin-right: 8px;">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" name="search" class="search-input" value="{{ $search }}" placeholder="Buscar por código, ubicación o vendedor..." style="background: transparent; border: none; outline: none; width: 100%; color: var(--text-color); font-size: 0.9rem; height: 100%;">
                    </div>
                </div>

                <!-- Tipo Desplegable -->
                <div style="flex: 1; min-width: 150px;">
                    <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted); text-transform: uppercase;">Tipo de Propiedad</label>
                    <select name="tipo" class="form-control" style="width:100%; height:42px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); color: var(--text-color); padding: 0 10px;" onchange="this.form.submit()">
                        <option value="todos" {{ $tipoActual === 'todos' ? 'selected' : '' }}>🏠 Todos</option>
                        <option value="terreno" {{ $tipoActual === 'terreno' ? 'selected' : '' }}>🌄 Terreno</option>
                        <option value="lote" {{ $tipoActual === 'lote' ? 'selected' : '' }}>🟩 Lote</option>
                        <option value="alquiler" {{ $tipoActual === 'alquiler' ? 'selected' : '' }}>🔑 Alquiler</option>
                    </select>
                </div>

                <!-- Estado Desplegable -->
                <div style="flex: 1; min-width: 150px;">
                    <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted); text-transform: uppercase;">Estado Aprobación</label>
                    <select name="filtro" class="form-control" style="width:100%; height:42px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); color: var(--text-color); padding: 0 10px;" onchange="this.form.submit()">
                        <option value="todos" {{ $filtroActual === 'todos' ? 'selected' : '' }}>⚖️ Todos</option>
                        <option value="pendiente" {{ $filtroActual === 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                        <option value="aprobado" {{ $filtroActual === 'aprobado' ? 'selected' : '' }}>✅ Aprobado</option>
                        <option value="rechazado" {{ $filtroActual === 'rechazado' ? 'selected' : '' }}>❌ Rechazado</option>
                    </select>
                </div>

                <!-- Fecha Desde -->
                <div style="min-width: 130px;">
                    <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted); text-transform: uppercase;">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ $fechaDesde }}" style="width:100%; height:42px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); color: var(--text-color); padding: 0 10px;">
                </div>

                <!-- Fecha Hasta -->
                <div style="min-width: 130px;">
                    <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted); text-transform: uppercase;">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ $fechaHasta }}" style="width:100%; height:42px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); color: var(--text-color); padding: 0 10px;">
                </div>

                <!-- Botones Accion -->
                <div style="display:flex; gap:.5rem;">
                    <button type="submit" class="btn btn-primary" style="height:42px; padding: 0 1.25rem;">🔍 Filtrar</button>
                    <a href="{{ route('admin.propiedades_panel') }}" class="btn btn-secondary" style="height:42px; display:inline-flex; align-items:center; justify-content:center; padding: 0 1.25rem;">✖ Limpiar</a>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- ═══ Listado Unificado ═══ -->
<div class="card" id="propertiesTableCard">
    <div class="card-header">
        <h2 class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                <line x1="3" y1="9" x2="21" y2="9" />
                <line x1="9" y1="21" x2="9" y2="9" />
            </svg>
            Propiedades Registradas
            <span class="badge badge-secondary">{{ $paginatedProperties->total() }}</span>
        </h2>
    </div>
    <div class="card-body no-padding">
        @if ($paginatedProperties->isEmpty())
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                    <line x1="3" y1="9" x2="21" y2="9" />
                    <line x1="9" y1="21" x2="9" y2="9" />
                </svg>
                <p>No se encontraron propiedades registradas con los filtros seleccionados.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="data-table" id="propertiesTable">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Vendedor</th>
                            <th>Ubicación</th>
                            <th>Precio</th>
                            <th>Dimensión</th>
                            <th>Imagen</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paginatedProperties as $p)
                            <tr class="property-row">
                                <td style="font-weight:700; color:var(--text-muted); font-size:.85rem;">
                                    {{ $p->codigo }}
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'terreno' => 'primary',
                                            'lote' => 'info',
                                            'alquiler' => 'secondary'
                                        ];
                                        $typeLabels = [
                                            'terreno' => '🌄 Terreno',
                                            'lote' => '🟩 Lote',
                                            'alquiler' => '🔑 Alquiler'
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $typeColors[$p->tipo] ?? 'otro' }}">
                                        {{ $typeLabels[$p->tipo] ?? ucfirst($p->tipo) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <span class="user-name-text">{{ $p->vendedor_nombre }}</span>
                                        <span class="user-email-text">{{ $p->vendedor_email }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="ubicacion-text" title="{{ $p->ubicacion }}">{{ Str::limit($p->ubicacion, 32) }}</span>
                                </td>
                                <td>
                                    <strong>
                                        @if($p->moneda === 'BOB' || $p->tipo === 'alquiler')
                                            Bs. {{ number_format($p->precio, 2) }}
                                        @else
                                            ${{ number_format($p->precio, 2) }} USD
                                        @endif
                                    </strong>
                                </td>
                                <td>
                                    @if($p->metros_cuadrados)
                                        {{ number_format($p->metros_cuadrados, 2) }} m²
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($p->imagen)
                                        <img src="{{ asset($p->imagen) }}" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;"
                                             onerror="this.style.display='none'">
                                    @else
                                        <span class="badge badge-secondary">Sin foto</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $p->estado === 'aprobado' ? 'success' : ($p->estado === 'rechazado' ? 'danger' : 'warning') }}">
                                        {{ $p->estado === 'aprobado' ? '✅ Aprobado' : ($p->estado === 'rechazado' ? '❌ Rechazado' : '⏳ Pendiente') }}
                                    </span>
                                </td>
                                <td class="date-cell">
                                    <div class="date-multi">
                                        <span class="date-main">{{ \Carbon\Carbon::parse($p->fecha)->timezone('America/La_Paz')->translatedFormat('d M Y') }}</span>
                                        <span class="date-time">{{ \Carbon\Carbon::parse($p->fecha)->timezone('America/La_Paz')->translatedFormat('H:i') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ $p->edit_route }}" class="btn btn-sm btn-outline" style="display:inline-flex; align-items:center; gap:4px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            Revisar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-center" style="margin-top:1.5rem; padding:1rem;">
                {{ $paginatedProperties->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
