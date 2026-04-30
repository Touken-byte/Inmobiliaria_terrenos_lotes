@extends('layouts.app')

@section('title', 'Agregar Folio - Terreno #' . $terreno->id)

@section('content')
<div class="card form-card">
    <div class="card-header">
        <h2 class="card-title">
            📋 Agregar Datos de Folio
        </h2>
        <p style="margin:0; color: var(--text-muted); font-size:0.9rem;">
            Terreno #{{ $terreno->id }} — {{ $terreno->ubicacion }}
        </p>
    </div>
    <div class="card-body">

        <div class="alert alert-info" style="margin-bottom:1.5rem;">
            <strong>ℹ️ ¿Para qué sirve el folio?</strong><br>
            Los datos del folio permiten a los compradores consultar información legal del terreno:
            superficie registrada, colindancias, propietarios vigentes, gravámenes y restricciones.
        </div>

        <form action="{{ route('vendedor.folio.store', $terreno->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="numero_folio">Número de Folio <span class="required">*</span></label>
                <input type="text" name="numero_folio" id="numero_folio" class="form-control"
                    placeholder="Ej: FOL-001" value="{{ old('numero_folio') }}" required>
                <small style="color: var(--text-muted);">Este número debe coincidir con el documento oficial del Registro de la Propiedad.</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="superficie">Superficie Registrada (m²) <span class="required">*</span></label>
                    <input type="number" name="superficie" id="superficie" class="form-control"
                        placeholder="Ej: 250.00" min="0" step="0.01"
                        value="{{ old('superficie', $terreno->metros_cuadrados) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="ubicacion">Ubicación Registrada <span class="required">*</span></label>
                <textarea name="ubicacion" id="ubicacion" class="form-control" rows="2"
                    placeholder="Ubicación exacta según el documento de folio..." required>{{ old('ubicacion', $terreno->ubicacion) }}</textarea>
            </div>

            <div class="form-group">
                <label for="colindancias">Colindancias</label>
                <textarea name="colindancias" id="colindancias" class="form-control" rows="3"
                    placeholder="Norte: ..., Sur: ..., Este: ..., Oeste: ...">{{ old('colindancias') }}</textarea>
                <small style="color: var(--text-muted);">Opcional pero recomendado. Indica los límites del terreno.</small>
            </div>

            <div class="alert" style="background: var(--bg-light); border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; margin-bottom:1.5rem;">
                <strong>⚠️ Nota:</strong> Una vez registrado el folio, los compradores podrán consultar esta información.
                Los datos deben ser exactos y coincidir con los documentos oficiales.
            </div>

            <div style="display:flex; gap:1rem;">
                <a href="{{ route('vendedor.terrenos.mis') }}" class="btn btn-secondary" style="flex:1; text-align:center;">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary" style="flex:2;">
                    💾 Guardar Datos del Folio
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-card { max-width: 700px; margin: 0 auto; }
    .form-group { margin-bottom: 1.25rem; }
    .form-row { display: flex; gap: 1.5rem; }
    .form-row .form-group { flex: 1; }
    label { display: block; margin-bottom: .4rem; font-weight: 500; font-size: .95rem; }
    .required { color: #dc3545; }
    .form-control {
        width: 100%; padding: .75rem 1rem; border-radius: 8px;
        border: 1px solid var(--border-color); font-size: 1rem;
        background: var(--bg-input, #fff); color: var(--text-primary);
    }
    .form-control:focus {
        outline: none; border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.15);
    }
</style>
@endsection