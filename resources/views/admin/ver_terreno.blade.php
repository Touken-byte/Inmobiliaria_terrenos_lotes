@extends('layouts.app')

@section('title', 'Terreno #' . $terreno->id)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')

<div class="page-actions">
    <a href="{{ route('admin.terrenos_panel') }}" class="btn btn-secondary" id="backToTerrenos">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12,19 5,12 12,5"/>
        </svg>
        Volver a Terrenos
    </a>
</div>

<div class="ci-detail-grid">

    {{-- ═══ Columna Izquierda: Datos del Terreno ═══ --}}
    <div class="ci-detail-left">
        <div class="card" id="terrenoInfoCard">
            <div class="card-header">
                <h2 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                    </svg>
                    Datos del Terreno #{{ $terreno->id }}
                </h2>
            </div>
            <div class="card-body">

                {{-- Info del Vendedor --}}
                <div class="vendor-profile" style="margin-bottom: 24px;">
                    <div class="vendor-avatar-lg">
                        {{ strtoupper(substr($terreno->vendedor->nombre ?? 'N', 0, 1)) }}
                    </div>
                    <div class="vendor-details">
                        <h3>{{ $terreno->vendedor->nombre ?? 'Sin vendedor' }}</h3>
                        <p class="vendor-email">{{ $terreno->vendedor->email ?? '' }}</p>
                        <div class="vendor-meta">
                            <span class="badge badge-{{ $terreno->estado === 'aprobado' ? 'success' : ($terreno->estado === 'rechazado' ? 'danger' : 'warning') }}">
                                {{ $terreno->estado === 'aprobado' ? '✅ Aprobado' : ($terreno->estado === 'rechazado' ? '❌ Rechazado' : '⏳ Pendiente') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Datos del terreno --}}
                <div class="current-doc-info" style="margin-bottom: 24px;">
                    <div class="doc-info-row">
                        <span class="doc-info-label">💰 Precio:</span>
                        <span class="doc-info-value" style="font-size:1.2rem; font-weight:700; color:var(--primary);">
                            ${{ number_format($terreno->precio, 2) }} USD
                        </span>
                    </div>
                    <div class="doc-info-row">
                        <span class="doc-info-label">📐 Metros Cuadrados:</span>
                        <span class="doc-info-value">{{ number_format($terreno->metros_cuadrados, 2) }} m²</span>
                    </div>
                    <div class="doc-info-row">
                        <span class="doc-info-label">📍 Ubicación:</span>
                        <span class="doc-info-value">{{ $terreno->ubicacion }}</span>
                    </div>
                    @if($terreno->latitud && $terreno->longitud)
                    <div class="doc-info-row">
                        <span class="doc-info-label">🌐 Coordenadas:</span>
                        <span class="doc-info-value" style="font-size:0.85rem; color:var(--text-muted);">
                            {{ $terreno->latitud }}, {{ $terreno->longitud }}
                        </span>
                    </div>
                    @endif
                    <div class="doc-info-row">
                        <span class="doc-info-label">📅 Publicado:</span>
                        <span class="doc-info-value">
                            {{ \Carbon\Carbon::parse($terreno->creado_en)->timezone('America/La_Paz')->translatedFormat('d \d\e F \d\e Y, H:i') }}
                        </span>
                    </div>
                    @if($terreno->adminAprobador)
                    <div class="doc-info-row">
                        <span class="doc-info-label">👨‍💼 Revisado por:</span>
                        <span class="doc-info-value">{{ $terreno->adminAprobador->nombre }}</span>
                    </div>
                    @endif
                </div>

                {{-- Descripción --}}
                <div style="margin-bottom: 24px;">
                    <h4 style="margin-bottom: 8px; font-size: 0.95rem; color: var(--text-secondary);">📝 Descripción</h4>
                    <div style="padding: 16px; background: var(--bg-light); border-radius: var(--border-radius-sm); border: 1px solid rgba(0,0,0,0.05); line-height: 1.7; color: var(--text-primary); font-size: 0.95rem;">
                        {{ $terreno->descripcion }}
                    </div>
                    <small style="color: var(--text-muted);">{{ Str::length($terreno->descripcion) }} caracteres</small>
                </div>

                {{-- Mapa de ubicación --}}
                @if($terreno->latitud && $terreno->longitud)
                <div style="margin-bottom: 24px;">
                    <h4 style="margin-bottom: 8px; font-size: 0.95rem; color: var(--text-secondary);">📍 Ubicación en el Mapa</h4>
                    <div id="mapaAdmin" style="height: 300px; border-radius: 10px; border: 2px solid var(--border-color);"></div>
                </div>
                @else
                <div style="margin-bottom: 24px; padding: 12px 16px; background: var(--bg-light); border-radius: 8px; border: 1px solid var(--border-color);">
                    <p style="margin:0; color: var(--text-muted); font-size:0.9rem;">⚠️ Este terreno no tiene coordenadas registradas. El vendedor no marcó la ubicación en el mapa.</p>
                </div>
                @endif

                {{-- Acciones de Aprobación --}}
                @if($terreno->estado === 'pendiente')
                <div class="action-panel">
                    <h4>Acciones de Revisión</h4>
                    <div class="action-buttons-lg">

                        {{-- Aprobar --}}
                        <form action="{{ route('admin.procesar_terreno') }}" method="POST"
                              style="display:inline;" id="approveTerreno">
                            @csrf
                            <input type="hidden" name="terreno_id" value="{{ $terreno->id }}">
                            <input type="hidden" name="accion" value="aprobado">
                            <input type="hidden" name="observacion" value="Terreno aprobado para publicación.">
                            <button type="submit" class="btn btn-success btn-lg"
                                    onclick="return confirm('¿Confirma la aprobación de este terreno para publicación?')">
                                ✅ Aprobar Terreno
                            </button>
                        </form>

                        {{-- Rechazar --}}
                        <button type="button" class="btn btn-danger btn-lg"
                                id="showRejectTerrenoBtn"
                                onclick="document.getElementById('rejectTerrenoSection').style.display='block'; this.style.display='none';">
                            ❌ Rechazar Terreno
                        </button>
                    </div>

                    <div id="rejectTerrenoSection" style="display:none; margin-top: 16px;">
                        <form action="{{ route('admin.procesar_terreno') }}" method="POST" id="rejectTerrenoForm">
                            @csrf
                            <input type="hidden" name="terreno_id" value="{{ $terreno->id }}">
                            <input type="hidden" name="accion" value="rechazado">
                            <div class="form-group">
                                <label for="rejectTerrenoComment" class="form-label">
                                    Motivo del rechazo <span class="required">*</span>
                                </label>
                                <textarea name="observacion" id="rejectTerrenoComment"
                                          class="form-control" rows="3"
                                          placeholder="Explique el motivo del rechazo del terreno..."
                                          required minlength="10"></textarea>
                                <small class="form-hint">Esta observación quedará registrada en el sistema.</small>
                            </div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-secondary"
                                        onclick="document.getElementById('rejectTerrenoSection').style.display='none';
                                                 document.getElementById('showRejectTerrenoBtn').style.display='inline-flex';">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-danger">❌ Confirmar Rechazo</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ═══ Columna Derecha: Galería de Imágenes ═══ --}}
    <div class="ci-detail-right">
        <div class="card" id="terrenoImagesCard">
            <div class="card-header">
                <h2 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21,15 16,10 5,21"/>
                    </svg>
                    Imágenes del Terreno
                    <span class="badge badge-info">{{ $terreno->imagenes->count() }} 📷</span>
                </h2>
            </div>
            <div class="card-body">

                @if($terreno->imagenes->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                    @foreach($terreno->imagenes as $img)

                    @php
                        $urlImagen = Storage::url(str_replace('/storage/', '', $img->ruta_archivo));
                    @endphp

                    <div style="border-radius: var(--border-radius-sm); overflow: hidden; border: 1px solid rgba(0,0,0,0.08); box-shadow: var(--shadow-sm); transition: var(--transition); cursor: pointer; position: relative;"
                         onclick="abrirImagen('{{ $urlImagen }}')"
                         onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='var(--shadow-lg)'"
                         onmouseout="this.style.transform=''; this.style.boxShadow='var(--shadow-sm)'">

                        <img src="{{ $urlImagen }}"
                             alt="Imagen terreno {{ $img->orden }}"
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
                    <p>Este terreno no tiene imágenes cargadas.</p>
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

@if($terreno->latitud && $terreno->longitud)
document.addEventListener('DOMContentLoaded', function() {
    var lat = {{ $terreno->latitud }};
    var lng = {{ $terreno->longitud }};

    var mapaAdmin = L.map('mapaAdmin', { zoomControl: true, dragging: true }).setView([lat, lng], 15);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OSM &copy; CartoDB',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(mapaAdmin);

    L.marker([lat, lng])
        .addTo(mapaAdmin)
        .bindPopup('<strong>{{ addslashes($terreno->ubicacion) }}</strong><br>Terreno #{{ $terreno->id }}')
        .openPopup();
});
@endif
</script>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@endsection
