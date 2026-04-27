@extends('layouts.app')

@section('title', 'Control de Lotes')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px; margin-right: 12px; color: var(--accent);">
                <rect x="3" y="3" width="7" height="7" />
                <rect x="14" y="3" width="7" height="7" />
                <rect x="14" y="14" width="7" height="7" />
                <rect x="3" y="14" width="7" height="7" />
            </svg>
            Control de Disponibilidad de Lotes
            <span class="badge badge-secondary">{{ count($terrenos) }}</span>
        </h2>
    </div>
    <div class="card-body no-padding">
        @if (count($terrenos) === 0)
            <div class="empty-state">
                <p>No se encontraron lotes registrados.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="data-table" id="lotesTable">
                    <thead>
                        <tr>
                            <th>ID Lote</th>
                            <th>Ubicación</th>
                            <th>Vendedor</th>
                            <th>Estado Actual</th>
                            <th>Último Cambio</th>
                            <th>Actualizar Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($terrenos as $t)
                            <tr class="terreno-row">
                                <td class="id-cell">#{{ $t->id }}</td>
                                <td>
                                    <span class="ubicacion-text" title="{{ $t->ubicacion }}">{{ Str::limit($t->ubicacion, 40) }}</span>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <span class="user-name-text">{{ $t->vendedor->nombre ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = 'secondary';
                                        if($t->estado_lote === 'disponible') $badgeClass = 'success';
                                        if($t->estado_lote === 'reservado') $badgeClass = 'warning';
                                        if($t->estado_lote === 'vendido') $badgeClass = 'danger';
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">
                                        {{ ucfirst($t->estado_lote) }}
                                    </span>
                                </td>
                                <td class="date-cell">
                                    <div class="date-multi">
                                        <span class="date-main" style="display: block; font-weight: 500;">{{ $t->actualizado_en ? \Carbon\Carbon::parse($t->actualizado_en)->locale('es')->diffForHumans() : 'Fecha no disponible' }}</span>
                                        @if($t->actualizado_en)
                                            <span class="date-time" style="font-size: 0.85em; opacity: 0.7;">{{ \Carbon\Carbon::parse($t->actualizado_en)->timezone('America/La_Paz')->translatedFormat('d M Y, H:i') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($t->estado === 'aprobado')
                                        @if(Auth::user() && Auth::user()->rol === 'vendedor')
                                            <form action="{{ route('vendedor.lotes.estado', $t->id) }}" method="POST" style="display: flex; gap: 4px; align-items: center; flex-wrap: wrap;">
                                                @csrf
                                                <button type="submit" name="estado_lote" value="disponible" 
                                                    class="btn btn-sm {{ $t->estado_lote === 'disponible' ? 'btn-primary' : '' }}" 
                                                    style="padding: 4px 8px; font-size: 0.8em; border: 1px solid rgba(255,255,255,0.2); background: {{ $t->estado_lote === 'disponible' ? 'rgba(34, 197, 94, 0.2)' : 'transparent' }}; color: {{ $t->estado_lote === 'disponible' ? '#4ade80' : '#ccc' }}; border-radius: 6px;">
                                                    Disponible
                                                </button>
                                                <button type="submit" name="estado_lote" value="reservado" 
                                                    class="btn btn-sm {{ $t->estado_lote === 'reservado' ? 'btn-primary' : '' }}" 
                                                    style="padding: 4px 8px; font-size: 0.8em; border: 1px solid rgba(255,255,255,0.2); background: {{ $t->estado_lote === 'reservado' ? 'rgba(234, 179, 8, 0.2)' : 'transparent' }}; color: {{ $t->estado_lote === 'reservado' ? '#facc15' : '#ccc' }}; border-radius: 6px;">
                                                    Reservado
                                                </button>
                                                <button type="submit" name="estado_lote" value="vendido" 
                                                    class="btn btn-sm {{ $t->estado_lote === 'vendido' ? 'btn-primary' : '' }}" 
                                                    style="padding: 4px 8px; font-size: 0.8em; border: 1px solid rgba(255,255,255,0.2); background: {{ $t->estado_lote === 'vendido' ? 'rgba(239, 68, 68, 0.2)' : 'transparent' }}; color: {{ $t->estado_lote === 'vendido' ? '#f87171' : '#ccc' }}; border-radius: 6px;">
                                                    Vendido
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge badge-secondary">Modificable por el Vendedor de la propiedad</span>
                                        @endif
                                    @elseif($t->estado === 'rechazado')
                                        <span class="badge badge-danger">Rechazado por moderación</span>
                                    @else
                                        <span class="badge badge-warning" style="color: #000;">Pendiente de aprobación</span>
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
