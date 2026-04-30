<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TerrenoSur')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @stack('styles')

    <style>
        :root {
            --void:         #050810;
            --deep:         #080d1a;
            --surface:      #0c1326;
            --card:         #0f1830;
            --card-raised:  #121e38;
            --rim:          rgba(120,160,255,0.10);
            --rim-bright:   rgba(120,160,255,0.28);
            --gold:         #c9a84c;
            --gold-light:   #e8c97a;
            --gold-glow:    rgba(201,168,76,0.18);
            --cobalt:       #3d7ef5;
            --cobalt-soft:  rgba(61,126,245,0.14);
            --emerald:      #1dba7e;
            --text-1:       #eef2fc;
            --text-2:       #8fa3cc;
            --text-3:       #3d5480;
            --font-serif:   'Cormorant Garamond', Georgia, serif;
            --font-sans:    'Outfit', system-ui, sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            background: var(--void);
            color: var(--text-1);
            font-family: var(--font-sans);
            font-weight: 400;
            min-height: 100svh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Grain overlay ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            background-size: 200px;
            pointer-events: none;
            z-index: 9999;
            opacity: 0.5;
        }

        main { flex: 1; }

        /* ══════════════════════════════════
           NAVBAR
        ══════════════════════════════════ */
        .ts-nav {
            position: sticky;
            top: 0;
            z-index: 100;
            height: 68px;
            background: rgba(5,8,16,0.85);
            backdrop-filter: blur(24px) saturate(1.5);
            -webkit-backdrop-filter: blur(24px) saturate(1.5);
            border-bottom: 1px solid var(--rim);
        }

        /* Top gold line */
        .ts-nav::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg,
                transparent 0%,
                var(--gold) 30%,
                var(--gold-light) 50%,
                var(--gold) 70%,
                transparent 100%
            );
            opacity: 0.5;
        }

        .ts-nav-inner {
            max-width: 1360px;
            margin: 0 auto;
            padding: 0 2rem;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Logo */
        .ts-logo {
            display: flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
            transition: opacity .2s;
        }
        .ts-logo:hover { opacity: .8; }

        .ts-logo-mark {
            position: relative;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
        }
        .ts-logo-mark svg { width: 100%; height: 100%; }

        .ts-logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }
        .ts-logo-name {
            font-family: var(--font-serif);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-1);
            letter-spacing: .01em;
        }
        .ts-logo-name em {
            font-style: italic;
            color: var(--gold);
        }
        .ts-logo-sub {
            font-size: .6rem;
            font-weight: 500;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--text-3);
            margin-top: .2rem;
        }

        /* Nav actions */
        .ts-nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .ts-user-chip {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .4rem .85rem .4rem .4rem;
            background: var(--card);
            border: 1px solid var(--rim);
            border-radius: 100px;
            transition: border-color .2s;
        }
        .ts-user-chip:hover { border-color: var(--rim-bright); }

        .ts-user-ava {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--cobalt) 0%, #6b3fd8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .65rem;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            flex-shrink: 0;
        }
        .ts-user-name {
            font-size: .8rem;
            font-weight: 600;
            color: var(--text-1);
        }

        .ts-btn-logout {
            padding: .4rem .9rem;
            border: 1px solid var(--rim);
            border-radius: 100px;
            background: transparent;
            font-family: var(--font-sans);
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .06em;
            color: var(--text-3);
            cursor: pointer;
            transition: all .2s;
        }
        .ts-btn-logout:hover {
            color: #f87171;
            border-color: rgba(248,113,113,0.3);
            background: rgba(248,113,113,0.06);
        }

        .ts-btn-login {
            padding: .5rem 1.25rem;
            background: transparent;
            border: 1px solid rgba(201,168,76,0.35);
            border-radius: 100px;
            font-family: var(--font-sans);
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: .06em;
            color: var(--gold);
            text-decoration: none;
            transition: all .25s;
        }
        .ts-btn-login:hover {
            background: var(--gold-glow);
            border-color: var(--gold);
            box-shadow: 0 0 24px var(--gold-glow);
        }

        /* ══════════════════════════════════
           FOOTER
        ══════════════════════════════════ */
        .ts-footer {
            position: relative;
            border-top: 1px solid var(--rim);
            padding: 2rem;
            overflow: hidden;
        }
        .ts-footer::before {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg,
                transparent 0%,
                var(--gold) 50%,
                transparent 100%
            );
            opacity: 0.2;
        }
        .ts-footer-inner {
            max-width: 1360px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .ts-footer-brand {
            font-family: var(--font-serif);
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-3);
            text-decoration: none;
        }
        .ts-footer-brand em { color: var(--gold); font-style: italic; }
        .ts-footer-copy {
            font-size: .72rem;
            color: var(--text-3);
            letter-spacing: .05em;
        }
    </style>
    @stack('scripts')
</body>
</html>

<!-- ── NAVBAR ── -->
<nav class="ts-nav">
    <div class="ts-nav-inner">
        <a href="{{ route('catalogo.terrenos') }}" class="ts-logo">
            <div class="ts-logo-mark">
                <svg viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="38" height="38" rx="10" fill="url(#lgm)"/>
                    <path d="M8 27L14.5 16L19 22L23.5 14L30 27H8Z" fill="white" fill-opacity="0.9"/>
                    <path d="M8 27L14.5 16L19 22" stroke="rgba(255,255,255,0.3)" stroke-width="0.5"/>
                    <defs>
                        <linearGradient id="lgm" x1="0" y1="0" x2="38" y2="38" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#1a3a7a"/>
                            <stop offset="1" stop-color="#0a1f4e"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <div class="ts-logo-text">
                <span class="ts-logo-name">Terreno<em>Sur</em></span>
                <span class="ts-logo-sub">Marketplace Premium</span>
            </div>
        </a>

        <div class="ts-nav-actions">
            @auth
                <div class="ts-user-chip">
                    <div class="ts-user-ava">{{ substr(Auth::user()->nombre, 0, 1) }}</div>
                    <span class="ts-user-name">{{ Auth::user()->nombre }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="ts-btn-logout">Salir</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="ts-btn-login">Iniciar Sesión</a>
            @endauth
        </div>
    </div>
</nav>

<main>@yield('content')</main>

<!-- ── FOOTER ── -->
<footer class="ts-footer">
    <div class="ts-footer-inner">
        <a href="{{ route('catalogo.terrenos') }}" class="ts-footer-brand">
            Terreno<em>Sur</em>
        </a>
        <p class="ts-footer-copy">
            &copy; {{ date('Y') }} TerrenoSur Marketplace &mdash; Todos los derechos reservados
        </p>
    </div>
</footer>

</body>
</html>
