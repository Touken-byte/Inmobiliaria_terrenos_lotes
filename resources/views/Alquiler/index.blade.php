@extends('layouts.comprador')

@section('title', 'Alquileres | TerrenoSur')

@section('content')
<style>
    :root {
        --void:         #050810;
        --deep:         #080d1a;
        --surface:      #0c1326;
        --card:         #0f1830;
        --card-h:       #111c35;
        --rim:          rgba(120,160,255,0.10);
        --rim-h:        rgba(120,160,255,0.26);
        --gold:         #c9a84c;
        --gold-light:   #e8c97a;
        --gold-glow:    rgba(201,168,76,0.15);
        --cobalt:       #3d7ef5;
        --cobalt-soft:  rgba(61,126,245,0.12);
        --emerald:      #1dba7e;
        --amber:        #f59e0b;
        --amber-soft:   rgba(245,158,11,0.12);
        --amber-glow:   rgba(245,158,11,0.15);
        --text-1:       #eef2fc;
        --text-2:       #8fa3cc;
        --text-3:       #3d5480;
        --font-serif:   'Cormorant Garamond', Georgia, serif;
        --font-sans:    'Outfit', system-ui, sans-serif;
    }

    body { background: var(--void); }

    .cat-wrap { min-height: 100vh; font-family: var(--font-sans); padding-bottom: 6rem; }

    /* ══ HERO ══ */
    .cat-hero {
        position: relative;
        padding: 5rem 2rem 4rem;
        text-align: center;
        overflow: hidden;
    }
    .cat-hero::before {
        content: '';
        position: absolute;
        top: -120px; left: 50%;
        transform: translateX(-50%);
        width: 900px; height: 500px;
        background: radial-gradient(ellipse at 50% 0%,
            rgba(245,158,11,0.10) 0%,
            rgba(201,168,76,0.04) 50%,
            transparent 70%
        );
        pointer-events: none;
    }
    .cat-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .6rem;
        padding: .4rem 1.1rem;
        border: 1px solid rgba(245,158,11,0.3);
        border-radius: 100px;
        background: rgba(245,158,11,0.06);
        font-size: .65rem;
        font-weight: 600;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: var(--amber);
        margin-bottom: 1.75rem;
    }
    .cat-hero-eyebrow::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--amber);
        flex-shrink: 0;
        box-shadow: 0 0 8px rgba(245,158,11,0.8);
        animation: pulse-dot 2s ease-in-out infinite;
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .5; transform: scale(.7); }
    }
    .cat-hero-title {
        font-family: var(--font-serif);
        font-size: clamp(2.8rem, 6vw, 5rem);
        font-weight: 700;
        color: var(--text-1);
        line-height: 1.05;
        letter-spacing: -.01em;
        margin-bottom: 1rem;
    }
    .cat-hero-title em { font-style: italic; color: var(--gold-light); }
    .cat-hero-sub {
        font-size: 1rem;
        color: var(--text-2);
        font-weight: 300;
        max-width: 520px;
        margin: 0 auto 3rem;
        line-height: 1.7;
    }

    /* ══ SEARCH BAR ══ */
    .cat-search-wrap {
        position: sticky;
        top: 68px;
        z-index: 80;
        padding: 1rem 2rem;
        background: rgba(5,8,16,0.9);
        backdrop-filter: blur(20px) saturate(1.4);
        border-bottom: 1px solid var(--rim);
    }
    .cat-search-form { max-width: 780px; margin: 0 auto; }
    .cat-search-inner {
        display: flex;
        align-items: center;
        height: 56px;
        background: var(--card);
        border: 1px solid var(--rim);
        border-radius: 16px;
        overflow: hidden;
        transition: border-color .25s, box-shadow .25s;
    }
    .cat-search-inner:focus-within {
        border-color: var(--amber);
        box-shadow: 0 0 0 3px var(--amber-glow), 0 8px 32px rgba(0,0,0,0.4);
    }
    .cat-search-icon { padding: 0 1.1rem 0 1.5rem; color: var(--text-3); font-size: .9rem; flex-shrink: 0; pointer-events: none; }
    .cat-search-input { flex: 1; background: transparent; border: none; outline: none; color: var(--text-1); font-family: var(--font-sans); font-size: .95rem; }
    .cat-search-input::placeholder { color: var(--text-3); }
    .cat-search-btn {
        height: 100%;
        padding: 0 1.75rem;
        background: linear-gradient(135deg, var(--amber) 0%, #b45309 100%);
        border: none;
        color: #1a0800;
        font-family: var(--font-sans);
        font-size: .82rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: .5rem;
        flex-shrink: 0;
        transition: filter .2s;
    }
    .cat-search-btn:hover { filter: brightness(1.1); }

    /* ══ MAIN LAYOUT ══ */
    .cat-main {
        max-width: 1360px;
        margin: 0 auto;
        padding: 3rem 2rem 0;
        display: flex;
        gap: 2.5rem;
        align-items: flex-start;
    }

    /* ══ SIDEBAR ══ */
    .cat-aside { width: 290px; flex-shrink: 0; position: sticky; top: calc(68px + 74px); }

    .cat-map-preview {
        position: relative;
        height: 190px;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 1.25rem;
        background: var(--card);
        border: 1px solid var(--rim);
        cursor: not-allowed;
    }
    .cat-map-preview::before {
        content: ''; position: absolute; inset: 0;
        background:
            radial-gradient(ellipse at 30% 70%, rgba(245,158,11,0.12) 0%, transparent 60%),
            radial-gradient(ellipse at 80% 20%, rgba(29,186,126,0.06) 0%, transparent 50%);
    }
    .cat-map-grid {
        position: absolute; inset: 0;
        background-image:
            linear-gradient(rgba(245,158,11,0.07) 1px, transparent 1px),
            linear-gradient(90deg, rgba(245,158,11,0.07) 1px, transparent 1px);
        background-size: 32px 32px;
    }
    .cat-map-roads { position: absolute; inset: 0; }
    .cat-map-roads::before { content: ''; position: absolute; top: 60%; left: 0; right: 0; height: 2px; background: rgba(245,158,11,0.14); }
    .cat-map-roads::after  { content: ''; position: absolute; left: 35%; top: 0; bottom: 0; width: 2px; background: rgba(245,158,11,0.10); }
    .cat-map-pin {
        position: absolute; top: 50%; left: 50%;
        width: 32px; height: 32px;
        background: var(--amber);
        border-radius: 50% 50% 50% 0;
        transform: translate(-50%, -50%) rotate(-45deg);
        box-shadow: 0 0 0 6px rgba(245,158,11,0.15), 0 4px 16px rgba(245,158,11,0.4);
    }
    .cat-map-pin::after { content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 10px; height: 10px; background: white; border-radius: 50%; }
    .cat-map-overlay { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; padding-bottom: 1.25rem; background: linear-gradient(to top, rgba(5,8,16,0.8) 0%, transparent 60%); }
    .cat-map-label { font-size: .62rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--text-3); }

    .cat-filter { background: var(--card); border: 1px solid var(--rim); border-radius: 20px; overflow: hidden; }
    .cat-filter-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--rim); display: flex; align-items: center; justify-content: space-between; }
    .cat-filter-head h3 { font-family: var(--font-serif); font-size: 1.2rem; font-weight: 700; color: var(--text-1); }
    .cat-filter-badge { font-size: .6rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; padding: .3rem .65rem; border-radius: 100px; background: var(--amber-soft); color: var(--amber); border: 1px solid rgba(245,158,11,0.25); }
    .cat-filter-body { padding: 1.5rem; opacity: .5; pointer-events: none; user-select: none; }
    .cat-filter-group { margin-bottom: 1.5rem; }
    .cat-filter-group:last-child { margin-bottom: 0; }
    .cat-filter-label { font-size: .6rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--text-3); margin-bottom: .85rem; }
    .cat-range-row { display: flex; gap: .5rem; align-items: center; }
    .cat-range-input { flex: 1; padding: .5rem .75rem; background: var(--surface); border: 1px solid var(--rim); border-radius: 10px; font-size: .75rem; color: var(--text-2); text-align: center; font-family: var(--font-sans); }
    .cat-range-sep { color: var(--text-3); font-size: .8rem; }
    .cat-divider { border: none; border-top: 1px solid var(--rim); margin: 1.25rem 0; }
    .cat-check-row { display: flex; align-items: center; gap: .75rem; margin-bottom: .65rem; }
    .cat-check-box { width: 18px; height: 18px; border: 1px solid var(--rim); border-radius: 5px; background: var(--surface); flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
    .cat-check-box.on { background: var(--amber); border-color: var(--amber); }
    .cat-check-box.on i { font-size: .55rem; color: #1a0800; }
    .cat-check-text { font-size: .8rem; color: var(--text-2); }
    .cat-tag { display: inline-flex; align-items: center; gap: .3rem; font-size: .7rem; padding: .3rem .7rem; background: var(--surface); border: 1px solid var(--rim); border-radius: 100px; color: var(--text-2); }
    .cat-soon-btn { width: 100%; margin-top: 1rem; padding: .7rem; background: transparent; border: 1px dashed var(--text-3); border-radius: 12px; font-family: var(--font-sans); font-size: .65rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--text-3); cursor: not-allowed; }

    /* ══ RESULTS ══ */
    .cat-results { flex: 1; min-width: 0; }
    .cat-results-head { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 2.25rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--rim); position: relative; }
    .cat-results-head::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 80px; height: 1px; background: var(--amber); }
    .cat-results-head h2 { font-family: var(--font-serif); font-size: 2.2rem; font-weight: 700; color: var(--text-1); line-height: 1.1; letter-spacing: -.01em; }
    .cat-results-head h2 em { font-style: italic; color: var(--gold-light); }
    .cat-results-head p { font-size: .82rem; color: var(--text-2); margin-top: .3rem; font-weight: 300; }
    .cat-count { display: flex; flex-direction: column; align-items: flex-end; flex-shrink: 0; }
    .cat-count-num { font-family: var(--font-serif); font-size: 2.5rem; font-weight: 700; color: var(--text-1); line-height: 1; }
    .cat-count-label { font-size: .65rem; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--text-3); }

    /* ══ GRID ══ */
    .cat-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem; }
    @media (max-width: 1100px) { .cat-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 700px)  { .cat-grid { grid-template-columns: 1fr; } }

    /* ══ CARD ══ */
    .ts-prop-card {
        background: var(--card);
        border: 1px solid var(--rim);
        border-radius: 20px;
        overflow: hidden;
        display: flex; flex-direction: column;
        transition: transform .4s cubic-bezier(.2,.8,.2,1), border-color .3s, box-shadow .4s cubic-bezier(.2,.8,.2,1);
        will-change: transform;
    }
    .ts-prop-card:hover {
        transform: translateY(-8px) scale(1.01);
        border-color: var(--rim-h);
        box-shadow: 0 30px 60px rgba(0,0,0,0.6), 0 0 0 1px rgba(245,158,11,0.08), 0 0 60px rgba(245,158,11,0.04);
        background: var(--card-h);
    }
    .ts-prop-img { position: relative; height: 220px; background: var(--surface); overflow: hidden; flex-shrink: 0; }
    .ts-prop-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .8s cubic-bezier(.2,.8,.2,1), filter .4s; filter: brightness(.9); }
    .ts-prop-card:hover .ts-prop-img img { transform: scale(1.08); filter: brightness(1); }
    .ts-prop-img-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(5,8,16,0.2) 0%, transparent 40%, transparent 55%, rgba(5,8,16,0.85) 100%); pointer-events: none; }
    .ts-no-img { width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .6rem; color: var(--text-3); }
    .ts-no-img i { font-size: 2.5rem; }
    .ts-no-img span { font-size: .6rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }

    .ts-badge-area { position: absolute; top: 1rem; left: 1rem; right: 1rem; display: flex; justify-content: space-between; align-items: flex-start; z-index: 2; }
    .ts-b-info { padding: .35rem .85rem; background: rgba(5,8,16,0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.12); border-radius: 100px; font-size: .7rem; font-weight: 600; color: var(--text-1); letter-spacing: .04em; }
    .ts-b-alquiler { padding: .35rem .85rem; background: var(--amber); border-radius: 100px; font-size: .58rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: #1a0800; box-shadow: 0 4px 16px rgba(245,158,11,0.4); }

    .ts-price-overlay { position: absolute; bottom: 1rem; left: 1rem; z-index: 2; }
    .ts-price-overlay .price { font-family: var(--font-serif); font-size: 1.75rem; font-weight: 700; color: var(--gold-light); line-height: 1; text-shadow: 0 2px 12px rgba(0,0,0,0.8); }
    .ts-price-overlay .currency { font-size: .7rem; font-weight: 500; color: rgba(232,201,122,0.6); letter-spacing: .08em; margin-top: .15rem; }

    .ts-prop-body { padding: 1.35rem 1.35rem 1.25rem; display: flex; flex-direction: column; flex-grow: 1; }
    .ts-prop-title { font-family: var(--font-serif); font-size: 1.15rem; font-weight: 700; color: var(--text-1); line-height: 1.25; margin-bottom: .35rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; transition: color .2s; }
    .ts-prop-card:hover .ts-prop-title { color: #d4e4ff; }
    .ts-prop-loc { display: flex; align-items: center; gap: .4rem; margin-bottom: .5rem; }
    .ts-prop-loc i { color: var(--cobalt); font-size: .75rem; flex-shrink: 0; }
    .ts-prop-loc span { font-size: .78rem; color: var(--text-2); overflow: hidden; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; }

    /* Amenidades: camas / baños */
    .ts-prop-amenities { display: flex; align-items: center; gap: 1rem; margin-bottom: .65rem; }
    .ts-amenity { display: flex; align-items: center; gap: .35rem; font-size: .75rem; color: var(--text-3); }
    .ts-amenity i { font-size: .7rem; color: var(--amber); }

    .ts-prop-desc { font-size: .82rem; color: var(--text-3); line-height: 1.65; flex-grow: 1; margin-bottom: 1.1rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    .ts-prop-cta { display: flex; align-items: center; justify-content: space-between; padding-top: 1rem; border-top: 1px solid var(--rim); }
    .ts-prop-cta-link { display: inline-flex; align-items: center; gap: .5rem; font-size: .78rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: var(--text-3); text-decoration: none; transition: color .25s, gap .25s; }
    .ts-prop-card:hover .ts-prop-cta-link { color: var(--gold-light); gap: .75rem; }
    .ts-prop-cta-link i { font-size: .7rem; transition: transform .25s; }
    .ts-prop-card:hover .ts-prop-cta-link i { transform: translateX(3px); }
    .ts-prop-cta-arrow { width: 36px; height: 36px; border-radius: 50%; background: var(--surface); border: 1px solid var(--rim); display: flex; align-items: center; justify-content: center; color: var(--text-3); font-size: .75rem; transition: background .25s, border-color .25s, color .25s, transform .3s; }
    .ts-prop-card:hover .ts-prop-cta-arrow { background: var(--amber); border-color: var(--amber); color: #1a0800; transform: rotate(45deg); }

    /* ══ EMPTY STATE ══ */
    .cat-empty { grid-column: 1 / -1; text-align: center; padding: 6rem 2rem; background: var(--card); border: 1px solid var(--rim); border-radius: 24px; }
    .cat-empty-icon { width: 88px; height: 88px; margin: 0 auto 2rem; background: var(--amber-soft); border: 1px solid rgba(245,158,11,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; color: var(--amber); }
    .cat-empty h3 { font-family: var(--font-serif); font-size: 2rem; font-weight: 700; color: var(--text-1); margin-bottom: .6rem; }
    .cat-empty p { font-size: .9rem; color: var(--text-2); max-width: 400px; margin: 0 auto 2rem; line-height: 1.7; font-weight: 300; }
    .cat-empty-btn { display: inline-flex; align-items: center; gap: .5rem; padding: .7rem 1.75rem; border: 1px solid var(--rim); border-radius: 100px; background: transparent; color: var(--text-1); font-size: .78rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; text-decoration: none; transition: border-color .2s, background .2s; }
    .cat-empty-btn:hover { border-color: var(--amber); background: var(--amber-soft); }

    /* ══ PAGINATION ══ */
    .cat-pag { margin-top: 4rem; display: flex; justify-content: center; }
    .cat-pag > div { background: var(--card); border: 1px solid var(--rim); border-radius: 16px; padding: .6rem 1.25rem; }

    @media (max-width: 960px) { .cat-main { flex-direction: column; } .cat-aside { width: 100%; position: static; } .cat-hero { padding: 3rem 1.5rem 2.5rem; } }
</style>

<div class="cat-wrap">

    {{-- HERO --}}
    <header class="cat-hero">
        <div class="cat-hero-eyebrow">
            <span>Alquileres Disponibles</span>
        </div>
        <h1 class="cat-hero-title">
            Tu próximo<br><em>hogar</em> te espera aquí
        </h1>
        <p class="cat-hero-sub">
            Encontrá cuartos y habitaciones disponibles para alquilar, con todos los servicios que necesitás al mejor precio.
        </p>
    </header>

    {{-- SEARCH --}}
    <div class="cat-search-wrap">
        <form action="{{ route('catalogo.alquileres') }}" method="GET" class="cat-search-form">
            <div class="cat-search-inner">
                <span class="cat-search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input
                    class="cat-search-input"
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Buscar por zona, barrio o tipo de habitación…"
                    autocomplete="off"
                >
                <button type="submit" class="cat-search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Buscar</span>
                </button>
            </div>
        </form>
    </div>

    {{-- MAIN --}}
    <div class="cat-main">

        {{-- SIDEBAR --}}
        <aside class="cat-aside">

            <div class="cat-map-preview">
                <div class="cat-map-grid"></div>
                <div class="cat-map-roads"></div>
                <div class="cat-map-pin"></div>
                <div class="cat-map-overlay">
                    <p class="cat-map-label">Vista en mapa · próximamente</p>
                </div>
            </div>

            <div class="cat-filter">
                <div class="cat-filter-head">
                    <h3>Filtros</h3>
                    <span class="cat-filter-badge">Próximamente</span>
                </div>
                <div class="cat-filter-body">
                    <div class="cat-filter-group">
                        <p class="cat-filter-label">Precio Mensual</p>
                        <div class="cat-range-row">
                            <div class="cat-range-input">Mínimo</div>
                            <span class="cat-range-sep">—</span>
                            <div class="cat-range-input">Máximo</div>
                        </div>
                    </div>
                    <hr class="cat-divider">
                    <div class="cat-filter-group">
                        <p class="cat-filter-label">Habitaciones</p>
                        <div class="cat-check-row">
                            <div class="cat-check-box on"><i class="fa-solid fa-check"></i></div>
                            <span class="cat-check-text">1 habitación</span>
                        </div>
                        <div class="cat-check-row">
                            <div class="cat-check-box"></div>
                            <span class="cat-check-text">2+ habitaciones</span>
                        </div>
                    </div>
                    <hr class="cat-divider">
                    <div class="cat-filter-group">
                        <p class="cat-filter-label">Servicios</p>
                        <div style="display:flex;flex-wrap:wrap;gap:.4rem;">
                            <span class="cat-tag"><i class="fa-solid fa-droplet" style="font-size:.6rem;color:var(--cobalt)"></i> Agua</span>
                            <span class="cat-tag"><i class="fa-solid fa-bolt" style="font-size:.6rem;color:var(--amber)"></i> Luz</span>
                            <span class="cat-tag"><i class="fa-solid fa-wifi" style="font-size:.6rem;color:var(--emerald)"></i> Internet</span>
                        </div>
                    </div>
                    <button class="cat-soon-btn">Aplicar Filtros</button>
                </div>
            </div>
        </aside>

        {{-- RESULTS --}}
        <section class="cat-results">

            <div class="cat-results-head">
                <div>
                    @if(request('search'))
                        <h2>Buscando: <em>"{{ request('search') }}"</em></h2>
                        <p>Resultados que coinciden con tu búsqueda</p>
                    @else
                        <h2>Alquileres <em>disponibles</em></h2>
                        <p>Habitaciones y cuartos — seleccionados para vos</p>
                    @endif
                </div>
                <div class="cat-count">
                    <span class="cat-count-num">{{ $alquileres->total() }}</span>
                    <span class="cat-count-label">Resultados</span>
                </div>
            </div>

            @if($alquileres->isEmpty())
                <div style="display:grid;">
                    <div class="cat-empty">
                        <div class="cat-empty-icon">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                        <h3>Sin resultados</h3>
                        <p>No hay alquileres disponibles que coincidan con tu búsqueda. Intentá con términos más amplios.</p>
                        <a href="{{ route('catalogo.alquileres') }}" class="cat-empty-btn">
                            <i class="fa-solid fa-xmark"></i>
                            Limpiar búsqueda
                        </a>
                    </div>
                </div>
            @else
                <div class="cat-grid">
                    @foreach($alquileres as $alquiler)
                    <article class="ts-prop-card">

                        {{-- IMAGE --}}
                        <div class="ts-prop-img">
                            @if($alquiler->imagenes->count() > 0)
                                <img src="{{ asset($alquiler->imagenes->first()->ruta_archivo) }}"
                                     alt="{{ $alquiler->titulo }}" loading="lazy">
                            @else
                                <div class="ts-no-img">
                                    <i class="fa-regular fa-images"></i>
                                    <span>Sin foto</span>
                                </div>
                            @endif
                            <div class="ts-prop-img-overlay"></div>

                            <div class="ts-badge-area">
                                @if($alquiler->metros_cuadrados)
                                    <span class="ts-b-info">{{ number_format($alquiler->metros_cuadrados, 0) }} m²</span>
                                @else
                                    <span class="ts-b-info">
                                        <i class="fa-solid fa-bed" style="margin-right:.3rem;"></i>{{ $alquiler->habitaciones }} hab.
                                    </span>
                                @endif
                                <span class="ts-b-alquiler">Alquiler</span>
                            </div>

                            <div class="ts-price-overlay">
                                <div class="price">Bs. {{ number_format($alquiler->precio_mensual, 0) }}</div>
                                <div class="currency">por mes</div>
                            </div>
                        </div>

                        {{-- BODY --}}
                        <div class="ts-prop-body">
                            <h3 class="ts-prop-title">{{ $alquiler->titulo }}</h3>
                            <div class="ts-prop-loc">
                                <i class="fa-solid fa-location-dot"></i>
                                <span>{{ $alquiler->ubicacion }}</span>
                            </div>
                            <div class="ts-prop-amenities">
                                <span class="ts-amenity">
                                    <i class="fa-solid fa-bed"></i>
                                    {{ $alquiler->habitaciones }} {{ $alquiler->habitaciones == 1 ? 'habitación' : 'habitaciones' }}
                                </span>
                                <span class="ts-amenity">
                                    <i class="fa-solid fa-shower"></i>
                                    {{ $alquiler->banos }} {{ $alquiler->banos == 1 ? 'baño' : 'baños' }}
                                </span>
                            </div>
                            <p class="ts-prop-desc">{{ $alquiler->descripcion }}</p>

                            <div class="ts-prop-cta">
                                <a href="{{ route('catalogo.detalle.alquiler', $alquiler->id) }}" class="ts-prop-cta-link">
                                    Ver detalles
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                                <a href="{{ route('catalogo.detalle.alquiler', $alquiler->id) }}" class="ts-prop-cta-arrow" aria-label="Ver alquiler">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                    </article>
                    @endforeach
                </div>

                <div class="cat-pag">
                    <div>{{ $alquileres->links() }}</div>
                </div>
            @endif

        </section>
    </div>
</div>
@endsection