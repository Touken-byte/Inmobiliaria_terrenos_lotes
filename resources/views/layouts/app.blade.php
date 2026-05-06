<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TerrenoSur - Sistema de Verificación de Usuarios IN-A01">
    <title>@yield('title', 'TerrenoSur') | TerrenoSur</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-theme.css') }}">
    @stack('styles')
</head>

<body class="@auth theme-{{ Auth::user()->rol === 'admin' ? 'admin' : 'vendedor' }} @endauth">
    @auth
        <div class="app-layout">
            <!-- ═══ Sidebar ═══ -->
            <aside class="sidebar" id="sidebar">
                <div class="sidebar-header">
                    <div class="sidebar-logo">
                        <div class="logo-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path
                                    d="M3 21h18M9 8h1M9 12h1M9 16h1M14 8h1M14 12h1M14 16h1M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16" />
                            </svg>
                        </div>
                        <div class="logo-text">
                            <span class="logo-name">TerrenoSur</span>
                            <span class="logo-module">IN-A01</span>
                        </div>
                    </div>
                </div>

                <nav class="sidebar-nav">
                    <div class="nav-section">
                        <span class="nav-section-title">Menú Principal</span>

                        @if(Auth::user()->rol === 'admin')
                            <a href="{{ url('/admin/panel') }}"
                                class="nav-link {{ request()->is('admin/panel') ? 'active' : '' }}" id="nav-panel">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7" rx="1" />
                                        <rect x="14" y="3" width="7" height="7" rx="1" />
                                        <rect x="3" y="14" width="7" height="7" rx="1" />
                                        <rect x="14" y="14" width="7" height="7" rx="1" />
                                    </svg>
                                </span>
                                <span>Panel de Verificación</span>
                            </a>
                            <a href="{{ url('/admin/historial') }}"
                                class="nav-link {{ request()->is('admin/historial') ? 'active' : '' }}" id="nav-historial">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12,6 12,12 16,14" />
                                    </svg>
                                </span>
                                <span>Historial</span>
                            </a>
                            <a href="{{ url('/admin/moderacion') }}"
                                class="nav-link {{ request()->is('admin/moderacion') ? 'active' : '' }}" id="nav-moderacion-admin">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2L2 7l10 5 10-5-10-5z" />
                                        <path d="M2 17l10 5 10-5" />
                                        <path d="M2 12l10 5 10-5" />
                                    </svg>
                                </span>
                                <span>Moderación Anuncios</span>
                            </a>
                            <a href="{{ url('/admin/terrenos') }}"
                                class="nav-link {{ request()->is('admin/terrenos*') ? 'active' : '' }}" id="nav-terrenos-admin">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path
                                            d="M3 21h18M9 8h1M9 12h1M9 16h1M14 8h1M14 12h1M14 16h1M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16" />
                                    </svg>
                                </span>
                                <span>Gestión Terrenos</span>
                            </a>
                            <a href="{{ route('admin.lotes') }}"
                                class="nav-link {{ request()->is('admin/lotes*') ? 'active' : '' }}" id="nav-lotes-admin">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7" />
                                        <rect x="14" y="3" width="7" height="7" />
                                        <rect x="14" y="14" width="7" height="7" />
                                        <rect x="3" y="14" width="7" height="7" />
                                    </svg>
                                </span>
                                <span>Control de Lotes</span>
                            </a>
                            <a href="{{ route('admin.tramites_legales.index') }}"
                                class="nav-link {{ request()->is('admin/tramites-legales*') ? 'active' : '' }}" id="nav-tramites-legales-admin">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </span>
                                <span>Gestión Legal</span>
                            </a>

                            {{-- ═══ FOLIOS ═══ --}}
                            <a href="{{ route('admin.folios_panel') }}"
                                class="nav-link {{ request()->is('admin/folios*') ? 'active' : '' }}" id="nav-folios-admin">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                        <polyline points="14,2 14,8 20,8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <polyline points="10,9 9,9 8,9"/>
                                    </svg>
                                </span>
                                <span>Folios</span>
                            </a>

                            {{-- ═══ INSCRIPCIONES DERECHOS REALES ═══ --}}
                            <a href="{{ route('admin.inscripciones') }}"
                                class="nav-link {{ request()->is('admin/inscripciones*') ? 'active' : '' }}" id="nav-inscripciones-admin">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <path d="M9 12l2 2 4-4"/>
                                    </svg>
                                </span>
                                <span>Derechos Reales</span>
                            </a>

                            {{-- ═══ AUDITORÍA ═══ --}}
                            <a href="{{ route('admin.auditoria') }}"
                                class="nav-link {{ request()->is('admin/auditoria*') ? 'active' : '' }}" id="nav-auditoria-admin">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                </span>
                                <span>Auditoría</span>
                            </a>

                            <a href="{{ route('vendedor.solicitudes.index') }}"
                                class="nav-link {{ request()->is('vendedor/solicitudes*') ? 'active' : '' }}" id="nav-solicitudes-admin">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                        <line x1="8" y1="8" x2="16" y2="16" />
                                        <line x1="16" y1="8" x2="8" y2="16" />
                                    </svg>
                                </span>
                                <span>Solicitudes de Visita</span>
                            </a>
                        @else
                            <a href="{{ url('/vendedor/dashboard') }}"
                                class="nav-link {{ request()->is('vendedor/dashboard') ? 'active' : '' }}" id="nav-dashboard">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        <polyline points="9,22 9,12 15,12 15,22" />
                                    </svg>
                                </span>
                                <span>Mi Dashboard</span>
                            </a>
                            <a href="{{ route('vendedor.terrenos.create') }}"
                                class="nav-link {{ request()->is('vendedor/terrenos/crear') ? 'active' : '' }}"
                                id="nav-terrenos">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                        <line x1="12" y1="8" x2="12" y2="16" />
                                        <line x1="8" y1="12" x2="16" y2="12" />
                                    </svg>
                                </span>
                                <span>Publicar Terreno</span>
                            </a>
                            <a href="{{ route('vendedor.terrenos.mis') }}"
                                class="nav-link {{ request()->is('vendedor/mis-terrenos') ? 'active' : '' }}"
                                id="nav-mis-terrenos">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                        <line x1="3" y1="9" x2="21" y2="9" />
                                        <line x1="9" y1="21" x2="9" y2="9" />
                                    </svg>
                                </span>
                                <span>Mis Terrenos</span>
                            </a>
                            <a href="{{ route('vendedor.alquileres.create') }}"
                                class="nav-link {{ request()->is('vendedor/alquileres/crear') ? 'active' : '' }}"
                                id="nav-alquileres-create">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        <polyline points="9,22 9,12 15,12 15,22" />
                                    </svg>
                                </span>
                                <span>Publicar Alquiler</span>
                            </a>
                            <a href="{{ route('vendedor.alquileres.mis') }}"
                                class="nav-link {{ request()->is('vendedor/mis-alquileres') ? 'active' : '' }}"
                                id="nav-mis-alquileres">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                        <path d="M3 9h18" />
                                        <path d="M9 21V9" />
                                    </svg>
                                </span>
                                <span>Mis Alquileres</span>
                            </a>
                            <a href="{{ route('vendedor.lotes') }}"
                                class="nav-link {{ request()->is('vendedor/lotes*') ? 'active' : '' }}" id="nav-lotes-vendedor">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7" />
                                        <rect x="14" y="3" width="7" height="7" />
                                        <rect x="14" y="14" width="7" height="7" />
                                        <rect x="3" y="14" width="7" height="7" />
                                    </svg>
                                </span>
                                <span>Control de Lotes</span>
                            </a>
                            <a href="{{ route('vendedor.proceso_legal') }}"
                                class="nav-link {{ request()->is('vendedor/proceso-legal*') ? 'active' : '' }}" id="nav-proceso-legal">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </span>
                                <span>Proceso Legal de Venta</span>
                            </a>
                            <a href="{{ route('vendedor.historial_legal') }}"
                                class="nav-link {{ request()->is('vendedor/historial-legal*') ? 'active' : '' }}" id="nav-historial-legal">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                                    </svg>
                                </span>
                                <span>Historial Legal</span>
                            </a>
                            <a href="{{ route('vendedor.solicitudes.index') }}"
                                class="nav-link {{ request()->is('vendedor/solicitudes*') ? 'active' : '' }}" id="nav-solicitudes">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                        <line x1="8" y1="8" x2="16" y2="16" />
                                        <line x1="16" y1="8" x2="8" y2="16" />
                                    </svg>
                                </span>
                                <span>Solicitudes de Visita</span>
                            </a>
                        @endif
                    </div>
                </nav>

                <div class="sidebar-footer">
                    <div class="sidebar-user">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <span class="user-name">{{ Auth::user()->nombre }}</span>
                            <span class="user-role badge badge-{{ Auth::user()->rol === 'admin' ? 'info' : 'secondary' }}">
                                {{ Auth::user()->rol === 'admin' ? '👨‍💼 Admin' : '👤 Vendedor' }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="nav-link nav-logout" id="nav-logout">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
                                <polyline points="16,17 21,12 16,7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                        </span>
                        <span>Cerrar Sesión</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </aside>

            <!-- ═══ Main Content ═══ -->
            <div class="main-wrapper">
                <header class="topbar">
                    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <line x1="3" y1="12" x2="21" y2="12" />
                            <line x1="3" y1="18" x2="21" y2="18" />
                        </svg>
                    </button>
                    <div class="topbar-title">
                        <h1 id="page-title">@yield('title', 'TerrenoSur')</h1>
                    </div>
                    <div class="topbar-actions">
                        <span class="topbar-greeting">Hola,
                            <strong>{{ explode(' ', Auth::user()->nombre)[0] }}</strong></span>
                    </div>
                </header>

                <main class="content">
                    @if (session('success'))
                        <div class="alert alert-success">
                            <div class="alert-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                    <polyline points="22,4 12,14.01 9,11.01" />
                                </svg>
                            </div>
                            <div class="alert-content">
                                {{ session('success') }}
                            </div>
                            <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-error">
                            <div class="alert-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="15" y1="9" x2="9" y2="15" />
                                    <line x1="9" y1="9" x2="15" y2="15" />
                                </svg>
                            </div>
                            <div class="alert-content">
                                {{ session('error') }}
                            </div>
                            <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-error">
                            <div class="alert-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="15" y1="9" x2="9" y2="15" />
                                    <line x1="9" y1="9" x2="15" y2="15" />
                                </svg>
                            </div>
                            <div class="alert-content">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                            <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                        </div>
                    @endif

                    @yield('content')
                </main>

                @if(!request()->routeIs('vendedor.proceso_legal') && !request()->routeIs('vendedor.historial_legal'))
                <footer class="app-footer">
                    <p>&copy; {{ date('Y') }} TerrenoSur — Módulos IN-A01 Verificación de Usuarios · IN-U01 Publicación de
                        Terrenos</p>
                </footer>
                @endif
            </div>
        </div>
    @endauth

    @guest
        @yield('content')
    @endguest

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>