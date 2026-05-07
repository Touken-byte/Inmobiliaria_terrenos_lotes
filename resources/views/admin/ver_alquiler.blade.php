@extends('layouts.app')

@section('title', 'Alquiler #' . $alquiler->id)

@section('content')

<div class="page-actions">
    <a href="{{ route('admin.alquileres_panel') }}" class="btn btn-secondary" id="backToAlquileres">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12,19 5,12 12,5"/>
        </svg>
        Volver a Alquileres
    </a>
</div>

<div class="ci-detail-grid">

    {{-- ═══ Columna Izquierda: Datos del Alquiler ═══ --}}
    <div class="ci-detail-left">
        <div class="card" id="alquilerInfoCard">
            <div class="card-header">
                <h2 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    Datos del Alquiler #{{ $alquiler->id }}
                </h2>
            </div>
            <div class="card-body">

                {{-- Info del Vendedor --}}
                <div class="vendor-profile" style="margin-bottom: 24px;">
                    <div class="vendor-avatar-lg">
                        {{ strtoupper(substr($alquiler->usuario->nombre ?? 'N', 0, 1)) }}
                    </div>
                    <div class="vendor-details">
                        <h3>{{ $alquiler->usuario->nombre ?? 'Sin vendedor' }}</h3>
                        <p class="vendor-email">{{ $alquiler->usuario->email ?? '' }}</p>
                        <div class="vendor-meta">
                            <span class="badge badge-{{ $alquiler->estado_aprobacion === 'aprobado' ? 'success' : ($alquiler->estado_aprobacion === 'rechazado' ? 'danger' : 'warning') }}">
                                {{ $alquiler->estado_aprobacion === 'aprobado' ? '✅ Aprobado' : ($alquiler->estado_aprobacion === 'rechazado' ? '❌ Rechazado' : '⏳ Pendiente') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Datos del Alquiler --}}
                <div class="current-doc-info" style="margin-bottom: 24px;">
                    <div class="doc-info-row">
                        <span class="doc-info-label">🔖 Título:</span>
                        <span class="doc-info-value" style="font-weight:700;">{{ $alquiler->titulo }}</span>
                    </div>
                    <div class="doc-info-row">
                        <span class="doc-info-label">💰 Precio:</span>
                        <span class="doc-info-value" style="font-size:1.2rem; font-weight:700; color:var(--info-color);">
                            ${{ number_format($alquiler->precio_mensual, 2) }} / mes
                        </span>
                    </div>
                    <div class="doc-info-row">
                        <span class="doc-info-label">📍 Ubicación:</span>
                        <span class="doc-info-value">{{ $alquiler->ubicacion }}</span>
                    </div>
                    <div class="doc-info-row">
                        <span class="doc-info-label">🛏️ Habitaciones:</span>
                        <span class="doc-info-value">{{ $alquiler->habitaciones }} Hab(s) | {{ $alquiler->banos }} Baño(s)</span>
                    </div>
                    @if($alquiler->metros_cuadrados)
                    <div class="doc-info-row">
                        <span class="doc-info-label">📐 Área:</span>
                        <span class="doc-info-value">{{ number_format($alquiler->metros_cuadrados, 2) }} m²</span>
                    </div>
                    @endif
                    <div class="doc-info-row">
                        <span class="doc-info-label">📅 Disponible desde:</span>
                        <span class="doc-info-value">
                            {{ \Carbon\Carbon::parse($alquiler->disponible_desde)->timezone('America/La_Paz')->translatedFormat('d \d\e F \d\e Y') }}
                        </span>
                    </div>
                </div>

                {{-- Servicios Incluidos --}}
                <div style="margin-bottom: 24px;">
                    <h4 style="margin-bottom: 8px; font-size: 0.95rem; color: var(--text-secondary);">🔌 Servicios Incluidos</h4>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        @if(is_array($alquiler->servicios_incluidos) && count($alquiler->servicios_incluidos) > 0)
                            @foreach($alquiler->servicios_incluidos as $servicio)
                                <span class="badge" style="background: var(--bg-light); color: var(--text-primary); border: 1px solid var(--border-color);">{{ $servicio }}</span>
                            @endforeach
                        @else
                            <span class="text-muted" style="font-size: 0.9em;">Ninguno especificado.</span>
                        @endif
                    </div>
                </div>

                {{-- Descripción --}}
                <div style="margin-bottom: 24px;">
                    <h4 style="margin-bottom: 8px; font-size: 0.95rem; color: var(--text-secondary);">📝 Descripción</h4>
                    <div style="padding: 16px; background: var(--bg-light); border-radius: var(--border-radius-sm); border: 1px solid rgba(0,0,0,0.05); line-height: 1.7; color: var(--text-primary); font-size: 0.95rem;">
                        {{ $alquiler->descripcion }}
                    </div>
                </div>

                {{-- Acciones de Aprobación --}}
                @if($alquiler->estado_aprobacion === 'pendiente')
                <div class="action-panel">
                    <h4>Acciones de Revisión</h4>
                    <div class="action-buttons-lg">

                        {{-- Aprobar --}}
                        <form action="{{ route('admin.procesar_alquiler') }}" method="POST"
                              style="display:inline;" id="approveAlquiler">
                            @csrf
                            <input type="hidden" name="alquiler_id" value="{{ $alquiler->id }}">
                            <input type="hidden" name="accion" value="aprobado">
                            <button type="submit" class="btn btn-success btn-lg"
                                    onclick="return confirm('¿Confirma la aprobación de este alquiler para publicación?')">
                                ✅ Aprobar Alquiler
                            </button>
                        </form>

                        {{-- Rechazar --}}
                        <form action="{{ route('admin.procesar_alquiler') }}" method="POST"
                              style="display:inline;" id="rejectAlquiler">
                            @csrf
                            <input type="hidden" name="alquiler_id" value="{{ $alquiler->id }}">
                            <input type="hidden" name="accion" value="rechazado">
                            <button type="submit" class="btn btn-danger btn-lg"
                                    onclick="return confirm('¿Confirma el RECHAZO de este alquiler?')">
                                ❌ Rechazar Alquiler
                            </button>
                        </form>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ═══ Columna Derecha: Galería de Imágenes ═══ --}}
    <div class="ci-detail-right">
        <div class="card" id="alquilerImagesCard">
            <div class="card-header">
                <h2 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21,15 16,10 5,21"/>
                    </svg>
                    Imágenes del Alquiler
                    <span class="badge badge-info">{{ $alquiler->imagenes->count() }} 📷</span>
                </h2>
            </div>
            <div class="card-body">

                @if($alquiler->imagenes->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                    @foreach($alquiler->imagenes as $img)

                    @php
                        $urlImagen = asset($img->ruta_archivo);
                    @endphp

                    <div style="border-radius: var(--border-radius-sm); overflow: hidden; border: 1px solid rgba(0,0,0,0.08); box-shadow: var(--shadow-sm); transition: var(--transition); cursor: pointer; position: relative;"
                         onclick="abrirImagen('{{ $urlImagen }}')"
                         onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='var(--shadow-lg)'"
                         onmouseout="this.style.transform=''; this.style.boxShadow='var(--shadow-sm)'">

                        <img src="{{ $urlImagen }}"
                             alt="Imagen alquiler {{ $img->orden }}"
                             style="width: 100%; height: 180px; object-fit: cover; display: block;"
                             onerror="this.style.display='none'; this.parentElement.innerHTML+='<div style=\'height:180px;display:flex;align-items:center;justify-content:center;background:var(--bg-light);color:var(--text-muted);font-size:0.85rem;\'>Imagen no disponible</div>';">

                        <div style="padding: 8px 12px; background: var(--bg-light); font-size: 0.8rem; color: var(--text-muted); text-align: center;">
                            Imagen {{ $img->orden }}
                        </div>
                    </div>

                    @endforeach
                </div>

                @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21,15 16,10 5,21"/>
                    </svg>
                    <p>Este alquiler no tiene imágenes cargadas.</p>
                </div>
                @endif

            </div>
        </div>
    </div>

</div>

{{-- Modal de imagen ampliada --}}
<div class="modal-overlay" id="modalImagen" style="display:none;">
    <div class="modal" style="max-width: 90vw; max-height: 90vh; background: #000;">
        <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
            <h3 style="color: white;">Vista Previa</h3>
            <button class="modal-close" type="button"
                    onclick="cerrarModal('modalImagen')"
                    style="color: white;">&times;</button>
        </div>
        <div style="display: flex; align-items: center; justify-content: center; padding: 16px;">
            <img id="modalImagenSrc" src="" alt="Imagen ampliada"
                 style="max-width: 100%; max-height: 75vh; object-fit: contain; border-radius: 8px;">
        </div>
    </div>
</div>

<script>
function abrirImagen(src) {
    document.getElementById('modalImagenSrc').src = src;
    document.getElementById('modalImagen').style.display = 'flex';
}
function cerrarModal(id) {
    document.getElementById(id).style.display = 'none';
}
</script>

@endsection
