@extends('layouts.app')

@section('title', 'Crear Cuenta')

@section('content')
<body class="login-page">
<div class="login-container">

    <!-- Hero -->
    <div class="login-hero">
        <div class="hero-content">
            <div class="hero-logo">
                <div class="hero-logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <div class="hero-logo-text">
                    <h1>TerrenoSur</h1>
                    <span class="hero-module">Registro de Cliente</span>
                </div>
            </div>
            <p class="hero-subtitle">Crea tu cuenta para explorar y consultar terrenos disponibles</p>
            <div class="hero-features">
                <div class="hero-feature">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span>Explora terrenos disponibles</span>
                </div>
                <div class="hero-feature">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <span>Consulta folios y documentación</span>
                </div>
                <div class="hero-feature">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span>Información verificada y segura</span>
                </div>
            </div>
        </div>
        <div class="hero-decoration">
            <div class="decoration-circle c1"></div>
            <div class="decoration-circle c2"></div>
            <div class="decoration-circle c3"></div>
        </div>
    </div>

    <!-- Form -->
    <div class="login-form-panel">
        <div class="login-form-wrapper">
            <div class="login-form-header">
                <h2>Crear Cuenta</h2>
                <p>Completa los datos para registrarte como cliente</p>
            </div>

            @if($errors->any())
                <div class="alert alert-error">
                    <div class="alert-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    </div>
                    <div class="alert-content">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                    <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            <form action="{{ route('registro.post') }}" method="POST" class="login-form">
                @csrf

                <div class="form-group">
                    <label for="nombre" class="form-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Nombre Completo
                    </label>
                    <input type="text" name="nombre" id="nombre" class="form-control"
                        placeholder="Tu nombre completo" required value="{{ old('nombre') }}">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        Correo Electrónico
                    </label>
                    <input type="email" name="email" id="email" class="form-control"
                        placeholder="tu@correo.com" required value="{{ old('email') }}">
                    <small style="color:#6c757d;font-size:0.75rem;margin-top:4px;display:block;">
                        🔒 Tu correo debe ser real y válido.
                    </small>
                </div>

                <div class="form-group">
                    <label for="telefono" class="form-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.56 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        Teléfono <small style="font-weight:400;">(opcional)</small>
                    </label>
                    <input type="text" name="telefono" id="telefono" class="form-control"
                        placeholder="+591 7XXXXXXX" value="{{ old('telefono') }}">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        Contraseña
                    </label>
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Mín. 8 car., mayúscula, número y símbolo" required
                        oninput="checkPasswordStrength(this.value)">
                    {{-- Indicador de fortaleza --}}
                    <div id="pass-strength-bar" style="height:4px;border-radius:2px;margin-top:6px;background:#e9ecef;overflow:hidden;">
                        <div id="pass-strength-fill" style="height:100%;width:0%;background:#dc3545;transition:width 0.3s,background 0.3s;"></div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:3px;margin-top:8px;" id="pass-reqs">
                        <span id="req-len"   style="font-size:0.72rem;color:#6c757d;">&#9675; Mínimo 8 caracteres</span>
                        <span id="req-upper" style="font-size:0.72rem;color:#6c757d;">&#9675; Al menos una mayúscula (A-Z)</span>
                        <span id="req-lower" style="font-size:0.72rem;color:#6c757d;">&#9675; Al menos una minúscula (a-z)</span>
                        <span id="req-num"   style="font-size:0.72rem;color:#6c757d;">&#9675; Al menos un número (0-9)</span>
                        <span id="req-sym"   style="font-size:0.72rem;color:#6c757d;">&#9675; Al menos un símbolo (!@#$%...)</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        Confirmar Contraseña
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="form-control" placeholder="Repite tu contraseña" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                    Crear Cuenta
                </button>
            </form>

            <div style="text-align:center; margin-top:1.25rem;">
                <p style="font-size:0.88rem; color:#6c757d;">
                    ¿Ya tienes cuenta?
                    <a href="{{ route('login') }}" style="color:#007bff; font-weight:600; text-decoration:none;">
                        Iniciar Sesión
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>
<script>
    document.body.classList.add('login-page');

    function checkPasswordStrength(val) {
        var checks = {
            'req-len':   val.length >= 8,
            'req-upper': /[A-Z]/.test(val),
            'req-lower': /[a-z]/.test(val),
            'req-num':   /[0-9]/.test(val),
            'req-sym':   /[^A-Za-z0-9]/.test(val),
        };
        var passed = 0;
        Object.keys(checks).forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.textContent = (checks[id] ? '✅' : '○') + ' ' + el.textContent.replace(/^[✅○]\s+/, '');
                el.style.color = checks[id] ? '#28a745' : '#6c757d';
            }
            if (checks[id]) passed++;
        });
        var fill = document.getElementById('pass-strength-fill');
        if (fill) {
            fill.style.width = (passed * 20) + '%';
            fill.style.background = passed <= 2 ? '#dc3545' : passed <= 3 ? '#ffc107' : passed === 4 ? '#17a2b8' : '#28a745';
        }
    }
</script>
@endsection