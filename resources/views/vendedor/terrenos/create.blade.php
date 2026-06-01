@extends('layouts.app')

@section('title', 'Publicar Terreno o Lote')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .form-card {
        max-width: 800px;
        margin: 0 auto;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-row {
        display: flex;
        gap: 1.5rem;
    }
    .form-row .form-group {
        flex: 1;
    }
    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        font-size: 0.95rem;
        color: #000 !important;
    }
    .required {
        color: #dc3545;
    }
    .char-counter {
        font-size: 0.8rem;
        color: #6c757d;
        text-align: right;
        margin-top: 0.25rem;
        font-family: monospace;
    }
    .images-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .preview-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        height: 130px;
        border: 1px solid #dee2e6;
        background: #f8f9fa;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.9;
        transition: 0.3s;
    }
    .preview-item:hover img {
        opacity: 1;
        transform: scale(1.05);
    }
    .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.7);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        width: 26px;
        height: 26px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        backdrop-filter: blur(4px);
        transition: 0.2s;
    }
    .remove-btn:hover {
        background: #dc3545;
        border-color: #dc3545;
        transform: scale(1.1);
    }
    .multi-dropzone {
        cursor: pointer;
        padding: 2.5rem;
        border: 2px dashed #ced4da;
        text-align: center;
        border-radius: 12px;
        transition: 0.3s;
        background: #f8f9fa;
    }
    .multi-dropzone:hover {
        border-color: #007bff;
        background: #e9ecef;
        box-shadow: 0 0 15px rgba(0,123,255,0.1);
    }
    .multi-dropzone svg {
        width: 48px;
        height: 48px;
        margin-bottom: 1rem;
        opacity: 0.5;
        color: #007bff;
        transition: 0.3s;
    }
    .multi-dropzone:hover svg {
        opacity: 1;
        transform: translateY(-5px);
    }
    .submit-btn {
        width: 100%;
        padding: 1rem;
        font-size: 1.1rem;
        border-radius: 8px;
        margin-top: 1rem;
        background: #007bff;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .submit-btn:hover {
        background: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,123,255,0.3);
    }
    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 1px solid #ced4da;
        background: #fff;
        color: #000 !important;
        font-size: 1rem;
        transition: all 0.3s;
        box-sizing: border-box;
    }
    .form-control:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.2);
        background: #fff;
    }
    .form-control::placeholder {
        color: #6c757d;
    }
    input[type="number"] {
        color: #000 !important;
    }
    .dropzone-content p {
        color: #555 !important;
    }
    .portada-selector {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 16px;
        margin-top: 16px;
    }
    .portada-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 12px;
        font-size: 0.95rem;
    }
    .portada-options {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .portada-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.9rem;
    }
    .portada-option:hover {
        border-color: #007bff;
        background: #e9ecef;
    }
    .portada-option.selected {
        border-color: #007bff;
        background: #e3f2fd;
        box-shadow: 0 2px 8px rgba(0,123,255,0.2);
    }
    .portada-option input[type="radio"] {
        margin: 0;
    }
    .portada-option img {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        object-fit: cover;
        border: 1px solid #dee2e6;
    }
    @keyframes spin {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .section-divider {
        margin: 1.5rem 0 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #007bff;
    }
    .section-divider h4 {
        margin: 0;
        font-size: 1.05rem;
        color: #333;
    }
    /* ══ SERVICIOS DISPONIBLES — Diseño Premium ══ */
    .services-section-wrapper {
        background: linear-gradient(135deg, #f0f4ff 0%, #fef9f0 50%, #f0fff4 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 1rem;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 0.5rem;
    }
    .service-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border: 2px solid #e8ecf1;
        border-radius: 16px;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
        overflow: hidden;
    }
    .service-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 14px;
        opacity: 0;
        transition: opacity 0.35s ease;
        z-index: 0;
    }
    .service-card:hover {
        border-color: #c0c8d4;
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    .service-card.active {
        box-shadow: 0 8px 28px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    /* Colores por servicio */
    .service-card[data-service="agua"].active {
        border-color: #3b82f6;
        background: linear-gradient(135deg, rgba(59,130,246,0.08), rgba(59,130,246,0.03));
    }
    .service-card[data-service="energia"].active {
        border-color: #f59e0b;
        background: linear-gradient(135deg, rgba(245,158,11,0.08), rgba(245,158,11,0.03));
    }
    .service-card[data-service="alcantarillado"].active {
        border-color: #8b5cf6;
        background: linear-gradient(135deg, rgba(139,92,246,0.08), rgba(139,92,246,0.03));
    }
    .service-card[data-service="gas"].active {
        border-color: #ef4444;
        background: linear-gradient(135deg, rgba(239,68,68,0.08), rgba(239,68,68,0.03));
    }
    .service-card[data-service="internet"].active {
        border-color: #10b981;
        background: linear-gradient(135deg, rgba(16,185,129,0.08), rgba(16,185,129,0.03));
    }
    .service-card input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    /* Icon bubble */
    .service-icon-wrap {
        position: relative;
        z-index: 1;
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: all 0.35s ease;
    }
    .service-card[data-service="agua"] .service-icon-wrap {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    }
    .service-card[data-service="energia"] .service-icon-wrap {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
    }
    .service-card[data-service="alcantarillado"] .service-icon-wrap {
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
    }
    .service-card[data-service="gas"] .service-icon-wrap {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
    }
    .service-card[data-service="internet"] .service-icon-wrap {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    }
    .service-card.active .service-icon-wrap {
        transform: scale(1.1);
        box-shadow: 0 4px 14px rgba(0,0,0,0.1);
    }
    /* Text area */
    .service-info {
        position: relative;
        z-index: 1;
        flex: 1;
        min-width: 0;
    }
    .service-name {
        font-size: 0.92rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.3;
        margin-bottom: 2px;
        transition: color 0.3s;
    }
    .service-desc {
        font-size: 0.78rem;
        color: #94a3b8;
        line-height: 1.3;
        transition: color 0.3s;
    }
    .service-card.active .service-desc {
        color: #64748b;
    }
    /* Toggle indicator */
    .service-toggle {
        position: relative;
        z-index: 1;
        width: 42px;
        min-width: 42px;
        height: 24px;
        border-radius: 12px;
        background: #cbd5e1;
        transition: background 0.35s ease;
        flex-shrink: 0;
    }
    .service-toggle::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.15);
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .service-card.active .service-toggle {
        background: #22c55e;
    }
    .service-card[data-service="agua"].active .service-toggle { background: #3b82f6; }
    .service-card[data-service="energia"].active .service-toggle { background: #f59e0b; }
    .service-card[data-service="alcantarillado"].active .service-toggle { background: #8b5cf6; }
    .service-card[data-service="gas"].active .service-toggle { background: #ef4444; }
    .service-card[data-service="internet"].active .service-toggle { background: #10b981; }
    .service-card.active .service-toggle::after {
        transform: translateX(18px);
    }
    /* Badge check */
    .service-check {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2;
        animation: popIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .service-card[data-service="agua"] .service-check { background: #3b82f6; box-shadow: 0 2px 8px rgba(59,130,246,0.4); }
    .service-card[data-service="energia"] .service-check { background: #f59e0b; box-shadow: 0 2px 8px rgba(245,158,11,0.4); }
    .service-card[data-service="alcantarillado"] .service-check { background: #8b5cf6; box-shadow: 0 2px 8px rgba(139,92,246,0.4); }
    .service-card[data-service="gas"] .service-check { background: #ef4444; box-shadow: 0 2px 8px rgba(239,68,68,0.4); }
    .service-card[data-service="internet"] .service-check { background: #10b981; box-shadow: 0 2px 8px rgba(16,185,129,0.4); }
    .service-card.active .service-check {
        display: flex;
    }
    @keyframes popIn {
        0%   { transform: scale(0); }
        100% { transform: scale(1); }
    }
    /* Services counter */
    .services-counter {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 14px;
        padding: 10px 16px;
        background: rgba(255,255,255,0.7);
        border-radius: 10px;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .services-counter-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1;
        transition: all 0.3s ease;
    }
    .services-counter-dot.active {
        background: #22c55e;
        box-shadow: 0 0 8px rgba(34,197,94,0.4);
    }
    .services-counter-text {
        font-size: 0.82rem;
        color: #64748b;
        font-weight: 500;
    }
    .services-counter-text strong {
        color: #1e293b;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="card form-card">
    <div class="card-header">
        <h2 class="card-title" id="formTitle">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Publicar Nueva Propiedad
        </h2>
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('vendedor.terrenos.store') }}" method="POST"
              enctype="multipart/form-data" id="terrenoForm">
            @csrf

            {{-- ══ SELECTOR DE TIPO (Terreno / Lote) ══ --}}
            <div class="form-group" style="margin-bottom:2rem;">
                <label style="font-weight:700; color:#000; font-size:1rem; margin-bottom:0.75rem; display:block;">
                    Tipo de Publicación <span class="required">*</span>
                </label>
                <div style="display:flex; gap:1rem;">
                    <label id="tipoTerrenoBtn" style="
                        flex:1; display:flex; align-items:center; justify-content:center;
                        gap:0.6rem; padding:1rem; border:2px solid #007bff;
                        border-radius:10px; background:#e3f2fd; cursor:pointer;
                        font-weight:600; font-size:0.95rem; transition:all 0.2s;
                    ">
                        <input type="radio" name="tipo" id="tipoTerreno" value="terreno"
                               style="display:none;" {{ old('tipo', request('tipo', 'terreno')) === 'terreno' ? 'checked' : '' }}>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:22px;height:22px;color:#007bff;">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        <span style="color:#007bff;">&#127981; Terreno</span>
                    </label>
                    <label id="tipoLoteBtn" style="
                        flex:1; display:flex; align-items:center; justify-content:center;
                        gap:0.6rem; padding:1rem; border:2px solid #ced4da;
                        border-radius:10px; background:#fff; cursor:pointer;
                        font-weight:600; font-size:0.95rem; transition:all 0.2s;
                    ">
                        <input type="radio" name="tipo" id="tipoLote" value="lote"
                               style="display:none;" {{ old('tipo', request('tipo')) === 'lote' ? 'checked' : '' }}>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:22px;height:22px;color:#6c757d;" id="iconLote">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                        </svg>
                        <span id="labelLote" style="color:#6c757d;">&#127800; Lote</span>
                    </label>
                </div>
            </div>

            <!-- SECCIONES ESPECIFICAS DE LOTE (solo visible si tipo=lote) -->
            <div id="loteFields" style="display:{{ old('tipo', request('tipo')) === 'lote' ? 'block' : 'none' }};">
                <div class="section-divider">
                    <h4>🏠 Relación con el Terreno</h4>
                </div>
                <div class="form-group" id="parentGroup">
                    <label for="parent_id">¿A qué terreno pertenece este lote? <span class="required">*</span>
                        <small>(Seleccione el terreno padre)</small>
                    </label>
                    @if($terrenosPadre->isEmpty())
                        <div style="padding:0.75rem 1rem; background:#fff3cd; border:1px solid #ffc107;
                                    border-radius:8px; color:#856404; font-size:0.9rem;">
                            ⚠️ No tienes terrenos aprobados para asociar lotes.
                            Primero crea y aprueba un Terreno.
                        </div>
                    @else
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="">Seleccione el terreno padre...</option>
                            @foreach($terrenosPadre as $tp)
                                <option value="{{ $tp->id }}" {{ old('parent_id') == $tp->id ? 'selected' : '' }}>
                                    {{ $tp->nombre ?? $tp->ubicacion }} ({{ number_format($tp->metros_cuadrados,0) }} m²)
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="numero_lote">Número de Lote</label>
                        <input type="text" name="numero_lote" id="numero_lote" class="form-control"
                               placeholder="Ej: 12" value="{{ old('numero_lote') }}">
                    </div>
                    <div class="form-group">
                        <label for="codigo_lote">Código del Lote</label>
                        <input type="text" name="codigo_lote" id="codigo_lote" class="form-control"
                               placeholder="Ej: LOT-012" value="{{ old('codigo_lote') }}">
                    </div>
                    <div class="form-group">
                        <label for="manzano_bloque">Manzano/Bloque</label>
                        <input type="text" name="manzano_bloque" id="manzano_bloque" class="form-control"
                               placeholder="Ej: B" value="{{ old('manzano_bloque') }}">
                    </div>
                </div>

                <div class="section-divider">
                    <h4>📐 Dimensiones del Lote</h4>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="frente">Frente (metros)</label>
                        <input type="number" name="frente" id="frente" class="form-control"
                               placeholder="Ej: 10" min="0" step="0.01" value="{{ old('frente') }}">
                    </div>
                    <div class="form-group">
                        <label for="fondo">Fondo (metros)</label>
                        <input type="number" name="fondo" id="fondo" class="form-control"
                               placeholder="Ej: 30" min="0" step="0.01" value="{{ old('fondo') }}">
                    </div>
                </div>

                <div class="section-divider">
                    <h4>🗺️ Colindancias</h4>
                </div>
                <div class="form-row" style="flex-wrap:wrap;">
                    <div class="form-group" style="flex:1;min-width:200px;">
                        <label for="colinda_norte">Colinda al Norte con</label>
                        <input type="text" name="colinda_norte" id="colinda_norte" class="form-control"
                               placeholder="Ej: Calle Los Andes" value="{{ old('colinda_norte') }}">
                    </div>
                    <div class="form-group" style="flex:1;min-width:200px;">
                        <label for="colinda_sur">Colinda al Sur con</label>
                        <input type="text" name="colinda_sur" id="colinda_sur" class="form-control"
                               placeholder="Ej: Propiedad Privada" value="{{ old('colinda_sur') }}">
                    </div>
                </div>
                <div class="form-row" style="flex-wrap:wrap;">
                    <div class="form-group" style="flex:1;min-width:200px;">
                        <label for="colinda_este">Colinda al Este con</label>
                        <input type="text" name="colinda_este" id="colinda_este" class="form-control"
                               placeholder="Ej: Av. Principal" value="{{ old('colinda_este') }}">
                    </div>
                    <div class="form-group" style="flex:1;min-width:200px;">
                        <label for="colinda_oeste">Colinda al Oeste con</label>
                        <input type="text" name="colinda_oeste" id="colinda_oeste" class="form-control"
                               placeholder="Ej: Calle Secundaria" value="{{ old('colinda_oeste') }}">
                    </div>
                </div>
            </div>

            <!-- SECCIONES ESPECIFICAS DE TERRENO (ocultas si tipo=lote) -->
            <div id="terrenoFields" style="display:{{ old('tipo', request('tipo', 'terreno')) !== 'lote' ? 'block' : 'none' }};">
                <div class="section-divider">
                    <h4>🏞️ Información General del Terreno</h4>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo_terreno">Tipo de Terreno</label>
                        <select name="tipo_terreno" id="tipo_terreno" class="form-control">
                            <option value="">Seleccione tipo...</option>
                            @foreach($tiposTerreno as $tt)
                                <option value="{{ $tt }}" {{ old('tipo_terreno') == $tt ? 'selected' : '' }}>
                                    {{ ucfirst($tt) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="topografia">Topografía</label>
                        <select name="topografia" id="topografia" class="form-control">
                            <option value="">Seleccione topografía...</option>
                            @foreach($topografias as $top)
                                <option value="{{ $top }}" {{ old('topografia') == $top ? 'selected' : '' }}>
                                    {{ ucfirst($top) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="largo">Largo (metros)</label>
                        <input type="number" name="largo" id="largo" class="form-control"
                               placeholder="Ej: 100" min="0" step="0.01" value="{{ old('largo') }}">
                    </div>
                    <div class="form-group">
                        <label for="ancho">Ancho (metros)</label>
                        <input type="number" name="ancho" id="ancho" class="form-control"
                               placeholder="Ej: 100" min="0" step="0.01" value="{{ old('ancho') }}">
                    </div>
                </div>

                <div class="section-divider">
                    <h4>📋 Información Legal</h4>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="numero_matricula">Número de Matrícula</label>
                        <input type="text" name="numero_matricula" id="numero_matricula" class="form-control"
                               placeholder="Ej: 201201201201" value="{{ old('numero_matricula') }}">
                    </div>
                    <div class="form-group">
                        <label for="codigo_catastral">Código Catastral</label>
                        <input type="text" name="codigo_catastral" id="codigo_catastral" class="form-control"
                               placeholder="Ej: 0123456" value="{{ old('codigo_catastral') }}">
                    </div>
                </div>
            </div>

            <!-- CAMPOS COMUNES (ambos tipos) -->
            <div class="section-divider">
                <h4>📝 Información General</h4>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre <span class="required">*</span></label>
                <input type="text" name="nombre" id="nombre" class="form-control"
                       placeholder="{{ old('tipo', request('tipo', 'terreno')) === 'lote' ? 'Ej: Lote 12 - Urbanización Las Palmeras' : 'Ej: Urbanización Las Palmeras' }}"
                       value="{{ old('nombre') }}" required>
            </div>

            <div class="section-divider">
                <h4>📍 Ubicación</h4>
            </div>
            <div class="form-row" style="flex-wrap:wrap;">
                <div class="form-group" style="flex:1;min-width:150px;">
                    <label for="pais">País</label>
                    <select name="pais" id="pais" class="form-control">
                        <option value="">Seleccione...</option>
                        @foreach($paises as $p)
                            <option value="{{ $p }}" {{ old('pais') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex:1;min-width:150px;">
                    <label for="departamento">Departamento</label>
                    <select name="departamento" id="departamento" class="form-control">
                        <option value="">Seleccione...</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep }}" {{ old('departamento') == $dep ? 'selected' : '' }}>{{ $dep }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row" style="flex-wrap:wrap;">
                <div class="form-group" style="flex:1;min-width:150px;">
                    <label for="provincia">Provincia</label>
                    <input type="text" name="provincia" id="provincia" class="form-control"
                           placeholder="Ej: Gran Chaco" value="{{ old('provincia') }}">
                </div>
                <div class="form-group" style="flex:1;min-width:150px;">
                    <label for="municipio">Municipio</label>
                    <input type="text" name="municipio" id="municipio" class="form-control"
                           placeholder="Ej: Yacuiba" value="{{ old('municipio') }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="zona_barrio">Zona o Barrio</label>
                    <input type="text" name="zona_barrio" id="zona_barrio" class="form-control"
                           placeholder="Ej: Zona Central" value="{{ old('zona_barrio') }}">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-control"
                           placeholder="Ej: Av. Principal #123" value="{{ old('direccion') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="ubicacion">Ubicación (resumen) <span class="required">*</span></label>
                <input type="text" name="ubicacion" id="ubicacion" class="form-control"
                       placeholder="Ej: Zona Norte, Yacuiba"
                       value="{{ old('ubicacion') }}" required>
            </div>

            <div class="section-divider">
                <h4>🔧 Servicios Disponibles</h4>
                <p style="color:#6c757d; font-size:0.85rem; margin-top:4px;">Selecciona los servicios con los que cuenta la propiedad</p>
            </div>
            <div class="services-section-wrapper">
                <div class="services-grid" id="servicesGrid">
                    <label class="service-card {{ old('agua_potable') ? 'active' : '' }}" data-service="agua">
                        <input type="checkbox" name="agua_potable" value="1" {{ old('agua_potable') ? 'checked' : '' }}>
                        <div class="service-icon-wrap">
                            <span>💧</span>
                        </div>
                        <div class="service-info">
                            <div class="service-name">Agua Potable</div>
                            <div class="service-desc">Red de agua tratada disponible</div>
                        </div>
                        <div class="service-toggle"></div>
                        <span class="service-check">✓</span>
                    </label>
                    <label class="service-card {{ old('energia_electrica') ? 'active' : '' }}" data-service="energia">
                        <input type="checkbox" name="energia_electrica" value="1" {{ old('energia_electrica') ? 'checked' : '' }}>
                        <div class="service-icon-wrap">
                            <span>⚡</span>
                        </div>
                        <div class="service-info">
                            <div class="service-name">Energía Eléctrica</div>
                            <div class="service-desc">Conexión a la red eléctrica</div>
                        </div>
                        <div class="service-toggle"></div>
                        <span class="service-check">✓</span>
                    </label>
                    <label class="service-card {{ old('alcantarillado') ? 'active' : '' }}" data-service="alcantarillado">
                        <input type="checkbox" name="alcantarillado" value="1" {{ old('alcantarillado') ? 'checked' : '' }}>
                        <div class="service-icon-wrap">
                            <span>🚰</span>
                        </div>
                        <div class="service-info">
                            <div class="service-name">Alcantarillado</div>
                            <div class="service-desc">Sistema de drenaje sanitario</div>
                        </div>
                        <div class="service-toggle"></div>
                        <span class="service-check">✓</span>
                    </label>
                    <label class="service-card {{ old('gas_domiciliario') ? 'active' : '' }}" data-service="gas">
                        <input type="checkbox" name="gas_domiciliario" value="1" {{ old('gas_domiciliario') ? 'checked' : '' }}>
                        <div class="service-icon-wrap">
                            <span>🔥</span>
                        </div>
                        <div class="service-info">
                            <div class="service-name">Gas Domiciliario</div>
                            <div class="service-desc">Instalación de gas natural</div>
                        </div>
                        <div class="service-toggle"></div>
                        <span class="service-check">✓</span>
                    </label>
                    <label class="service-card {{ old('internet') ? 'active' : '' }}" data-service="internet">
                        <input type="checkbox" name="internet" value="1" {{ old('internet') ? 'checked' : '' }}>
                        <div class="service-icon-wrap">
                            <span>🌐</span>
                        </div>
                        <div class="service-info">
                            <div class="service-name">Internet</div>
                            <div class="service-desc">Cobertura de fibra óptica o WiFi</div>
                        </div>
                        <div class="service-toggle"></div>
                        <span class="service-check">✓</span>
                    </label>
                </div>

                {{-- Contador de servicios seleccionados --}}
                <div class="services-counter" id="servicesCounter">
                    <span class="services-counter-dot" id="sDot1"></span>
                    <span class="services-counter-dot" id="sDot2"></span>
                    <span class="services-counter-dot" id="sDot3"></span>
                    <span class="services-counter-dot" id="sDot4"></span>
                    <span class="services-counter-dot" id="sDot5"></span>
                    <span class="services-counter-text"><strong id="servicesCount">0</strong> de 5 servicios seleccionados</span>
                </div>
            </div>

            <div class="section-divider">
                <h4>💰 Información Comercial</h4>
            </div>
            <div class="form-row" style="flex-wrap:wrap;">
                <div class="form-group" style="flex:2;min-width:200px;">
                    <label for="precio">Precio <span class="required">*</span></label>
                    <input type="number" name="precio" id="precio" class="form-control"
                           placeholder="Ej: 50000" min="0" step="0.01"
                           value="{{ old('precio') }}" required>
                </div>
                <div class="form-group" style="flex:1;min-width:120px;">
                    <label for="moneda">Moneda</label>
                    <select name="moneda" id="moneda" class="form-control">
                        <option value="USD" {{ old('moneda', 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                        <option value="BOB" {{ old('moneda') == 'BOB' ? 'selected' : '' }}>BOB (Bs.)</option>
                    </select>
                </div>
                <div class="form-group" style="flex:1;min-width:150px;">
                    <label for="forma_pago">Forma de Pago</label>
                    <select name="forma_pago" id="forma_pago" class="form-control">
                        <option value="ambos" {{ old('forma_pago', 'ambos') == 'ambos' ? 'selected' : '' }}>Ambos</option>
                        <option value="contado" {{ old('forma_pago') == 'contado' ? 'selected' : '' }}>Contado</option>
                        <option value="financiamiento" {{ old('forma_pago') == 'financiamiento' ? 'selected' : '' }}>Financiamiento</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="metros_cuadrados">Superficie (m²) <span class="required">*</span></label>
                    <input type="number" name="metros_cuadrados" id="metros_cuadrados"
                           class="form-control" placeholder="Ej: 300" min="0" step="0.01"
                           value="{{ old('metros_cuadrados') }}" required>
                </div>
                <div class="form-group">
                    <label for="categoria_id">Categoría <span class="required">*</span></label>
                    <select name="categoria_id" id="categoria_id" class="form-control" required>
                        <option value="">Seleccione una categoría...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }} data-tipo-propiedad="{{ $cat->tipo_propiedad }}">
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Mapa selector --}}
            <div class="form-group">
                <label>
                    Ubicación en el Mapa <span class="required">*</span>
                    <small>(Arrastra el pin a la ubicación exacta del terreno)</small>
                </label>
                <div id="mapaSelector" style="height:350px; border-radius:10px; border:2px solid #ced4da; margin-bottom:8px;"></div>
                <div style="display:flex; gap:12px; margin-top:6px;">
                    <div class="form-group" style="flex:1; margin-bottom:0;">
                        <label for="latitud" style="font-size:0.85rem;">Latitud</label>
                        <input type="text" name="latitud" id="latitud" class="form-control"
                               value="{{ old('latitud') }}"
                               placeholder="Se completa al mover el pin" readonly>
                    </div>
                    <div class="form-group" style="flex:1; margin-bottom:0;">
                        <label for="longitud" style="font-size:0.85rem;">Longitud</label>
                        <input type="text" name="longitud" id="longitud" class="form-control"
                               value="{{ old('longitud') }}"
                               placeholder="Se completa al mover el pin" readonly>
                    </div>
                </div>
                <small style="color:#6c757d;">
                    💡 Haz clic en el mapa o arrastra el marcador para ajustar la ubicación exacta.
                </small>
            </div>

            {{-- Descripción --}}
            <div class="form-group">
                <label for="descripcion">
                    Descripción <span class="required">*</span>
                    <small>(Mínimo 50 caracteres)</small>
                </label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="4"
                          placeholder="Describe detalladamente las características del terreno..."
                          required>{{ old('descripcion') }}</textarea>
                <div class="char-counter"><span id="charCount">0</span> caracteres</div>
            </div>

            {{-- Imágenes --}}
            <div class="form-group">
                <label>
                    Imágenes del Terreno <span class="required">*</span>
                    <small>(Hasta 10 imágenes, JPG/PNG, máx 5MB c/u)</small>
                </label>

                <div class="dropzone multi-dropzone" id="imagesDropzone">
                    <div class="dropzone-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <p>Haga clic para seleccionar imágenes</p>
                    </div>
                </div>

                <input type="file" name="imagenes[]" id="imagenesInput"
                       accept=".jpg,.jpeg,.png" multiple style="display:none;" required>

                <div class="images-preview-grid" id="imagesPreviewGrid"></div>

                {{-- Selector de portada --}}
                <div class="portada-selector" id="portadaSelector" style="display:none; margin-top:15px;">
                    <div class="portada-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             style="width:18px;height:18px;">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Seleccionar imagen de portada
                    </div>
                    <div class="portada-options" id="portadaOptions"></div>
                    <input type="hidden" name="portada_index" id="portadaIndex" value="0">
                    <small class="form-hint" style="color:#6c757d; margin-top:8px; display:block;">
                        La imagen de portada será la principal que se mostrará en las tarjetas de presentación.
                    </small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary submit-btn" id="submitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     style="width:18px;height:18px;margin-right:8px;vertical-align:middle;">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
                Guardar y Enviar para Aprobación
            </button>
        </form>
    </div>
</div>
@endsection

<!-- SCRIPTS -->
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── 1. Contador de caracteres ──────────────────────────────
    var descInput = document.getElementById('descripcion');
    var charCount = document.getElementById('charCount');

    function actualizarContador() {
        var len = descInput.value.length;
        charCount.textContent = len;
        charCount.style.color = len < 50 ? '#ffc107' : '#28a745';
    }
    descInput.addEventListener('input', actualizarContador);
    actualizarContador();

    // ── 1b. Servicios — toggle + contador ─────────────────────
    var serviceCards = document.querySelectorAll('.service-card');
    var servicesCountEl = document.getElementById('servicesCount');
    var dots = [
        document.getElementById('sDot1'),
        document.getElementById('sDot2'),
        document.getElementById('sDot3'),
        document.getElementById('sDot4'),
        document.getElementById('sDot5')
    ];

    function updateServicesCounter() {
        var checked = document.querySelectorAll('.service-card.active').length;
        if (servicesCountEl) servicesCountEl.textContent = checked;
        dots.forEach(function(dot, i) {
            if (dot) dot.classList.toggle('active', i < checked);
        });
    }

    serviceCards.forEach(function(card) {
        card.addEventListener('click', function() {
            var checkbox = card.querySelector('input[type="checkbox"]');
            // The <label> already toggles the checkbox, just handle the class
            setTimeout(function() {
                card.classList.toggle('active', checkbox.checked);
                updateServicesCounter();
            }, 10);
        });
    });

    // Initialize on load
    updateServicesCounter();

    // ── 2. Mini mapa selector de ubicación ────────────────────
    var defaultLat = -22.0186;   // Yacuiba, Tarija
    var defaultLng = -63.6774;

    var mapaSelector = L.map('mapaSelector').setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OSM &copy; CartoDB',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(mapaSelector);

    var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(mapaSelector);

    function actualizarCoordenadas(lat, lng) {
        document.getElementById('latitud').value  = lat.toFixed(8);
        document.getElementById('longitud').value = lng.toFixed(8);
    }

    // Inicializar con coordenadas por defecto
    actualizarCoordenadas(defaultLat, defaultLng);

    marker.on('dragend', function (e) {
        var pos = e.target.getLatLng();
        actualizarCoordenadas(pos.lat, pos.lng);
    });

    mapaSelector.on('click', function (e) {
        marker.setLatLng(e.latlng);
        actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
    });

    // ── 3. Subida múltiple de imágenes ────────────────────────
    var dropzone   = document.getElementById('imagesDropzone');
    var fileInput  = document.getElementById('imagenesInput');
    var previewGrid = document.getElementById('imagesPreviewGrid');

    var selectedFiles = [];

    dropzone.addEventListener('click', function () { fileInput.click(); });

    dropzone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropzone.style.borderColor = '#007bff';
        dropzone.style.background  = '#e9ecef';
    });

    dropzone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        dropzone.style.borderColor = '#ced4da';
        dropzone.style.background  = '#f8f9fa';
    });

    dropzone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropzone.style.borderColor = '#ced4da';
        dropzone.style.background  = '#f8f9fa';
        if (e.dataTransfer.files) {
            handleFiles(e.dataTransfer.files);
        }
    });

    fileInput.addEventListener('change', function (e) {
        handleFiles(e.target.files);
        // Limpiar el input para permitir volver a seleccionar los mismos archivos
        fileInput.value = '';
    });

    function handleFiles(filesList) {
        var files  = Array.from(filesList);
        var errors = [];
        var valid  = [];

        files.forEach(function (file) {
            if (file.size > 5 * 1024 * 1024) {
                errors.push('• ' + file.name + ' supera los 5MB.');
                return;
            }
            if (!file.type.match('image/(jpeg|jpg|png)')) {
                errors.push('• ' + file.name + ' no es JPG/PNG válido.');
                return;
            }
            valid.push(file);
        });

        if (errors.length > 0) {
            alert('Observaciones:\n' + errors.join('\n'));
        }

        var espacioDisponible = 10 - selectedFiles.length;
        if (valid.length > espacioDisponible) {
            alert('Límite de 10 imágenes. Solo se agregarán las primeras ' + espacioDisponible + '.');
            valid = valid.slice(0, espacioDisponible);
        }

        selectedFiles = selectedFiles.concat(valid);
        renderPreviews();
        syncFileInput();
    }

    function renderPreviews() {
        previewGrid.innerHTML = '';

        selectedFiles.forEach(function (file, index) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML =
                    '<img src="' + e.target.result + '" alt="Preview ' + (index + 1) + '">' +
                    '<button type="button" class="remove-btn" ' +
                        'onclick="window.removeFile(' + index + ')" title="Eliminar">&times;</button>';
                previewGrid.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        updatePortadaSelector();

        if (selectedFiles.length > 0) {
            fileInput.removeAttribute('required');
        } else {
            fileInput.setAttribute('required', 'required');
        }
    }

    function updatePortadaSelector() {
        var portadaSelector = document.getElementById('portadaSelector');
        var portadaOptions  = document.getElementById('portadaOptions');
        var portadaIndex    = document.getElementById('portadaIndex');

        if (selectedFiles.length === 0) {
            portadaSelector.style.display = 'none';
            portadaIndex.value = '0';
            return;
        }

        portadaSelector.style.display = 'block';
        portadaOptions.innerHTML = '';

        var currentPortada = parseInt(portadaIndex.value) || 0;
        if (currentPortada >= selectedFiles.length) {
            currentPortada = 0;
            portadaIndex.value = '0';
        }

        selectedFiles.forEach(function (file, index) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var option = document.createElement('div');
                option.className = 'portada-option' + (index === currentPortada ? ' selected' : '');
                option.setAttribute('data-index', index);
                option.onclick = function () { selectPortada(index); };
                option.innerHTML =
                    '<input type="radio" name="portada_radio" value="' + index + '" ' +
                        (index === currentPortada ? 'checked' : '') + '>' +
                    '<img src="' + e.target.result + '" alt="Imagen ' + (index + 1) + '">' +
                    '<span>Imagen ' + (index + 1) + '</span>';
                portadaOptions.appendChild(option);
            };
            reader.readAsDataURL(file);
        });
    }

    function selectPortada(index) {
        document.getElementById('portadaIndex').value = index;
        document.querySelectorAll('.portada-option').forEach(function (opt, i) {
            opt.classList.toggle('selected', i === index);
            var radio = opt.querySelector('input[type="radio"]');
            if (radio) radio.checked = (i === index);
        });
    }

    // Exponer removeFile globalmente para los onclick inline
    window.removeFile = function (index) {
        selectedFiles.splice(index, 1);
        // Ajustar portada si el índice eliminado era la portada actual
        var portadaIndex = document.getElementById('portadaIndex');
        var currentPortada = parseInt(portadaIndex.value) || 0;
        if (currentPortada >= selectedFiles.length && selectedFiles.length > 0) {
            portadaIndex.value = selectedFiles.length - 1;
        } else if (selectedFiles.length === 0) {
            portadaIndex.value = '0';
        }
        renderPreviews();
        syncFileInput();
    };

    function syncFileInput() {
        try {
            var dt = new DataTransfer();
            selectedFiles.forEach(function (file) { dt.items.add(file); });
            fileInput.files = dt.files;
        } catch (e) {
            // DataTransfer no soportado en algunos navegadores viejos
            console.warn('DataTransfer no disponible:', e);
        }
    }

    // ── 4. Envío del formulario ────────────────────────────────
    var form      = document.getElementById('terrenoForm');
    var submitBtn = document.getElementById('submitBtn');
    var enviando  = false;

    form.addEventListener('submit', function (e) {
        // Prevenir doble envío
        if (enviando) {
            e.preventDefault();
            return false;
        }

        if (selectedFiles.length === 0) {
            e.preventDefault();
            alert('Debe adjuntar al menos una imagen del terreno.');
            return false;
        }

        if (descInput.value.trim().length < 50) {
            e.preventDefault();
            alert('La descripción debe contener al menos 50 caracteres.');
            return false;
        }

        // Sincronizar archivos al input antes de enviar
        syncFileInput();

        enviando = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML =
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" ' +
                'style="width:18px;height:18px;margin-right:8px;vertical-align:middle;' +
                'animation:spin 1s linear infinite;">' +
                '<circle cx="12" cy="12" r="10"/>' +
                '<path d="M12 2v4M12 22v-4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83' +
                'M2 12h4M22 12h-4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>' +
            '</svg> Procesando...';

        return true;
    });

    // Seguridad: re-habilitar el botón si el usuario navega de vuelta
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            enviando = false;
            submitBtn.disabled = false;
            submitBtn.innerHTML =
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" ' +
                    'style="width:18px;height:18px;margin-right:8px;vertical-align:middle;">' +
                    '<path d="M5 12h14M12 5l7 7-7 7"/>' +
                '</svg> Guardar y Enviar para Aprobación';
        }
    });

    // ── 5. Selector de Tipo (Terreno / Lote) ──────────────────────────────
    var radios       = document.querySelectorAll('input[name="tipo"]');
    var parentGroup  = document.getElementById('parentGroup');
    var formTitle    = document.getElementById('formTitle');
    var tipoTerrBtn  = document.getElementById('tipoTerrenoBtn');
    var tipoLoteBtn  = document.getElementById('tipoLoteBtn');
    var iconLote     = document.getElementById('iconLote');
    var labelLote    = document.getElementById('labelLote');
    var loteFields   = document.getElementById('loteFields');
    var terrenoFields = document.getElementById('terrenoFields');

    var selectCat = document.getElementById('categoria_id');
    var allCatOptions = selectCat ? Array.from(selectCat.querySelectorAll('option')) : [];

    function filterCategories(tipoVal) {
        if (!selectCat) return;
        var currentSelectedValue = selectCat.value;
        selectCat.innerHTML = '';
        allCatOptions.forEach(function(opt) {
            if (opt.value === '' || opt.getAttribute('data-tipo-propiedad') === 'todos' || opt.getAttribute('data-tipo-propiedad') === tipoVal) {
                selectCat.appendChild(opt);
            }
        });
        selectCat.value = currentSelectedValue;
    }

    function applyTipo(val) {
        filterCategories(val);
        if (val === 'lote') {
            Object.assign(tipoLoteBtn.style, { borderColor: '#17a2b8', background: '#e0f7fa' });
            iconLote.style.color = '#17a2b8';
            labelLote.style.color = '#17a2b8';
            Object.assign(tipoTerrBtn.style, { borderColor: '#ced4da', background: '#fff' });
            if (loteFields) loteFields.style.display = 'block';
            if (terrenoFields) terrenoFields.style.display = 'none';
            formTitle.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Publicar Nuevo Lote';
        } else {
            Object.assign(tipoTerrBtn.style, { borderColor: '#007bff', background: '#e3f2fd' });
            Object.assign(tipoLoteBtn.style, { borderColor: '#ced4da', background: '#fff' });
            iconLote.style.color = '#6c757d';
            labelLote.style.color = '#6c757d';
            if (loteFields) loteFields.style.display = 'none';
            if (terrenoFields) terrenoFields.style.display = 'block';
            formTitle.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg> Publicar Nuevo Terreno';
        }
    }

    // Inicializar
    var currentTipo = document.querySelector('input[name="tipo"]:checked');
    if (currentTipo) applyTipo(currentTipo.value);

    radios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            applyTipo(this.value);
        });
    });

    // Hacer clic en los labels activa el radio
    tipoTerrBtn.addEventListener('click', function() {
        document.getElementById('tipoTerreno').checked = true;
        applyTipo('terreno');
    });
    tipoLoteBtn.addEventListener('click', function() {
        document.getElementById('tipoLote').checked = true;
        applyTipo('lote');
    });

});
</script>
@endpush