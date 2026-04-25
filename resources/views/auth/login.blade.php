@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')

    <body class="login-page">

        <div class="login-container">
            <!-- Hero Section -->
            <div class="login-hero">
                <div class="hero-content">
                    <div class="hero-logo">
                        <div class="hero-logo-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5z" />
                                <path d="M2 17l10 5 10-5" />
                                <path d="M2 12l10 5 10-5" />
                            </svg>
                        </div>
                        <div class="hero-logo-text">
                            <h1>TerrenoSur</h1>
                            <span class="hero-module">Módulo IN-A01</span>
                        </div>
                    </div>

                    <p class="hero-subtitle">Sistema de Verificación de Identidad</p>

                    <div class="hero-features">
                        <div class="hero-feature">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                            <span>Almacenamiento seguro de documentos</span>
                        </div>
                        <div class="hero-feature">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                            </svg>
                            <span>Panel de administración intuitivo</span>
                        </div>
                        <div class="hero-feature">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                            </svg>
                            <span>Proceso de verificación rápido</span>
                        </div>
                    </div>
                </div>

                <div class="hero-decoration">
                    <div class="decoration-circle c1"></div>
                    <div class="decoration-circle c2"></div>
                    <div class="decoration-circle c3"></div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="login-form-panel">
                <div class="login-form-wrapper">
                    <div class="login-form-header">
                        <h2>Acceso al Sistema</h2>
                        <p>Ingrese sus credenciales para comenzar</p>
                    </div>

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

                    <form action="{{ route('login.post') }}" method="POST" class="login-form" id="loginForm">
                        @csrf
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                Correo Electrónico
                            </label>
                            <input type="email" name="email" id="email" class="form-control"
                                placeholder="usuario@ejemplo.com" required autocomplete="email" value="{{ old('email') }}">
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                Contraseña
                            </label>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="••••••••" required autocomplete="current-password">
                                <button type="button" class="password-toggle" id="passwordToggle"
                                    title="Mostrar/ocultar contraseña">
                                    <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" style="display:none;">
                                        <path
                                            d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                                        <line x1="1" y1="1" x2="23" y2="23" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" id="loginBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                <polyline points="10,17 15,12 10,7" />
                                <line x1="15" y1="12" x2="3" y2="12" />
                            </svg>
                            Iniciar Sesión
                        </button>
                    </form>

                    <div class="login-footer">
                        <div class="login-security">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                <polyline points="9,12 12,15 15,12" />
                            </svg>
                            Sistema seguro con autenticación CSRF
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Se ejecuta sobre el body porque en Blade extendemos todo el file pero el login-page body lo ponemos dentro del yield que quizas no reemplace el <body> raiz si no se modifica el layout.
            // Vamos a corregir la inyección de clases en el HTML general.
            document.body.classList.add('login-page');
        </script>
@endsection