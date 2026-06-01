@extends('layouts.comprador')

@section('title', 'Mis Favoritos | TerrenoSur')

@section('content')
<style>
    :root {
        --void: #050810; --surface: #0c1326; --card: #0f1830; --card-h: #111c35;
        --rim: rgba(120,160,255,0.10); --rim-h: rgba(120,160,255,0.26);
        --gold: #c9a84c; --gold-light: #e8c97a; --gold-glow: rgba(201,168,76,0.15);
        --cobalt: #3d7ef5; --cobalt-soft: rgba(61,126,245,0.12);
        --emerald: #1dba7e; --rose: #f43f5e; --rose-soft: rgba(244,63,94,0.12);
        --amber: #f59e0b;
        --text-1: #eef2fc; --text-2: #8fa3cc; --text-3: #3d5480;
        --font-serif: 'Cormorant Garamond', Georgia, serif;
        --font-sans: 'Outfit', system-ui, sans-serif;
    }
    body { background: var(--void); }

    .fav-wrap { min-height: 100vh; font-family: var(--font-sans); padding-bottom: 6rem; }

    /* HERO */
    .fav-hero { padding: 4rem 2rem 3rem; text-align: center; position: relative; overflow: hidden; }
    .fav-hero::before {
        content: ''; position: absolute; top: -100px; left: 50%; transform: translateX(-50%);
        width: 800px; height: 400px;
        background: radial-gradient(ellipse at 50% 0%, rgba(244,63,94,0.10) 0%, transparent 70%);
        pointer-events: none;
    }
    .fav-hero-eyebrow {
        display: inline-flex; align-items: center; gap: .6rem;
        padding: .4rem 1.1rem; border: 1px solid rgba(244,63,94,0.3);
        border-radius: 100px; background: rgba(244,63,94,0.06);
        font-size: .65rem; font-weight: 600; letter-spacing: .18em; text-transform: uppercase;
        color: var(--rose); margin-bottom: 1.5rem;
    }
    .fav-hero-eyebrow::before {
        content: ''; width: 6px; height: 6px; border-radius: 50%;
        background: var(--rose); flex-shrink: 0;
        box-shadow: 0 0 8px rgba(244,63,94,0.8);
        animation: pulse-dot 2s ease-in-out infinite;
    }
    @keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.7)} }
    .fav-hero h1 {
        font-family: var(--font-serif); font-size: clamp(2.4rem,5vw,4.2rem);
        font-weight: 700; color: var(--text-1); line-height: 1.05; margin-bottom: .75rem;
    }
    .fav-hero h1 em { font-style: italic; color: var(--gold-light); }
    .fav-hero p { font-size: .92rem; color: var(--text-2); font-weight: 300; max-width: 480px; margin: 0 auto; line-height: 1.7; }

    /* LIMIT BAR */
    .fav-limit {
        max-width: 900px; margin: 0 auto 2rem;
        padding: .85rem 1.5rem;
        background: var(--card); border: 1px solid var(--rim); border-radius: 14px;
        display: flex; align-items: center; gap: 1rem;
    }
    .fav-limit-bar-wrap {
        flex: 1; height: 6px; background: var(--surface); border-radius: 100px; overflow: hidden;
    }
    .fav-limit-bar {
        height: 100%; border-radius: 100px;
        background: linear-gradient(90deg, var(--rose) 0%, #fb7185 100%);
        transition: width .4s ease;
    }
    .fav-limit-text { font-size: .75rem; color: var(--text-2); white-space: nowrap; }
    .fav-limit-text strong { color: var(--text-1); }

    /* TABS */
    .fav-main { max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
    .fav-tabs {
        display: flex; gap: .5rem; margin-bottom: 2.5rem;
        border-bottom: 1px solid var(--rim); padding-bottom: 0;
    }
    .fav-tab {
        padding: .65rem 1.5rem; border: none; background: transparent;
        font-family: var(--font-sans); font-size: .82rem; font-weight: 600;
        letter-spacing: .06em; color: var(--text-3); cursor: pointer;
        border-bottom: 2px solid transparent; margin-bottom: -1px;
        transition: color .2s, border-color .2s;
    }
    .fav-tab:hover { color: var(--text-2); }
    .fav-tab.active { color: var(--gold); border-bottom-color: var(--gold); }
    .fav-tab-count {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 20px; height: 20px; padding: 0 .4rem;
        background: var(--surface); border-radius: 100px;
        font-size: .65rem; font-weight: 700; margin-left: .4rem;
    }

    /* PANEL */
    .fav-panel { display: none; }
    .fav-panel.active { display: block; }

    /* GRID */
    .fav-grid {
        display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem;
    }
    @media(max-width:1050px){ .fav-grid{ grid-template-columns: repeat(2,1fr); } }
    @media(max-width:660px){ .fav-grid{ grid-template-columns: 1fr; } }

    /* CARD */
    .fav-card {
        background: var(--card); border: 1px solid var(--rim); border-radius: 20px;
        overflow: hidden; display: flex; flex-direction: column;
        transition: transform .35s cubic-bezier(.2,.8,.2,1), border-color .3s, box-shadow .35s;
    }
    .fav-card:hover {
        transform: translateY(-6px) scale(1.01);
        border-color: var(--rim-h);
        box-shadow: 0 24px 48px rgba(0,0,0,0.5);
        background: var(--card-h);
    }

    /* Card image */
    .fav-card-img { position: relative; height: 200px; background: var(--surface); overflow: hidden; flex-shrink: 0; }
    .fav-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .7s cubic-bezier(.2,.8,.2,1); filter: brightness(.9); }
    .fav-card:hover .fav-card-img img { transform: scale(1.07); filter: brightness(1); }
    .fav-card-img-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(5,8,16,0.15) 0%, transparent 40%, transparent 55%, rgba(5,8,16,0.8) 100%);
        pointer-events: none;
    }
    .fav-no-img {
        width:100%;height:100%;display:flex;flex-direction:column;
        align-items:center;justify-content:center;gap:.5rem;color:var(--text-3);
    }
    .fav-no-img i { font-size: 2rem; }
    .fav-no-img span { font-size:.6rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase; }

    /* Badges sobre imagen */
    .fav-badge-area {
        position: absolute; top: .85rem; left: .85rem; right: .85rem;
        display: flex; justify-content: space-between; align-items: flex-start; z-index: 2;
    }
    .fav-b-tipo {
        padding: .3rem .75rem;
        background: rgba(5,8,16,0.7); backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.12); border-radius: 100px;
        font-size: .65rem; font-weight: 600; color: var(--text-1); letter-spacing: .04em;
    }

    /* Badge de estado — aviso visual si cambió */
    .fav-estado-badge {
        padding: .3rem .75rem; border-radius: 100px;
        font-size: .58rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase;
    }
    .fav-estado-badge.disponible  { background: var(--emerald); color: #002916; box-shadow: 0 4px 12px rgba(29,186,126,0.35); }
    .fav-estado-badge.vendido     { background: rgba(239,68,68,0.9); color: white; box-shadow: 0 4px 12px rgba(239,68,68,0.4); }
    .fav-estado-badge.reservado   { background: var(--amber); color: #1a0800; box-shadow: 0 4px 12px rgba(245,158,11,0.4); }
    .fav-estado-badge.alquilado   { background: rgba(239,68,68,0.9); color: white; }
    .fav-estado-badge.inactivo    { background: var(--surface); color: var(--text-3); border: 1px solid var(--rim); }
    .fav-estado-badge.pendiente   { background: rgba(99,102,241,0.85); color: white; }

    /* Precio sobre imagen */
    .fav-price-overlay { position: absolute; bottom: .85rem; left: .85rem; z-index: 2; }
    .fav-price-overlay .price {
        font-family: var(--font-serif); font-size: 1.6rem; font-weight: 700;
        color: var(--gold-light); line-height: 1; text-shadow: 0 2px 12px rgba(0,0,0,0.8);
    }
    .fav-price-overlay .currency {
        font-size: .65rem; font-weight: 500; color: rgba(232,201,122,0.6);
        letter-spacing: .08em; margin-top: .1rem;
    }

    /* Botón quitar favorito */
    .fav-remove-btn {
        position: absolute; top: .85rem; right: .85rem; z-index: 3;
        width: 32px; height: 32px; border-radius: 50%;
        background: rgba(244,63,94,0.2); border: 1px solid rgba(244,63,94,0.4);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all .2s; padding: 0;
    }
    .fav-remove-btn:hover { background: rgba(244,63,94,0.35); transform: scale(1.1); }
    .fav-remove-btn i { color: var(--rose); font-size: .8rem; pointer-events: none; }

    /* Body */
    .fav-card-body { padding: 1.2rem 1.2rem 1.1rem; display: flex; flex-direction: column; flex-grow: 1; }
    .fav-card-title {
        font-family: var(--font-serif); font-size: 1.1rem; font-weight: 700;
        color: var(--text-1); line-height: 1.25; margin-bottom: .35rem;
        overflow: hidden; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;
    }
    .fav-card-loc {
        display: flex; align-items: center; gap: .35rem; margin-bottom: .5rem;
    }
    .fav-card-loc i { color: var(--cobalt); font-size: .7rem; flex-shrink: 0; }
    .fav-card-loc span {
        font-size: .75rem; color: var(--text-2);
        overflow: hidden; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;
    }
    .fav-card-meta { display: flex; gap: 1rem; margin-bottom: .75rem; }
    .fav-card-meta span { font-size: .75rem; color: var(--text-3); display: flex; align-items: center; gap: .3rem; }
    .fav-card-meta i { color: var(--gold); font-size: .7rem; }

    /* Aviso de cambio de estado */
    .fav-estado-aviso {
        padding: .5rem .75rem; border-radius: 10px; margin-bottom: .75rem;
        background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25);
        font-size: .72rem; color: #fca5a5; display: flex; align-items: center; gap: .4rem;
    }
    .fav-estado-aviso.reservado {
        background: rgba(245,158,11,0.1); border-color: rgba(245,158,11,0.25); color: #fcd34d;
    }

    .fav-card-cta {
        margin-top: auto; padding-top: .85rem; border-top: 1px solid var(--rim);
        display: flex; align-items: center; justify-content: space-between;
    }
    .fav-card-link {
        display: inline-flex; align-items: center; gap: .4rem;
        font-size: .75rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase;
        color: var(--text-3); text-decoration: none; transition: color .2s, gap .2s;
    }
    .fav-card:hover .fav-card-link { color: var(--gold-light); gap: .6rem; }
    .fav-card-arrow {
        width: 32px; height: 32px; border-radius: 50%;
        background: var(--surface); border: 1px solid var(--rim);
        display: flex; align-items: center; justify-content: center;
        color: var(--text-3); font-size: .7rem;
        transition: background .25s, border-color .25s, color .25s, transform .3s;
    }
    .fav-card:hover .fav-card-arrow {
        background: var(--gold); border-color: var(--gold); color: #1a0f00; transform: rotate(45deg);
    }

    /* EMPTY */
    .fav-empty {
        text-align: center; padding: 5rem 2rem;
        background: var(--card); border: 1px solid var(--rim); border-radius: 24px;
    }
    .fav-empty-icon {
        width: 80px; height: 80px; margin: 0 auto 1.5rem;
        background: var(--rose-soft); border: 1px solid rgba(244,63,94,0.2);
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: var(--rose);
    }
    .fav-empty h3 {
        font-family: var(--font-serif); font-size: 1.8rem; font-weight: 700;
        color: var(--text-1); margin-bottom: .5rem;
    }
    .fav-empty p { font-size: .88rem; color: var(--text-2); max-width: 360px; margin: 0 auto 1.75rem; line-height: 1.7; }
    .fav-empty-links { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
    .fav-empty-btn {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .65rem 1.5rem; border: 1px solid var(--rim); border-radius: 100px;
        background: transparent; color: var(--text-1); font-size: .75rem; font-weight: 600;
        letter-spacing: .06em; text-transform: uppercase; text-decoration: none; transition: all .2s;
    }
    .fav-empty-btn:hover { border-color: var(--gold); background: var(--gold-glow); }
</style>

<div class="fav-wrap">

    {{-- HERO --}}
    <header class="fav-hero">
        <div class="fav-hero-eyebrow">Mis Favoritos</div>
        <h1>Tu lista de<br><em>propiedades guardadas</em></h1>
        <p>Todas las propiedades que marcaste con ❤️, en un solo lugar.</p>
    </header>

    <div class="fav-main">

        {{-- BARRA DE LÍMITE --}}
        @php $porcentaje = min(100, ($total / 50) * 100); @endphp
        <div class="fav-limit">
            <span class="fav-limit-text"><strong>{{ $total }}</strong> / 50 favoritos</span>
            <div class="fav-limit-bar-wrap">
                <div class="fav-limit-bar" style="width: {{ $porcentaje }}%;
                    {{ $porcentaje >= 90 ? 'background: linear-gradient(90deg,#ef4444,#f87171);' : '' }}">
                </div>
            </div>
            @if($total >= 50)
                <span class="fav-limit-text" style="color:#f87171;font-weight:600;">Límite alcanzado</span>
            @elseif($total >= 40)
                <span class="fav-limit-text" style="color:#fcd34d;">Casi al límite</span>
            @endif
        </div>

        {{-- TABS --}}
        <div class="fav-tabs">
            <button class="fav-tab active" onclick="switchTab('terrenos', this)">
                <i class="fa-solid fa-map" style="margin-right:.35rem;font-size:.75rem;"></i>
                Terrenos
                <span class="fav-tab-count">{{ $terrenosFav->count() }}</span>
            </button>
            <button class="fav-tab" onclick="switchTab('lotes', this)">
                <i class="fa-solid fa-layer-group" style="margin-right:.35rem;font-size:.75rem;"></i>
                Lotes
                <span class="fav-tab-count">{{ $lotesFav->count() }}</span>
            </button>
            <button class="fav-tab" onclick="switchTab('alquileres', this)">
                <i class="fa-solid fa-bed" style="margin-right:.35rem;font-size:.75rem;"></i>
                Alquileres
                <span class="fav-tab-count">{{ $alquileresFav->count() }}</span>
            </button>
        </div>

        {{-- PANEL TERRENOS --}}
        <div class="fav-panel active" id="panel-terrenos">
            @if($terrenosFav->isEmpty())
                <div class="fav-empty">
                    <div class="fav-empty-icon"><i class="fa-regular fa-heart"></i></div>
                    <h3>Sin terrenos guardados</h3>
                    <p>Explora el catálogo y toca el ❤️ en los terrenos que te interesen.</p>
                    <div class="fav-empty-links">
                        <a href="{{ route('catalogo.terrenos') }}" class="fav-empty-btn">
                            <i class="fa-solid fa-map"></i> Ver Terrenos
                        </a>
                    </div>
                </div>
            @else
                <div class="fav-grid">
                    @foreach($terrenosFav as $fav)
                        @php $t = $fav->favoriteable; @endphp
                        <article class="fav-card">
                            <div class="fav-card-img">
                                @if($t->imagenes->count() > 0)
                                    <img src="{{ asset($t->imagenes->first()->ruta_archivo) }}" alt="Terreno" loading="lazy">
                                @else
                                    <div class="fav-no-img">
                                        <i class="fa-regular fa-images"></i>
                                        <span>Sin foto</span>
                                    </div>
                                @endif
                                <div class="fav-card-img-overlay"></div>

                                <div class="fav-badge-area">
                                    <span class="fav-b-tipo">{{ number_format($t->metros_cuadrados, 0) }} m²</span>
                                        @php
                                            // estado_lote controla disponibilidad del lote (disponible/reservado/vendido)
                                            // Si no existe estado_lote, el terreno está disponible (ya pasó filtro estado=aprobado)
                                            $estadoTerreno = $t->estado_lote ?? 'disponible';
                                            $esInactivo = in_array($estadoTerreno, ['vendido', 'reservado']);
                                        @endphp
                                    <span class="fav-estado-badge {{ $estadoTerreno }}">
                                        {{ ucfirst($estadoTerreno) }}
                                    </span>
                                </div>

                                {{-- Botón quitar favorito --}}
                                <button
                                    class="fav-remove-btn"
                                    data-id="{{ $t->id }}"
                                    data-type="terreno"
                                    data-fav-id="{{ $fav->id }}"
                                    onclick="quitarFavorito(this)"
                                    title="Quitar de favoritos">
                                    <i class="fa-solid fa-heart-crack"></i>
                                </button>

                                <div class="fav-price-overlay">
                                    <div class="price">${{ number_format($t->precio, 0) }}</div>
                                    <div class="currency">USD</div>
                                </div>
                            </div>

                            <div class="fav-card-body">
                                {{-- Aviso visual si cambió estado --}}
                                @if(in_array($estadoTerreno, ['vendido', 'rechazado']))
                                    <div class="fav-estado-aviso">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        Este terreno ya no está disponible ({{ ucfirst($estadoTerreno) }})
                                    </div>
                                @elseif($estadoTerreno === 'reservado')
                                    <div class="fav-estado-aviso reservado">
                                        <i class="fa-solid fa-clock"></i>
                                        Este terreno está reservado actualmente
                                    </div>
                                @endif

                                <h3 class="fav-card-title">Terreno en {{ $t->ubicacion }}</h3>
                                <div class="fav-card-loc">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span>{{ $t->ubicacion }}</span>
                                </div>
                                @if($t->categoria)
                                    <div class="fav-card-meta">
                                        <span>
                                            <i class="fa-solid fa-tag"></i>
                                            {{ $t->categoria->nombre }}
                                        </span>
                                    </div>
                                @endif

                                <div class="fav-card-cta">
                                    <a href="{{ route('catalogo.detalle', $t->id) }}" class="fav-card-link">
                                        Ver detalles
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a href="{{ route('catalogo.detalle', $t->id) }}" class="fav-card-arrow">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- PANEL LOTES --}}
        <div class="fav-panel" id="panel-lotes">
            @if($lotesFav->isEmpty())
                <div class="fav-empty">
                    <div class="fav-empty-icon"><i class="fa-regular fa-heart"></i></div>
                    <h3>Sin lotes guardados</h3>
                    <p>Explora el catálogo y guarda los lotes que te interesen.</p>
                    <div class="fav-empty-links">
                        <a href="{{ route('catalogo.lotes') }}" class="fav-empty-btn">
                            <i class="fa-solid fa-layer-group"></i> Ver Lotes
                        </a>
                    </div>
                </div>
            @else
                <div class="fav-grid">
                    @foreach($lotesFav as $fav)
                        @php $t = $fav->favoriteable; @endphp
                        <article class="fav-card">
                            <div class="fav-card-img">
                                @if($t->imagenes->count() > 0)
                                    <img src="{{ asset($t->imagenes->first()->ruta_archivo) }}" alt="Lote" loading="lazy">
                                @else
                                    <div class="fav-no-img">
                                        <i class="fa-regular fa-images"></i>
                                        <span>Sin foto</span>
                                    </div>
                                @endif
                                <div class="fav-card-img-overlay"></div>

                                <div class="fav-badge-area">
                                    <span class="fav-b-tipo">{{ number_format($t->metros_cuadrados, 0) }} m²</span>
                                    @php
                                        $estadoLote = $t->estado_lote ?? 'disponible';
                                        $esInactivoLote = in_array($estadoLote, ['vendido', 'reservado']);
                                    @endphp
                                    <span class="fav-estado-badge {{ $estadoLote }}">{{ ucfirst($estadoLote) }}</span>
                                </div>

                                {{-- Botón quitar favorito --}}
                                <button
                                    class="fav-remove-btn"
                                    data-id="{{ $t->id }}"
                                    data-type="lote"
                                    data-fav-id="{{ $fav->id }}"
                                    onclick="quitarFavorito(this)"
                                    title="Quitar de favoritos">
                                    <i class="fa-solid fa-heart-crack"></i>
                                </button>

                                <div class="fav-price-overlay">
                                    <div class="price">${{ number_format($t->precio, 0) }}</div>
                                    <div class="currency">USD</div>
                                </div>
                            </div>

                            <div class="fav-card-body">
                                @if($esInactivoLote)
                                    <div class="fav-estado-aviso">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        Este lote ya no está disponible ({{ ucfirst($estadoLote) }})
                                    </div>
                                @endif

                                <h3 class="fav-card-title">Lote en {{ $t->ubicacion }}</h3>
                                <div class="fav-card-loc">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span>{{ $t->ubicacion }}</span>
                                </div>
                                @if($t->categoria)
                                    <div class="fav-card-meta">
                                        <span>
                                            <i class="fa-solid fa-tag"></i>
                                            {{ $t->categoria->nombre }}
                                        </span>
                                    </div>
                                @endif

                                <div class="fav-card-cta">
                                    <a href="{{ route('catalogo.detalle.lote', $t->id) }}" class="fav-card-link">
                                        Ver detalles
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a href="{{ route('catalogo.detalle.lote', $t->id) }}" class="fav-card-arrow">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- PANEL ALQUILERES --}}
        <div class="fav-panel" id="panel-alquileres">
            @if($alquileresFav->isEmpty())
                <div class="fav-empty">
                    <div class="fav-empty-icon"><i class="fa-regular fa-heart"></i></div>
                    <h3>Sin alquileres guardados</h3>
                    <p>Explora los alquileres disponibles y guarda los que más te interesen.</p>
                    <div class="fav-empty-links">
                        <a href="{{ route('catalogo.alquileres') }}" class="fav-empty-btn">
                            <i class="fa-solid fa-bed"></i> Ver Alquileres
                        </a>
                    </div>
                </div>
            @else
                <div class="fav-grid">
                    @foreach($alquileresFav as $fav)
                        @php $a = $fav->favoriteable; @endphp
                        <article class="fav-card">
                            <div class="fav-card-img">
                                @if($a->imagenes->count() > 0)
                                    <img src="{{ asset($a->imagenes->first()->ruta_archivo) }}" alt="{{ $a->titulo }}" loading="lazy">
                                @else
                                    <div class="fav-no-img">
                                        <i class="fa-regular fa-images"></i>
                                        <span>Sin foto</span>
                                    </div>
                                @endif
                                <div class="fav-card-img-overlay"></div>

                                <div class="fav-badge-area">
                                    <span class="fav-b-tipo">
                                        <i class="fa-solid fa-bed" style="margin-right:.3rem;"></i>
                                        {{ $a->habitaciones }} hab.
                                    </span>
                                    @php
                                        $estadoAlq = $a->estado;
                                        $esInactivoAlq = in_array($estadoAlq, ['alquilado', 'inactivo']);
                                    @endphp
                                    <span class="fav-estado-badge {{ $estadoAlq }}">
                                        {{ ucfirst($estadoAlq) }}
                                    </span>
                                </div>

                                {{-- Botón quitar favorito --}}
                                <button
                                    class="fav-remove-btn"
                                    data-id="{{ $a->id }}"
                                    data-type="alquiler"
                                    data-fav-id="{{ $fav->id }}"
                                    onclick="quitarFavorito(this)"
                                    title="Quitar de favoritos">
                                    <i class="fa-solid fa-heart-crack"></i>
                                </button>

                                <div class="fav-price-overlay">
                                    <div class="price">Bs. {{ number_format($a->precio_mensual, 0) }}</div>
                                    <div class="currency">por mes</div>
                                </div>
                            </div>

                            <div class="fav-card-body">
                                @if($esInactivoAlq)
                                    <div class="fav-estado-aviso">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        Este alquiler ya no está disponible ({{ ucfirst($estadoAlq) }})
                                    </div>
                                @endif

                                <h3 class="fav-card-title">{{ $a->titulo }}</h3>
                                <div class="fav-card-loc">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span>{{ $a->ubicacion }}</span>
                                </div>
                                <div class="fav-card-meta">
                                    <span><i class="fa-solid fa-shower"></i>{{ $a->banos }} baños</span>
                                    @if($a->metros_cuadrados)
                                        <span><i class="fa-solid fa-vector-square"></i>{{ $a->metros_cuadrados }} m²</span>
                                    @endif
                                </div>

                                <div class="fav-card-cta">
                                    <a href="{{ route('catalogo.detalle.alquiler', $a->id) }}" class="fav-card-link">
                                        Ver detalles
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a href="{{ route('catalogo.detalle.alquiler', $a->id) }}" class="fav-card-arrow">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script>
function switchTab(tab, btn) {
    document.querySelectorAll('.fav-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.fav-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + tab).classList.add('active');
}

function quitarFavorito(btn) {
    const id   = btn.dataset.id;
    const type = btn.dataset.type;
    const card = btn.closest('.fav-card');

    fetch('{{ route("favoritos.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ id, type })
    })
    .then(r => r.json())
    .then(data => {
        if (data.action === 'removed') {
            card.style.transition = 'opacity .3s, transform .3s';
            card.style.opacity    = '0';
            card.style.transform  = 'scale(0.95)';
            setTimeout(() => {
                card.remove();
                // Actualizar contadores
                location.reload();
            }, 320);
        }
    })
    .catch(() => alert('Error al quitar favorito.'));
}
</script>
@endpush
@endsection