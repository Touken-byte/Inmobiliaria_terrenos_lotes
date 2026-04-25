@extends('layouts.app')

@section('title', 'Nueva Solicitud de Visita')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="12" y1="18" x2="12" y2="12"></line>
                <line x1="9" y1="15" x2="15" y2="15"></line>
            </svg>
            <div>
                <h1 style="font-size: 1.5rem; margin-bottom:0;">Formulario de Solicitud</h1>
                <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal; margin-top:4px;">Crea una solicitud de visita manualmente</p>
            </div>
        </div>
        <a href="{{ route('vendedor.solicitudes.index') }}" class="btn btn-secondary btn-sm">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Volver al Listado
        </a>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-error">
                    <div class="alert-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <div class="alert-content">
                        <strong>Errores en el formulario:</strong>
                        <ul style="margin-top: 4px; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('vendedor.solicitudes.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="terreno_id" class="form-label">
                        Terreno a visitar <span class="required">*</span>
                    </label>
                    <select name="terreno_id" id="terreno_id" class="form-control" required>
                        <option value="" disabled {{ old('terreno_id') ? '' : 'selected' }}>Seleccione el terreno de interés</option>
                        @foreach($terrenos as $terreno)
                            <option value="{{ $terreno->id }}" {{ old('terreno_id') == $terreno->id ? 'selected' : '' }}>
                                {{ $terreno->nombre }} - {{ $terreno->ubicacion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="vendedor_id" class="form-label">
                        Vendedor Asignado <span class="required">*</span>
                    </label>
                    <select name="vendedor_id" id="vendedor_id" class="form-control" required>
                        <option value="" disabled {{ old('vendedor_id') ? '' : 'selected' }}>Seleccione el vendedor responsable</option>
                        @foreach($vendedores as $vendedor)
                            <option value="{{ $vendedor->id }}" {{ old('vendedor_id') == $vendedor->id ? 'selected' : '' }}>
                                {{ $vendedor->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha_visita" class="form-label">
                        Fecha de visita <span class="required">*</span>
                    </label>
                    <input type="date" name="fecha_visita" id="fecha_visita" class="form-control"
                        value="{{ old('fecha_visita', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                </div>

                <div style="display: flex; gap: 16px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="hora_inicio" class="form-label">
                            Hora Inicio <span class="required">*</span>
                        </label>
                        <input type="time" name="hora_inicio" id="hora_inicio" class="form-control"
                            value="{{ old('hora_inicio', '09:00') }}" required>
                    </div>

                    <div class="form-group" style="flex: 1;">
                        <label for="hora_fin" class="form-label">
                            Hora Fin <span class="required">*</span>
                        </label>
                        <input type="time" name="hora_fin" id="hora_fin" class="form-control"
                            value="{{ old('hora_fin', '10:00') }}" required>
                    </div>
                </div>

                <!-- Mensaje de disponibilidad (dinámico si se quisiera agregar JS aquí, pero por ahora estático) -->
                <div id="disponibilidad-mensaje" style="display:none;" class="alert alert-info">
                    <div class="alert-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div class="alert-content">
                        <span id="disponibilidad-texto"></span>
                    </div>
                </div>

                <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05); display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="{{ route('vendedor.solicitudes.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Guardar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card" style="background: linear-gradient(135deg, var(--primary-50), var(--bg-light));">
        <div class="card-body">
            <h3 style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 16px;">💡 Sugerencia</h3>
            <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 16px;">
                Para una mejor experiencia al agendar, evita conflictos de horarios utilizando nuestro calendario interactivo.
                El calendario detecta automáticamente la disponibilidad del vendedor.
            </p>
            <a href="{{ route('vendedor.solicitudes.calendario') }}" class="btn btn-primary btn-block">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Usar Calendario
            </a>
        </div>
    </div>
</div>
@endsection