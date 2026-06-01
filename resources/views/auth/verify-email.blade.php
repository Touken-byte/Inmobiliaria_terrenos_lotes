@extends('layouts.app')

@section('title', 'Verifica tu Correo | TerrenoSur')

@section('content')
<style>
    .verify-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #050810 0%, #0c1326 50%, #080d1a 100%);
        padding: 2rem;
        font-family: 'Outfit', system-ui, sans-serif;
    }

    .verify-card {
        background: rgba(15, 24, 48, 0.95);
        border: 1px solid rgba(120, 160, 255, 0.15);
        border-radius: 20px;
        padding: 3rem 2.5rem;
        max-width: 480px;
        width: 100%;
        text-align: center;
        box-shadow:
            0 25px 50px rgba(0,0,0,0.5),
            0 0 80px rgba(61,126,245,0.06),
            inset 0 1px 0 rgba(120,160,255,0.08);
        position: relative;
        overflow: hidden;
    }

    .verify-card::before {
        content: '';
        position: absolute;
        top: -80px; left: 50%;
        transform: translateX(-50%);
        width: 400px; height: 300px;
        background: radial-gradient(ellipse at center,
            rgba(61,126,245,0.10) 0%,
            transparent 70%
        );
        pointer-events: none;
    }

    .verify-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, rgba(61,126,245,0.2), rgba(29,186,126,0.1));
        border: 2px solid rgba(61,126,245,0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        animation: float-icon 3s ease-in-out infinite;
    }

    @keyframes float-icon {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-8px); }
    }

    .verify-icon svg {
        width: 36px;
        height: 36px;
        color: #3d7ef5;
    }

    .verify-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.9rem;
        background: rgba(61,126,245,0.1);
        border: 1px solid rgba(61,126,245,0.25);
        border-radius: 100px;
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: #3d7ef5;
        margin-bottom: 1.2rem;
    }

    .verify-title {
        font-size: 1.9rem;
        font-weight: 700;
        color: #eef2fc;
        margin-bottom: 0.75rem;
        line-height: 1.2;
    }

    .verify-desc {
        font-size: 0.95rem;
        color: #8fa3cc;
        line-height: 1.7;
        margin-bottom: 2rem;
    }

    .verify-desc strong {
        color: #eef2fc;
    }

    .alert-success-custom {
        background: rgba(29,186,126,0.12);
        border: 1px solid rgba(29,186,126,0.3);
        border-radius: 12px;
        padding: 0.9rem 1.2rem;
        color: #1dba7e;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .btn-verify {
        width: 100%;
        padding: 0.9rem;
        background: linear-gradient(135deg, #3d7ef5, #2563c7);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 20px rgba(61,126,245,0.3);
    }

    .btn-verify:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(61,126,245,0.45);
    }

    .btn-verify:active {
        transform: translateY(0);
    }

    .btn-verify svg {
        width: 18px; height: 18px;
    }

    .divider {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 1.5rem 0;
        color: #3d5480;
        font-size: 0.8rem;
    }

    .divider::before, .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(120,160,255,0.1);
    }

    .steps {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(120,160,255,0.08);
        border-radius: 12px;
        padding: 1.25rem;
        text-align: left;
        margin-bottom: 1.5rem;
    }

    .steps-title {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #3d5480;
        margin-bottom: 1rem;
    }

    .step-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .step-item:last-child { margin-bottom: 0; }

    .step-num {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(61,126,245,0.15);
        border: 1px solid rgba(61,126,245,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        color: #3d7ef5;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .step-text {
        font-size: 0.85rem;
        color: #8fa3cc;
        line-height: 1.5;
    }

    .back-login {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: #3d5480;
        font-size: 0.85rem;
        text-decoration: none;
        transition: color 0.2s;
        margin-top: 0.5rem;
    }

    .back-login:hover {
        color: #8fa3cc;
    }

    .back-login svg {
        width: 14px; height: 14px;
    }
</style>

<div class="verify-page">
    <div class="verify-card">

        {{-- Ícono animado --}}
        <div class="verify-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
        </div>

        <div class="verify-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:10px;height:10px;">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Verificación de Seguridad
        </div>

        <h1 class="verify-title">Verifica tu correo</h1>

        <p class="verify-desc">
            Hemos enviado un enlace de verificación a tu correo electrónico.
            Revisa tu bandeja de entrada y haz clic en el enlace para activar tu cuenta.
        </p>

        {{-- Mensaje de éxito al reenviar --}}
        @if (session('success'))
            <div class="alert-success-custom">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;flex-shrink:0;">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 15.01 9 12.01"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Pasos de verificación --}}
        <div class="steps">
            <div class="steps-title">¿Qué debes hacer?</div>
            <div class="step-item">
                <div class="step-num">1</div>
                <div class="step-text">Abre tu bandeja de entrada o la carpeta <strong>Spam/Correo no deseado</strong></div>
            </div>
            <div class="step-item">
                <div class="step-num">2</div>
                <div class="step-text">Busca un correo de <strong>TerrenoSur</strong> con el asunto "Verifica tu correo"</div>
            </div>
            <div class="step-item">
                <div class="step-num">3</div>
                <div class="step-text">Haz clic en el botón de verificación dentro del correo</div>
            </div>
        </div>

        {{-- Botón para reenviar enlace --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-verify">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 4 23 10 17 10"/>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                </svg>
                Reenviar enlace de verificación
            </button>
        </form>

        <div class="divider">o</div>

        <a href="{{ route('logout') }}" class="back-login"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Volver al inicio de sesión
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>
</div>
@endsection
