@extends('layouts.app')
@section('title', 'Gestión Legal de Venta')

@section('content')
<style>
:root {
    --glass: rgba(255, 255, 255, 0.03);
    --glass-border: rgba(255, 255, 255, 0.08);
}
.proceso-step { 
    border-left: 3px solid var(--glass-border); 
    padding-left: 2rem; 
    margin-left: 1.2rem; 
    position: relative;
    padding-bottom: 2rem;
}
.proceso-step:last-child { border-left: none; }
.proceso-step.activo { border-left-color: var(--primary); }
.proceso-step.completado { border-left-color: #10b981; }
.proceso-step.bloqueado { opacity: 0.5; pointer-events: none; }

.step-badge { 
    position: absolute;
    left: -20px;
    top: 0;
    width: 36px; height: 36px; 
    border-radius: 10px; 
    display: flex; align-items: center; justify-content: center; 
    font-weight: 800; font-size: 0.9rem; 
    z-index: 2;
    background: #1f2937;
    border: 2px solid var(--glass-border);
}
.step-badge.activo { background: var(--primary); color: #fff; border-color: var(--primary); box-shadow: 0 0 15px rgba(124,58,237,0.4); }
.step-badge.completado { background: #10b981; color: #fff; border-color: #10b981; box-shadow: 0 0 15px rgba(16,185,129,0.3); }

.progress-container { margin-bottom: 3rem; }
.progress-bar-track { height: 8px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden; margin-top: 10px; }
.progress-bar-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg, #7c3aed, #10b981); transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1); }

.status-chip {
    padding: 4px 12px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
}
.status-pendiente { background: rgba(245,158,11,0.1); color: #f59e0b; border: 1px solid rgba(245,158,11,0.2); }
.status-aprobado  { background: rgba(16,185,129,0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
.status-rechazado { background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }

.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1.5rem; }
.info-label { font-size: 0.65rem; text-transform: uppercase; opacity: 0.4; font-weight: 700; letter-spacing: 0.1em; display: block; margin-bottom: 4px; }
.info-value { font-weight: 600; font-size: 0.95rem; color: #fff; }

.obs-box { 
    background: rgba(239,68,68,0.05); 
    border: 1px solid rgba(239,68,68,0.15); 
    border-radius: 12px; padding: 1rem; margin-top: 1rem;
    font-size: 0.85rem; color: #fca5a5;
}

/* Estilos Premium para Inputs */
.form-group label {
    font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.6; margin-bottom: 8px; display: block;
}
.input-premium {
    background: rgba(255,255,255,0.03) !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    border-radius: 12px !important;
    color: #fff !important;
    padding: 12px 16px !important;
    transition: all 0.3s ease;
    width: 100%;
}
.input-premium:focus {
    background: rgba(255,255,255,0.07) !important;
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 4px rgba(124,58,237,0.1) !important;
    outline: none;
}

/* Date Picker Customization */
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
    opacity: 0.6;
    transition: 0.2s;
}
input[type="date"]::-webkit-calendar-picker-indicator:hover { opacity: 1; }

/* File Upload Premium */
.upload-area {
    border: 2px dashed rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background: rgba(255,255,255,0.01);
    position: relative;
}
.upload-area:hover {
    border-color: var(--primary);
    background: rgba(124,58,237,0.05);
}
.upload-area input[type="file"] {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0; left: 0;
    opacity: 0;
    cursor: pointer;
}
.upload-icon { font-size: 2.5rem; margin-bottom: 1rem; display: block; opacity: 0.5; }
.upload-text { font-weight: 600; display: block; margin-bottom: 4px; }
.upload-hint { font-size: 0.75rem; opacity: 0.4; }
</style>

<div class="row">
    <div class="col-12">
        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="mb-1">Gestión Legal de Venta</h2>
                <p class="text-muted mb-0">Seguimiento del proceso documental para el cierre del terreno.</p>
            </div>
            @php
                $estadoGeneral = 'Iniciado';
                $claseGeneral  = 'badge-info';
                if ($paso === 3) { $estadoGeneral = 'En Revisión'; $claseGeneral = 'badge-warning'; }
                if ($minuta && $minuta->estado === 'aprobada' && $comprobante && $comprobante->estado === 'aprobado') { 
                    $estadoGeneral = 'Finalizado'; $claseGeneral = 'badge-success'; 
                }
                if ($minuta && $minuta->estado === 'completada') { 
                    $estadoGeneral = 'Finalizado'; $claseGeneral = 'badge-success'; 
                }
                if (($minuta && $minuta->estado === 'rechazada') || ($comprobante && $comprobante->estado === 'rechazado')) {
                    $estadoGeneral = 'Requiere Corrección'; $claseGeneral = 'badge-danger';
                }
            @endphp
            <span class="badge {{ $claseGeneral }}">{{ $estadoGeneral }}</span>
        </div>

        {{-- Alerta de Disponibilidad --}}
        @if($terrenos->isEmpty() && !$minuta)
            <div class="alert alert-warning mb-4" style="border-radius: 12px; background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2); color: #f59e0b;">
                <i class="fas fa-exclamation-triangle mr-2"></i> No tienes lotes disponibles para iniciar un nuevo trámite legal. Los lotes con trámites activos o ya vendidos no aparecerán en la lista.
            </div>
        @endif

    {{-- Progress --}}
    <div class="progress-container">
        <div style="display: flex; justify-content: space-between; font-size: 0.7rem; font-weight: 800; opacity: 0.4; text-transform: uppercase;">
            <span>1. Minuta</span><span>2. Impuesto IT</span><span>3. Finalización</span>
        </div>
        <div class="progress-bar-track">
            @php $progreso = $paso === 1 ? 15 : ($paso === 2 ? 50 : 100); @endphp
            <div class="progress-bar-fill" style="width: {{ $progreso }}%;"></div>
        </div>
    </div>

    {{-- STEP 1 --}}
    <div class="proceso-step {{ $minuta ? 'completado' : 'activo' }}">
        <div class="step-badge {{ $minuta ? 'completado' : 'activo' }}">
            @if($minuta && in_array($minuta->estado, ['aprobada', 'completada'])) ✓ @else 1 @endif
        </div>
        <div style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700;">Minuta de Compraventa</h3>
            <p style="margin: 0; font-size: 0.85rem; opacity: 0.5;">Primer documento legal del proceso de transferencia.</p>
        </div>

        @if($minuta)
            <div class="card" style="border-radius: 20px; overflow: hidden; background: var(--glass);">
                <div class="card-body" style="padding: 1.75rem;">
                    <div class="info-grid">
                        <div>
                            <span class="info-label">Terreno</span>
                            <div class="info-value">{{ $minuta->terreno->ubicacion ?? 'N/D' }}</div>
                        </div>
                        <div>
                            <span class="info-label">Comprador</span>
                            <div class="info-value">{{ $minuta->comprador->nombre ?? 'N/D' }}</div>
                        </div>
                        <div>
                            <span class="info-label">Monto Pactado</span>
                            <div class="info-value" style="color: var(--accent); font-weight: 800;">${{ number_format($minuta->monto, 2) }}</div>
                        </div>
                        <div>
                            <span class="info-label">Estado Doc</span>
                            <div style="margin-top: 3px;">
                                <span class="estado-badge estado-{{ in_array($minuta->estado, ['aprobada', 'completada']) ? 'aprobado' : ($minuta->estado === 'rechazada' ? 'rechazado' : 'pendiente') }}">
                                    {{ ucfirst($minuta->estado) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($minuta->estado === 'rechazada' && $minuta->observacion)
                        <div class="obs-box">
                            <strong>⚠️ Observación Admin:</strong><br>
                            {{ $minuta->observacion }}
                        </div>
                    @endif

                    <div style="margin-top: 1.5rem; display: flex; gap: 12px;">
                        @if($minuta->archivo)
                            <a href="{{ route('vendedor.minuta.archivo', $minuta->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="border-radius: 8px;">
                                📄 Ver Documento
                            </a>
                        @endif
                        @if($minuta->estado !== 'completada')
                            <button onclick="toggleElement('form-minuta')" class="btn btn-primary btn-sm" style="border-radius: 8px;">
                                {{ $minuta->estado === 'rechazada' ? '🔄 Corregir Minuta' : '✏️ Editar Datos' }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <div id="form-minuta" style="display: none; margin-top: 1.5rem;">
        @else
            <div id="form-minuta">
        @endif
            <div class="card" style="border-radius: 20px;">
                <div class="card-body" style="padding: 2rem;">
                    <form action="{{ route('vendedor.proceso_legal.minuta.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Seleccionar Terreno *</label>
                                    <select name="terreno_id" class="input-premium" required>
                                        <option value="" disabled selected>-- Lotes disponibles --</option>
                                        @foreach($terrenos as $t)
                                            <option value="{{ $t->id }}" {{ old('terreno_id', $minuta?->terreno_id) == $t->id ? 'selected' : '' }}>{{ $t->ubicacion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Comprador *</label>
                                    <select name="comprador_id" class="input-premium" required>
                                        <option value="" disabled selected>-- Cliente --</option>
                                        @foreach($compradores as $c)
                                            <option value="{{ $c->id }}" {{ old('comprador_id', $minuta?->comprador_id) == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Monto de Venta ($) *</label>
                                    <input type="number" name="monto" id="monto_venta" class="input-premium" step="0.01" min="0.01" placeholder="Ej: 50000" value="{{ old('monto', $minuta?->monto) }}" required oninput="calculateIT(this.value)">
                                    <div id="it_preview" class="mt-2" style="font-size: 0.8rem; color: #10b981; font-weight: 600; display: none;">
                                        Estimado IT (3%): $<span id="it_val">0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Fecha de Firma *</label>
                                    <input type="date" name="fecha" class="input-premium" value="{{ old('fecha', $minuta?->fecha?->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Documento Digital (Minuta PDF) *</label>
                                    <div class="upload-area" id="dropzone-minuta">
                                        <span class="upload-icon">📄</span>
                                        <span class="upload-text">Seleccionar Minuta</span>
                                        <span class="upload-hint">Click o arrastra el archivo PDF aquí (Máx. 10MB)</span>
                                        <input type="file" name="archivo" accept="application/pdf" {{ !$minuta ? 'required' : '' }} onchange="updateFileName(this)">
                                        <div class="file-name-preview mt-2 text-primary font-weight-bold" style="display:none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4" style="width: 100%; border-radius: 12px; height: 50px; font-weight: 700;">
                            {{ $minuta ? '💾 Guardar Cambios' : '🚀 Iniciar Trámite' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @php
        $minutaAprobada = ($minuta && in_array($minuta->estado, ['aprobada', 'completada']));
    @endphp

    {{-- STEP 2 --}}
    <div class="proceso-step {{ !$minutaAprobada ? 'bloqueado' : ($comprobante ? 'completado' : 'activo') }}">
        <div class="step-badge {{ !$minutaAprobada ? 'bloqueado' : ($comprobante ? 'completado' : 'activo') }}">
            @if($comprobante && in_array($comprobante->estado, ['aprobado', 'completado'])) ✓ @else 2 @endif
        </div>
        <div style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700;">Comprobante IT</h3>
            <p style="margin: 0; font-size: 0.85rem; opacity: 0.5;">Pago del Impuesto a la Transferencia de Bienes Inmuebles.</p>
        </div>

        @if(!$minuta)
            <div class="card" style="border: 2px dashed var(--glass-border); background: transparent; text-align: center; padding: 3rem;">
                <div style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;">🔒</div>
                <h4 style="margin: 0; font-weight: 700;">Paso Bloqueado</h4>
                <p style="opacity: 0.4; font-size: 0.85rem; margin-top: 5px;">Primero debes registrar la minuta en el Paso 1.</p>
            </div>
        @elseif($minuta->estado === 'pendiente')
            <div class="card" style="border: 2px dashed #f59e0b33; background: rgba(245, 158, 11, 0.05); text-align: center; padding: 3rem;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">⏳</div>
                <h4 style="margin: 0; font-weight: 700; color: #f59e0b;">Esperando Aprobación</h4>
                <p style="opacity: 0.6; font-size: 0.85rem; margin-top: 5px;">Tu minuta está en revisión. El Administrador debe aprobarla antes de que puedas subir el comprobante IT.</p>
            </div>
        @elseif($minuta->estado === 'rechazada')
            <div class="card" style="border: 2px dashed #ef444433; background: rgba(239, 68, 68, 0.05); text-align: center; padding: 3rem;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">❌</div>
                <h4 style="margin: 0; font-weight: 700; color: #ef4444;">Minuta Rechazada</h4>
                <p style="opacity: 0.6; font-size: 0.85rem; margin-top: 5px;">Debes corregir la minuta en el Paso 1 y esperar su aprobación para continuar.</p>
            </div>
        @elseif($comprobante)
            <div class="card" style="border-radius: 20px; background: var(--glass);">
                <div class="card-body" style="padding: 1.75rem;">
                    <div class="info-grid">
                        <div>
                            <span class="info-label">N° Recibo</span>
                            <div class="info-value">{{ $comprobante->numero_recibo }}</div>
                        </div>
                        <div>
                            <span class="info-label">Monto Pagado</span>
                            <div class="info-value" style="color: #10b981;">${{ number_format($comprobante->monto, 2) }}</div>
                        </div>
                        <div>
                            <span class="info-label">Fecha Pago</span>
                            <div class="info-value">{{ $comprobante->fecha_pago->format('d/m/Y') }}</div>
                        </div>
                        <div>
                            <span class="info-label">Estado IT</span>
                            <div style="margin-top: 3px;">
                                <span class="estado-badge estado-{{ in_array($comprobante->estado, ['aprobado', 'completado']) ? 'aprobado' : ($comprobante->estado === 'rechazado' ? 'rechazado' : 'pendiente') }}">
                                    {{ ucfirst($comprobante->estado) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($comprobante->estado === 'rechazado' && $comprobante->observacion)
                        <div class="obs-box">
                            <strong>⚠️ Observación Admin:</strong><br>
                            {{ $comprobante->observacion }}
                        </div>
                    @endif

                    <div style="margin-top: 1.5rem; display: flex; gap: 12px;">
                        @if($comprobante->archivo)
                            <a href="{{ route('vendedor.comprobante_it.archivo', $comprobante->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="border-radius: 8px;">
                                📄 Ver Recibo
                            </a>
                        @endif
                        @if($minuta?->estado !== 'completada')
                            <button onclick="toggleElement('form-it')" class="btn btn-primary btn-sm" style="border-radius: 8px;">
                                {{ $comprobante->estado === 'rechazado' ? '🔄 Reemplazar Comprobante' : '✏️ Editar Datos' }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <div id="form-it" style="display: none; margin-top: 1.5rem;">
        @else
            <div id="form-it">
        @endif
            <div class="card" style="border-radius: 20px;">
                <div class="card-body" style="padding: 2rem;">
                    <form action="{{ route('vendedor.comprobante_it.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>N° de Recibo Oficial *</label>
                                    <input type="text" name="numero_recibo" class="input-premium" value="{{ old('numero_recibo', $comprobante?->numero_recibo) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Fecha de Pago *</label>
                                    <input type="date" name="fecha_pago" class="input-premium" value="{{ old('fecha_pago', $comprobante?->fecha_pago?->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Monto del Impuesto (Cálculo 3%)</label>
                                    <div class="input-premium" style="background: rgba(16,185,129,0.1) !important; border-color: rgba(16,185,129,0.2) !important; color: #10b981 !important; font-weight: 800;">
                                        @if($minuta)
                                            ${{ number_format($minuta->monto * 0.03, 2) }}
                                        @else
                                            $0.00
                                        @endif
                                    </div>
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        Calculado automáticamente basado en el monto de venta 
                                        (@if($minuta) ${{ number_format($minuta->monto, 2) }} @else $0.00 @endif)
                                    </small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Archivo del Comprobante (PDF/Imagen) *</label>
                                    <div class="upload-area" id="dropzone-it">
                                        <span class="upload-icon">🧾</span>
                                        <span class="upload-text">Subir Comprobante IT</span>
                                        <span class="upload-hint">Click o arrastra la imagen o PDF aquí</span>
                                        <input type="file" name="archivo" accept="image/*,application/pdf" {{ !$comprobante ? 'required' : '' }} onchange="updateFileName(this)">
                                        <div class="file-name-preview mt-2 text-success font-weight-bold" style="display:none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success mt-4" style="width: 100%; border-radius: 12px; height: 50px; font-weight: 700;">
                            {{ $comprobante ? '💾 Actualizar Comprobante' : '📤 Enviar Comprobante IT' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 3 --}}
    <div class="proceso-step {{ in_array($minuta?->estado, ['completada']) ? 'completado' : ($paso === 3 ? 'activo' : 'bloqueado') }}" style="margin-bottom: 2rem;">
        <div class="step-badge {{ in_array($minuta?->estado, ['completada']) ? 'completado' : ($paso === 3 ? 'activo' : 'bloqueado') }}">
            @if($minuta?->estado === 'completada') ✓ @else 3 @endif
        </div>
        <div>
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700;">Revisión y Finalización</h3>
            <p style="margin: 0; font-size: 0.85rem; opacity: 0.5;">Validación administrativa final para cierre de venta.</p>
        </div>

        @if($paso === 3 || $minuta?->estado === 'completada')
            <div class="card" style="margin-top: 1.5rem; border-radius: 20px; border-color: {{ $minuta?->estado === 'completada' ? '#10b981' : 'rgba(251,191,36,0.3)' }}; background: {{ $minuta?->estado === 'completada' ? 'rgba(16,185,129,0.05)' : 'rgba(251,191,36,0.05)' }};">
                <div class="card-body" style="text-align: center; padding: 2rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">{{ $minuta?->estado === 'completada' ? '🏆' : '⏳' }}</div>
                    <h4 style="font-weight: 700; margin-bottom: 0.5rem;">{{ $minuta?->estado === 'completada' ? '¡Venta Finalizada!' : 'Documentación completa' }}</h4>
                    <p style="opacity: 0.6; max-width: 420px; margin: 0 auto;">
                        {{ $minuta?->estado === 'completada' ? 'El proceso legal ha concluido con éxito. El terreno ya ha sido transferido legalmente.' : 'Su trámite está siendo revisado por el equipo administrativo. Le notificaremos el resultado a la brevedad.' }}
                    </p>
                </div>
            </div>
        @endif
    </div>

    </div> {{-- Close col-12 --}}
</div> {{-- Close row --}}

<script>
function toggleElement(id) {
    const el = document.getElementById(id);
    if(el) {
        el.style.display = (el.style.display === 'none') ? 'block' : 'none';
        if(el.style.display === 'block') {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
}

function updateFileName(input) {
    const preview = input.parentElement.querySelector('.file-name-preview');
    if (input.files && input.files[0]) {
        preview.textContent = '✅ Seleccionado: ' + input.files[0].name;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

function calculateIT(value) {
    const preview = document.getElementById('it_preview');
    const val = document.getElementById('it_val');
    if(value > 0) {
        const it = (value * 0.03).toFixed(2);
        val.textContent = it;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

// Inicializar si ya hay valor
window.onload = function() {
    const monto = document.getElementById('monto_venta');
    if(monto && monto.value) calculateIT(monto.value);
};
</script>
@endsection
