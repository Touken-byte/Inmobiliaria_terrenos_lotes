@extends('layouts.app')

@section('title', 'Gestor de Propiedades')

@section('content')
<!-- Header con botón de publicar -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="margin: 0; font-size: 1.75rem; font-weight: 700; color: var(--text-color);">Gestor de Propiedades</h1>
        <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.9rem;">Administra tus terrenos, lotes y publicaciones de alquiler desde un solo lugar.</p>
    </div>
    <div>
        <a href="{{ route('vendedor.publicar_propiedad') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 18px; height: 18px;">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Publicar Propiedad
        </a>
    </div>
</div>

<!-- Mensajes de Estado -->
@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 8px; border-left: 4px solid var(--success-color);">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 8px; border-left: 4px solid var(--danger-color);">
        {{ session('error') }}
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 8px; border-left: 4px solid var(--info-color);">
        {{ session('info') }}
    </div>
@endif

<!-- ═══ Stats Cards ═══ -->
<div class="stats-grid" id="propertiesStatsSection" style="margin-bottom: 2rem;">
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
        <form method="GET" action="{{ route('vendedor.propiedades_panel') }}" id="filterForm">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                
                <!-- Buscador -->
                <div style="flex: 2; min-width: 220px;">
                    <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted); text-transform: uppercase;">Buscar</label>
                    <div class="search-box" style="width: 100%; margin: 0; display: flex; align-items: center; background: var(--bg-light); border: 1px solid var(--border-color); border-radius: 8px; padding: 0 10px; height: 42px;">
                        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; color: var(--text-muted); margin-right: 8px;">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" name="search" class="search-input" value="{{ $search }}" placeholder="Buscar por código, ubicación, título..." style="background: transparent; border: none; outline: none; width: 100%; color: var(--text-color); font-size: 0.9rem; height: 100%;">
                    </div>
                </div>

                <!-- Tipo Desplegable -->
                <div style="flex: 1; min-width: 130px;">
                    <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted); text-transform: uppercase;">Tipo</label>
                    <select name="tipo" class="form-control" style="width:100%; height:42px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); color: var(--text-color); padding: 0 10px;" onchange="this.form.submit()">
                        <option value="todos" {{ $tipoActual === 'todos' ? 'selected' : '' }}>🏠 Todos</option>
                        <option value="terreno" {{ $tipoActual === 'terreno' ? 'selected' : '' }}>🌄 Terreno</option>
                        <option value="lote" {{ $tipoActual === 'lote' ? 'selected' : '' }}>🟩 Lote</option>
                        <option value="alquiler" {{ $tipoActual === 'alquiler' ? 'selected' : '' }}>🔑 Alquiler</option>
                    </select>
                </div>

                <!-- Estado Aprobación -->
                <div style="flex: 1; min-width: 130px;">
                    <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted); text-transform: uppercase;">Aprobación</label>
                    <select name="filtro" class="form-control" style="width:100%; height:42px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); color: var(--text-color); padding: 0 10px;" onchange="this.form.submit()">
                        <option value="todos" {{ $filtroActual === 'todos' ? 'selected' : '' }}>⚖️ Todos</option>
                        <option value="pendiente" {{ $filtroActual === 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                        <option value="aprobado" {{ $filtroActual === 'aprobado' ? 'selected' : '' }}>✅ Aprobado</option>
                        <option value="rechazado" {{ $filtroActual === 'rechazado' ? 'selected' : '' }}>❌ Rechazado</option>
                    </select>
                </div>

                <!-- Estado Disponibilidad -->
                <div style="flex: 1; min-width: 130px;">
                    <label style="display:block; font-size:.75rem; font-weight:600; margin-bottom:.35rem; color:var(--text-muted); text-transform: uppercase;">Disponibilidad</label>
                    <select name="estado_disponibilidad" class="form-control" style="width:100%; height:42px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); color: var(--text-color); padding: 0 10px;" onchange="this.form.submit()">
                        <option value="todos" {{ $estadoActual === 'todos' ? 'selected' : '' }}>🏷️ Todos</option>
                        <option value="disponible" {{ $estadoActual === 'disponible' ? 'selected' : '' }}>🟢 Disponible</option>
                        <option value="reservado" {{ $estadoActual === 'reservado' ? 'selected' : '' }}>🟡 Reservado</option>
                        <option value="vendido" {{ $estadoActual === 'vendido' ? 'selected' : '' }}>🔴 Vendido</option>
                        <option value="alquilado" {{ $estadoActual === 'alquilado' ? 'selected' : '' }}>🔑 Alquilado / Ocupado</option>
                    </select>
                </div>

                <!-- Botones Accion -->
                <div style="display:flex; gap:.5rem;">
                    <button type="submit" class="btn btn-primary" style="height:42px; padding: 0 1.25rem;">🔍 Filtrar</button>
                    <a href="{{ route('vendedor.propiedades_panel') }}" class="btn btn-secondary" style="height:42px; display:inline-flex; align-items:center; justify-content:center; padding: 0 1.25rem;">✖ Limpiar</a>
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
            Mis Propiedades
            <span class="badge badge-secondary">{{ $paginatedProperties->total() }}</span>
        </h2>
    </div>
    <div class="card-body no-padding">
        @if ($paginatedProperties->isEmpty())
            <div class="empty-state" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 48px; height: 48px; margin-bottom: 1rem; opacity: 0.5;">
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
                            <th>Título / Ubicación</th>
                            <th>Precio</th>
                            <th>Dimensión</th>
                            <th>Imagen</th>
                            <th>Aprobación</th>
                            <th>Disponibilidad</th>
                            <th>Folio Real / DD.RR.</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paginatedProperties as $p)
                            <tr class="property-row">
                                <!-- Código -->
                                <td style="font-weight:700; color:var(--text-muted); font-size:.85rem;">
                                    {{ $p->codigo }}
                                </td>
                                
                                <!-- Tipo -->
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
                                
                                <!-- Título / Ubicación -->
                                <td>
                                    <div style="font-weight: 600; color: var(--text-color);">
                                        {{ $p->nombre }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.8rem; display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        <span title="{{ $p->ubicacion }}">{{ Str::limit($p->ubicacion, 32) }}</span>
                                    </div>
                                </td>
                                
                                <!-- Precio -->
                                <td>
                                    <strong>
                                        @if($p->moneda === 'BOB' || $p->tipo === 'alquiler')
                                            Bs. {{ number_format($p->precio, 2) }}
                                        @else
                                            ${{ number_format($p->precio, 2) }} USD
                                        @endif
                                    </strong>
                                </td>
                                
                                <!-- Dimensión -->
                                <td>
                                    @if($p->metros_cuadrados)
                                        {{ number_format($p->metros_cuadrados, 2) }} m²
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                
                                <!-- Imagen -->
                                <td>
                                    @if($p->imagen)
                                        <img src="{{ asset($p->imagen) }}" 
                                             style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);"
                                             onerror="this.style.display='none'">
                                    @else
                                        <span class="badge badge-secondary" style="font-size: 0.75rem; padding: 4px 6px;">Sin foto</span>
                                    @endif
                                </td>
                                
                                <!-- Estado Aprobación -->
                                <td>
                                    <span class="badge badge-{{ $p->estado_aprobacion === 'aprobado' ? 'success' : ($p->estado_aprobacion === 'rechazado' ? 'danger' : 'warning') }}">
                                        {{ $p->estado_aprobacion === 'aprobado' ? '✅ Aprobado' : ($p->estado_aprobacion === 'rechazado' ? '❌ Rechazado' : '⏳ Pendiente') }}
                                    </span>
                                    @if($p->estado_aprobacion === 'rechazado' && $p->motivo_rechazo)
                                        <div class="alert alert-danger" style="margin-top: 6px; padding: 4px 8px; font-size: 0.75rem; border-radius: 6px; border: 1px solid rgba(220,53,69,0.2); background: rgba(220,53,69,0.05); color: #dc3545; max-width: 180px; white-space: normal; line-height: 1.2;">
                                            <strong>Motivo:</strong> {{ $p->motivo_rechazo }}
                                        </div>
                                    @endif
                                                    <td>
                                    @if($p->estado_aprobacion === 'aprobado')
                                        @if($p->tipo === 'alquiler')
                                            <!-- Select Alquiler -->
                                            <form action="{{ route('vendedor.alquileres.toggle_estado', $p->id) }}" method="POST">
                                                @csrf
                                                <select name="estado_lote" onchange="if(confirm('¿Seguro que deseas cambiar el estado de disponibilidad del alquiler?')) this.form.submit()" class="form-control form-control-sm" style="font-size: 0.75rem; height: auto; padding: 4px 8px; border-radius: 20px; width: 115px; border: 1px solid var(--border-color); font-weight: 600; cursor: pointer; color: var(--text-color); background: var(--bg-light);">
                                                    <option value="disponible" {{ $p->estado_disponibilidad === 'disponible' ? 'selected' : '' }}>🟢 Disponible</option>
                                                    <option value="reservado" {{ $p->estado_disponibilidad === 'reservado' ? 'selected' : '' }}>🟡 Reservado</option>
                                                    <option value="vendido" {{ in_array($p->estado_disponibilidad, ['vendido', 'alquilado']) ? 'selected' : '' }}>🔴 Vendido</option>
                                                </select>
                                            </form>
                                        @elseif($p->tipo === 'terreno')
                                            <!-- Select Terreno -->
                                            <form action="{{ route('vendedor.terrenos.toggle_estado', $p->id) }}" method="POST">
                                                @csrf
                                                <select name="estado_lote" onchange="if(confirm('¿Seguro que deseas cambiar el estado de disponibilidad del terreno?')) this.form.submit()" class="form-control form-control-sm" style="font-size: 0.75rem; height: auto; padding: 4px 8px; border-radius: 20px; width: 115px; border: 1px solid var(--border-color); font-weight: 600; cursor: pointer; color: var(--text-color); background: var(--bg-light);">
                                                    <option value="disponible" {{ $p->estado_disponibilidad === 'disponible' ? 'selected' : '' }}>🟢 Disponible</option>
                                                    <option value="reservado" {{ $p->estado_disponibilidad === 'reservado' ? 'selected' : '' }}>🟡 Reservado</option>
                                                    <option value="vendido" {{ $p->estado_disponibilidad === 'vendido' ? 'selected' : '' }}>🔴 Vendido</option>
                                                </select>
                                            </form>
                                        @elseif($p->tipo === 'lote')
                                            <!-- Select Lote -->
                                            <form action="{{ route('vendedor.lotes.estado', $p->id) }}" method="POST">
                                                @csrf
                                                <select name="estado_lote" onchange="if(confirm('¿Seguro que deseas cambiar el estado del lote?')) this.form.submit()" class="form-control form-control-sm" style="font-size: 0.75rem; height: auto; padding: 4px 8px; border-radius: 20px; width: 115px; border: 1px solid var(--border-color); font-weight: 600; cursor: pointer; color: var(--text-color); background: var(--bg-light);">
                                                    <option value="disponible" {{ $p->estado_disponibilidad === 'disponible' ? 'selected' : '' }}>🟢 Disponible</option>
                                                    <option value="reservado" {{ $p->estado_disponibilidad === 'reservado' ? 'selected' : '' }}>🟡 Reservado</option>
                                                    <option value="vendido" {{ $p->estado_disponibilidad === 'vendido' ? 'selected' : '' }}>🔴 Vendido</option>
                                                </select>
                                            </form>
                                        @endif
                                    @else
                                        <!-- Estático para no aprobados -->
                                        @php
                                            $dispBadges = [
                                                'disponible' => 'success',
                                                'reservado' => 'warning',
                                                'vendido' => 'danger',
                                                'alquilado' => 'secondary',
                                                'ocupado' => 'secondary'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $dispBadges[$p->estado_disponibilidad] ?? 'secondary' }}" style="font-size: 0.75rem;">
                                            {{ ucfirst($p->estado_disponibilidad === 'alquilado' ? 'ocupado' : $p->estado_disponibilidad) }}
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- Folio Real / Derechos Reales -->
                                <td>
                                    @if($p->tipo === 'alquiler')
                                        <span class="text-muted">—</span>
                                    @else
                                        <div class="folio-status-wrapper" style="font-size: 0.8rem; line-height: 1.4;">
                                            @if(!$p->folio)
                                                <a href="{{ route('vendedor.folio.create', $p->id) }}" class="btn btn-sm btn-warning" style="font-size:0.75rem; padding: 4px 8px; border-radius: 6px; font-weight:600; display: inline-flex; align-items: center; gap: 4px;">
                                                    📋 Agregar Folio
                                                </a>
                                            @elseif($p->folio->estado === 'pendiente')
                                                <div class="text-warning" style="font-weight: 500;">
                                                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #ffc107; margin-right: 4px;"></span>
                                                    Folio enviado (Pendiente)
                                                </div>
                                                <a href="{{ route('vendedor.folio.edit', $p->id) }}" class="text-primary" style="font-size:0.75rem; text-decoration:underline; font-weight: 600;">✏️ Editar Folio</a>
                                            @elseif($p->folio->estado === 'rechazado')
                                                <div class="text-danger" style="font-weight: 500;">
                                                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #dc3545; margin-right: 4px;"></span>
                                                    Folio Rechazado
                                                </div>
                                                <a href="{{ route('vendedor.folio.edit', $p->id) }}" class="btn btn-sm btn-danger" style="font-size:0.7rem; padding: 2px 6px; border-radius: 4px; font-weight: 600; margin-top:2px; display:inline-block;">Corregir</a>
                                            @elseif($p->folio->estado === 'verificado')
                                                <div class="text-success" style="font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="width: 12px; height: 12px; color: var(--success-color);">
                                                        <polyline points="20 6 9 17 4 12" />
                                                    </svg>
                                                    Folio: {{ $p->folio->numero_folio }}
                                                </div>
                                                
                                                <!-- Derechos Reales -->
                                                @php $ins = $p->folio->inscripcionDerechosReales; @endphp
                                                @if(!$ins)
                                                    <a href="{{ route('vendedor.inscripcion.create', $p->folio->id) }}" class="btn btn-sm btn-primary" style="font-size:0.75rem; padding: 4px 8px; border-radius: 6px; font-weight:600; margin-top: 4px; display:inline-block;">
                                                        🏛️ Inscribir DD.RR.
                                                    </a>
                                                @elseif($ins->estado === 'pendiente')
                                                    <div class="text-warning" style="font-size:0.75rem; margin-top:2px; font-weight:500;"><i class="fas fa-hourglass-half"></i> DD.RR. En revisión</div>
                                                @elseif($ins->estado === 'en_revision')
                                                    <div class="text-info" style="font-size:0.75rem; margin-top:2px; font-weight:500;"><i class="fas fa-search"></i> DD.RR. Siendo revisada</div>
                                                @elseif($ins->estado === 'rechazado')
                                                    <div class="text-danger" style="font-size:0.75rem; margin-top:2px; font-weight:500;"><i class="fas fa-exclamation-triangle"></i> DD.RR. Rechazada</div>
                                                    @if($ins->observacion_admin)
                                                        <div style="font-size:0.7rem; color:#721c24; background:#f8d7da; padding: 2px 6px; border-radius:4px; margin-top:2px; max-width: 180px; white-space: normal;">
                                                            {{ Str::limit($ins->observacion_admin, 35) }}
                                                        </div>
                                                    @endif
                                                    <a href="{{ route('vendedor.inscripcion.create', $p->folio->id) }}" class="text-primary" style="font-size:0.75rem; text-decoration:underline; display:inline-block; margin-top:2px; font-weight:600;">🔄 Corregir</a>
                                                @elseif($ins->estado === 'inscrito')
                                                    <div class="text-success" style="font-size:0.75rem; font-weight:600; margin-top:2px; display: flex; align-items: center; gap: 4px;">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="width: 12px; height: 12px; color: var(--success-color);">
                                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                                        </svg>
                                                        DD.RR. Inscrito
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                
                                <!-- Acciones -->
                                <td>
                                    <div style="display: flex; gap: 6px; align-items: center;">
                                        <!-- Editar -->
                                        @if($p->estado_disponibilidad !== 'vendido')
                                            <a href="{{ $p->edit_route }}" class="btn btn-sm btn-outline-secondary" style="padding: 6px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;" title="Editar">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; color: var(--text-color);">
                                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" style="padding: 6px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; opacity: 0.5; cursor: not-allowed;" title="No se puede editar propiedad vendida" disabled>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </button>
                                        @endif

                                        <!-- Ver Catálogo -->
                                        @if($p->estado_aprobacion === 'aprobado' && $p->estado_disponibilidad !== 'vendido' && $p->estado_disponibilidad !== 'alquilado')
                                            <a href="{{ $p->catalog_route }}" target="_blank" class="btn btn-sm btn-outline-primary" style="padding: 6px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;" title="Ver en Catálogo Público">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                            </a>
                                        @endif

                                        <!-- Subir Documentos (Terrenos y Lotes) -->
                                        @if($p->documentos_route && $p->estado_disponibilidad !== 'vendido')
                                            <a href="{{ $p->documentos_route }}" class="btn btn-sm btn-outline-info" style="padding: 6px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;" title="Subir Documentos Complementarios">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48" />
                                                </svg>
                                            </a>
                                        @endif

                                        <!-- Solicitudes de Visita (OBS-V10) -->
                                        <a href="{{ route('vendedor.propiedades.solicitudes', ['tipo' => $p->tipo, 'id' => $p->id]) }}" class="btn btn-sm btn-outline-warning" style="padding: 6px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;" title="Solicitudes de Visita">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; color: #d97706;">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                        </a>

                                        @if($p->tipo !== 'alquiler')
                                            <a href="{{ route('vendedor.expediente', $p->id) }}"
                                               class="btn btn-sm"
                                               style="padding: 6px; border-radius: 8px; display: inline-flex; 
                                                      align-items: center; justify-content: center; width: 32px; height: 32px;
                                                      background: rgba(234, 179, 8, 0.15); border: 1px solid rgba(234, 179, 8, 0.4);
                                                      color: #eab308;"
                                               title="Expediente Legal">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" 
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 1 2-2V8z"/>
                                                    <polyline points="14 2 14 8 20 8"/>
                                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                                    <polyline points="10 9 9 9 8 9"/>
                                                </svg>
                                            </a>
                                        @endif

                                        @if($p->chat_route)
                                            <a href="{{ $p->chat_route }}"
                                               class="btn btn-sm btn-primary position-relative"
                                               style="padding: 0; border-radius: 999px; display: inline-flex;
                                                      align-items: center; justify-content: center; width: 36px; height: 36px;
                                                      border: none; background: #1e62ff; color: #fff;"
                                               title="Mensajes">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" 
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                                </svg>
                                                @if($p->unread_count > 0)
                                                    <span style="
                                                        position: absolute;
                                                        top: 4px;
                                                        right: 4px;
                                                        width: 8px;
                                                        height: 8px;
                                                        border-radius: 50%;
                                                        background: #dc3545;
                                                    "></span>
                                                @endif
                                            </a>
                                        @else
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    style="padding: 6px; border-radius: 8px; display: inline-flex; 
                                                           align-items: center; justify-content: center; 
                                                           width: 32px; height: 32px; opacity: 0.4; cursor: not-allowed;"
                                                    title="Sin chat disponible" disabled>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" 
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                                </svg>
                                            </button>
                                        @endif

                                        <!-- Eliminar Alquiler -->
                                        @if($p->tipo === 'alquiler')
                                            <form action="{{ route('vendedor.alquileres.destroy', $p->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta publicación de alquiler?');" style="margin: 0; display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm" style="padding: 6px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); cursor: pointer;" title="Eliminar Alquiler">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
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
