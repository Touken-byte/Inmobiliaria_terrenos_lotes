@extends('layouts.app')

@section('title', isset($editando) ? 'Editar Propiedad' : 'Publicar Propiedad')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* ═══ TARJETA PRINCIPAL ═══ */
    .pub-card {
        max-width: 920px;
        margin: 2rem auto;
        background: var(--bg-lighter, #ffffff);
        border-radius: var(--border-radius-xl, 24px);
        box-shadow: var(--shadow-md, 0 10px 15px rgba(0,0,0,0.1));
        border: 1px solid rgba(76, 29, 149, 0.08);
        overflow: hidden;
    }

    /* ═══ HEADER MORADO ═══ */
    .pub-header {
        background: linear-gradient(135deg, #2d0a52 0%, #4c1d95 40%, #7c3aed 100%);
        padding: 2.5rem 3rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .pub-header::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -15%;
        width: 450px;
        height: 450px;
        background: radial-gradient(circle, rgba(251, 191, 36, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .pub-header::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: -5%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(124, 58, 237, 0.25) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .pub-header-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: flex-start;
        gap: 1.5rem;
    }
    .pub-header-icon {
        flex-shrink: 0;
        width: 56px;
        height: 56px;
        background: rgba(251, 191, 36, 0.15);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(251, 191, 36, 0.15);
    }
    .pub-header-icon svg {
        width: 28px;
        height: 28px;
        stroke: #fbbf24;
    }
    .pub-header-text h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: #ffffff;
    }
    .pub-header-text p {
        margin: 0.35rem 0 0;
        opacity: 0.85;
        font-size: 0.9rem;
        max-width: 520px;
        color: rgba(255,255,255,0.85);
    }
    .pub-header-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(251, 191, 36, 0.12);
        border: 1px solid rgba(251, 191, 36, 0.15);
        padding: 0.3rem 0.85rem;
        border-radius: 100px;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        margin-top: 0.75rem;
        color: #fcd34d;
    }
    .pub-header-badge svg {
        stroke: #fbbf24;
    }

    /* ═══ BODY ═══ */
    .pub-body {
        padding: 2.5rem 3rem 3rem;
        background: var(--bg-lighter, #ffffff);
    }

    /* ═══ SELECTOR DE TIPO (Terreno/Lote/Alquiler) ═══ */
    .type-selector {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }
    .type-option {
        flex: 1;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        padding: 1.1rem 0.75rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.35rem;
        user-select: none;
        position: relative;
    }
    .type-option input { display: none; }
    .type-option:hover:not(.disabled) {
        border-color: #7c3aed;
        background: #faf5ff;
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(76, 29, 149, 0.15);
    }
    .type-option.active {
        border-color: #4c1d95;
        background: #f5f0ff;
        box-shadow: 0 4px 20px rgba(76, 29, 149, 0.2);
        transform: translateY(-3px);
    }
    .type-option.active::after {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 10px;
        background: #4c1d95;
        color: #fff;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        font-size: 0.65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .type-option .icon { font-size: 1.85rem; line-height: 1; }
    .type-option .title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #1f2937;
    }
    .type-option .desc {
        font-size: 0.68rem;
        color: #9ca3af;
    }
    .type-option.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f9fafb;
    }
    .type-option.disabled:hover {
        transform: none;
        box-shadow: none;
        border-color: #e5e7eb;
    }

    /* ═══ FORM GROUP ═══ */
    .form-group { margin-bottom: 1.5rem; }
    .form-row {
        display: flex;
        gap: 1.25rem;
        flex-wrap: wrap;
    }
    .form-row .form-group {
        flex: 1;
        min-width: 220px;
    }
    label {
        display: block;
        margin-bottom: 0.4rem;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: var(--text-secondary, #334155);
    }
    .required { color: #dc2626; }

    /* ═══ INPUTS Y SELECTS ═══ */
    .form-control {
        width: 100%;
        padding: 0.72rem 1rem;
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        background: #f9fafb;
        color: #1f2937;
        font-size: 0.92rem;
        transition: all 0.25s;
        box-sizing: border-box;
        appearance: auto;
    }
    .form-control:focus {
        outline: none;
        border-color: #7c3aed;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
    }
    .form-control::placeholder {
        color: #9ca3af;
        font-size: 0.85rem;
    }
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    select.form-control {
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }

    /* ═══ SECTION DIVIDERS (MODERN CARDS) ═══ */
    .section-divider {
        margin: 2rem 0 1.5rem;
        padding: 1.1rem 1.25rem;
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #3730a3 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(49, 46, 129, 0.25);
    }
    .section-divider::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: radial-gradient(ellipse at 20% 50%, rgba(99, 102, 241, 0.15) 0%, transparent 60%);
        pointer-events: none;
    }
    .section-divider::after {
        content: '';
        position: absolute;
        right: -40px; top: -40px;
        width: 120px; height: 120px;
        border-radius: 50%;
        background: rgba(129, 140, 248, 0.06);
        pointer-events: none;
    }
    .section-divider .sec-icon {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        position: relative;
        z-index: 1;
    }
    .section-divider .sec-content {
        position: relative;
        z-index: 1;
        flex: 1;
    }
    .section-divider .sec-content h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: 0.3px;
    }
    .section-divider .sec-content p {
        margin: 0.2rem 0 0;
        font-size: 0.75rem;
        color: rgba(199, 210, 254, 0.8);
        font-weight: 400;
        letter-spacing: 0.2px;
    }
    .section-divider .sec-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 100px;
        font-size: 0.6rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        background: rgba(255, 255, 255, 0.1);
        color: rgba(199, 210, 254, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.08);
        white-space: nowrap;
        position: relative;
        z-index: 1;
    }

    /* ═══ SERVICES GRID (Toggle Switch Mejorado) ═══ */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(175px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .service-card {
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 0.7rem 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        background: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
        position: relative;
    }
    .service-card input { display: none; }
    .service-card:hover {
        border-color: #7c3aed;
        background: #faf5ff;
        box-shadow: 0 2px 8px rgba(76, 29, 149, 0.06);
    }
    .service-card.active {
        border-color: #4c1d95;
        background: linear-gradient(135deg, #f5f0ff 0%, #ede9fe 100%);
        box-shadow: 0 2px 12px rgba(76, 29, 149, 0.1);
    }
    .service-card .toggle-track {
        flex-shrink: 0;
        width: 36px;
        height: 20px;
        border-radius: 10px;
        background: #e5e7eb;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #d1d5db;
    }
    .service-card.active .toggle-track {
        background: #4c1d95;
        border-color: #4c1d95;
    }
    .service-card .toggle-dot {
        position: absolute;
        top: 1px;
        left: 1px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }
    .service-card.active .toggle-dot {
        left: 17px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .service-card .service-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: #374151;
        transition: color 0.25s;
    }
    .service-card.active .service-label {
        color: #4c1d95;
        font-weight: 600;
    }
    .service-card .service-icon {
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    /* ═══ DROPZONE ═══ */
    .dropzone-container {
        border: 2px dashed #d1d5db;
        border-radius: 16px;
        padding: 2.5rem 2rem;
        text-align: center;
        background: #faf5ff;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
    }
    .dropzone-container:hover {
        border-color: #7c3aed;
        background: #f5f0ff;
    }
    .dropzone-container input[type="file"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    .dropzone-icon-big { font-size: 2.2rem; margin-bottom: 0.3rem; }
    .dropzone-title {
        font-weight: 700;
        color: #4c1d95;
        font-size: 0.95rem;
    }
    .dropzone-hint {
        font-size: 0.78rem;
        color: #9ca3af;
        margin-top: 0.2rem;
    }

    /* ═══ GALLERY ═══ */
    .gallery-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
        margin-top: 1.25rem;
    }
    .gallery-item {
        position: relative;
        height: 120px;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        background: #f3f4f6;
        transition: all 0.3s;
        cursor: pointer;
    }
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
    .gallery-item.is-cover {
        border-color: #4c1d95;
        box-shadow: 0 0 0 3px rgba(76, 29, 149, 0.25);
    }
    .gallery-item .cover-badge {
        position: absolute;
        bottom: 4px; left: 4px;
        background: #4c1d95;
        color: #ffffff;
        font-size: 0.6rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        text-transform: uppercase;
        display: none;
    }
    .gallery-item.is-cover .cover-badge { display: block; }
    .gallery-item .delete-btn {
        position: absolute;
        top: 4px; right: 4px;
        background: rgba(239, 68, 68, 0.9);
        color: #ffffff;
        border: none;
        border-radius: 50%;
        width: 22px; height: 22px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.75rem;
        transition: all 0.2s;
        z-index: 10;
    }
    .gallery-item .delete-btn:hover {
        background: #dc2626;
        transform: scale(1.1);
    }

    /* ═══ MAP ═══ */
    #map {
        height: 280px;
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        margin-bottom: 1rem;
    }

    /* ═══ BOTÓN SUBMIT (MORADO + DORADO) ═══ */
    .submit-btn {
        background: linear-gradient(135deg, #4c1d95 0%, #6d28d9 50%, #7c3aed 100%);
        color: #ffffff;
        border: none;
        padding: 0.9rem 2rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 16px rgba(76, 29, 149, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
    }
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(76, 29, 149, 0.4);
    }
    .submit-btn:active { transform: translateY(0); }
    .submit-btn svg { width: 18px; height: 18px; }

    /* ═══ ERROR BOX ═══ */
    .error-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-left: 4px solid #dc2626;
        color: #991b1b;
        padding: 1rem 1.25rem;
        border-radius: 10px;
        margin-bottom: 2rem;
    }
    .error-box ul { margin: 0; padding-left: 1.25rem; font-size: 0.88rem; }

    /* ═══ INLINE HELP ═══ */
    .field-hint {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 0.25rem;
    }

    /* ═══ RESPONSIVE ═══ */
    @media (max-width: 768px) {
        .pub-header { padding: 1.5rem; }
        .pub-body { padding: 1.5rem; }
        .pub-header-content { flex-direction: column; }
        .pub-header-icon { width: 44px; height: 44px; }
        .pub-header-text h1 { font-size: 1.25rem; }
        .type-selector { flex-direction: column; gap: 0.5rem; }
        .form-row .form-group { min-width: 100%; }
        .services-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
    }
</style>
@endpush

@section('content')
<div class="pub-card">
    <div class="pub-header">
        <div class="pub-header-content">
            <div class="pub-header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                    <line x1="12" y1="8" x2="12" y2="16" />
                    <line x1="8" y1="12" x2="16" y2="12" />
                </svg>
            </div>
            <div class="pub-header-text">
                <h1>{{ isset($editando) ? 'Editar Anuncio' : 'Publicar Anuncio' }}</h1>
                <p>{{ isset($editando) ? 'Modifica los datos de tu propiedad registrada.' : 'Completa el formulario para crear una publicación atractiva y encuentra clientes interesados.' }}</p>
                <div class="pub-header-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" style="width: 12px; height: 12px;">
                        <path d="M12 2L2 7l10 5 10-5-10-5z" />
                        <path d="M2 17l10 5 10-5" />
                        <path d="M2 12l10 5 10-5" />
                    </svg>
                    Terreno · Lote · Alquiler
                </div>
            </div>
        </div>
    </div>

    <div class="pub-body">
        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $propType = old('tipo', $propiedad->tipo ?? request('tipo', 'terreno'));
            $actionUrl = isset($editando) 
                ? ($propType === 'alquiler' ? route('vendedor.alquileres.update', $propiedad->id) : route('vendedor.terrenos.update', $propiedad->id))
                : route('vendedor.terrenos.store');
            
            $departamentos = [
                'Beni' => ['Trinidad', 'Rurrenabaque', 'Guayaramerín', 'Riberalta', 'Santa Ana de Yacuma', 'San Borja', 'San Ignacio de Moxos'],
                'Chuquisaca' => ['Sucre', 'Monteagudo', 'Camargo', 'Villa Serrano', 'Tarabuco', 'Yotala', 'Padilla'],
                'Cochabamba' => ['Cochabamba', 'Quillacollo', 'Sacaba', 'Vinto', 'Tiquipaya', 'Colcapirhua', 'Punata', 'Cliza', 'Arani', 'Mizque', 'Aiquile', 'Independencia', 'Capinota', 'Tarata'],
                'La Paz' => ['La Paz', 'El Alto', 'Viacha', 'Achacachi', 'Coroico', 'Caranavi', 'Patacamaya', 'Laja', 'Guaqui', 'Desaguadero', 'Copacabana', 'Apolo', 'Mapiri', 'Sorata'],
                'Pando' => ['Cobija', 'Porvenir', 'Puerto Rico', 'Filadelfia', 'Bella Vista', 'San Lorenzo', 'Santa Rosa del Abuná'],
                'Oruro' => ['Oruro', 'Huanuni', 'Challapata', 'Caracollo', 'Poopó', 'Sabaya', 'Corque', 'Turco', 'Escara', 'Machacamarca', 'Eucaliptus'],
                'Potosi' => ['Potosí', 'Villazón', 'Tupiza', 'Uyuni', 'Llallagua', 'Uncía', 'Betanzos', 'Cotagaita', 'Atocha', 'Colquechaca', 'Puna', 'Caiza "D"'],
                'Santa Cruz' => ['Santa Cruz de la Sierra', 'Montero', 'Warnes', 'La Guardia', 'San Ignacio de Velasco', 'Camiri', 'Puerto Suárez', 'Roboré', 'San José de Chiquitos', 'Cotoca', 'El Torno', 'Portachuelo', 'Yapacaní', 'Ascensión de Guarayos', 'Vallegrande', 'Samaipata', 'Comarapa', 'Buenavista', 'San Matías', 'San Miguel de Velasco', 'Charagua', 'Pailón', 'Cabezas'],
                'Tarija' => ['Tarija', 'Yacuiba', 'Villamontes', 'Bermejo', 'Padcaya', 'Uriondo', 'Entre Ríos', 'Caraparí', 'San Lorenzo', 'El Puente']
            ];
            $paises = ['Bolivia'];
        @endphp

        <form action="{{ $actionUrl }}" method="POST" enctype="multipart/form-data" id="publishForm">
            @csrf
            @if(isset($editando))
                @method('PUT')
            @endif

            <input type="hidden" name="portada_id" id="portada_id_input" value="{{ $propiedad->portada_id ?? '' }}">
            <input type="hidden" name="portada_index" id="portada_index_input" value="-1">

            {{-- 1. TIPO DE PROPIEDAD --}}
            <label style="margin-bottom: 0.75rem; display: block;">Tipo de Anuncio <span class="required">*</span></label>
            <div class="type-selector">
                <label class="type-option {{ $propType === 'terreno' ? 'active' : '' }} {{ isset($editando) && $propType !== 'terreno' ? 'disabled' : '' }}" id="typeTerrenoLabel">
                    <input type="radio" name="tipo" value="terreno" {{ $propType === 'terreno' ? 'checked' : '' }} {{ isset($editando) ? 'disabled' : '' }} onchange="switchType('terreno')">
                    <span class="icon">🌄</span>
                    <span class="title">Terreno</span>
                    <span class="desc">Venta de suelo o parcelas</span>
                </label>
                <label class="type-option {{ $propType === 'lote' ? 'active' : '' }} {{ isset($editando) && $propType !== 'lote' ? 'disabled' : '' }}" id="typeLoteLabel">
                    <input type="radio" name="tipo" value="lote" {{ $propType === 'lote' ? 'checked' : '' }} {{ isset($editando) ? 'disabled' : '' }} onchange="switchType('lote')">
                    <span class="icon">🟩</span>
                    <span class="title">Lote</span>
                    <span class="desc">Fracciones de terreno urbano</span>
                </label>
                <label class="type-option {{ $propType === 'alquiler' ? 'active' : '' }} {{ isset($editando) && $propType !== 'alquiler' ? 'disabled' : '' }}" id="typeAlquilerLabel">
                    <input type="radio" name="tipo" value="alquiler" {{ $propType === 'alquiler' ? 'checked' : '' }} {{ isset($editando) ? 'disabled' : '' }} onchange="switchType('alquiler')">
                    <span class="icon">🔑</span>
                    <span class="title">Alquiler</span>
                    <span class="desc">Habitaciones, departamentos, etc.</span>
                </label>
            </div>
            @if(isset($editando))
                <input type="hidden" name="tipo" value="{{ $propType }}">
            @endif

            {{-- 2. INFORMACIÓN BÁSICA --}}
            <div class="section-divider">
                <div class="sec-icon">📝</div>
                <div class="sec-content">
                    <h3>Información Básica</h3>
                    <p>Completa los datos principales de tu propiedad: título, precio, categoría y superficie</p>
                </div>
                <span class="sec-badge">Paso 1</span>
            </div>
            
            <div class="form-group">
                <label for="nombre">Título del Anuncio <span class="required">*</span></label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: Hermoso Terreno Plano en Zona Norte" value="{{ old('nombre', $propiedad->nombre ?? $propiedad->titulo ?? '') }}" required>
                        <input type="hidden" name="titulo" id="titulo_hidden" value="{{ old('titulo', $propiedad->titulo ?? $propiedad->nombre ?? '') }}">
            </div>

            <div class="form-row">
                <div class="form-group" id="precioGroup">
                    <label for="precio" id="precioLabel">Precio Venta (USD) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="precio" id="precio" class="form-control" placeholder="0.00" value="{{ old('precio', $propiedad->precio ?? '') }}">
                </div>
                <div class="form-group" id="precioAlquilerGroup" style="display:none;">
                    <label for="precio_mensual">Precio Mensual (Bs.) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="precio_mensual" id="precio_mensual" class="form-control" placeholder="0.00" value="{{ old('precio_mensual', $propiedad->precio_mensual ?? '') }}">
                </div>
                <div class="form-group">
                    <label for="categoria_id">Categoría <span class="required">*</span></label>
                    <select name="categoria_id" id="categoria_id" class="form-control" required>
                        <option value="">Seleccione...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" data-prop-type="{{ $cat->tipo_propiedad }}" {{ old('categoria_id', $propiedad->categoria_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="metros_cuadrados">Área / Superficie (m²) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="metros_cuadrados" id="metros_cuadrados" class="form-control" placeholder="0.00" value="{{ old('metros_cuadrados', $propiedad->metros_cuadrados ?? '') }}" required>
                </div>
                <div class="form-group alquiler-only" style="display:none;">
                    <label for="habitaciones">Habitaciones <span class="required">*</span></label>
                    <input type="number" name="habitaciones" id="habitaciones" class="form-control" value="{{ old('habitaciones', $propiedad->habitaciones ?? 1) }}" min="1">
                </div>
                <div class="form-group alquiler-only" style="display:none;">
                    <label for="banos">Baños <span class="required">*</span></label>
                    <input type="number" name="banos" id="banos" class="form-control" value="{{ old('banos', $propiedad->banos ?? 1) }}" min="1">
                </div>
            </div>

            {{-- 3. DISPONIBILIDAD --}}
            <div class="section-divider">
                <div class="sec-icon">🏷️</div>
                <div class="sec-content">
                    <h3>Disponibilidad</h3>
                    <p>Indica si la propiedad está disponible, reservada o vendida</p>
                </div>
                <span class="sec-badge">Estado</span>
            </div>
            <div class="form-group" style="max-width:280px;">
                <label for="estado_lote">Estado <span class="required">*</span></label>
                <select name="estado_lote" id="estado_lote" class="form-control" required>
                    @php
                        $currEst = old('estado_lote', $propiedad->estado_lote ?? $propiedad->estado ?? 'disponible');
                    @endphp
                    <option value="disponible" {{ $currEst === 'disponible' ? 'selected' : '' }}>🟢 Disponible</option>
                    <option value="reservado" {{ $currEst === 'reservado' ? 'selected' : '' }}>🟡 Reservado</option>
                    <option value="vendido" {{ in_array($currEst, ['vendido', 'alquilado']) ? 'selected' : '' }}>🔴 Vendido / Alquilado</option>
                </select>
            </div>

            {{-- 4. CAMPOS DE LOTE --}}
            <div id="loteSection" style="display:none;">
                <div class="section-divider">
                    <div class="sec-icon">🏠</div>
                    <div class="sec-content">
                        <h3>Asociación & Detalles de Lote</h3>
                        <p>Asigna el terreno padre y completa la identificación catastral del lote</p>
                    </div>
                    <span class="sec-badge">Lote</span>
                </div>
                <div class="form-group">
                    <label for="parent_id">Terreno Padre <span class="required">*</span></label>
                    <select name="parent_id" id="parent_id" class="form-control">
                        <option value="">Seleccione el terreno al que pertenece...</option>
                        @foreach($terrenosPadre ?? [] as $tp)
                            <option value="{{ $tp->id }}" {{ old('parent_id', $propiedad->parent_id ?? '') == $tp->id ? 'selected' : '' }}>
                                {{ $tp->nombre }} ({{ $tp->ubicacion }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="numero_lote">Número de Lote</label>
                        <input type="text" name="numero_lote" id="numero_lote" class="form-control" value="{{ old('numero_lote', $propiedad->numero_lote ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="codigo_lote">Código del Lote</label>
                        <input type="text" name="codigo_lote" id="codigo_lote" class="form-control" value="{{ old('codigo_lote', $propiedad->codigo_lote ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="manzano_bloque">Manzano / Bloque</label>
                        <input type="text" name="manzano_bloque" id="manzano_bloque" class="form-control" value="{{ old('manzano_bloque', $propiedad->manzano_bloque ?? '') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="frente">Frente (m)</label>
                        <input type="number" step="0.01" name="frente" id="frente" class="form-control" value="{{ old('frente', $propiedad->frente ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="fondo">Fondo (m)</label>
                        <input type="number" step="0.01" name="fondo" id="fondo" class="form-control" value="{{ old('fondo', $propiedad->fondo ?? '') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="colinda_norte">Colinda al Norte</label>
                        <input type="text" name="colinda_norte" id="colinda_norte" class="form-control" value="{{ old('colinda_norte', $propiedad->colinda_norte ?? '') }}" placeholder="Ej: Calle 10">
                    </div>
                    <div class="form-group">
                        <label for="colinda_sur">Colinda al Sur</label>
                        <input type="text" name="colinda_sur" id="colinda_sur" class="form-control" value="{{ old('colinda_sur', $propiedad->colinda_sur ?? '') }}" placeholder="Ej: Av. Principal">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="colinda_este">Colinda al Este</label>
                        <input type="text" name="colinda_este" id="colinda_este" class="form-control" value="{{ old('colinda_este', $propiedad->colinda_este ?? '') }}" placeholder="Ej: Prop. Pérez">
                    </div>
                    <div class="form-group">
                        <label for="colinda_oeste">Colinda al Oeste</label>
                        <input type="text" name="colinda_oeste" id="colinda_oeste" class="form-control" value="{{ old('colinda_oeste', $propiedad->colinda_oeste ?? '') }}" placeholder="Ej: Prop. García">
                    </div>
                </div>
            </div>

            {{-- 5. CAMPOS DE TERRENO --}}
            <div id="terrenoSection" style="display:none;">
                <div class="section-divider">
                    <div class="sec-icon">🏞️</div>
                    <div class="sec-content">
                        <h3>Características Físicas y Legales</h3>
                        <p>Especifica el tipo de terreno, dimensiones, topografía y datos registrales</p>
                    </div>
                    <span class="sec-badge">Terreno</span>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo_terreno">Tipo de Terreno</label>
                        <select name="tipo_terreno" id="tipo_terreno" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="urbano" {{ old('tipo_terreno', $propiedad->tipo_terreno ?? '') === 'urbano' ? 'selected' : '' }}>Urbano</option>
                            <option value="rural" {{ old('tipo_terreno', $propiedad->tipo_terreno ?? '') === 'rural' ? 'selected' : '' }}>Rural</option>
                            <option value="agricola" {{ old('tipo_terreno', $propiedad->tipo_terreno ?? '') === 'agricola' ? 'selected' : '' }}>Agrícola</option>
                            <option value="comercial" {{ old('tipo_terreno', $propiedad->tipo_terreno ?? '') === 'comercial' ? 'selected' : '' }}>Comercial</option>
                            <option value="industrial" {{ old('tipo_terreno', $propiedad->tipo_terreno ?? '') === 'industrial' ? 'selected' : '' }}>Industrial</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="topografia">Topografía</label>
                        <select name="topografia" id="topografia" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="plano" {{ old('topografia', $propiedad->topografia ?? '') === 'plano' ? 'selected' : '' }}>Plano</option>
                            <option value="semiplano" {{ old('topografia', $propiedad->topografia ?? '') === 'semiplano' ? 'selected' : '' }}>Semiplano</option>
                            <option value="inclinado" {{ old('topografia', $propiedad->topografia ?? '') === 'inclinado' ? 'selected' : '' }}>Inclinado</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="largo">Largo (m)</label>
                        <input type="number" step="0.01" name="largo" id="largo" class="form-control" value="{{ old('largo', $propiedad->largo ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="ancho">Ancho (m)</label>
                        <input type="number" step="0.01" name="ancho" id="ancho" class="form-control" value="{{ old('ancho', $propiedad->ancho ?? '') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="numero_matricula">Número de Matrícula</label>
                        <input type="text" name="numero_matricula" id="numero_matricula" class="form-control" value="{{ old('numero_matricula', $propiedad->numero_matricula ?? '') }}" placeholder="Ej: 2.01.1.01.1234567">
                    </div>
                    <div class="form-group">
                        <label for="codigo_catastral">Código Catastral</label>
                        <input type="text" name="codigo_catastral" id="codigo_catastral" class="form-control" value="{{ old('codigo_catastral', $propiedad->codigo_catastral ?? '') }}" placeholder="Ej: 701-001-001-0001">
                    </div>
                </div>
            </div>

            {{-- 6. CAMPOS DE ALQUILER --}}
            <div id="alquilerSection" style="display:none;">
                <div class="section-divider">
                    <span class="icon">📅</span>
                    <h3>Disponibilidad Alquiler</h3>
                </div>
                <div class="form-group" style="max-width:280px;">
                    <label for="disponible_desde">Disponible Desde</label>
                    <input type="date" name="disponible_desde" id="disponible_desde" class="form-control" value="{{ old('disponible_desde', isset($propiedad->disponible_desde) ? $propiedad->disponible_desde->format('Y-m-d') : date('Y-m-d')) }}">
                </div>
            </div>

            {{-- 7. UBICACIÓN CON SELECTORES --}}
            <div class="section-divider">
                <div class="sec-icon">📍</div>
                <div class="sec-content">
                    <h3>Ubicación</h3>
                    <p>Selecciona el país, departamento y dirección exacta de la propiedad</p>
                </div>
                <span class="sec-badge">Dirección</span>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="pais">País</label>
                    <select name="pais" id="pais" class="form-control">
                        @foreach($paises as $p)
                            <option value="{{ $p }}" {{ old('pais', $propiedad->pais ?? 'Bolivia') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="departamento">Departamento <span class="required">*</span></label>
                    <select name="departamento" id="departamento" class="form-control" required onchange="cargarProvincias()">
                        <option value="">Seleccione departamento...</option>
                        @foreach(array_keys($departamentos) as $dep)
                            @php $selected = old('departamento', $propiedad->departamento ?? '') === $dep; @endphp
                            <option value="{{ $dep }}" {{ $selected ? 'selected' : '' }}>{{ $dep }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="provincia">Provincia / Municipio</label>
                    <select name="provincia" id="provincia" class="form-control">
                        <option value="">Seleccione primero un departamento...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="municipio">Zona / Localidad</label>
                    <input type="text" name="municipio" id="municipio" class="form-control" value="{{ old('municipio', $propiedad->municipio ?? '') }}" placeholder="Ej: Equipetrol, Zona Norte, Centro">
                </div>
            </div>
            <div class="form-group">
                <label for="ubicacion">Dirección Completa <span class="required">*</span></label>
                <input type="text" name="ubicacion" id="ubicacion" class="form-control" value="{{ old('ubicacion', $propiedad->ubicacion ?? '') }}" placeholder="Ej: Avenida San Martín #123, Equipetrol" required>
                <div class="field-hint">Calle, número, zona o referencia adicional</div>
            </div>

            {{-- 8. SERVICIOS --}}
            <div class="section-divider">
                <span class="icon">⚡</span>
                <h3>Servicios e Instalaciones</h3>
            </div>
            
            <div id="serviciosTerreno" class="services-grid">
                @php
                    $servs = [
                        'agua_potable' => '💧 Agua Potable',
                        'energia_electrica' => '⚡ Electricidad',
                        'alcantarillado' => '🚰 Alcantarillado',
                        'gas_domiciliario' => '🔥 Gas Domiciliario',
                        'internet' => '🌐 Internet / Fibra'
                    ];
                @endphp
                @foreach($servs as $field => $label)
                    @php $hasServ = old($field, $propiedad->$field ?? false); @endphp
                    <label class="service-card {{ $hasServ ? 'active' : '' }}">
                        <input type="checkbox" name="{{ $field }}" value="1" {{ $hasServ ? 'checked' : '' }} onchange="this.parentElement.classList.toggle('active', this.checked)">
                        <span class="service-icon">{{ explode(' ', $label)[0] }}</span>
                        <span class="toggle-track"><span class="toggle-dot"></span></span>
                        <span class="service-label">{{ explode(' ', $label, 2)[1] ?? $label }}</span>
                    </label>
                @endforeach
            </div>

            <div id="serviciosAlquiler" class="services-grid" style="display:none;">
                @php
                    $servsAlq = ['Agua 💧', 'Luz ⚡', 'Internet 🌐', 'Gas 🔥', 'Cable 📺'];
                    $hasServsAlq = old('servicios_incluidos', $propiedad->servicios_incluidos ?? []);
                @endphp
                @foreach($servsAlq as $sa)
                    @php 
                        $hasSa = in_array($sa, $hasServsAlq);
                        $saClean = trim(preg_replace('/[^\w\sáéíóúÁÉÍÓÚñÑ]/u', '', $sa));
                        $saIcon = trim(str_replace($saClean, '', $sa));
                    @endphp
                    <label class="service-card {{ $hasSa ? 'active' : '' }}">
                        <input type="checkbox" name="servicios_incluidos[]" value="{{ $sa }}" {{ $hasSa ? 'checked' : '' }} onchange="this.parentElement.classList.toggle('active', this.checked)">
                        <span class="service-icon">{{ $saIcon ?: '📋' }}</span>
                        <span class="toggle-track"><span class="toggle-dot"></span></span>
                        <span class="service-label">{{ $saClean }}</span>
                    </label>
                @endforeach
            </div>

            {{-- 9. MAPA --}}
            <div class="section-divider">
                <span class="icon">📍</span>
                <h3>Ubicación Geográfica</h3>
            </div>
            <label>Coloca un pin en el mapa para señalar la ubicación exacta</label>
            <div id="map"></div>
            <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $propiedad->latitud ?? '') }}">
            <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $propiedad->longitud ?? '') }}">

            {{-- 10. DESCRIPCIÓN --}}
            <div class="section-divider">
                <span class="icon">💬</span>
                <h3>Descripción</h3>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción detallada <span class="required">*</span></label>
                <textarea name="descripcion" id="descripcion" rows="5" class="form-control" required minlength="50" placeholder="Describe las características destacables, beneficios de la zona, accesos, servicios cercanos, etc. (mínimo 50 caracteres)">{{ old('descripcion', $propiedad->descripcion ?? '') }}</textarea>
                <div class="char-counter" id="charCounter" style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.4rem;">
                    <span class="field-hint" id="charCountMsg" style="color: #9ca3af;">0 caracteres</span>
                    <span class="field-hint" id="charWarning" style="color: #dc2626; font-weight: 600; display: none;">Mínimo 50 caracteres</span>
                </div>
            </div>

            {{-- 11. IMÁGENES (Múltiples + Selección de Portada) --}}
            <div class="section-divider">
                <span class="icon">🖼️</span>
                <h3>Galería de Imágenes</h3>
            </div>

            <div style="background: linear-gradient(135deg, #f5f0ff 0%, #ede9fe 100%); border: 2px dashed #7c3aed; border-radius: 16px; padding: 2rem; text-align: center; margin-bottom: 1.5rem;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📸</div>
                <div style="font-weight: 700; font-size: 1rem; color: #4c1d95; margin-bottom: 0.3rem;">Sube varias imágenes de tu propiedad</div>
                <div style="font-size: 0.82rem; color: #7c6b9e; margin-bottom: 1rem;">Puedes seleccionar varias fotos a la vez. Luego elige cuál será la <strong>Portada</strong>.</div>
                
                <label style="display: inline-flex; align-items: center; gap: 0.5rem; background: #4c1d95; color: #fff; padding: 0.7rem 1.5rem; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 0.9rem; transition: all 0.3s; box-shadow: 0 4px 12px rgba(76, 29, 149, 0.2);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 18px; height: 18px;">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Seleccionar Imágenes
                    <input type="file" name="imagenes[]" id="imagenesInput" multiple accept="image/*" onchange="previewFiles(this)" style="display: none;">
                </label>
                <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem;">JPG, JPEG, PNG · Máximo 10 imágenes (5MB c/u) · Puedes seleccionar varias</div>
            </div>

            {{-- INSTRUCCIONES PARA PORTADA --}}
            <div id="coverInstruction" style="display: none; background: #ecfdf5; border: 1px solid #059669; border-left: 3px solid #059669; padding: 0.7rem 1rem; border-radius: 10px; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.2rem;">👆</span>
                    <span style="font-size: 0.85rem; color: #065f46; font-weight: 500;">Haz clic sobre cualquier imagen de abajo para definirla como <strong>Portada principal</strong> (se marcará en morado)</span>
                </div>
            </div>

            {{-- GRILLA DE IMÁGENES --}}
            <div id="noImagesMsg" style="text-align: center; padding: 1.5rem; background: #f9fafb; border-radius: 12px; border: 1px dashed #d1d5db;">
                <div style="font-size: 1.5rem; margin-bottom: 0.3rem;">🖼️</div>
                <div style="font-size: 0.85rem; color: #9ca3af;">Aún no has subido imágenes. Usa el botón de arriba para seleccionar varias fotos.</div>
            </div>

            <div class="gallery-preview" id="imageGallery" style="display: none;">
                @if(isset($editando) && $propiedad->imagenes->isNotEmpty())
                    @foreach($propiedad->imagenes as $img)
                        @php
                            $isPort = ($propiedad->portada_id === $img->id);
                        @endphp
                        <div class="gallery-item {{ $isPort ? 'is-cover' : '' }}" onclick="selectExistingCover({{ $img->id }}, this)">
                            <img src="{{ asset($img->ruta_archivo) }}" alt="Propiedad">
                            <span class="cover-badge">⭐ Portada</span>
                            @php
                                $delRoute = $propType === 'alquiler' 
                                    ? route('vendedor.alquileres.imagen.destroy', $img->id)
                                    : route('vendedor.terrenos.imagen.destroy', $img->id);
                            @endphp
                            <button type="button" class="delete-btn" onclick="event.stopPropagation(); if(confirm('¿Seguro de eliminar esta imagen?')) deleteImage('{{ $delRoute }}')">×</button>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Info de cuántas imágenes se subieron --}}
            <div id="imageCountBadge" style="display: none; margin-top: 0.75rem; text-align: center;">
                <span style="display: inline-flex; align-items: center; gap: 0.4rem; background: #f5f0ff; color: #4c1d95; padding: 0.35rem 1rem; border-radius: 100px; font-size: 0.8rem; font-weight: 600;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <polyline points="21 15 16 10 5 21" />
                    </svg>
                    <span id="imageCountText">0 imágenes subidas</span>
                </span>
            </div>

            {{-- BOTÓN --}}
            <div style="margin-top: 2.5rem;">
                <button type="submit" class="submit-btn" id="submitFormBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                    {{ isset($editando) ? 'Guardar Cambios' : 'Publicar Propiedad' }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Hidden form for individual image deletion --}}
<form id="deleteImageForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ═══ DATOS DE PROVINCIAS POR DEPARTAMENTO ═══
    const provincias = @json($departamentos);

    // ═══ MAPA ═══
    let map, marker;
    let initialLat = {{ old('latitud', $propiedad->latitud ?? -22.0167) }};
    let initialLng = {{ old('longitud', $propiedad->longitud ?? -63.6833) }};

    document.addEventListener("DOMContentLoaded", function() {
        // ═══ CHARACTER COUNTER ═══
        const descEl = document.getElementById('descripcion');
        if (descEl) {
            descEl.addEventListener('input', actualizarContador);
            actualizarContador();
        }

        // Inicializar mapa
        map = L.map('map').setView([initialLat, initialLng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

        marker = L.marker([initialLat, initialLng], {draggable: true}).addTo(map);

        marker.on('dragend', function(e) {
            let latlng = marker.getLatLng();
            document.getElementById('latitud').value = latlng.lat.toFixed(6);
            document.getElementById('longitud').value = latlng.lng.toFixed(6);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById('latitud').value = e.latlng.lat.toFixed(6);
            document.getElementById('longitud').value = e.latlng.lng.toFixed(6);
        });

        // Cargar provincias si ya hay un departamento seleccionado
        cargarProvincias();

        // Trigger switchType
        switchType("{{ $propType }}");
    });

    // ═══ CARGAR PROVINCIAS SEGÚN DEPARTAMENTO ═══
    function cargarProvincias() {
        const depto = document.getElementById('departamento').value;
        const selectProv = document.getElementById('provincia');
        const oldProv = "{{ old('provincia', $propiedad->provincia ?? '') }}";

        selectProv.innerHTML = '<option value="">Seleccione provincia / municipio...</option>';

        if (depto && provincias[depto]) {
            provincias[depto].forEach(function(prov) {
                const opt = document.createElement('option');
                opt.value = prov;
                opt.textContent = prov;
                if (opt.value === oldProv) opt.selected = true;
                selectProv.appendChild(opt);
            });
        }
    }

    // ═══ AL ENVIAR EL FORM, SINCRONIZAR TODAS LAS IMÁGENES ACUMULADAS ═══
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('publishForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (imagenesAcumuladas.length > 0) {
                    const dt = new DataTransfer();
                    imagenesAcumuladas.forEach(function(file) {
                        dt.items.add(file);
                    });
                    document.getElementById('imagenesInput').files = dt.files;
                }
            });
        }
    });

    // ═══ SWITCH TYPE ═══
    function switchType(type) {
        document.querySelectorAll('.type-option').forEach(el => el.classList.remove('active'));
        
        let labelId = type === 'terreno' ? 'typeTerrenoLabel' : type === 'lote' ? 'typeLoteLabel' : 'typeAlquilerLabel';
        let activeLabel = document.getElementById(labelId);
        if (activeLabel) activeLabel.classList.add('active');

        const isEdit = {{ isset($editando) ? 'true' : 'false' }};
        const publishForm = document.getElementById('publishForm');

        if (!isEdit) {
            publishForm.action = type === 'alquiler' 
                ? "{{ route('vendedor.alquileres.store') }}"
                : "{{ route('vendedor.terrenos.store') }}";
        }

        // Toggle sections
        document.getElementById('terrenoSection').style.display = type === 'terreno' ? 'block' : 'none';
        document.getElementById('loteSection').style.display = type === 'lote' ? 'block' : 'none';
        document.getElementById('alquilerSection').style.display = type === 'alquiler' ? 'block' : 'none';
        document.getElementById('precioGroup').style.display = type === 'alquiler' ? 'none' : 'block';
        document.getElementById('precioAlquilerGroup').style.display = type === 'alquiler' ? 'block' : 'none';
        document.getElementById('serviciosTerreno').style.display = type === 'alquiler' ? 'none' : 'grid';
        document.getElementById('serviciosAlquiler').style.display = type === 'alquiler' ? 'grid' : 'none';

        document.querySelectorAll('.alquiler-only').forEach(el => {
            el.style.display = type === 'alquiler' ? 'block' : 'none';
        });

        if (type === 'terreno' || type === 'lote') {
            document.getElementById('precioLabel').innerHTML = 'Precio Venta (USD) <span class="required">*</span>';
            document.getElementById('precio').setAttribute('required', 'required');
        } else {
            document.getElementById('precio').removeAttribute('required');
        }

        if (type === 'lote') {
            document.getElementById('parent_id').setAttribute('required', 'required');
        } else {
            document.getElementById('parent_id').removeAttribute('required');
        }

        // Filtrar categorías
        const catSelect = document.getElementById('categoria_id');
        let hasMatch = false;
        for (let i = 0; i < catSelect.options.length; i++) {
            let option = catSelect.options[i];
            let typeAttr = option.getAttribute('data-prop-type');
            if (!typeAttr) continue;
            if (typeAttr === 'todos' || typeAttr === type) {
                option.style.display = 'block';
                if (option.selected) hasMatch = true;
            } else {
                option.style.display = 'none';
                if (option.selected) option.selected = false;
            }
        }
        if (!hasMatch) catSelect.value = '';
    }

    // ═══ COVER SELECTION ═══
    function selectExistingCover(imageId, element) {
        document.querySelectorAll('.gallery-item').forEach(el => el.classList.remove('is-cover'));
        element.classList.add('is-cover');
        document.getElementById('portada_id_input').value = imageId;
        document.getElementById('portada_index_input').value = '-1';
    }

    function deleteImage(route) {
        const form = document.getElementById('deleteImageForm');
        form.action = route;
        form.submit();
    }

    // ═══ CHARACTER COUNTER ═══
    function actualizarContador() {
        const textarea = document.getElementById('descripcion');
        const count = textarea.value.length;
        const msg = document.getElementById('charCountMsg');
        const warning = document.getElementById('charWarning');
        
        msg.textContent = count + ' caracteres';
        
        if (count < 50) {
            warning.style.display = 'inline';
            msg.style.color = '#dc2626';
        } else {
            warning.style.display = 'none';
            msg.style.color = '#059669';
        }
    }

    // ═══ ACUMULADOR DE IMÁGENES (para subir en tandas sin perder las anteriores) ═══
    let imagenesAcumuladas = []; // Array con todos los File objects seleccionados
    let previewCounter = 0;     // Contador único para index de portada

    // ═══ PREVIEW FILES (Subir múltiples imágenes acumulativas + elegir portada) ═══
    function previewFiles(input) {
        const gallery = document.getElementById('imageGallery');
        const noMsg = document.getElementById('noImagesMsg');
        const coverInst = document.getElementById('coverInstruction');
        const countBadge = document.getElementById('imageCountBadge');
        const countText = document.getElementById('imageCountText');

        if (!input.files || input.files.length === 0) return;

        // ⚠️ Limitar a máximo 10 imágenes en total
        if (imagenesAcumuladas.length + input.files.length > 10) {
            const permitidas = 10 - imagenesAcumuladas.length;
            alert('Solo puedes subir máximo 10 imágenes. Puedes agregar ' + permitidas + ' más.');
            if (permitidas <= 0) return;
            // Solo tomar las primeras 'permitidas'
            const dt = new DataTransfer();
            for (let i = 0; i < permitidas; i++) {
                dt.items.add(input.files[i]);
            }
            // Reemplazar files
            Object.defineProperty(input, 'files', { value: dt.files });
            if (input.files.length === 0) return;
        }

        // Acumular los nuevos archivos
        for (let i = 0; i < input.files.length; i++) {
            imagenesAcumuladas.push(input.files[i]);
        }

        // Mostrar/ocultar elementos
        if (noMsg) noMsg.style.display = 'none';
        gallery.style.display = 'grid';

        const total = imagenesAcumuladas.length;
        if (countBadge) {
            countBadge.style.display = 'block';
            countText.textContent = total + ' imagen' + (total !== 1 ? 'es' : '');
        }

        // Mostrar instrucción portada si hay más de 1 imagen
        if (total > 1 && coverInst) coverInst.style.display = 'block';

        // Leer y mostrar solo los NUEVOS archivos (los que vienen en input.files)
        Array.from(input.files).forEach((file) => {
            const currentPreviewIndex = previewCounter;
            previewCounter++;

            const reader = new FileReader();
            reader.onload = function(e) {
                const item = document.createElement('div');
                item.className = 'gallery-item new-preview-item';
                
                // ¿Hay alguna portada ya seleccionada?
                const hasCover = document.querySelectorAll('.gallery-item.is-cover').length > 0;

                // Si no hay portada, la PRIMERA imagen acumulada es portada
                if (!hasCover && imagenesAcumuladas.indexOf(file) === 0) {
                    item.classList.add('is-cover');
                    document.getElementById('portada_index_input').value = String(currentPreviewIndex);
                    document.getElementById('portada_id_input').value = '';
                }

                // Click para seleccionar como portada
                item.onclick = function() {
                    document.querySelectorAll('.gallery-item').forEach(el => el.classList.remove('is-cover'));
                    item.classList.add('is-cover');
                    document.getElementById('portada_index_input').value = String(currentPreviewIndex);
                    document.getElementById('portada_id_input').value = '';
                };

                item.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <span class="cover-badge">⭐ Portada</span>
                `;
                gallery.appendChild(item);
            };
            reader.readAsDataURL(file);
        });

        // Limpiar el input para permitir seleccionar los mismos archivos de nuevo
        input.value = '';
    }
</script>
@endpush