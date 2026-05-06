@extends('layouts.app')

@section('title', 'Mis Terrenos')

@section('content')
    <div class="page-header">
        <h1>Mis Terrenos</h1>
        <a href="{{ route('vendedor.terrenos.create') }}" class="btn btn-primary">+ Publicar nuevo terreno</a>
    </div>

    @if($terrenos->isEmpty())
        <div class="empty-state">
            <p>No has publicado ningún terreno aún.</p>
            <a href="{{ route('vendedor.terrenos.create') }}" class="btn btn-primary">Publicar mi primer terreno</a>
        </div>
    @else
        <div class="terrenos-grid">
            @foreach($terrenos as $terreno)
                <div class="card terreno-card">
                    <div class="terreno-imagen">
                        @if($terreno->portada)
                            <img src="{{ $terreno->portada->url }}" alt="Portada">
                        @elseif($terreno->imagenes->first())
                            <img src="{{ $terreno->imagenes->first()->url }}" alt="Terreno">
                        @else
                            <div class="sin-imagen">📷 Sin imagen</div>
                        @endif
                    </div>
                    <div class="terreno-info">
                        <h3>{{ $terreno->ubicacion }}</h3>
                        <p class="precio">${{ number_format($terreno->precio, 2) }} USD</p>
                        <p>{{ $terreno->metros_cuadrados }} m²</p>
                        <span class="badge badge-{{ $terreno->estado === 'aprobado' ? 'success' : ($terreno->estado === 'rechazado' ? 'danger' : 'warning') }}">
                            {{ ucfirst($terreno->estado) }}
                        </span>

                        @if($terreno->estado === 'rechazado' && $terreno->motivo_rechazo)
                            <div class="alert alert-danger mt-3 mb-0" style="padding: 0.5rem; font-size: 0.9rem;">
                                <strong>Motivo de rechazo:</strong> {{ $terreno->motivo_rechazo }}
                            </div>
                        @endif

                        @if($terreno->estado === 'aprobado')
                            @php
                                $badgeLote = 'secondary';
                                if($terreno->estado_lote === 'disponible') $badgeLote = 'success';
                                if($terreno->estado_lote === 'reservado') $badgeLote = 'warning';
                                if($terreno->estado_lote === 'vendido') $badgeLote = 'danger';
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

                        <div class="acciones">
                            <a href="{{ route('vendedor.terrenos.edit', $terreno->id) }}" class="btn btn-sm btn-secondary">Editar</a>
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