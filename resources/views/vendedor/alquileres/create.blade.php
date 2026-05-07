@extends('layouts.app')

@section('title', 'Publicar Habitación')

@section('content')
<style>
    :root {
        --void:       #050810;
        --surface:    #0c1326;
        --card:       #0f1830;
        --card-h:     #111c35;
        --rim:        rgba(120,160,255,0.10);
        --amber:      #f59e0b;
        --amber-soft: rgba(245,158,11,0.10);
        --amber-glow: rgba(245,158,11,0.20);
        --cobalt:     #3d7ef5;
        --emerald:    #1dba7e;
        --text-1:     #eef2fc;
        --text-2:     #8fa3cc;
        --text-3:     #3d5480;
        --font-serif: 'Cormorant Garamond', Georgia, serif;
        --font-sans:  'Outfit', system-ui, sans-serif;
    }

    .pub-wrap {
        max-width: 860px;
        margin: 2.5rem auto;
        padding: 0 1.5rem 5rem;
        font-family: var(--font-sans);
        color: var(--text-1);
    }

    /* ── Header ── */
    .pub-header {
        margin-bottom: 2.5rem;
        padding-bottom: 1.75rem;
        border-bottom: 1px solid var(--rim);
        position: relative;
    }
    .pub-header::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 0;
        width: 80px; height: 1px;
        background: var(--amber);
    }
    .pub-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--amber);
        margin-bottom: .75rem;
    }
    .pub-eyebrow::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--amber);
        box-shadow: 0 0 8px var(--amber);
        animation: pulse-dot 2s ease-in-out infinite;
    }
    @keyframes pulse-dot {
        0%,100% { opacity:1; transform:scale(1); }
        50%      { opacity:.4; transform:scale(.6); }
    }
    .pub-title {
        font-family: var(--font-serif);
        font-size: clamp(1.8rem, 4vw, 2.8rem);
        font-weight: 700;
        line-height: 1.1;
        color: var(--text-1);
    }
    .pub-title em { font-style: italic; color: #e8c97a; }
    .pub-sub {
        margin-top: .5rem;
        font-size: .88rem;
        color: var(--text-2);
        font-weight: 300;
    }

    /* ── Error alert ── */
    .pub-errors {
        background: rgba(239,68,68,0.08);
        border: 1px solid rgba(239,68,68,0.25);
        border-radius: 14px;
        padding: 1rem 1.25rem;
        margin-bottom: 2rem;
        font-size: .85rem;
        color: #fca5a5;
    }
    .pub-errors ul { margin: 0; padding-left: 1.25rem; }

    /* ── Card sections ── */
    .pub-section {
        background: var(--card);
        border: 1px solid var(--rim);
        border-radius: 20px;
        padding: 1.75rem;
        margin-bottom: 1.25rem;
        position: relative;
        overflow: hidden;
        transition: border-color .2s;
    }
    .pub-section:focus-within { border-color: rgba(245,158,11,0.3); }
    .pub-section::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(245,158,11,0.3), transparent);
        opacity: 0;
        transition: opacity .3s;
    }
    .pub-section:focus-within::before { opacity: 1; }

    .pub-section-label {
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--text-3);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: .6rem;
    }
    .pub-section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--rim);
    }
    .pub-section-label i { color: var(--amber); font-size: .75rem; }

    /* ── Grid helpers ── */
    .pub-row { display: grid; gap: 1rem; }
    .pub-row-2 { grid-template-columns: 1fr 1fr; }
    .pub-row-3 { grid-template-columns: 1fr 1fr 1fr; }
    .pub-row-84 { grid-template-columns: 2fr 1fr; }
    @media (max-width: 600px) {
        .pub-row-2, .pub-row-3, .pub-row-84 { grid-template-columns: 1fr; }
    }

    /* ── Form fields ── */
    .pub-field { display: flex; flex-direction: column; gap: .5rem; }

    .pub-label {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--text-2);
    }

    .pub-input,
    .pub-textarea,
    .pub-select {
        background: var(--surface);
        border: 1px solid var(--rim);
        border-radius: 12px;
        padding: .75rem 1rem;
        color: var(--text-1);
        font-family: var(--font-sans);
        font-size: .9rem;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        width: 100%;
        box-sizing: border-box;
    }
    .pub-input::placeholder,
    .pub-textarea::placeholder { color: var(--text-3); font-size: .85rem; }
    .pub-input:focus,
    .pub-textarea:focus,
    .pub-select:focus {
        border-color: var(--amber);
        box-shadow: 0 0 0 3px var(--amber-soft);
    }
    .pub-textarea { resize: vertical; min-height: 110px; }

    /* Number input with icon prefix */
    .pub-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .pub-input-wrap .pub-input-prefix {
        position: absolute;
        left: 1rem;
        color: var(--amber);
        font-size: .85rem;
        font-weight: 700;
        pointer-events: none;
    }
    .pub-input-wrap .pub-input { padding-left: 2rem; }

    /* ── Servicios checkboxes ── */
    .pub-checks {
        display: flex;
        flex-wrap: wrap;
        gap: .6rem;
    }
    .pub-check-label {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem 1rem;
        background: var(--surface);
        border: 1px solid var(--rim);
        border-radius: 100px;
        font-size: .8rem;
        color: var(--text-2);
        cursor: pointer;
        transition: border-color .2s, background .2s, color .2s;
        user-select: none;
    }
    .pub-check-label input { display: none; }
    .pub-check-label .check-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--text-3);
        flex-shrink: 0;
        transition: background .2s, box-shadow .2s;
    }
    .pub-check-label:has(input:checked) {
        border-color: var(--amber);
        background: var(--amber-soft);
        color: var(--amber);
    }
    .pub-check-label:has(input:checked) .check-dot {
        background: var(--amber);
        box-shadow: 0 0 6px var(--amber);
    }

    /* ── File upload ── */
    .pub-file-wrap {
        position: relative;
        border: 2px dashed var(--rim);
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        background: var(--surface);
    }
    .pub-file-wrap:hover {
        border-color: rgba(245,158,11,0.4);
        background: var(--amber-soft);
    }
    .pub-file-wrap input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .pub-file-icon { font-size: 2rem; margin-bottom: .5rem; }
    .pub-file-text { font-size: .88rem; color: var(--text-2); }
    .pub-file-hint { font-size: .72rem; color: var(--text-3); margin-top: .35rem; }
    .pub-file-preview {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        margin-top: 1rem;
        justify-content: center;
    }
    .pub-file-preview img {
        width: 70px; height: 70px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid var(--rim);
    }

    /* ── Actions ── */
    .pub-actions {
        display: flex;
        justify-content: flex-end;
        gap: .75rem;
        margin-top: 1.75rem;
    }
    .pub-btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .75rem 1.5rem;
        border: 1px solid var(--rim);
        border-radius: 12px;
        background: transparent;
        color: var(--text-2);
        font-family: var(--font-sans);
        font-size: .85rem;
        font-weight: 600;
        text-decoration: none;
        transition: border-color .2s, color .2s;
    }
    .pub-btn-cancel:hover { border-color: var(--text-2); color: var(--text-1); }

    .pub-btn-submit {
        display: inline-flex;
        align-items: center;
        gap: .6rem;
        padding: .75rem 2rem;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--amber) 0%, #b45309 100%);
        color: #1a0800;
        font-family: var(--font-sans);
        font-size: .9rem;
        font-weight: 700;
        letter-spacing: .04em;
        cursor: pointer;
        transition: filter .2s, transform .2s, box-shadow .2s;
        box-shadow: 0 6px 20px rgba(245,158,11,0.25);
    }
    .pub-btn-submit:hover {
        filter: brightness(1.08);
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(245,158,11,0.35);
    }
    .pub-btn-submit:active { transform: translateY(0); }
</style>

<div class="pub-wrap">

    {{-- Header --}}
    <div class="pub-header">
        <div class="pub-eyebrow">Nueva publicación</div>
        <h1 class="pub-title">Publicar <em>habitación</em><br>para alquiler</h1>
        <p class="pub-sub">Completa los datos para publicar tu propiedad en el catálogo.</p>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="pub-errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vendedor.alquileres.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Sección 1: Info básica --}}
        <div class="pub-section">
            <p class="pub-section-label"><i class="fa-solid fa-tag"></i> Información básica</p>
            <div class="pub-row pub-row-84" style="margin-bottom:1rem;">
                <div class="pub-field">
                    <label class="pub-label" for="titulo">Título de la publicación</label>
                    <input class="pub-input" type="text" id="titulo" name="titulo"
                           value="{{ old('titulo') }}" required
                           placeholder="Ej: Habitación amoblada cerca de la universidad">
                </div>
                <div class="pub-field">
                    <label class="pub-label" for="precio_mensual">Precio mensual (Bs.)</label>
                    <div class="pub-input-wrap">
                        <span class="pub-input-prefix">Bs.</span>
                        <input class="pub-input" type="number" step="0.01" id="precio_mensual"
                               name="precio_mensual" value="{{ old('precio_mensual') }}" required
                               placeholder="350">
                    </div>
                </div>
            </div>
            <div class="pub-field">
                <label class="pub-label" for="ubicacion">Ubicación / Dirección</label>
                <input class="pub-input" type="text" id="ubicacion" name="ubicacion"
                       value="{{ old('ubicacion') }}" required
                       placeholder="Ej: Av. Principal #123, Zona Sur">
            </div>
        </div>

        {{-- Sección 2: Detalles --}}
        <div class="pub-section">
            <p class="pub-section-label"><i class="fa-solid fa-ruler-combined"></i> Detalles del inmueble</p>
            <div class="pub-row pub-row-3">
                <div class="pub-field">
                    <label class="pub-label" for="metros_cuadrados">Área (m²)</label>
                    <input class="pub-input" type="number" step="0.01" id="metros_cuadrados"
                           name="metros_cuadrados" value="{{ old('metros_cuadrados') }}"
                           placeholder="20">
                </div>
                <div class="pub-field">
                    <label class="pub-label" for="habitaciones">Habitaciones</label>
                    <input class="pub-input" type="number" id="habitaciones" name="habitaciones"
                           value="{{ old('habitaciones', 1) }}" required min="1">
                </div>
                <div class="pub-field">
                    <label class="pub-label" for="banos">Baños</label>
                    <input class="pub-input" type="number" id="banos" name="banos"
                           value="{{ old('banos', 1) }}" required min="1">
                </div>
            </div>
        </div>

        {{-- Sección 3: Descripción --}}
        <div class="pub-section">
            <p class="pub-section-label"><i class="fa-solid fa-align-left"></i> Descripción</p>
            <div class="pub-field">
                <label class="pub-label" for="descripcion">Descripción detallada</label>
                <textarea class="pub-textarea" id="descripcion" name="descripcion" required
                          placeholder="Describe las comodidades, reglas de convivencia, accesos, etc.">{{ old('descripcion') }}</textarea>
            </div>
        </div>

        {{-- Sección 4: Servicios y disponibilidad --}}
        <div class="pub-section">
            <p class="pub-section-label"><i class="fa-solid fa-bolt"></i> Servicios y disponibilidad</p>
            <div class="pub-row pub-row-2">
                <div class="pub-field">
                    <label class="pub-label">Servicios incluidos</label>
                    <div class="pub-checks">
                        <label class="pub-check-label">
                            <input type="checkbox" name="servicios_incluidos[]" value="Agua">
                            <span class="check-dot"></span> 💧 Agua
                        </label>
                        <label class="pub-check-label">
                            <input type="checkbox" name="servicios_incluidos[]" value="Luz">
                            <span class="check-dot"></span> ⚡ Luz
                        </label>
                        <label class="pub-check-label">
                            <input type="checkbox" name="servicios_incluidos[]" value="Internet">
                            <span class="check-dot"></span> 📶 Wi-Fi
                        </label>
                        <label class="pub-check-label">
                            <input type="checkbox" name="servicios_incluidos[]" value="Gas">
                            <span class="check-dot"></span> 🔥 Gas
                        </label>
                        <label class="pub-check-label">
                            <input type="checkbox" name="servicios_incluidos[]" value="Cable">
                            <span class="check-dot"></span> 📺 Cable
                        </label>
                    </div>
                </div>
                <div class="pub-field">
                    <label class="pub-label" for="disponible_desde">Disponible desde</label>
                    <input class="pub-input" type="date" id="disponible_desde" name="disponible_desde"
                           value="{{ old('disponible_desde', date('Y-m-d')) }}" required>
                </div>
            </div>
        </div>

        {{-- Sección 5: Imágenes --}}
        <div class="pub-section">
            <p class="pub-section-label"><i class="fa-solid fa-images"></i> Fotografías</p>
            <div class="pub-file-wrap" id="dropZone">
                <input type="file" id="imagenes" name="imagenes[]" multiple accept="image/*"
                       onchange="previewImages(this)">
                <div class="pub-file-icon">🖼️</div>
                <div class="pub-file-text">Arrastrá tus fotos aquí o hacé clic para seleccionar</div>
                <div class="pub-file-hint">JPG, PNG, JPEG · Máx 5 imágenes · 5MB por imagen</div>
                <div class="pub-file-preview" id="imagePreview"></div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="pub-actions">
            <a href="{{ route('vendedor.alquileres.index') }}" class="pub-btn-cancel">
                <i class="fa-solid fa-xmark"></i> Cancelar
            </a>
            <button type="submit" class="pub-btn-submit">
                <i class="fa-solid fa-paper-plane"></i> Publicar habitación
            </button>
        </div>

    </form>
</div>

<script>
function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    const files = Array.from(input.files).slice(0, 5);
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endsection