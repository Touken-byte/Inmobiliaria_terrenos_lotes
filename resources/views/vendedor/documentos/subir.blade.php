@extends('layouts.app')

@section('title', 'Título de Propiedad')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Título de Propiedad</h1>
        <p class="page-subtitle">Gestione el título de propiedad del terreno</p>
    </div>
</div>

<div class="content-wrapper">
    <!-- Información del terreno -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="card-icon">
                    <path d="M3 21h18M9 8h1M9 12h1M9 16h1M14 8h1M14 12h1M14 16h1M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16" />
                </svg>
                Información del Terreno
            </h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Ubicación</span>
                    <span class="info-value">{{ $terreno->ubicacion }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Precio</span>
                    <span class="info-value">Bs. {{ number_format($terreno->precio, 2) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Metros Cuadrados</span>
                    <span class="info-value">{{ $terreno->metros_cuadrados }} m²</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Estado</span>
                    <span class="badge badge-{{ $terreno->estado === 'aprobado' ? 'success' : ($terreno->estado === 'rechazado' ? 'danger' : 'warning') }}">
                        {{ ucfirst($terreno->estado) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de documento de propiedad -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="card-icon">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14,2 14,8 20,8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10,9 9,9 8,9" />
                </svg>
                Título de Propiedad (PDF)
            </h3>
        </div>
        <div class="card-body">
            @if($documento)
                <!-- Documento existente -->
                <div class="documento-existente">
                    <div class="documento-info">
                        <div class="documento-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14,2 14,8 20,8" />
                            </svg>
                        </div>
                        <div class="documento-detalles">
                            <p class="documento-nombre">{{ $documento->nombre_original }}</p>
                            <p class="documento-meta">
                                Subido: {{ $documento->creado_en->format('d/m/Y H:i') }} • 
                                Tamaño: {{ number_format($documento->tamano / 1024, 1) }} KB
                            </p>
                            <p class="documento-estado">
                                Estado: 
                                <span class="badge badge-{{ $documento->estado === 'verificado' ? 'success' : ($documento->estado === 'observado' ? 'danger' : 'warning') }}">
                                    @if($documento->estado === 'verificado')
                                        ✓ Verificado
                                    @elseif($documento->estado === 'observado')
                                        ⚠ Observado
                                    @else
                                        ⏳ En verificación
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="documento-acciones">
                        <a href="{{ route('vendedor.documentos.ver', $documento->id) }}" 
                           class="btn btn-outline" 
                           target="_blank">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            Ver documento
                        </a>
                        @if(in_array($documento->estado, ['en_verificacion', 'observado']))
                            <form action="{{ route('vendedor.documentos.destroy', $terreno->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este documento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline btn-danger">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3,6 5,6 21,6" />
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        @endif
                    </div>

                    @if($documento->estado === 'observado')
                        <div class="alert alert-warning mt-3">
                            <div class="alert-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                    <line x1="12" y1="9" x2="12" y2="13" />
                                    <line x1="12" y1="17" x2="12.01" y2="17" />
                                </svg>
                            </div>
                            <div class="alert-content">
                                Su documento ha sido observado. Por favor, suba un nuevo título de propiedad que cumpla con los requisitos.
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <!-- Formulario para subir documento -->
                <div class="upload-section">
                    <div class="upload-info">
                        <h4>Subir Título de Propiedad</h4>
                        <p>El título de propiedad debe ser un archivo PDF que acredite la propiedad del terreno. 
                           El archivo será revisado por un administrador.</p>
                        <ul class="upload-requisitos">
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20,6 9,17 4,12" />
                                </svg>
                                Formato: PDF
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20,6 9,17 4,12" />
                                </svg>
                                Tamaño máximo: 10 MB
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20,6 9,17 4,12" />
                                </svg>
                                El documento debe ser legible
                            </li>
                        </ul>
                    </div>

                    <form action="{{ route('vendedor.documentos.store', $terreno->id) }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          class="upload-form">
                        @csrf
                        
                        <div class="form-group">
                            <label for="archivo" class="form-label">Seleccionar archivo PDF</label>
                            <div class="file-input-wrapper">
                                <input type="file" 
                                       id="archivo" 
                                       name="archivo" 
                                       accept=".pdf,application/pdf" 
                                       class="form-control file-input"
                                       required>
                                <span class="file-input-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="17,8 12,3 7,8" />
                                        <line x1="12" y1="3" x2="12" y2="15" />
                                    </svg>
                                    <span>Elegir archivo PDF</span>
                                </span>
                            </div>
                            <span class="form-hint" id="file-name"></span>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="17,8 12,3 7,8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                                Subir Título de Propiedad
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Botón volver -->
    <div class="form-actions mt-3">
        <a href="{{ route('vendedor.terrenos.mis') }}" class="btn btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12" />
                <polyline points="12,19 5,12 12,5" />
            </svg>
            Volver a Mis Terrenos
        </a>
    </div>
</div>

<style>
    .page-header {
        margin-bottom: 1.5rem;
    }

    .page-header-left {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
    }

    .page-subtitle {
        color: #6b7280;
        font-size: 0.875rem;
        margin: 0;
    }

    .content-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 500;
        color: #1a1a2e;
    }

    .documento-existente {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .documento-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 0.5rem;
    }

    .documento-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fee2e2;
        border-radius: 0.5rem;
        color: #dc2626;
    }

    .documento-icon svg {
        width: 24px;
        height: 24px;
    }

    .documento-detalles {
        flex: 1;
    }

    .documento-nombre {
        font-weight: 500;
        color: #1a1a2e;
        margin: 0 0 0.25rem 0;
        word-break: break-all;
    }

    .documento-meta {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0 0 0.5rem 0;
    }

    .documento-acciones {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .upload-section {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .upload-info h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0 0 0.5rem 0;
    }

    .upload-info p {
        color: #6b7280;
        font-size: 0.875rem;
        margin: 0 0 1rem 0;
        line-height: 1.5;
    }

    .upload-requisitos {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .upload-requisitos li {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #374151;
    }

    .upload-requisitos svg {
        width: 16px;
        height: 16px;
        color: #10b981;
    }

    .upload-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
    }

    .file-input-wrapper {
        position: relative;
    }

    .file-input {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-input-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 2rem;
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        background: #f9fafb;
        color: #6b7280;
        transition: all 0.2s;
    }

    .file-input-label:hover {
        border-color: #3b82f6;
        background: #eff6ff;
        color: #3b82f6;
    }

    .file-input-label svg {
        width: 24px;
        height: 24px;
    }

    .form-hint {
        font-size: 0.75rem;
        color: #9ca3af;
    }

    .form-actions {
        display: flex;
        gap: 0.75rem;
    }

    .d-inline {
        display: inline;
    }

    .mt-3 {
        margin-top: 1rem;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 9999px;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.5rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn svg {
        width: 16px;
        height: 16px;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .btn-outline {
        background: white;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-outline:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    .btn-danger {
        color: #dc2626;
        border-color: #fecaca;
    }

    .btn-danger:hover {
        background: #fef2f2;
    }

    .card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .card-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
    }

    .card-icon {
        width: 20px;
        height: 20px;
        color: #3b82f6;
    }

    .card-body {
        padding: 1.5rem;
    }

    .alert {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: 0.5rem;
    }

    .alert-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .alert-icon svg {
        width: 100%;
        height: 100%;
    }

    .alert-content {
        flex: 1;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

    .alert-close {
        background: none;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        opacity: 0.7;
        padding: 0;
        line-height: 1;
    }

    .alert-close:hover {
        opacity: 1;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('archivo');
        const fileNameSpan = document.getElementById('file-name');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const sizeInKB = (file.size / 1024).toFixed(1);
                    fileNameSpan.textContent = `Archivo seleccionado: ${file.name} (${sizeInKB} KB)`;
                } else {
                    fileNameSpan.textContent = '';
                }
            });
        }
    });
</script>
@endsection