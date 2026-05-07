@extends('layouts.app')

@section('title', 'Gestión de Alquileres')

@section('content')
<!-- ═══ Stats Cards ═══ -->
<div class="stats-grid" id="alquilerStatsSection">
    <div class="stat-card stat-total">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                <polyline points="9,22 9,12 15,12 15,22" />
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats->total }}</span>
            <span class="stat-label">Total Alquileres</span>
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
            <span class="stat-label">Aprobados</span>
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
            <span class="stat-label">Rechazados</span>
        </div>
    </div>
</div>

<!-- ═══ Filtros ═══ -->
<div class="card" id="alquilerFiltersCard">
    <div class="card-body">
        <div class="filters-row">
            <div class="search-box">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <input type="text" id="searchAlquiler" class="search-input" placeholder="Buscar por título, ubicación o vendedor...">
            </div>
            <div class="filter-buttons">
                <a href="{{ url('/admin/alquileres?filtro=todos') }}" class="btn-filter {{ $filtroActual === 'todos' ? 'active' : '' }}">Todos</a>
                <a href="{{ url('/admin/alquileres?filtro=pendiente') }}" class="btn-filter {{ $filtroActual === 'pendiente' ? 'active' : '' }}">⏳ Pendientes</a>
                <a href="{{ url('/admin/alquileres?filtro=aprobado') }}" class="btn-filter {{ $filtroActual === 'aprobado' ? 'active' : '' }}">✅ Aprobados</a>
                <a href="{{ url('/admin/alquileres?filtro=rechazado') }}" class="btn-filter {{ $filtroActual === 'rechazado' ? 'active' : '' }}">❌ Rechazados</a>
            </div>
        </div>
    </div>
</div>

<!-- ═══ Tabla de Alquileres ═══ -->
<div class="card" id="alquileresTableCard">
    <div class="card-header">
        <h2 class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                <polyline points="9,22 9,12 15,12 15,22" />
            </svg>
            Publicaciones de Alquileres
            <span class="badge badge-secondary">{{ count($alquileres) }}</span>
        </h2>
    </div>
    <div class="card-body no-padding">
        @if (count($alquileres) === 0)
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    <polyline points="9,22 9,12 15,12 15,22" />
                </svg>
                <p>No se encontraron alquileres con el filtro seleccionado.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="data-table" id="alquileresTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vendedor</th>
                            <th>Título</th>
                            <th>Ubicación</th>
                            <th>Precio/mes</th>
                            <th>Hab/Baños</th>
                            <th>Imagen</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alquileres as $a)
                            <tr class="alquiler-row">
                                <td class="id-cell">{{ $a->id }}</td>
                                <td>
                                    <div class="user-cell">
                                        <span class="user-name-text">{{ $a->usuario->nombre ?? 'N/A' }}</span>
                                        <span class="user-email-text">{{ $a->usuario->email ?? '' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="ubicacion-text" title="{{ $a->titulo }}">{{ Str::limit($a->titulo, 30) }}</span>
                                </td>
                                <td>
                                    <span class="ubicacion-text" title="{{ $a->ubicacion }}">{{ Str::limit($a->ubicacion, 30) }}</span>
                                </td>
                                <td><strong>${{ number_format($a->precio_mensual, 2) }}</strong></td>
                                <td>{{ $a->habitaciones }} 🛏️ / {{ $a->banos }} 🚿</td>
                                <td>
                                    @if($a->imagenes->first())
                                        <img src="{{ asset($a->imagenes->first()->ruta_archivo) }}" 
                                        style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                        onerror="this.src='https://via.placeholder.com/60?text=Error'">
                                        <span class="badge badge-info" style="margin-left: 5px;">{{ $a->imagenes->count() }}</span>
                                    @else
                                        <span class="badge badge-secondary">Sin imagen</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $a->estado_aprobacion === 'aprobado' ? 'success' : ($a->estado_aprobacion === 'rechazado' ? 'danger' : 'warning') }}">
                                        {{ $a->estado_aprobacion === 'aprobado' ? '✅ Aprobado' : ($a->estado_aprobacion === 'rechazado' ? '❌ Rechazado' : '⏳ Pendiente') }}
                                    </span>
                                </td>
                                <td class="date-cell">
                                    <div class="date-multi">
                                        <span class="date-main">{{ \Carbon\Carbon::parse($a->created_at)->timezone('America/La_Paz')->translatedFormat('d M Y') }}</span>
                                        <span class="date-time">{{ \Carbon\Carbon::parse($a->created_at)->timezone('America/La_Paz')->translatedFormat('H:i') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.ver_alquiler', $a->id) }}" class="btn btn-sm btn-outline" title="Ver detalle">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
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
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchAlquiler');
    const table = document.getElementById('alquileresTable');
    if (searchInput && table) {
        const rows = table.querySelectorAll('tbody tr');
        searchInput.addEventListener('input', function (e) {
            const term = e.target.value.toLowerCase().trim();
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
});
</script>
@endsection
