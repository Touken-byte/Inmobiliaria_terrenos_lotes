@extends('layouts.app')

@section('title', 'Moderación de Anuncios')

@section('content')
<!-- ═══ Stats Cards ═══ -->
<div class="stats-grid" id="terrenoStatsSection">
    <div class="stat-card stat-pending" style="border-left: 4px solid var(--warning-color);">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12,6 12,12 16,14" />
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats->pendientes }}</span>
            <span class="stat-label">Anuncios Pendientes por Revisar</span>
        </div>
    </div>
    <div class="stat-card stat-verified" style="border-left: 4px solid var(--success-color);">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                <polyline points="22,4 12,14.01 9,11.01" />
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats->total_aprobados }}</span>
            <span class="stat-label">Total Aprobados en Catálogo</span>
        </div>
    </div>
</div>

<!-- Filtros eliminados de moderación -->

<!-- ═══ Tabla de Terrenos ═══ -->
<div class="card" id="terrenosTableCard">
    <div class="card-header">
        <h2 class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                <line x1="3" y1="9" x2="21" y2="9" />
                <line x1="9" y1="21" x2="9" y2="9" />
            </svg>
            Cola de Aprobación
            <span class="badge badge-secondary">{{ count($terrenos) }}</span>
        </h2>
    </div>
    <div class="card-body no-padding">
        @if (count($terrenos) === 0)
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                    <line x1="3" y1="9" x2="21" y2="9" />
                    <line x1="9" y1="21" x2="9" y2="9" />
                </svg>
                <p>No se encontraron terrenos con el filtro seleccionado.</p>
            </div>
        @else
            <div class="terrenos-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; padding: 1rem;">
                @foreach ($terrenos as $t)
                    <div class="card terreno-card" style="display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--border-color);">
                        <div class="terreno-imagen" style="height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; position: relative;">
                            @if($t->imagenes->first())
                                <img src="{{ $t->imagenes->first()->url }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://via.placeholder.com/300x200?text=Error'">
                                <span class="badge badge-info" style="position: absolute; bottom: 10px; right: 10px;">{{ $t->imagenes->count() }} fotos</span>
                            @else
                                <span style="font-size: 2rem; color: #ccc;">📷 Sin imagen</span>
                            @endif
                            <span class="badge badge-warning" style="position: absolute; top: 10px; left: 10px;">⏳ Pendiente</span>
                        </div>
                        <div class="terreno-info" style="padding: 1rem; flex-grow: 1; display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <strong>#{{ $t->id }}</strong>
                                <span style="font-size: 0.85em; color: var(--text-muted);">{{ \Carbon\Carbon::parse($t->creado_en)->timezone('America/La_Paz')->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                            
                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.1rem; line-height: 1.3;" title="{{ $t->ubicacion }}">{{ Str::limit($t->ubicacion, 50) }}</h3>
                            <p style="margin: 0 0 0.5rem 0; font-size: 1.2rem; font-weight: bold; color: var(--primary-color);">${{ number_format($t->precio, 2) }} USD</p>
                            <p style="margin: 0 0 1rem 0; color: var(--text-muted);">{{ number_format($t->metros_cuadrados, 2) }} m²</p>
                            
                            <div style="margin-bottom: 1rem; padding: 0.5rem; background: var(--bg-color); border-radius: 6px; font-size: 0.9em;">
                                <strong>Vendedor:</strong><br>
                                {{ $t->vendedor->nombre ?? 'N/A' }}<br>
                                <span style="color: var(--text-muted);">{{ $t->vendedor->email ?? '' }}</span>
                            </div>

                            <div style="margin-top: auto; display: flex; gap: 0.5rem;">
                                <a href="{{ route('admin.ver_terreno', $t->id) }}" class="btn btn-primary" style="flex-grow: 1; justify-content: center;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px; margin-right: 5px;">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    Revisar Anuncio
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchTerreno');
    const table = document.getElementById('terrenosTable');
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