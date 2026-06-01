@extends('layouts.app')

@section('title', 'Visitas para ' . ($propiedad->nombre ?? $propiedad->titulo ?? 'Propiedad'))

@section('content')
<style>
    .page-header {
        margin-bottom: 2rem;
    }
    .page-header h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-color);
    }
    .page-header p {
        margin: 0.25rem 0 0;
        color: var(--text-muted);
        font-size: 0.9rem;
    }
    .glass-modal {
        display: none;
        position: fixed;
        z-index: 1000;
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
    }
    .glass-modal-content {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 20px 45px rgba(0,0,0,0.15);
        overflow: hidden;
        border: 1px solid #eef2f6;
    }
    .glass-modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #eff2f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f9fafb;
    }
    .glass-modal-body {
        padding: 24px;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        background: #f9fafb;
        color: #1f2937;
        font-size: 0.95rem;
    }
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        background: #ffffff;
    }
    .btn-row {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 1.5rem;
    }
</style>

<div class="page-header">
    <a href="{{ route('vendedor.propiedades_panel') }}" style="display:inline-flex; align-items:center; gap:6px; text-decoration:none; color:var(--primary); font-weight:600; font-size:0.9rem; margin-bottom:1rem;">
        ← Volver a Mis Propiedades
    </a>
    <h1>Solicitudes de Visita</h1>
    <p>Gestionando visitas para la propiedad: <strong>{{ $propiedad->nombre ?? $propiedad->titulo }}</strong> (Código: {{ $propiedad->codigo ?? ('ALQ-' . str_pad($propiedad->id, 3, '0', STR_PAD_LEFT)) }})</p>
</div>

<!-- Mensajes de respuesta -->
@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 8px; border-left: 4px solid var(--success-color);">
        {{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 8px; border-left: 4px solid var(--danger-color);">
        <ul style="margin:0; padding-left: 1.25rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h2 class="card-title">⏳ Visitas Pendientes por Procesar</h2>
    </div>
    <div class="card-body no-padding">
        @php
            $pendientes = $solicitudes->where('estado', 'pendiente');
        @endphp

        @if($pendientes->isEmpty())
            <div style="padding:3rem; text-align:center; color:var(--text-muted);">
                <span style="font-size:2rem;">📅</span>
                <p style="margin-top:10px;">No tienes solicitudes de visita pendientes para esta propiedad.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Fecha de Visita</th>
                            <th>Rango de Horario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendientes as $sol)
                            <tr>
                                <td>
                                    <strong style="color:var(--text-color);">{{ $sol->usuario->nombre ?? 'N/A' }}</strong><br>
                                    <span class="text-muted" style="font-size:0.8rem;">✉️ {{ $sol->usuario->email ?? '' }}</span>
                                </td>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($sol->fecha_visita)->format('d/m/Y') }}</strong>
                                </td>
                                <td>
                                    🕒 {{ substr($sol->hora_inicio, 0, 5) }} - {{ substr($sol->hora_fin, 0, 5) }}
                                </td>
                                <td>
                                    <div style="display:flex; gap:8px;">
                                        <!-- Aprobar -->
                                        <form action="{{ route('vendedor.solicitudes.aprobar', $sol->id) }}" method="POST" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" style="padding:6px 12px; border-radius:6px; font-weight:600; border:none; cursor:pointer;">
                                                ✓ Aprobar
                                            </button>
                                        </form>

                                        <!-- Rechazar Trigger -->
                                        <button class="btn btn-sm btn-danger" style="padding:6px 12px; border-radius:6px; font-weight:600; border:none; cursor:pointer;" onclick="openRechazarModal({{ $sol->id }})">
                                            ✗ Rechazar
                                        </button>

                                        <!-- Reprogramar Trigger -->
                                        <button class="btn btn-sm btn-secondary" style="padding:6px 12px; border-radius:6px; font-weight:600; cursor:pointer;" onclick="openReprogramarModal({{ $sol->id }}, '{{ $sol->fecha_visita->format('Y-m-d') }}', '{{ $sol->hora_inicio }}', '{{ $sol->hora_fin }}')">
                                            🔄 Reprogramar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">📜 Historial de Visitas Sólidas</h2>
    </div>
    <div class="card-body no-padding">
        @php
            $historial = $solicitudes->where('estado', '!=', 'pendiente');
        @endphp

        @if($historial->isEmpty())
            <div style="padding:3rem; text-align:center; color:var(--text-muted);">
                <span style="font-size:2rem;">📁</span>
                <p style="margin-top:10px;">Aún no se registran visitas pasadas o procesadas para esta propiedad.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historial as $sol)
                            <tr>
                                <td>
                                    <strong style="color:var(--text-color);">{{ $sol->usuario->nombre ?? 'N/A' }}</strong><br>
                                    <span class="text-muted" style="font-size:0.8rem;">✉️ {{ $sol->usuario->email ?? '' }}</span>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($sol->fecha_visita)->format('d/m/Y') }}
                                </td>
                                <td>
                                    🕒 {{ substr($sol->hora_inicio, 0, 5) }} - {{ substr($sol->hora_fin, 0, 5) }}
                                </td>
                                <td>
                                    @if($sol->estado === 'aprobada')
                                        <span class="badge badge-success">Aprobada</span>
                                    @elseif($sol->estado === 'rechazada')
                                        <span class="badge badge-danger">Rechazada</span>
                                    @elseif($sol->estado === 'cancelada')
                                        <span class="badge badge-secondary">Cancelada</span>
                                    @endif
                                </td>
                                <td style="max-width:250px; white-space:normal; font-size:0.85rem; color:var(--text-muted);">
                                    @if($sol->estado === 'rechazada')
                                        <strong>Motivo:</strong> {{ $sol->motivo_rechazo ?? 'Ninguno indicado.' }}
                                    @elseif($sol->estado === 'aprobada')
                                        <span style="color:var(--success-color);">Visita confirmada por el vendedor.</span>
                                    @elseif($sol->estado === 'cancelada')
                                        <span style="color:var(--text-muted);">Cancelada por el usuario.</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- MODAL RECHAZAR --}}
<div id="rechazarModal" class="glass-modal">
    <div class="glass-modal-content">
        <div class="glass-modal-header">
            <h3 style="margin:0; font-weight:700;">Rechazar Solicitud de Visita</h3>
            <button onclick="closeRechazarModal()" style="background:none; border:none; cursor:pointer; font-size:1.5rem; font-weight:bold;">&times;</button>
        </div>
        <div class="glass-modal-body">
            <form id="rechazarForm" method="POST">
                @csrf
                <div class="form-group">
                    <label for="motivo">Motivo del Rechazo <span class="required">*</span></label>
                    <textarea name="motivo" id="motivo" rows="4" class="form-control" placeholder="Indica detalladamente la razón por la que rechazas la solicitud..." required></textarea>
                </div>
                <div class="btn-row">
                    <button type="button" class="btn btn-secondary" onclick="closeRechazarModal()">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL REPROGRAMAR --}}
<div id="reprogramarModal" class="glass-modal">
    <div class="glass-modal-content">
        <div class="glass-modal-header">
            <h3 style="margin:0; font-weight:700;">Reprogramar Cita</h3>
            <button onclick="closeReprogramarModal()" style="background:none; border:none; cursor:pointer; font-size:1.5rem; font-weight:bold;">&times;</button>
        </div>
        <div class="glass-modal-body">
            <form id="reprogramarForm" method="POST">
                @csrf
                <div class="form-group">
                    <label for="fecha_visita">Nueva Fecha <span class="required">*</span></label>
                    <input type="date" name="fecha_visita" id="rep_fecha" class="form-control" required min="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label for="hora_inicio">Hora de Inicio <span class="required">*</span></label>
                    <input type="time" name="hora_inicio" id="rep_inicio" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="hora_fin">Hora de Cierre <span class="required">*</span></label>
                    <input type="time" name="hora_fin" id="rep_fin" class="form-control" required>
                </div>
                <div class="btn-row">
                    <button type="button" class="btn btn-secondary" onclick="closeReprogramarModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Nueva Fecha</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openRechazarModal(solicitudId) {
        const form = document.getElementById('rechazarForm');
        form.action = `/vendedor/solicitudes/${solicitudId}/rechazar`;
        document.getElementById('rechazarModal').classList.add('active');
    }
    
    function closeRechazarModal() {
        document.getElementById('rechazarModal').classList.remove('active');
    }

    function openReprogramarModal(solicitudId, fecha, inicio, fin) {
        const form = document.getElementById('reprogramarForm');
        form.action = `/vendedor/solicitudes/${solicitudId}/reprogramar`;
        
        document.getElementById('rep_fecha').value = fecha;
        // Trim seconds if any
        document.getElementById('rep_inicio').value = inicio.substring(0, 5);
        document.getElementById('rep_fin').value = fin.substring(0, 5);
        
        document.getElementById('reprogramarModal').classList.add('active');
    }

    function closeReprogramarModal() {
        document.getElementById('reprogramarModal').classList.remove('active');
    }
</script>
@endsection
