@extends('layouts.app')

@section('title', 'Mis Terrenos')

@section('content')
    <div class="page-header">
        <h1>Mis Terrenos y Lotes</h1>
        <a href="{{ route('vendedor.terrenos.create') }}" class="btn btn-primary">+ Publicar nueva propiedad</a>
    </div>

    @if($terrenos->isEmpty())
        <div class="empty-state">
            <p>No has publicado ningún terreno aún.</p>
            <a href="{{ route('vendedor.terrenos.create') }}" class="btn btn-primary">Publicar mi primera propiedad</a>
        </div>
    @else
        <div class="terrenos-grid">
            @foreach($terrenos as $terreno)
                <div class="card terreno-card">
                    <div class="terreno-imagen">
                        @if($terreno->portada)
                            <img src="{{ asset($terreno->portada->ruta_archivo) }}" alt="Portada">
                        @elseif($terreno->imagenes->first())
                            <img src="{{ asset($terreno->imagenes->first()->ruta_archivo) }}" alt="Terreno">
                        @else
                            <div class="sin-imagen">📷 Sin imagen</div>
                        @endif
                        
                        @if($terreno->estado_lote === 'vendido')
                            <div style="position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                🏆 VENDIDO
                            </div>
                        @endif
                    </div>
                    <div class="terreno-info">
                        <div class="mb-2">
                            <span class="badge badge-{{ ($terreno->tipo ?? 'terreno') === 'lote' ? 'info' : 'primary' }}">
                                {{ ($terreno->tipo ?? 'terreno') === 'lote' ? 'Lote' : 'Terreno' }}
                            </span>
                        </div>
                        <h3>{{ $terreno->ubicacion }}</h3>
                        <p class="precio">${{ number_format($terreno->precio, 2) }} USD</p>
                        <p>{{ $terreno->metros_cuadrados }} m²</p>
                        @if($terreno->estado_lote === 'vendido')
                            <span class="badge badge-danger">Vendido Oficialmente</span>
                        @else
                            <span class="badge badge-{{ $terreno->estado === 'aprobado' ? 'success' : ($terreno->estado === 'rechazado' ? 'danger' : 'warning') }}">
                                {{ ucfirst($terreno->estado) }}
                            </span>
                        @endif

                        @if($terreno->estado === 'rechazado' && $terreno->motivo_rechazo)
                            <div class="alert alert-danger mt-3 mb-0" style="padding: 0.5rem; font-size: 0.9rem;">
                                <strong>Motivo de rechazo:</strong> {{ $terreno->motivo_rechazo }}
                            </div>
                        @endif

                        @if(($terreno->tipo ?? 'terreno') === 'lote' && $terreno->terrenoPadre)
                            <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                                Terreno padre: {{ $terreno->terrenoPadre->ubicacion }}
                            </p>
                        @endif

                        @if($terreno->estado === 'aprobado' && $terreno->estado_lote !== 'vendido')
                            @php
                                $badgeLote = 'secondary';
                                if($terreno->estado_lote === 'disponible') $badgeLote = 'success';
                                if($terreno->estado_lote === 'reservado') $badgeLote = 'warning';
                            @endphp
                            <div class="mt-2">
                                <span style="font-size: 0.85em; color: var(--text-muted);">Estado del Lote:</span>
                                <span class="badge badge-{{ $badgeLote }}">{{ ucfirst($terreno->estado_lote) }}</span>
                            </div>
                        @endif

                        {{-- ═══ SECCIÓN FOLIO E INSCRIPCIÓN ═══ --}}
                        @if($terreno->estado === 'aprobado')
                            <div class="folio-section">

                                {{-- FOLIO: no tiene → botón agregar --}}
                                @if(!$terreno->folio)
                                    <a href="{{ route('vendedor.folio.create', $terreno->id) }}"
                                       class="btn btn-sm btn-warning" style="width:100%; text-align:center;">
                                        📋 Agregar Folio Real
                                    </a>

                                {{-- FOLIO: pendiente de verificación --}}
                                @elseif($terreno->folio->estado === 'pendiente')
                                    <div class="folio-status folio-pendiente">
                                        <span>📋 Folio enviado</span>
                                        <span class="folio-badge badge-pendiente">🕐 Pendiente verificación</span>
                                    </div>
                                    <a href="{{ route('vendedor.folio.edit', $terreno->id) }}"
                                       class="btn btn-sm btn-secondary" style="width:100%; text-align:center; margin-top:.4rem;">
                                        ✏️ Editar Folio
                                    </a>

                                {{-- FOLIO: rechazado --}}
                                @elseif($terreno->folio->estado === 'rechazado')
                                    <div class="folio-status folio-rechazado">
                                        <span>📋 Folio</span>
                                        <span class="folio-badge badge-rechazado">❌ Rechazado</span>
                                    </div>
                                    <a href="{{ route('vendedor.folio.edit', $terreno->id) }}"
                                       class="btn btn-sm btn-danger" style="width:100%; text-align:center; margin-top:.4rem;">
                                        ✏️ Corregir Folio
                                    </a>

                                {{-- FOLIO: verificado → mostrar estado + botón inscripción --}}
                                @elseif($terreno->folio->estado === 'verificado')
                                    <div class="folio-status folio-verificado">
                                        <span>📋 Folio <strong>{{ $terreno->folio->numero_folio }}</strong></span>
                                        <span class="folio-badge badge-verificado">✅ Verificado</span>
                                    </div>

                                    {{-- INSCRIPCIÓN DERECHOS REALES --}}
                                    @php $ins = $terreno->folio->inscripcionDerechosReales; @endphp

                                    @if(!$ins)
                                        {{-- Sin inscripción → botón enviar --}}
                                        <a href="{{ route('vendedor.inscripcion.create', $terreno->folio->id) }}"
                                           class="btn btn-sm btn-primary" style="width:100%; text-align:center; margin-top:.5rem;">
                                            🏛️ Inscribir en Derechos Reales
                                        </a>

                                    @elseif($ins->estado === 'pendiente')
                                        <div class="folio-status" style="margin-top:.5rem;">
                                            <span>🏛️ Inscripción</span>
                                            <span class="folio-badge badge-pendiente">🕐 En revisión</span>
                                        </div>

                                    @elseif($ins->estado === 'en_revision')
                                        <div class="folio-status" style="margin-top:.5rem;">
                                            <span>🏛️ Inscripción</span>
                                            <span class="folio-badge badge-en_revision">🔍 Siendo revisada</span>
                                        </div>

                                    @elseif($ins->estado === 'rechazado')
                                        <div class="folio-status" style="margin-top:.5rem;">
                                            <span>🏛️ Inscripción</span>
                                            <span class="folio-badge badge-rechazado">❌ Rechazada</span>
                                        </div>
                                        @if($ins->observacion_admin)
                                            <div style="font-size:.78rem; color:#721c24; margin-top:.3rem; padding:.4rem .6rem; background:#f8d7da; border-radius:6px;">
                                                {{ $ins->observacion_admin }}
                                            </div>
                                        @endif
                                        <a href="{{ route('vendedor.inscripcion.create', $terreno->folio->id) }}"
                                           class="btn btn-sm btn-danger" style="width:100%; text-align:center; margin-top:.4rem;">
                                            🔄 Corregir Inscripción
                                        </a>

                                    @elseif($ins->estado === 'inscrito')
                                        <div class="folio-status" style="margin-top:.5rem;">
                                            <span>🏛️ Inscripción</span>
                                            <span class="folio-badge badge-inscrito">✅ Inscrito</span>
                                        </div>
                                    @endif

                                @endif
                            </div>
                        @endif
                        {{-- ═══ FIN SECCIÓN FOLIO ═══ --}}

                        <div class="acciones" style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
                            @if($terreno->estado === 'aprobado' && $terreno->estado_lote !== 'vendido')
                                {{-- Botones para los 3 estados (disponible / reservado / vendido) --}}
                                <form action="{{ route('vendedor.terrenos.toggle_estado', $terreno->id) }}" method="POST" style="display:flex; gap:6px; flex-wrap:wrap;">
                                    @csrf
                                    <button type="submit" name="estado_lote" value="disponible" 
                                        class="btn btn-sm {{ $terreno->estado_lote === 'disponible' ? 'btn-success' : 'btn-outline-success' }}" 
                                        style="padding:5px 14px; font-size:0.78em; font-weight:700; border-radius:20px; {{ $terreno->estado_lote === 'disponible' ? 'background:#28a745;color:#fff;border:2px solid #28a745;' : 'background:#fff;color:#28a745;border:2px solid #28a745;' }}"
                                        onclick="return confirm('¿Cambiar a DISPONIBLE?')">
                                        🟢 Disponible
                                    </button>
                                    <button type="submit" name="estado_lote" value="reservado" 
                                        class="btn btn-sm {{ $terreno->estado_lote === 'reservado' ? 'btn-warning' : 'btn-outline-warning' }}" 
                                        style="padding:5px 14px; font-size:0.78em; font-weight:700; border-radius:20px; {{ $terreno->estado_lote === 'reservado' ? 'background:#ffc107;color:#fff;border:2px solid #ffc107;' : 'background:#fff;color:#ffc107;border:2px solid #ffc107;' }}"
                                        onclick="return confirm('¿Marcar como RESERVADO?')">
                                        🟡 Reservado
                                    </button>
                                    <button type="submit" name="estado_lote" value="vendido" 
                                        class="btn btn-sm {{ $terreno->estado_lote === 'vendido' ? 'btn-danger' : 'btn-outline-danger' }}" 
                                        style="padding:5px 14px; font-size:0.78em; font-weight:700; border-radius:20px; {{ $terreno->estado_lote === 'vendido' ? 'background:#dc3545;color:#fff;border:2px solid #dc3545;' : 'background:#fff;color:#dc3545;border:2px solid #dc3545;' }}"
                                        onclick="return confirm('¿Marcar como VENDIDO? La propiedad dejará de aparecer en el catálogo.')">
                                        🔴 Vendido
                                    </button>
                                </form>
                            @elseif(($terreno->tipo ?? 'terreno') === 'lote' && $terreno->estado === 'aprobado' && $terreno->estado_lote !== 'vendido')
                                <a href="{{ route('vendedor.lotes') }}" class="btn btn-sm btn-outline-info" style="padding:5px 14px; font-weight:600;">Gestionar en Control de Lotes</a>
                            @endif

                            @if($terreno->estado_lote !== 'vendido' && in_array($terreno->estado, ['pendiente', 'rechazado']))
                                <a href="{{ route('vendedor.terrenos.edit', $terreno->id) }}" class="btn btn-sm btn-secondary" style="padding:5px 14px; font-weight:600;">✏️ Editar</a>
                            @elseif($terreno->estado_lote === 'vendido')
                                <span class="btn btn-sm btn-outline-secondary" style="opacity: 0.6; cursor: not-allowed; padding:5px 14px;">Trámite finalizado</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

<style>
    .terrenos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .terreno-card {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .terreno-imagen {
        height: 200px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .terreno-imagen img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sin-imagen {
        font-size: 2rem;
        color: #ccc;
    }

    .terreno-info {
        padding: 1rem;
    }

    .precio {
        font-size: 1.2rem;
        font-weight: bold;
        color: #007bff;
    }

    .acciones {
        margin-top: 1rem;
        display: flex;
        gap: 0.5rem;
    }

    /* ── Folio section ── */
    .folio-section {
        margin-top: 1rem;
        padding-top: .75rem;
        border-top: 1px solid var(--border-color, #e5e7eb);
        display: flex;
        flex-direction: column;
        gap: .3rem;
    }

    .folio-status {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: .83rem;
        color: var(--text-secondary, #555);
    }

    .folio-badge {
        font-size: .72rem;
        font-weight: 700;
        padding: .2rem .65rem;
        border-radius: 100px;
    }

    .badge-pendiente   { background:#fff3cd; border:1px solid #ffeeba; color:#856404; }
    .badge-verificado  { background:#d4edda; border:1px solid #c3e6cb; color:#155724; }
    .badge-rechazado   { background:#f8d7da; border:1px solid #f5c6cb; color:#721c24; }
    .badge-en_revision { background:#cce5ff; border:1px solid #b8daff; color:#004085; }
    .badge-inscrito    { background:#d4edda; border:1px solid #c3e6cb; color:#155724; }
</style>

@push('scripts')
<script>
function confirmDisponible() {
    return confirm('¿Marcar como DISPONIBLE? La propiedad volverá a aparecer en el catálogo para los compradores.');
}
</script>
@endpush
