@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- ═══ Estado de Verificación ═══ -->
<div class="status-banner status-{{ $estado }}" id="statusBanner">
    <div class="status-banner-icon">
        @if ($estado === 'verificado')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                <polyline points="22,4 12,14.01 9,11.01" />
            </svg>
        @elseif ($estado === 'rechazado')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <line x1="15" y1="9" x2="9" y2="15" />
                <line x1="9" y1="9" x2="15" y2="15" />
            </svg>
        @else
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12,6 12,12 16,14" />
            </svg>
        @endif
    </div>
    <div class="status-banner-content">
        <h3>Estado de su verificación:
            <span class="badge badge-{{ $estado === 'verificado' ? 'success' : ($estado === 'rechazado' ? 'danger' : 'warning') }}">
                {{ $estado === 'verificado' ? '✅ Verificado' : ($estado === 'rechazado' ? '❌ Rechazado' : '⏳ Pendiente') }}
            </span>
        </h3>
        <p>
            @if ($estado === 'verificado')
                Su identidad ha sido verificada exitosamente. Tiene acceso completo a la plataforma.
            @elseif ($estado === 'rechazado')
                Su documento fue rechazado. Por favor, suba un nuevo documento que cumpla los requisitos.
            @else
                Su documento está pendiente de revisión por un administrador. Le notificaremos cuando sea procesado.
            @endif
        </p>
    </div>
</div>

<!-- ═══ Progress Steps ═══ -->
<div class="card" style="background: transparent; border: none; box-shadow: none; margin-bottom: 10px;">
    <div class="progress-steps">
        <div class="progress-line"
            style="width: {{ $estado === 'verificado' ? '100%' : ($documento ? '50%' : '0%') }}; background: {{ $estado === 'rechazado' ? 'var(--danger)' : 'var(--primary)' }};">
        </div>
        <div class="step completed">
            <div class="step-icon">1</div>
            <span class="step-label">Registro</span>
        </div>
        <div class="step {{ $documento ? 'completed' : 'active' }}">
            <div class="step-icon">2</div>
            <span class="step-label">Subir CI</span>
        </div>
        <div class="step {{ $estado === 'verificado' ? 'completed' : ($estado === 'rechazado' ? 'completed' : '') }}"
            style="{{ $estado === 'rechazado' ? 'color: var(--danger);' : '' }}">
            <div class="step-icon"
                style="{{ $estado === 'rechazado' ? 'border-color: var(--danger); background: var(--danger); color: white;' : '' }}">
                @if ($estado === 'rechazado') ❌ @elseif ($estado === 'verificado') ✅ @else 3 @endif
            </div>
            <span class="step-label">
                {{ $estado === 'verificado' ? 'Verificado' : ($estado === 'rechazado' ? 'Rechazado' : 'En Revisión') }}
            </span>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- ═══ Columna Izquierda: Subir Documento ═══ -->
    <div class="card" id="uploadCard">
        <div class="card-header">
            <h2 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                    <polyline points="17,8 12,3 7,8" />
                    <line x1="12" y1="3" x2="12" y2="15" />
                </svg>
                {{ $documento ? 'Reemplazar Documento CI' : 'Subir Documento CI' }}
            </h2>
        </div>
        <div class="card-body">
            <form action="{{ route('vendedor.subir_ci') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="dropzone" id="dropzone">
                    <div class="dropzone-content" id="dropzoneContent">
                        <div class="dropzone-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <p class="dropzone-text">Arrastre su documento aquí</p>
                        <p class="dropzone-subtext">o haga clic para seleccionar</p>
                        <p class="dropzone-hint">JPG, PNG o PDF · Máximo 5MB</p>
                    </div>
                    <div class="dropzone-preview" id="dropzonePreview" style="display:none;">
                        <img id="previewImage" src="" alt="Vista previa" style="display:none;">
                        <div id="previewPdf" style="display:none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="pdf-icon">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                <polyline points="14,2 14,8 20,8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <polyline points="10,9 9,9 8,9" />
                            </svg>
                            <span id="pdfFileName"></span>
                        </div>
                        <button type="button" class="preview-remove" id="previewRemove" title="Quitar archivo">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                    </div>
                    <input type="file" name="documento_ci" id="fileInput" accept=".jpg,.jpeg,.png,.pdf" style="display:none;">
                </div>
                <div class="file-info" id="fileInfo" style="display:none;">
                    <span id="fileInfoName"></span>
                    <span id="fileInfoSize"></span>
                </div>
                <button type="submit" class="btn btn-primary btn-block" id="uploadBtn" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                        <polyline points="17,8 12,3 7,8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                    </svg>
                    Subir Documento
                </button>
            </form>
        </div>
    </div>

    <!-- ═══ Columna Derecha: Documento Actual + Historial ═══ -->
    <div class="dashboard-right">
        @if ($documento)
            <div class="card" id="currentDocCard">
                <div class="card-header">
                    <h2 class="card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                            <polyline points="14,2 14,8 20,8" />
                        </svg>
                        Documento Actual
                    </h2>
                </div>
                <div class="card-body">
                    <div class="current-doc">
                        @if (str_starts_with($documento->tipo_mime, 'image/'))
                            <div class="current-doc-preview">
                                <img src="{{ route('vendedor.mi_ci') }}" alt="Documento CI actual"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="preview-placeholder" style="display:none;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <polyline points="21,15 16,10 5,21" />
                                    </svg>
                                    <span>No se puede cargar la vista previa</span>
                                </div>
                            </div>
                        @else
                            <div class="current-doc-pdf">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                    <polyline points="14,2 14,8 20,8" />
                                </svg>
                                <span>Documento PDF</span>
                            </div>
                        @endif
                        <div class="current-doc-info">
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
                                <span class="doc-info-value">
                                    {{ \Carbon\Carbon::parse($documento->fecha_subida)->timezone('America/La_Paz')->translatedFormat('d M Y, H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('vendedor.eliminar_ci') }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este documento?');" style="margin-top: 20px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;margin-right:8px;">
                                <polyline points="3,6 5,6 21,6" />
                                <path d="M19,6v14a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6" />
                                <line x1="10" y1="11" x2="10" y2="17" />
                                <line x1="14" y1="11" x2="14" y2="17" />
                            </svg>
                            Eliminar documento actual
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if ($historial && count($historial) > 0)
            <div class="card" id="historyCard">
                <div class="card-header">
                    <h2 class="card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12,6 12,12 16,14" />
                        </svg>
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
                                    <p class="timeline-admin">Por: {{ $h->admin->nombre ?? 'Admin' }}</p>
                                    @if ($h->comentario)
                                        <p class="timeline-comment">"{{ $h->comentario }}"</p>
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
/* Forzar badges negros en historial del vendedor */
body.theme-vendedor .badge-success,
body.theme-vendedor .badge-danger {
    color: #000000 !important;
    background-color: #f8f9fa !important;
    border: 1px solid #dee2e6;
}
body.theme-vendedor .timeline-content {
    background: #ffffff !important;
    color: #000000 !important;
}
body.theme-vendedor .timeline-admin,
body.theme-vendedor .timeline-comment {
    color: #000000 !important;
}
</style>
@endsection