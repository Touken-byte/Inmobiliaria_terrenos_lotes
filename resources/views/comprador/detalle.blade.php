@extends('layouts.comprador')

@section('title', 'Detalles del Terreno | TerrenoSur')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<style>
    :root {
        --void:         #050810;
        --deep:         #080d1a;
        --surface:      #0c1326;
        --card:         #0f1830;
        --card-2:       #0d172e;
        --rim:          rgba(120,160,255,0.10);
        --rim-gold:     rgba(201,168,76,0.22);
        --gold:         #c9a84c;
        --gold-light:   #e8c97a;
        --gold-glow:    rgba(201,168,76,0.15);
        --cobalt:       #3d7ef5;
        --cobalt-soft:  rgba(61,126,245,0.12);
        --emerald:      #1dba7e;
        --wa:           #25d366;
        --wa-dark:      #128c3f;
        --text-1:       #eef2fc;
        --text-2:       #8fa3cc;
        --text-3:       #3d5480;
        --font-serif:   'Cormorant Garamond', Georgia, serif;
        --font-sans:    'Outfit', system-ui, sans-serif;
    }

    body { background: var(--void); }

    .det-wrap {
        min-height: 100vh;
        font-family: var(--font-sans);
        padding-bottom: 6rem;
        color: var(--text-1);
    }

    /* ── Ambient glows ── */
    .det-orb-1 {
        position: fixed;
        top: 0; left: 0;
        width: 600px; height: 600px;
        background: radial-gradient(ellipse at 20% 20%, rgba(61,126,245,0.07) 0%, transparent 65%);
        pointer-events: none;
        z-index: 0;
    }
    .det-orb-2 {
        position: fixed;
        bottom: 0; right: 0;
        width: 500px; height: 500px;
        background: radial-gradient(ellipse at 80% 80%, rgba(201,168,76,0.06) 0%, transparent 65%);
        pointer-events: none;
        z-index: 0;
    }

    .det-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2.5rem 2rem 0;
        position: relative;
        z-index: 1;
    }

    /* ── Back ── */
    .det-back {
        display: inline-flex;
        align-items: center;
        gap: .65rem;
        text-decoration: none;
        color: var(--text-3);
        font-size: .78rem;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: 2.5rem;
        transition: color .2s;
    }
    .det-back:hover { color: var(--text-2); }
    .det-back-circle {
        width: 34px; height: 34px;
        border-radius: 50%;
        background: var(--card);
        border: 1px solid var(--rim);
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem;
        transition: border-color .2s, transform .2s;
    }
    .det-back:hover .det-back-circle {
        border-color: var(--gold);
        transform: translateX(-3px);
    }

    /* ══════════════════════════════════
       HERO IMAGE (FULL BLEED)
    ══════════════════════════════════ */
    .det-hero-img {
        position: relative;
        width: 100%;
        height: min(65vh, 640px);
        border-radius: 24px;
        overflow: hidden;
        background: var(--surface);
        margin-bottom: 2.5rem;
        box-shadow: 0 40px 80px rgba(0,0,0,0.6), 0 0 0 1px var(--rim);
    }
    .det-hero-img img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Cinematic vignette */
    .det-hero-vignette {
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse at 50% 100%, rgba(5,8,16,0.9) 0%, transparent 60%),
            linear-gradient(to top, rgba(5,8,16,0.8) 0%, rgba(5,8,16,0.1) 40%, transparent 60%),
            linear-gradient(to bottom, rgba(5,8,16,0.3) 0%, transparent 30%);
        pointer-events: none;
    }

    /* No image fallback */
    .det-no-img {
        width: 100%; height: 100%;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: 1rem;
        color: var(--text-3);
    }
    .det-no-img i { font-size: 4rem; }
    .det-no-img span { font-size: .65rem; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; }

    /* Badges on hero */
    .det-hero-badges {
        position: absolute;
        top: 1.75rem; left: 1.75rem;
        display: flex;
        gap: .6rem;
        z-index: 2;
    }
    .det-badge-venta {
        padding: .45rem 1.1rem;
        background: var(--emerald);
        border-radius: 100px;
        font-size: .62rem;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #001e0e;
        box-shadow: 0 4px 20px rgba(29,186,126,0.4);
    }
    .det-badge-m2 {
        padding: .45rem 1.1rem;
        background: rgba(5,8,16,0.65);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.13);
        border-radius: 100px;
        font-size: .72rem;
        font-weight: 600;
        color: var(--text-1);
        letter-spacing: .04em;
    }

    /* Price overlay */
    .det-hero-price {
        position: absolute;
        bottom: 2.25rem; left: 2.25rem;
        z-index: 2;
    }
    .det-price-label {
        font-size: .62rem;
        font-weight: 700;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: rgba(232,201,122,0.55);
        margin-bottom: .35rem;
    }
    .det-price-amount {
        font-family: var(--font-serif);
        font-size: clamp(2.8rem, 5vw, 4.5rem);
        font-weight: 700;
        color: var(--gold-light);
        line-height: 1;
        letter-spacing: -.01em;
        text-shadow: 0 4px 24px rgba(0,0,0,0.8);
    }
    .det-price-cur {
        font-size: .9rem;
        font-weight: 500;
        color: rgba(232,201,122,0.45);
        margin-left: .4rem;
        letter-spacing: .08em;
    }

    /* ══════════════════════════════════
       CONTENT GRID
    ══════════════════════════════════ */
    .det-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        align-items: flex-start;
    }
    @media (max-width: 900px) {
        .det-grid { grid-template-columns: 1fr; }
    }

    /* ══ LEFT COLUMN ══ */
    .det-col-main {}

    /* Property header */
    .det-prop-head {
        margin-bottom: 2.5rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid var(--rim);
        position: relative;
    }
    .det-prop-head::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 0;
        width: 100px; height: 1px;
        background: linear-gradient(90deg, var(--gold), transparent);
    }

    .det-prop-eyebrow {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: 1rem;
    }
    .det-prop-eyebrow span {
        font-size: .62rem;
        font-weight: 700;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: var(--text-3);
    }
    .det-prop-eyebrow::before, .det-prop-eyebrow::after {
        content: '';
        flex: 1;
        max-width: 40px;
        height: 1px;
        background: var(--rim);
    }
    .det-prop-eyebrow::before { max-width: 0; flex: 0; }

    .det-prop-title {
        font-family: var(--font-serif);
        font-size: clamp(2rem, 4vw, 3.2rem);
        font-weight: 700;
        color: var(--text-1);
        line-height: 1.1;
        letter-spacing: -.01em;
        margin-bottom: 1rem;
    }

    .det-prop-loc {
        display: flex;
        align-items: flex-start;
        gap: .6rem;
    }
    .det-prop-loc i {
        color: var(--cobalt);
        font-size: .85rem;
        margin-top: .1rem;
        flex-shrink: 0;
    }
    .det-prop-loc p {
        font-size: .9rem;
        color: var(--text-2);
        line-height: 1.5;
        font-weight: 300;
    }

    /* Stats row */
    .det-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 2.5rem;
    }
    @media (max-width: 600px) { .det-stats { grid-template-columns: 1fr 1fr; } }

    .det-stat {
        background: var(--card);
        border: 1px solid var(--rim);
        border-radius: 16px;
        padding: 1.25rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        transition: border-color .2s;
    }
    .det-stat::before {
        content: '';
        position: absolute;
        top: 0; left: 50%; transform: translateX(-50%);
        width: 60%; height: 1px;
        background: linear-gradient(90deg, transparent, var(--gold), transparent);
        opacity: 0;
        transition: opacity .3s;
    }
    .det-stat:hover { border-color: var(--rim-gold); }
    .det-stat:hover::before { opacity: 1; }

    .det-stat-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: var(--cobalt-soft);
        border: 1px solid rgba(61,126,245,0.15);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto .75rem;
        color: var(--cobalt);
        font-size: .95rem;
        transition: background .2s;
    }
    .det-stat:hover .det-stat-icon { background: rgba(61,126,245,0.18); }

    .det-stat-val {
        font-family: var(--font-serif);
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text-1);
        line-height: 1;
        margin-bottom: .25rem;
    }
    .det-stat-label {
        font-size: .62rem;
        font-weight: 600;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--text-3);
    }

    /* Description */
    .det-section-title {
        display: flex;
        align-items: center;
        gap: .75rem;
        font-size: .62rem;
        font-weight: 700;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: var(--text-3);
        margin-bottom: 1.25rem;
    }
    .det-section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--rim);
    }

    .det-desc-block {
        background: var(--card);
        border: 1px solid var(--rim);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .det-desc-text {
        font-size: .95rem;
        color: var(--text-2);
        line-height: 1.85;
        font-weight: 300;
    }

    /* Features grid */
    .det-features {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: .75rem;
        margin-bottom: 2rem;
    }
    .det-feature {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .85rem 1.1rem;
        background: var(--card);
        border: 1px solid var(--rim);
        border-radius: 14px;
    }
    .det-feature-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--gold);
        flex-shrink: 0;
        box-shadow: 0 0 8px var(--gold-glow);
    }
    .det-feature-text {
        font-size: .8rem;
        font-weight: 500;
        color: var(--text-2);
    }

    /* ══ RIGHT COLUMN (Sticky card) ══ */
    .det-col-aside { position: sticky; top: calc(68px + 2rem); }

    .det-contact-card {
        background: var(--card);
        border: 1px solid var(--rim);
        border-radius: 24px;
        overflow: hidden;
    }

    /* Card top accent */
    .det-contact-card::before {
        content: '';
        display: block;
        height: 3px;
        background: linear-gradient(90deg, transparent 0%, var(--gold) 30%, var(--gold-light) 50%, var(--gold) 70%, transparent 100%);
    }

    .det-contact-head {
        padding: 1.75rem 2rem 1.5rem;
        border-bottom: 1px solid var(--rim);
    }
    .det-contact-eyebrow {
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: .6rem;
    }
    .det-contact-price-box {}
    .det-contact-price {
        font-family: var(--font-serif);
        font-size: 2.6rem;
        font-weight: 700;
        color: var(--text-1);
        line-height: 1;
        letter-spacing: -.02em;
    }
    .det-contact-cur {
        font-size: .8rem;
        color: var(--text-3);
        font-weight: 500;
        letter-spacing: .08em;
        margin-top: .2rem;
    }

    /* Stats mini */
    .det-contact-stats {
        padding: 1.25rem 2rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        border-bottom: 1px solid var(--rim);
    }
    .det-cs-item {}
    .det-cs-label {
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--text-3);
        margin-bottom: .3rem;
    }
    .det-cs-value {
        font-family: var(--font-serif);
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-1);
    }

    /* CTA section */
    .det-contact-cta {
        padding: 1.75rem 2rem;
    }

    .det-wa-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .85rem;
        width: 100%;
        padding: 1.1rem;
        border-radius: 16px;
        border: none;
        background: linear-gradient(135deg, #1a7a41 0%, #0e5c2e 100%);
        color: #e8fff2;
        font-family: var(--font-sans);
        font-size: .95rem;
        font-weight: 700;
        letter-spacing: .02em;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: transform .2s, box-shadow .3s, filter .2s;
        box-shadow: 0 8px 28px rgba(37,211,102,0.2);
        text-decoration: none;
    }
    .det-wa-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.07) 0%, transparent 55%);
        pointer-events: none;
    }
    .det-wa-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 40px rgba(37,211,102,0.32);
        filter: brightness(1.06);
    }
    .det-wa-btn:active { transform: translateY(0); }
    .det-wa-btn i { font-size: 1.3rem; }
    .det-wa-btn .btn-sub { font-size: .7rem; font-weight: 400; opacity: .7; display: block; line-height: 1; }

    .det-wa-note {
        text-align: center;
        font-size: .68rem;
        color: var(--text-3);
        margin-top: .85rem;
        letter-spacing: .04em;
    }

    /* Divider */
    .det-divider {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: 1.25rem 0;
    }
    .det-divider::before, .det-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--rim);
    }
    .det-divider span {
        font-size: .6rem;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--text-3);
        font-weight: 600;
        white-space: nowrap;
    }

    /* Share / save row */
    .det-action-row {
        display: flex;
        gap: .75rem;
    }
    .det-action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .7rem;
        border: 1px solid var(--rim);
        border-radius: 12px;
        background: transparent;
        color: var(--text-2);
        font-family: var(--font-sans);
        font-size: .75rem;
        font-weight: 600;
        cursor: pointer;
        transition: border-color .2s, background .2s, color .2s;
        text-decoration: none;
    }
    .det-action-btn:hover {
        border-color: var(--rim-gold);
        background: var(--gold-glow);
        color: var(--gold-light);
    }

    /* Trust signals */
    .det-trust {
        padding: 1.25rem 2rem;
        border-top: 1px solid var(--rim);
        display: flex;
        flex-direction: column;
        gap: .7rem;
    }
    .det-trust-item {
        display: flex;
        align-items: center;
        gap: .75rem;
        font-size: .78rem;
        color: var(--text-3);
    }
    .det-trust-item i { color: var(--gold); font-size: .8rem; width: 14px; flex-shrink: 0; }
</style>

<div class="det-wrap">

    <!-- Ambient -->
    <div class="det-orb-1"></div>
    <div class="det-orb-2"></div>

    <div class="det-inner">

        <!-- Back link -->
        <a href="{{ route('catalogo.terrenos') }}" class="det-back">
            <span class="det-back-circle"><i class="fa-solid fa-arrow-left"></i></span>
            Volver al catálogo
        </a>

        <!-- ── HERO IMAGE ── -->
        <div class="det-hero-img">
            @if($terreno->imagenes->count() > 0)
                <img src="{{ asset($terreno->imagenes->first()->ruta_archivo) }}" alt="Terreno en {{ $terreno->ubicacion }}">
            @else
                <div class="det-no-img">
                    <i class="fa-regular fa-images"></i>
                    <span>Sin fotografía disponible</span>
                </div>
            @endif

            <div class="det-hero-vignette"></div>

            <div class="det-hero-badges">
                <span class="det-badge-venta">En Venta</span>
                <span class="det-badge-m2">{{ number_format($terreno->metros_cuadrados, 0) }} m²</span>
            </div>

            <div class="det-hero-price">
                <p class="det-price-label">Precio de Venta</p>
                <div>
                    <span class="det-price-amount">${{ number_format($terreno->precio, 0) }}</span>
                    <span class="det-price-cur">USD</span>
                </div>
            </div>
        </div>

        <!-- ── CONTENT GRID ── -->
        <div class="det-grid">

            <!-- LEFT -->
            <div class="det-col-main">

                <!-- Property header -->
                <div class="det-prop-head">
                    <div class="det-prop-eyebrow">
                        <span>Propiedad en venta</span>
                    </div>
                    <h1 class="det-prop-title">
                        Lote en {{ Str::words($terreno->ubicacion, 5, '') }}
                    </h1>
                    <div class="det-prop-loc">
                        <i class="fa-solid fa-location-dot"></i>
                        <p>{{ $terreno->ubicacion }}</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="det-stats">
                    <div class="det-stat">
                        <div class="det-stat-icon"><i class="fa-solid fa-vector-square"></i></div>
                        <div class="det-stat-val">{{ number_format($terreno->metros_cuadrados, 0) }}</div>
                        <div class="det-stat-label">Metros² totales</div>
                    </div>
                    <div class="det-stat">
                        <div class="det-stat-icon"><i class="fa-solid fa-city"></i></div>
                        <div class="det-stat-val">Urbano</div>
                        <div class="det-stat-label">Tipo de terreno</div>
                    </div>
                    <div class="det-stat">
                        <div class="det-stat-icon"><i class="fa-solid fa-tag"></i></div>
                        <div class="det-stat-val">Venta</div>
                        <div class="det-stat-label">Modalidad</div>
                    </div>
                </div>

                <!-- Description -->
                <p class="det-section-title">Descripción del lugar</p>
                <div class="det-desc-block">
                    <p class="det-desc-text">{{ $terreno->descripcion }}</p>
                </div>

                <!-- Features -->
                <p class="det-section-title">Características</p>
                <div class="det-features">
                    <div class="det-feature">
                        <div class="det-feature-dot"></div>
                        <span class="det-feature-text">{{ number_format($terreno->metros_cuadrados, 0) }} m² de superficie</span>
                    </div>
                    <div class="det-feature">
                        <div class="det-feature-dot"></div>
                        <span class="det-feature-text">Terreno Urbano</span>
                    </div>
                    <div class="det-feature">
                        <div class="det-feature-dot"></div>
                        <span class="det-feature-text">Disponible de inmediato</span>
                    </div>
                    <div class="det-feature">
                        <div class="det-feature-dot"></div>
                        <span class="det-feature-text">
                            {{ $terreno->folio ? '✅ Con folio registrado' : '⚠️ Sin folio registrado' }}
                        </span>
                    </div>
                </div>

                <!-- Sección de Folio -->
                <p class="det-section-title">Documentación Legal</p>

                @if($terreno->folio)
                <div style="background: var(--card); border: 1px solid var(--rim); border-radius: 20px; overflow: hidden; margin-bottom: 2rem;">

                    {{-- Header del folio --}}
                    <div style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--rim); display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; align-items:center; gap:.75rem;">
                            <div style="width:38px; height:38px; border-radius:10px; background:rgba(29,186,126,0.15); border:1px solid rgba(29,186,126,0.25); display:flex; align-items:center; justify-content:center; font-size:1rem;">
                                📄
                            </div>
                            <div>
                                <p style="margin:0; font-size:.62rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color: var(--text-3);">Folio Real</p>
                                <p style="margin:0; font-size:1rem; font-weight:700; color: var(--text-1);">{{ $terreno->folio->numero_folio }}</p>
                            </div>
                        </div>
                        <span style="padding:.35rem .9rem; background:rgba(29,186,126,0.15); border:1px solid rgba(29,186,126,0.3); border-radius:100px; font-size:.65rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#1dba7e;">
                            ✅ Registrado
                        </span>
                    </div>

                    {{-- Datos básicos siempre visibles --}}
                    <div style="padding: 1.25rem 1.75rem; display:grid; grid-template-columns:1fr 1fr; gap:1rem; border-bottom: 1px solid var(--rim);">
                        <div>
                            <p style="margin:0 0 .25rem; font-size:.6rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--text-3);">Superficie</p>
                            <p style="margin:0; font-size:1rem; font-weight:600; color:var(--text-1);">{{ number_format($terreno->folio->superficie, 2) }} m²</p>
                        </div>
                        <div>
                            <p style="margin:0 0 .25rem; font-size:.6rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--text-3);">Ubicación Registrada</p>
                            <p style="margin:0; font-size:.88rem; color:var(--text-2); line-height:1.4;">{{ $terreno->folio->ubicacion }}</p>
                        </div>
                        @if($terreno->folio->colindancias)
                        <div style="grid-column: span 2;">
                            <p style="margin:0 0 .25rem; font-size:.6rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--text-3);">Colindancias</p>
                            <p style="margin:0; font-size:.88rem; color:var(--text-2); line-height:1.5;">{{ $terreno->folio->colindancias }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Botones de consulta --}}
                    <div style="padding: 1.25rem 1.75rem; display:flex; gap:.75rem; flex-wrap:wrap;">
                        @auth
                            <a href="{{ route('folio.consultar.form') }}"
                               style="display:inline-flex; align-items:center; gap:.5rem; padding:.65rem 1.25rem; background:rgba(61,126,245,0.12); border:1px solid rgba(61,126,245,0.25); border-radius:12px; color:#3d7ef5; font-size:.82rem; font-weight:600; text-decoration:none; transition: all .2s;"
                               onmouseover="this.style.background='rgba(61,126,245,0.2)'"
                               onmouseout="this.style.background='rgba(61,126,245,0.12)'">
                                🔍 Información Rápida
                            </a>
                            <a href="{{ route('folio.completo', $terreno->folio->id) }}"
                               style="display:inline-flex; align-items:center; gap:.5rem; padding:.65rem 1.25rem; background:rgba(201,168,76,0.12); border:1px solid rgba(201,168,76,0.25); border-radius:12px; color:#c9a84c; font-size:.82rem; font-weight:600; text-decoration:none; transition: all .2s;"
                               onmouseover="this.style.background='rgba(201,168,76,0.2)'"
                               onmouseout="this.style.background='rgba(201,168,76,0.12)'">
                                📋 Ver Folio Completo
                            </a>
                        @else
                            <p style="margin:0; font-size:.82rem; color:var(--text-3);">
                                🔒 <a href="{{ route('login') }}" style="color:#3d7ef5;">Inicia sesión</a> para consultar el folio completo de este terreno.
                            </p>
                        @endauth
                    </div>

                </div>

                @else

                <div style="background: var(--card); border: 1px solid var(--rim); border-radius: 20px; padding: 1.75rem; margin-bottom: 2rem; display:flex; align-items:flex-start; gap:1rem;">
                    <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,193,7,0.12); border:1px solid rgba(255,193,7,0.2); display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0;">
                        ⚠️
                    </div>
                    <div>
                        <p style="margin:0 0 .4rem; font-size:.95rem; font-weight:600; color:var(--text-1);">Sin folio registrado</p>
                        <p style="margin:0; font-size:.85rem; color:var(--text-2); line-height:1.6;">
                            Este terreno aún no tiene datos de folio registrados en el sistema.
                            Puedes contactar al vendedor para solicitar la documentación legal antes de proceder con la compra.
                        </p>
                    </div>
                </div>

                @endif

                {{-- Mapa de ubicación --}}
                @if($terreno->latitud && $terreno->longitud)
                <p class="det-section-title">Ubicación en el Mapa</p>
                <div id="mapaDetalle" style="height:320px; border-radius:20px; overflow:hidden; border:1px solid var(--rim); margin-bottom:2rem;"></div>
                @endif

            </div>

            <!-- RIGHT (sticky card) -->
            <aside class="det-col-aside">
                <div class="det-contact-card">

                    <div class="det-contact-head">
                        <p class="det-contact-eyebrow">Precio de Venta</p>
                        <div class="det-contact-price-box">
                            <div class="det-contact-price">${{ number_format($terreno->precio, 0) }}</div>
                            <div class="det-contact-cur">Dólares Americanos (USD)</div>
                        </div>
                    </div>

                    <div class="det-contact-stats">
                        <div class="det-cs-item">
                            <div class="det-cs-label">Superficie</div>
                            <div class="det-cs-value">{{ number_format($terreno->metros_cuadrados, 0) }} m²</div>
                        </div>
                        <div class="det-cs-item">
                            <div class="det-cs-label">Tipo</div>
                            <div class="det-cs-value">Urbano</div>
                        </div>
                    </div>

                    <div class="det-contact-cta">
                        <button class="det-wa-btn">
                            <i class="fa-brands fa-whatsapp"></i>
                            <div>
                                <span>Contactar Inmobiliaria</span>
                                <span class="btn-sub">Respuesta en minutos</span>
                            </div>
                        </button>
                        <p class="det-wa-note">* Funcionalidad de contacto próximamente</p>

                        <div class="det-divider"><span>o también</span></div>

                        <div class="det-action-row">
                            <button class="det-action-btn">
                                <i class="fa-regular fa-bookmark"></i>
                                Guardar
                            </button>
                            <button class="det-action-btn">
                                <i class="fa-solid fa-share-nodes"></i>
                                Compartir
                            </button>
                        </div>
                    </div>

                    <div class="det-trust">
                        <div class="det-trust-item">
                            <i class="fa-solid fa-shield-halved"></i>
                            Propiedad verificada por TerrenoSur
                        </div>
                        <div class="det-trust-item">
                            <i class="fa-solid fa-file-contract"></i>
                            Documentación revisada y en regla
                        </div>
                        <div class="det-trust-item">
                            <i class="fa-solid fa-lock"></i>
                            Transacción segura garantizada
                        </div>
                    </div>

                </div>
            </aside>

        </div>

    </div>
</div>
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@if($terreno->latitud && $terreno->longitud)
<script>
document.addEventListener('DOMContentLoaded', function() {
    var lat = {{ $terreno->latitud }};
    var lng = {{ $terreno->longitud }};

    var mapaDetalle = L.map('mapaDetalle', { zoomControl: true }).setView([lat, lng], 15);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OSM &copy; CartoDB',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(mapaDetalle);

    var icon = L.divIcon({
        html: '<div style="width:18px;height:18px;background:#c9a84c;border:3px solid #fff;border-radius:50%;box-shadow:0 0 12px rgba(201,168,76,0.6);"></div>',
        iconSize: [18, 18],
        iconAnchor: [9, 9],
        className: ''
    });

    L.marker([lat, lng], { icon: icon })
        .addTo(mapaDetalle)
        .bindPopup('<strong style="color:#111;">{{ addslashes($terreno->ubicacion) }}</strong>')
        .openPopup();
});
</script>
@endif
@endpush
@endsection
