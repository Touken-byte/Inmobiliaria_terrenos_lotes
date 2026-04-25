@extends('layouts.app')

@section('title', 'Solicitudes de Visita')

@section('content')
<style>
    /* Estilos extra para modales de rechazo/creación usando la paleta premium si no están en style.css */
    .glass-modal {
        display: none;
        position: fixed;
        z-index: var(--z-modal);
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 20, 25, 0.6);
        backdrop-filter: blur(5px);
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
        animation: slideUp 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .glass-modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.4));
    }
    .glass-modal-body {
        padding: 24px;
    }
    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <div>
                <h1 style="font-size: 1.5rem; margin-bottom:0;">Solicitudes de Visita</h1>
                <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal; margin-top:4px;">Gestiona todas las solicitudes de visita a terrenos</p>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('vendedor.solicitudes.calendario') }}" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Ver Calendario
            </a>
            <button onclick="document.getElementById('modal-crear').classList.add('active')" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Nueva Solicitud
            </button>
        </div>
    </div>
    
    <div class="card-body">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 1.1rem; color: var(--text-primary);">Listado de Solicitudes</h2>
            
            <div style="display: flex; gap: 8px;">
                <span class="badge badge-warning">Pendiente</span>
                <span class="badge badge-success">Aprobada</span>
                <span class="badge badge-danger">Rechazada</span>
                <span class="badge badge-secondary">Cancelada</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Terreno</th>
                        <th>Usuario / Cliente</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $solicitud)
                        <tr>
                            <td>
                                <div class="date-multi">
                                    <span class="date-main">{{ \Carbon\Carbon::parse($solicitud->fecha_visita)->format('d/m/Y') }}</span>
                                    <span class="date-time">🕒 {{ substr($solicitud->hora_inicio, 0, 5) }} - {{ substr($solicitud->hora_fin, 0, 5) }}</span>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $solicitud->terreno->nombre ?? 'N/A' }}</strong><br>
                                <span class="text-muted" style="font-size:0.8rem;">📍 {{ $solicitud->terreno->ubicacion ?? '' }}</span>
                            </td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-sm">
                                        {{ strtoupper(substr($solicitud->usuario->nombre ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="user-name-text">{{ $solicitud->usuario->nombre ?? 'N/A' }}</span>
                                        <span class="user-email-text">{{ $solicitud->usuario->email ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($solicitud->estado === 'pendiente')
                                    <span class="badge badge-warning">Pendiente</span>
                                @elseif($solicitud->estado === 'aprobada')
                                    <span class="badge badge-success">Aprobada</span>
                                @elseif($solicitud->estado === 'rechazada')
                                    <span class="badge badge-danger">Rechazada</span>
                                @elseif($solicitud->estado === 'cancelada')
                                    <span class="badge badge-secondary">Cancelada</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('vendedor.solicitudes.show', $solicitud->id) }}" class="btn btn-secondary btn-sm" title="Ver Detalles">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        Ver
                                    </a>

                                    @if(Auth::user()->rol === 'admin' && $solicitud->estado === 'pendiente')
                                        <form action="{{ route('vendedor.solicitudes.aprobar', $solicitud->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Aprobar
                                            </button>
                                        </form>
                                        
                                        <button onclick="document.getElementById('rechazar-{{ $solicitud->id }}').classList.add('active')" class="btn btn-danger btn-sm">
                                            <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Rechazar
                                        </button>

                                        <!-- Modal de Rechazo -->
                                        <div id="rechazar-{{ $solicitud->id }}" class="glass-modal">
                                            <div class="glass-modal-content">
                                                <div class="glass-modal-header">
                                                    <h3 style="font-size:1.1rem; color:var(--text-primary); margin:0;">Rechazar Solicitud</h3>
                                                    <button onclick="document.getElementById('rechazar-{{ $solicitud->id }}').classList.remove('active')" style="background:none; border:none; cursor:pointer;">
                                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                                <div class="glass-modal-body">
                                                    <form action="{{ route('vendedor.solicitudes.rechazar', $solicitud->id) }}" method="POST">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label class="form-label">Motivo del rechazo <span class="required">*</span></label>
                                                            <textarea name="motivo" class="form-control" rows="3" required placeholder="Escriba el motivo real del rechazo aquí..."></textarea>
                                                        </div>
                                                        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                                                            <button type="button" onclick="document.getElementById('rechazar-{{ $solicitud->id }}').classList.remove('active')" class="btn btn-secondary">Cancelar</button>
                                                            <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($solicitud->estado === 'aprobada' && $solicitud->puedeCancelar())
                                        <form action="{{ route('vendedor.solicitudes.cancelar', $solicitud->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de cancelar esta visita?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-sm" style="color: var(--text-muted); border-color: var(--text-muted);">
                                                <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Cancelar
                                                </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <h3 style="color:var(--text-primary); font-size:1.2rem; margin-bottom:8px;">No hay solicitudes registradas</h3>
                                    <p>Comienza creando una nueva solicitud o espera a que los clientes envíen las suyas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $solicitudes->links() }}
        </div>
    </div>
</div>

<!-- Modal global para crear solicitud rápida -->
<div id="modal-crear" class="glass-modal">
    <div class="glass-modal-content">
        <div class="glass-modal-header">
            <h3 style="font-size:1.1rem; color:var(--text-primary); margin:0;">Nueva Solicitud Manual</h3>
            <button onclick="document.getElementById('modal-crear').classList.remove('active')" style="background:none; border:none; cursor:pointer;">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="glass-modal-body">
            <div style="text-align:center; padding: 20px;">
                <div style="width:60px; height:60px; border-radius:50%; background:var(--primary-100); color:var(--primary); display:flex; justify-content:center; align-items:center; margin: 0 auto 16px;">
                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                        <path d="M12 14v4M10 16h4"></path>
                    </svg>
                </div>
                <h4 style="margin-bottom:10px;">Agendamiento Interactivo</h4>
                <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:20px;">Te recomendamos utilizar la vista de Calendario tipo Google Calendar para encontrar los horarios disponibles más fácilmente, evitar conflictos y gestionar las citas visualmente.</p>
                <div style="display:flex; justify-content:center; gap:12px;">
                    <a href="{{ route('vendedor.solicitudes.create') }}" class="btn btn-secondary">Crear Manualmente</a>
                    <a href="{{ route('vendedor.solicitudes.calendario') }}" class="btn btn-primary" style="box-shadow: 0 4px 15px var(--primary-100);">Ir al Calendario Avanzado</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection