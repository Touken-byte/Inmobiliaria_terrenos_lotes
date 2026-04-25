@extends('layouts.app')

@section('title', 'Detalle de Solicitud')

@section('content')
<style>
    .glass-modal {
        display: none;
        position: fixed;
        z-index: var(--z-modal);
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 20, 25, 0.6);
        backdrop-filter: blur(8px);
        align-items: center;
        justify-content: center;
    }
    .glass-modal.active {
        display: flex;
        animation: fadeIn 0.3s ease;
    }
    .glass-modal-content {
        background: var(--bg-lighter);
        border-radius: var(--border-radius-lg);
        width: 100%;
        max-width: 500px;
        box-shadow: var(--shadow-xl);
        overflow: hidden;
        animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .glass-modal-header {
        padding: 24px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .glass-modal-body {
        padding: 24px;
        background: var(--bg-lighter);
    }
    @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <div>
                <h1 style="font-size: 1.5rem; margin-bottom:0;">Detalle de Solicitud de Visita</h1>
                <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal; margin-top:4px;">Información completa y estado de la solicitud</p>
            </div>
        </div>
        <a href="{{ route('vendedor.solicitudes.index') }}" class="btn btn-secondary btn-sm">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Volver
        </a>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Columna Izquierda -->
    <div class="dashboard-left" style="display:flex; flex-direction:column; gap:24px;">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="font-size: 1.1rem;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                    Información de la Solicitud
                </h3>
            </div>
            <div class="card-body">
                <div class="doc-info-row">
                    <span class="doc-info-label">Fecha de visita:</span>
                    <span class="doc-info-value">{{ $solicitud->fecha_visita->format('d/m/Y') }}</span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Horario:</span>
                    <span class="doc-info-value">{{ substr($solicitud->hora_inicio, 0, 5) }} - {{ substr($solicitud->hora_fin, 0, 5) }}</span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Estado:</span>
                    <span class="doc-info-value" style="display:flex; justify-content:flex-end;">
                        @if($solicitud->estado === 'pendiente')
                            <span class="badge badge-warning">Pendiente</span>
                        @elseif($solicitud->estado === 'aprobada')
                            <span class="badge badge-success">Aprobada</span>
                        @elseif($solicitud->estado === 'rechazada')
                            <span class="badge badge-danger">Rechazada</span>
                        @elseif($solicitud->estado === 'cancelada')
                            <span class="badge badge-secondary">Cancelada</span>
                        @endif
                    </span>
                </div>
                
                @if($solicitud->estado === 'rechazada' && $solicitud->motivo_rechazo)
                <div class="alert alert-error" style="margin-top: 20px; border-radius: 8px;">
                    <div class="alert-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></div>
                    <div class="alert-content">
                        <strong>Motivo de rechazo:</strong>
                        <p style="margin-top:4px;">{{ $solicitud->motivo_rechazo }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="font-size: 1.1rem;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    Información del Terreno
                </h3>
            </div>
            <div class="card-body">
                <div class="doc-info-row">
                    <span class="doc-info-label">Nombre:</span>
                    <span class="doc-info-value">{{ $solicitud->terreno->nombre }}</span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Ubicación:</span>
                    <span class="doc-info-value">{{ $solicitud->terreno->ubicacion }}</span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Área:</span>
                    <span class="doc-info-value">{{ $solicitud->terreno->area }} m²</span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Precio:</span>
                    <span class="doc-info-value" style="color:var(--success); font-weight:700;">${{ number_format($solicitud->terreno->precio, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Columna Derecha -->
    <div class="dashboard-right">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="font-size: 1.1rem;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Información del Cliente
                </h3>
            </div>
            <div class="card-body">
                <div style="display:flex; align-items:center; gap:16px; margin-bottom: 20px;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--accent)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; box-shadow: var(--shadow-sm);">
                        {{ strtoupper(substr($solicitud->usuario->nombre, 0, 1)) }}
                    </div>
                    <div>
                        <h4 style="font-size:1.1rem; margin:0;">{{ $solicitud->usuario->nombre }}</h4>
                        <span style="color:var(--text-muted); font-size:0.9rem;">Cliente Solicitante</span>
                    </div>
                </div>

                <div class="doc-info-row">
                    <span class="doc-info-label">Email:</span>
                    <span class="doc-info-value">{{ $solicitud->usuario->email }}</span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Teléfono:</span>
                    <span class="doc-info-value">{{ $solicitud->usuario->telefono }}</span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Cédula:</span>
                    <span class="doc-info-value">{{ $solicitud->usuario->cedula }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="font-size: 1.1rem;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5c-1.1 0-2 .9-2 2v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                    Información del Vendedor
                </h3>
            </div>
            <div class="card-body">
                <div class="doc-info-row">
                    <span class="doc-info-label">Nombre:</span>
                    <span class="doc-info-value">{{ $solicitud->vendedor->nombre }}</span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Email:</span>
                    <span class="doc-info-value">{{ $solicitud->vendedor->email }}</span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Teléfono:</span>
                    <span class="doc-info-value">{{ $solicitud->vendedor->telefono }}</span>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Barra de acciones -->
<div class="card" style="margin-top: 24px; background: var(--bg-lighter);">
    <div class="card-body" style="display: flex; gap: 12px; justify-content: flex-end; align-items: center;">
        <a href="{{ route('vendedor.solicitudes.index') }}" class="btn btn-secondary">
            Volver al listado
        </a>

        @if(Auth::user()->rol === 'admin' && $solicitud->estado === 'pendiente')
            <form action="{{ route('vendedor.solicitudes.aprobar', $solicitud->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-success">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"></path></svg>
                    Aprobar Solicitud
                </button>
            </form>
            <button onclick="document.getElementById('modal-rechazo').classList.add('active')" class="btn btn-danger">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                Rechazar Solicitud
            </button>

            <!-- Modal para rechazar -->
            <div id="modal-rechazo" class="glass-modal">
                <div class="glass-modal-content">
                    <div class="glass-modal-header">
                        <h3 style="font-size:1.2rem; color:var(--text-primary); margin:0;">Rechazar Solicitud</h3>
                        <button type="button" onclick="document.getElementById('modal-rechazo').classList.remove('active')" style="background:none; border:none; cursor:pointer;"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 6L6 18M6 6l12 12"></path></svg></button>
                    </div>
                    <form action="{{ route('vendedor.solicitudes.rechazar', $solicitud->id) }}" method="POST">
                        @csrf
                        <div class="glass-modal-body">
                            <div class="form-group">
                                <label class="form-label">Motivo del rechazo <span class="required">*</span></label>
                                <textarea name="motivo" class="form-control" rows="3" required placeholder="Escriba el motivo real del rechazo aquí..."></textarea>
                            </div>
                            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                                <button type="button" onclick="document.getElementById('modal-rechazo').classList.remove('active')" class="btn btn-secondary">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Rechazar Permanentemente</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if($solicitud->estado === 'aprobada' && $solicitud->puedeCancelar())
            <form action="{{ route('vendedor.solicitudes.cancelar', $solicitud->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de cancelar esta visita?')">
                @csrf
                <button type="submit" class="btn btn-outline" style="color:var(--text-muted); border-color:var(--text-muted);">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    Cancelar Visita
                </button>
            </form>
        @endif
    </div>
</div>
@endsection