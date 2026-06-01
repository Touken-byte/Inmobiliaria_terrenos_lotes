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
    @keyframes pulse-glow {
        0%, 100% { transform: scale(1); box-shadow: 0 4px 12px rgba(255,107,107,0.4); }
        50% { transform: scale(1.05); box-shadow: 0 4px 20px rgba(255,107,107,0.7); }
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
    .cat-search-form { max-width: 780px; margin: 0 auto; position: relative; }
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

    /* ══ MAIN LAYOUT (SIN SIDEBAR) ══ */
    .cat-main {
        max-width: 1360px;
        margin: 0 auto;
        padding: 3rem 2rem 0;
    }

    /* ══ SIDEBAR OCULTO ══ */
    .cat-aside { display: none; }

    /* Map preview (no usado) */
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

    /* Filter (legacy, no se usa) */
    .cat-filter { background: var(--card); border: 1px solid var(--rim); border-radius: 20px; overflow: hidden; }
    .cat-filter-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--rim); display: flex; align-items: center; justify-content: space-between; }
    .cat-filter-head h3 { font-family: var(--font-serif); font-size: 1.2rem; font-weight: 700; color: var(--text-1); }
    .cat-filter-badge { font-size: .6rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; padding: .3rem .65rem; border-radius: 100px; background: var(--amber-soft); color: var(--amber); border: 1px solid rgba(245,158,11,0.25); }
    .cat-filter-body { padding: 1.5rem; }
    .cat-filter-group { margin-bottom: 1.5rem; }
    .cat-filter-group:last-child { margin-bottom: 0; }
    .cat-filter-label { font-size: .6rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--text-3); margin-bottom: .85rem; }
    .cat-range-row { display: flex; gap: .5rem; align-items: center; }
    .cat-range-input {
        flex: 1;
        padding: .5rem .75rem;
        background: var(--surface);
        border: 1px solid var(--rim);
        border-radius: 10px;
        font-size: .75rem;
        color: var(--text-2);
        font-family: var(--font-sans);
        outline: none;
        transition: border-color .2s;
    }
    .cat-range-input:focus { border-color: var(--amber); color: var(--text-1); }
    .cat-range-input::placeholder { color: var(--text-3); }
    .cat-range-sep { color: var(--text-3); font-size: .8rem; }
    .cat-divider { border: none; border-top: 1px solid var(--rim); margin: 1.25rem 0; }
    .cat-check-row { display: flex; align-items: center; gap: .75rem; margin-bottom: .65rem; }
    .cat-check-box { width: 18px; height: 18px; border: 1px solid var(--rim); border-radius: 5px; background: var(--surface); flex-shrink: 0; display: flex; align-items: center; justify-content: center; transition: background .2s, border-color .2s; }
    .cat-check-text { font-size: .8rem; color: var(--text-2); }
    .cat-radio-label { display: flex; align-items: center; gap: .75rem; cursor: pointer; width: 100%; }

    .cat-apply-btn {
        width: 100%;
        margin-top: 1rem;
        padding: .75rem;
        background: var(--amber-soft);
        border: 1px solid var(--amber);
        border-radius: 12px;
        font-family: var(--font-sans);
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--amber);
        cursor: pointer;
        transition: background .2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
    }
    .cat-apply-btn:hover { background: rgba(245,158,11,0.2); }

    .cat-clear-link {
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        padding: .3rem .65rem;
        border-radius: 100px;
        background: rgba(239,68,68,0.12);
        color: #f87171;
        border: 1px solid rgba(239,68,68,0.25);
        text-decoration: none;
        transition: background .2s;
    }
    .cat-clear-link:hover { background: rgba(239,68,68,0.2); }

    /* ══ RESULTS ══ */
    .cat-results { flex: 1; min-width: 0; width: 100%; }
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

    .ts-cat-badge {
        display: inline-block;
        margin-bottom: .6rem;
        padding: .2rem .65rem;
        border-radius: 100px;
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .ts-prop-title { font-family: var(--font-serif); font-size: 1.15rem; font-weight: 700; color: var(--text-1); line-height: 1.25; margin-bottom: .35rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; transition: color .2s; }
    .ts-prop-card:hover .ts-prop-title { color: #d4e4ff; }
    .ts-prop-loc { display: flex; align-items: center; gap: .4rem; margin-bottom: .5rem; }
    .ts-prop-loc i { color: var(--cobalt); font-size: .75rem; flex-shrink: 0; }
    .ts-prop-loc span { font-size: .78rem; color: var(--text-2); overflow: hidden; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; }

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
    /* ══ FAVORITO BTN ══ */
    .fav-btn {
        background: rgba(5,8,16,0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 50%;
        width: 34px; height: 34px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all .25s;
        flex-shrink: 0;
        padding: 0;
    }
    .fav-btn:hover {
        border-color: rgba(244,63,94,0.5);
        background: rgba(244,63,94,0.15);
        transform: scale(1.1);
    }
    .fav-btn.activo {
        border-color: rgba(244,63,94,0.6);
        background: rgba(244,63,94,0.18);
    }

    /* ══ NUEVOS ESTILOS PARA FILTROS DROPDOWN Y CHIPS ══ */
    .filter-toggle-btn {
        height: 100%;
        padding: 0 1.25rem;
        background: transparent;
        border: none;
        border-left: 1px solid var(--rim);
        color: var(--text-2);
        font-family: var(--font-sans);
        font-size: .82rem;
        font-weight: 600;
        letter-spacing: .04em;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: .45rem;
        flex-shrink: 0;
        transition: color .2s, background .2s;
        position: relative;
    }
    .filter-toggle-btn:hover { color: var(--text-1); background: rgba(255,255,255,0.03); }
    .filter-toggle-btn.active { color: var(--amber); }
    .filter-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--amber);
        position: absolute;
        top: 12px; right: 10px;
        box-shadow: 0 0 6px var(--amber);
    }

    .filter-panel {
        display: none;
        position: absolute;
        top: calc(100% + 10px);
        left: 50%;
        transform: translateX(-50%);
        width: min(780px, 96vw);
        background: var(--card);
        border: 1px solid var(--rim-h);
        border-radius: 20px;
        padding: 1.75rem 2rem 1.5rem;
        z-index: 200;
        box-shadow: 0 24px 60px rgba(0,0,0,0.7), 0 0 0 1px rgba(120,160,255,0.06);
        animation: panel-in .2s cubic-bezier(.2,.8,.2,1);
    }
    .filter-panel.open { display: block; }
    @keyframes panel-in {
        from { opacity: 0; transform: translateX(-50%) translateY(-8px); }
        to   { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    .filter-panel-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem 2rem;
        margin-bottom: 1.5rem;
    }
    @media(max-width:600px) { .filter-panel-grid { grid-template-columns: 1fr; } }

    .filter-section-cats { grid-column: 1 / -1; }

    .filter-section-label {
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: var(--text-3);
        margin-bottom: .7rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    .cats-grid {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
    }
    .cat-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .3rem .85rem;
        border: 1px solid var(--rim);
        border-radius: 100px;
        background: var(--surface);
        color: var(--text-2);
        font-size: .75rem;
        font-weight: 500;
        cursor: pointer;
        transition: all .2s;
    }
    .cat-pill:hover { border-color: var(--rim-h); color: var(--text-1); }
    .cat-pill.active { border-color: var(--amber); background: var(--amber-soft); color: var(--text-1); }

    .filter-panel-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 1.25rem;
        border-top: 1px solid var(--rim);
        gap: 1rem;
    }
    .filter-clear-btn {
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .06em;
        color: var(--text-3);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: .35rem;
        transition: color .2s;
    }
    .filter-clear-btn:hover { color: #f87171; }
    .filter-apply-btn {
        padding: .65rem 1.75rem;
        background: linear-gradient(135deg, var(--amber) 0%, #b45309 100%);
        border: none;
        border-radius: 12px;
        font-family: var(--font-sans);
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .06em;
        color: #1a0800;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: .5rem;
        transition: filter .2s, transform .2s;
    }
    .filter-apply-btn:hover { filter: brightness(1.15); transform: translateY(-1px); }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .28rem .75rem;
        background: var(--amber-soft);
        border: 1px solid rgba(245,158,11,0.3);
        border-radius: 100px;
        font-size: .7rem;
        font-weight: 500;
        color: var(--text-2);
    }
    .chip-remove {
        color: var(--text-3);
        text-decoration: none;
        font-size: .65rem;
        margin-left: .15rem;
        transition: color .15s;
    }
    .chip-remove:hover { color: #f87171; }
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

    {{-- SEARCH + FILTROS --}}
    <div class="cat-search-wrap">
        <div class="cat-search-form" style="position:relative;">

            @php
                $filtrosActivos = array_filter(request()->only(['precio_min','precio_max','metros_min','metros_max','categoria_id','ubicacion','lat','lng','radio','con_promocion']), fn($v) => $v !== '' && $v !== null);
            @endphp
            @if(count($filtrosActivos))
            <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.6rem;max-width:780px;margin-left:auto;margin-right:auto;">
                @if(request('precio_min') || request('precio_max'))
                <span class="filter-chip">
                    <i class="fa-solid fa-bolivar-sign"></i>
                    Bs. {{ request('precio_min') ?: '0' }} — {{ request('precio_max') ?: '∞' }}
                    <a href="{{ route('catalogo.alquileres', request()->except(['precio_min','precio_max','page'])) }}" class="chip-remove">✕</a>
                </span>
                @endif
                @if(request('con_promocion'))
                <span class="filter-chip" style="border-color:rgba(255,107,107,0.4);background:rgba(255,107,107,0.12);color:#ff8787;">
                    🏷️ Solo con Promoción
                    <a href="{{ route('catalogo.alquileres', request()->except(['con_promocion','page'])) }}" class="chip-remove">✕</a>
                </span>
                @endif
                @if(request('metros_min') || request('metros_max'))
                <span class="filter-chip">
                    <i class="fa-solid fa-vector-square"></i>
                    {{ request('metros_min') ?: '0' }} — {{ request('metros_max') ?: '∞' }} m²
                    <a href="{{ route('catalogo.alquileres', request()->except(['metros_min','metros_max','page'])) }}" class="chip-remove">✕</a>
                </span>
                @endif
                @if(request('ubicacion'))
                <span class="filter-chip">
                    <i class="fa-solid fa-location-dot"></i>
                    {{ request('ubicacion') }}
                    <a href="{{ route('catalogo.alquileres', request()->except(['ubicacion','page'])) }}" class="chip-remove">✕</a>
                </span>
                @endif
                @if(request('categoria_id'))
                @php $catActiva = $categorias->firstWhere('id', request('categoria_id')); @endphp
                @if($catActiva)
                <span class="filter-chip" style="border-color:{{ $catActiva->color }}55;background:{{ $catActiva->color }}15;">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $catActiva->color }};flex-shrink:0;"></span>
                    {{ $catActiva->nombre }}
                    <a href="{{ route('catalogo.alquileres', request()->except(['categoria_id','page'])) }}" class="chip-remove">✕</a>
                </span>
                @endif
                @endif
                @if(request('lat') && request('lng'))
                <span class="filter-chip" style="border-color:rgba(245,158,11,0.4);background:rgba(245,158,11,0.12);">
                    <i class="fa-solid fa-location-crosshairs" style="color:var(--amber);"></i>
                    Radio {{ request('radio',5) }} km
                    <a href="{{ route('catalogo.alquileres', request()->except(['lat','lng','radio','page'])) }}" class="chip-remove">✕</a>
                </span>
                @endif
                <a href="{{ route('catalogo.alquileres', request()->only('search')) }}" style="font-size:.65rem;color:var(--text-3);text-decoration:none;margin-left:.25rem;" onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='var(--text-3)'">
                    Limpiar todo ✕
                </a>
            </div>
            @endif

            <form action="{{ route('catalogo.alquileres') }}" method="GET" style="max-width:780px;margin:0 auto;position:relative;">
                @foreach(request()->except(['search','page']) as $key => $val)
                    @if($val !== '' && $val !== null)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endif
                @endforeach
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
                    @php $hayFiltros = count($filtrosActivos) > 0; @endphp
                    <button type="button" class="filter-toggle-btn {{ $hayFiltros ? 'active' : '' }}" onclick="toggleFilterPanel('panel-filtros-a')" id="btn-filtros-a">
                        <i class="fa-solid fa-sliders"></i>
                        <span>Filtros</span>
                        @if($hayFiltros)
                        <span class="filter-dot" style="background:var(--amber);box-shadow:0 0 6px var(--amber);"></span>
                        @endif
                    </button>
                    <button type="submit" class="cat-search-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Buscar</span>
                    </button>
                </div>
            </form>

            {{-- PANEL DESPLEGABLE --}}
            <div class="filter-panel" id="panel-filtros-a">
                <form action="{{ route('catalogo.alquileres') }}" method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('lat'))
                        <input type="hidden" name="lat" value="{{ request('lat') }}">
                        <input type="hidden" name="lng" value="{{ request('lng') }}">
                        <input type="hidden" name="radio" value="{{ request('radio',5) }}">
                    @endif

                    <div class="filter-panel-grid">
                        <div class="filter-section">
                            <p class="filter-section-label"><i class="fa-solid fa-bolivar-sign"></i> Precio mensual (Bs.)</p>
                            <div class="cat-range-row">
                                <input type="number" name="precio_min" placeholder="Mínimo" value="{{ request('precio_min') }}" class="cat-range-input" min="0">
                                <span class="cat-range-sep">—</span>
                                <input type="number" name="precio_max" placeholder="Máximo" value="{{ request('precio_max') }}" class="cat-range-input" min="0">
                            </div>
                        </div>
                        <div class="filter-section">
                            <p class="filter-section-label"><i class="fa-solid fa-vector-square"></i> Superficie (m²)</p>
                            <div class="cat-range-row">
                                <input type="number" name="metros_min" placeholder="Mínimo" value="{{ request('metros_min') }}" class="cat-range-input" min="0">
                                <span class="cat-range-sep">—</span>
                                <input type="number" name="metros_max" placeholder="Máximo" value="{{ request('metros_max') }}" class="cat-range-input" min="0">
                            </div>
                        </div>
                        <div class="filter-section">
                            <p class="filter-section-label"><i class="fa-solid fa-location-dot"></i> Ubicación</p>
                            <input type="text" name="ubicacion" placeholder="Zona o barrio…" value="{{ request('ubicacion') }}" class="cat-range-input" style="width:100%;box-sizing:border-box;">
                        </div>
                        <div class="filter-section">
                            <p class="filter-section-label"><i class="fa-solid fa-percent"></i> Ofertas Especiales</p>
                            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                                <label class="cat-pill {{ request('con_promocion') === '1' ? 'active' : '' }}"
                                       style="{{ request('con_promocion') === '1' ? 'background:rgba(255,107,107,0.15);border-color:rgba(255,107,107,0.5);color:#ff8787;' : '' }}">
                                    <input type="checkbox" name="con_promocion" value="1" {{ request('con_promocion') === '1' ? 'checked' : '' }} style="display:none;" onchange="this.closest('form').submit();">
                                    🏷️ Solo con Promoción
                                </label>
                            </div>
                        </div>
                        @if($categorias->count() > 0)
                        <div class="filter-section filter-section-cats">
                            <p class="filter-section-label"><i class="fa-solid fa-tag"></i> Categoría</p>
                            <div class="cats-grid">
                                <label class="cat-pill {{ !request('categoria_id') ? 'active' : '' }}" style="{{ !request('categoria_id') ? 'border-color:var(--amber);background:rgba(245,158,11,0.12);' : '' }}"
                                      onclick="this.querySelector('input[type=radio]').checked=true; this.closest('form').submit();">
                                    <input type="radio" name="categoria_id" value="" {{ !request('categoria_id') ? 'checked' : '' }} style="display:none;">
                                    Todas
                                </label>
                                @foreach($categorias as $cat)
                                <label class="cat-pill {{ request('categoria_id') == $cat->id ? 'active' : '' }}"
                                       style="{{ request('categoria_id') == $cat->id ? 'background:'.$cat->color.'22;border-color:'.$cat->color.'66;color:'.$cat->color.';' : '' }}"
                                       onclick="this.querySelector('input[type=radio]').checked=true; this.closest('form').submit();">
                                    <input type="radio" name="categoria_id" value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'checked' : '' }} style="display:none;" data-color="{{ $cat->color }}">
                                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $cat->color }};flex-shrink:0;"></span>
                                    {{ $cat->nombre }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="filter-panel-footer">
                        <a href="{{ route('catalogo.alquileres', request()->only('search')) }}" class="filter-clear-btn">
                            <i class="fa-solid fa-xmark"></i> Limpiar filtros
                        </a>
                        <button type="submit" class="filter-apply-btn" style="background:linear-gradient(135deg,var(--amber) 0%,#b45309 100%);color:#1a0800;">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Aplicar y buscar
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- MAIN --}}
    <div class="cat-main">

        {{-- Sidebar eliminado: filtros ahora en barra superior --}}

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
                                <div style="display:flex;align-items:center;gap:.5rem;">
                                    @if($alquiler->promocion)
                                        <span class="ts-b-alquiler" style="background:linear-gradient(135deg, #ff6b6b 0%, #ff8787 100%); color:white; font-weight:900; box-shadow: 0 4px 12px rgba(255,107,107,0.4); animation: pulse-glow 2s infinite;">
                                            🔥 {{ number_format($alquiler->promocion->descuento_porcentaje, 0) }}% OFF
                                        </span>
                                    @endif
                                    <span class="ts-b-alquiler">Alquiler</span>
                                    @auth
                                        @if($alquiler->estado === 'disponible' && $alquiler->estado_aprobacion === 'aprobado')
                                        <button
                                            class="fav-btn {{ $alquiler->esFavorito() ? 'activo' : '' }}"
                                            data-id="{{ $alquiler->id }}"
                                            data-type="alquiler"
                                            title="{{ $alquiler->esFavorito() ? 'Quitar de favoritos' : 'Agregar a favoritos' }}"
                                            onclick="toggleFavorito(this)">
                                            <i class="fa-{{ $alquiler->esFavorito() ? 'solid' : 'regular' }} fa-heart"
                                                style="color:{{ $alquiler->esFavorito() ? '#f43f5e' : 'var(--text-2)' }};font-size:.85rem;pointer-events:none;"></i>
                                        </button>
                                        @endif
                                    @endauth
                                </div>
                            </div>

                            <div class="ts-price-overlay">
                                @if($alquiler->promocion)
                                    <div style="font-size:0.85rem; text-decoration:line-through; opacity:0.6; color:#fff; font-weight:600; line-height:1; margin-bottom:2px;">
                                        Bs. {{ number_format($alquiler->precio_mensual, 0) }}
                                    </div>
                                    <div class="price" style="color:#ff8787;">
                                        Bs. {{ number_format($alquiler->precio_mensual * (1 - $alquiler->promocion->descuento_porcentaje / 100), 0) }}
                                    </div>
                                @else
                                    <div class="price">Bs. {{ number_format($alquiler->precio_mensual, 0) }}</div>
                                @endif
                                <div class="currency">por mes</div>
                            </div>
                        </div>

                        {{-- BODY --}}
                        <div class="ts-prop-body">

                            {{-- Badge de categoría --}}
                            @if($alquiler->categoria)
                                <span class="ts-cat-badge" style="background:{{ $alquiler->categoria->color }}22;color:{{ $alquiler->categoria->color }};border:1px solid {{ $alquiler->categoria->color }}44;">
                                    {{ $alquiler->categoria->nombre }}
                                </span>
                            @endif

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
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
// ── Toggle panel filtros ──
function toggleFilterPanel(id) {
    const panel = document.getElementById(id);
    if (panel) panel.classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const panel = document.getElementById('panel-filtros-a');
    const btn = document.getElementById('btn-filtros-a');
    if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target)) {
        panel.classList.remove('open');
    }
});

// ── Mapa Alquileres ──
(function() {
    var initLat = {{ request('lat', -21.5355) }};
    var initLng = {{ request('lng', -63.6724) }};
    var initRadio = {{ request('radio', 5) }};
    var hasFiltro = {{ (request('lat') && request('lng')) ? 'true' : 'false' }};

    var map = L.map('map-alquileres', { zoomControl: true, attributionControl: false })
        .setView([initLat, initLng], hasFiltro ? 11 : 8);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19
    }).addTo(map);

    var marker = null;
    var circle = null;

    function setPoint(lat, lng, radio) {
        if (marker) { map.removeLayer(marker); map.removeLayer(circle); }
        marker = L.marker([lat, lng], {
            icon: L.divIcon({
                className: '',
                html: '<div style="width:14px;height:14px;background:#f59e0b;border:2px solid white;border-radius:50%;box-shadow:0 0 8px rgba(245,158,11,0.8);"></div>',
                iconAnchor: [7, 7]
            })
        }).addTo(map);
        circle = L.circle([lat, lng], {
            radius: radio * 1000,
            color: '#f59e0b',
            fillColor: '#f59e0b',
            fillOpacity: 0.08,
            weight: 1.5
        }).addTo(map);
        document.getElementById('map-lat-a').value = lat.toFixed(6);
        document.getElementById('map-lng-a').value = lng.toFixed(6);
    }

    if (hasFiltro) { setPoint(initLat, initLng, initRadio); }

    map.on('click', function(e) {
        var radio = parseInt(document.getElementById('map-radio-a').value) || 5;
        setPoint(e.latlng.lat, e.latlng.lng, radio);
        document.getElementById('form-mapa-alquileres').submit();
    });

    var radioSlider = document.getElementById('radio-slider-a');
    if (radioSlider) {
        radioSlider.addEventListener('change', function() {
            if (marker) {
                var lat = parseFloat(document.getElementById('map-lat-a').value);
                var lng = parseFloat(document.getElementById('map-lng-a').value);
                var radio = parseInt(this.value);
                setPoint(lat, lng, radio);
            }
        });
    }
})();

function toggleFavorito(btn) {
    const id   = btn.dataset.id;
    const type = btn.dataset.type;

    fetch('{{ route("favoritos.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ id, type })
    })
    .then(r => r.json())
    .then(data => {
        if (data.action === 'limit') {
            alert('Alcanzaste el límite de 50 favoritos. Elimina alguno para agregar otro.');
            return;
        }
        const icon = btn.querySelector('i');
        if (data.action === 'added') {
            icon.className       = 'fa-solid fa-heart';
            icon.style.color     = '#f43f5e';
            btn.classList.add('activo');
            btn.title            = 'Quitar de favoritos';
        } else {
            icon.className       = 'fa-regular fa-heart';
            icon.style.color     = 'var(--text-2)';
            btn.classList.remove('activo');
            btn.title            = 'Agregar a favoritos';
        }
    })
    .catch(() => alert('Error al actualizar favorito. Intenta de nuevo.'));
}

// ── Interactividad visual de los filtros de categoría ──
document.addEventListener('DOMContentLoaded', function() {
    const catLabels = document.querySelectorAll('.cat-pill');
    catLabels.forEach(label => {
        label.addEventListener('click', function() {
            const group = this.closest('.cats-grid');
            if (group) {
                group.querySelectorAll('.cat-pill').forEach(pill => {
                    pill.classList.remove('active');
                    pill.style.background = '';
                    pill.style.borderColor = '';
                    pill.style.color = '';
                });
            }
            this.classList.add('active');
            const radio = this.querySelector('input[type="radio"]');
            if (radio && radio.value !== '') {
                const color = radio.dataset.color;
                if (color) {
                    this.style.borderColor = color + '66';
                    this.style.color = color;
                    this.style.background = color + '22';
                }
            }
        });
    });
});
</script>
@endpush
@endsection