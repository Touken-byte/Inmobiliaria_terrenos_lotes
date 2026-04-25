@extends('layouts.app')

@section('title', 'CI - ' . $vendedor->nombre)

@section('content')
<div class="page-actions">
    <a href="{{ url('/admin/panel') }}" class="btn btn-secondary" id="backToPanel">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12,19 5,12 12,5"/></svg>
        Volver al Panel
    </a>
</div>

<div class="ci-detail-grid">
    <!-- ═══ Columna Izquierda: Datos del Vendedor + Documento ═══ -->
    <div class="ci-detail-left">
        <!-- Info del vendedor -->
        <div class="card" id="vendorInfoCard">
            <div class="card-header">
                <h2 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="12" r="4"/></svg>
                    Datos del Vendedor
                </h2>
            </div>
            <div class="card-body">
                <div class="vendor-profile">
                    <div class="vendor-avatar-lg">
                        {{ strtoupper(substr($vendedor->nombre, 0, 1)) }}
                    </div>
                    <div class="vendor-details">
                        <h3>{{ $vendedor->nombre }}</h3>
                        <p class="vendor-email">{{ $vendedor->email }}</p>
                        @if ($vendedor->telefono)
                            <p class="vendor-phone">{{ $vendedor->telefono }}</p>
                        @endif
                        <div class="vendor-meta">
                            <span class="badge badge-{{ $vendedor->estado_verificacion === 'verificado' ? 'success' : ($vendedor->estado_verificacion === 'rechazado' ? 'danger' : 'warning') }}">
                                {{ $vendedor->estado_verificacion === 'verificado' ? '✅ Verificado' : ($vendedor->estado_verificacion === 'rechazado' ? '❌ Rechazado' : '⏳ Pendiente') }}
                            </span>
                            <span class="vendor-date">
                                Registrado: {{ \Carbon\Carbon::parse($vendedor->fecha_registro)->timezone('America/La_Paz')->translatedFormat('d \d\e F \d\e Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                @if ($documento)
                <div class="action-panel">
                    <h4>Acciones de Verificación</h4>
                    <div class="action-buttons-lg">
                        <form action="{{ route('admin.procesar_verificacion') }}" method="POST" style="display:inline;" id="quickApproveForm">
                            @csrf
                            <input type="hidden" name="usuario_id" value="{{ $vendedor->id }}">
                            <input type="hidden" name="accion" value="aprobado">
                            <input type="hidden" name="comentario" value="Documento verificado correctamente.">
                            <button type="submit" class="btn btn-success btn-lg" id="quickApproveBtn" onclick="return confirm('¿Confirma la aprobación de este vendedor?')">
                                ✅ Aprobar Vendedor
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger btn-lg" onclick="document.getElementById('rejectSection').style.display='block'; this.style.display='none';" id="showRejectBtn">
                            ❌ Rechazar Documento
                        </button>
                    </div>

                    <div id="rejectSection" style="display:none;">
                        <form action="{{ route('admin.procesar_verificacion') }}" method="POST" id="rejectForm">
                            @csrf
                            <input type="hidden" name="usuario_id" value="{{ $vendedor->id }}">
                            <input type="hidden" name="accion" value="rechazado">
                            <div class="form-group">
                                <label for="rejectComment" class="form-label">Motivo del rechazo <span class="required">*</span></label>
                                <textarea name="comentario" id="rejectComment" class="form-control" rows="3" placeholder="Explique el motivo del rechazo..." required minlength="10"></textarea>
                                <small class="form-hint">Este comentario será enviado al vendedor por email.</small>
                            </div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('rejectSection').style.display='none'; document.getElementById('showRejectBtn').style.display='inline-flex';">Cancelar</button>
                                <button type="submit" class="btn btn-danger">❌ Confirmar Rechazo</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ═══ Columna Derecha: Vista Previa del CI ═══ -->
    <div class="ci-detail-right">
        <div class="card" id="ciPreviewCard">
            <div class="card-header">
                <h2 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                    Documento CI
                </h2>
            </div>
            <div class="card-body">
                @if ($documento)
                <div class="ci-preview-container">
                    @if (str_starts_with($documento->tipo_mime, 'image/'))
                        <div class="ci-image-viewer">
                            <img src="{{ url('/admin/servir-ci/' . $documento->id) }}" alt="Documento CI de {{ $vendedor->nombre }}" class="ci-full-image" id="ciImage">
                        </div>
                    @else
                        <div class="ci-pdf-viewer">
                            <iframe src="{{ url('/admin/servir-ci/' . $documento->id) }}" width="100%" height="600px" title="Documento CI PDF"></iframe>
                        </div>
                    @endif
                    <div class="ci-doc-meta">
                        <div class="doc-info-row">
                            <span class="doc-info-label">Archivo:</span>
                            <span class="doc-info-value">{{ $documento->nombre_original }}</span>
                        </div>
                        <div class="doc-info-row">
                            <span class="doc-info-label">Tipo:</span>
                            <span class="doc-info-value">{{ $documento->tipo_mime }}</span>
                        </div>
                        <div class="doc-info-row">
                            <span class="doc-info-label">Tamaño:</span>
                            <span class="doc-info-value">{{ $documento->tamano_formateado }}</span>
                        </div>
                        <div class="doc-info-row">
                            <span class="doc-info-label">Subido:</span>
                            <span class="doc-info-value">{{ \Carbon\Carbon::parse($documento->fecha_subida)->timezone('America/La_Paz')->translatedFormat('d \d\e F \d\e Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
                @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    <p>Este vendedor aún no ha subido un documento de identidad.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Historial -->
        @if ($historial && count($historial) > 0)
        <div class="card" id="historyDetailCard">
            <div class="card-header">
                <h2 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                    Historial de Revisiones
                </h2>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach ($historial as $h)
                    <div class="timeline-item timeline-{{ $h->accion }}">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="badge badge-{{ $h->accion === 'aprobado' ? 'success' : 'danger' }}">
                                    {{ $h->accion === 'aprobado' ? '✅ Aprobado' : '❌ Rechazado' }}
                                </span>
                                <span class="timeline-date">
                                    {{ \Carbon\Carbon::parse($h->fecha)->timezone('America/La_Paz')->translatedFormat('d M Y, H:i') }}
                                </span>
                            </div>
                            <p class="timeline-admin">Revisado por: <strong>{{ $h->admin->nombre ?? 'Admin' }}</strong></p>
                            @if ($h->comentario)
                            <div class="timeline-comment">"{{ $h->comentario }}"</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Forzar texto negro en el panel de admin para CI */
body.theme-admin .ci-doc-meta,
body.theme-admin .ci-doc-meta .doc-info-row,
body.theme-admin .ci-doc-meta .doc-info-label,
body.theme-admin .ci-doc-meta .doc-info-value {
    color: #000000 !important;
    background: #ffffff !important;
}
body.theme-admin .timeline-content,
body.theme-admin .timeline-admin,
body.theme-admin .timeline-comment,
body.theme-admin .timeline-date {
    color: #000000 !important;
    background: #ffffff !important;
}
body.theme-admin .badge-success,
body.theme-admin .badge-danger {
    color: #000000 !important;
    background-color: #e9ecef !important;
}
</style>
@endsection