@extends('layouts.app')

@section('title', 'Editar Folio - Terreno #' . $terreno->id)

@section('content')
<div class="card form-card">
    <div class="card-header">
        <h2 class="card-title">
            ✏️ Editar Datos de Folio
        </h2>
        <p style="margin:0; color: var(--text-muted); font-size:0.9rem;">
            Terreno #{{ $terreno->id }} — {{ $terreno->ubicacion }}
        </p>
    </div>
    <div class="card-body">

        {{-- Badge de estado --}}
        <div class="alert alert-info" style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem;">
            <div>
                <strong>📄 Folio:</strong> {{ $folio->numero_folio }} —
                Registrado el {{ \Carbon\Carbon::parse($folio->created_at)->translatedFormat('d \d\e F \d\e Y') }}
            </div>
            @if($folio->estado === 'verificado')
                <span style="padding:.3rem .9rem; background:#d4edda; border:1px solid #c3e6cb; border-radius:100px; font-size:.8rem; font-weight:700; color:#155724;">
                    ✅ Verificado
                </span>
            @else
                <span style="padding:.3rem .9rem; background:#fff3cd; border:1px solid #ffeeba; border-radius:100px; font-size:.8rem; font-weight:700; color:#856404;">
                    🕐 Pendiente de verificación
                </span>
            @endif
        </div>

        {{-- Bloqueo si ya está verificado --}}
        @if($folio->estado === 'verificado')
            <div class="alert" style="background:#d4edda; border:1px solid #c3e6cb; border-radius:8px; padding:1rem; margin-bottom:1.5rem;">
                <strong>🔒 Folio verificado.</strong> Este folio ya fue aprobado por el administrador y no puede modificarse.
            </div>
            <a href="{{ route('vendedor.terrenos.mis') }}" class="btn btn-secondary" style="width:100%; text-align:center; display:block;">
                ← Volver a mis terrenos
            </a>
        @else

        <form action="{{ route('vendedor.folio.update', $terreno->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="numero_folio">Número de Folio <span class="required">*</span></label>
                <input type="text" name="numero_folio" id="numero_folio" class="form-control"
                    value="{{ old('numero_folio', $folio->numero_folio) }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="superficie">Superficie Registrada (m²) <span class="required">*</span></label>
                    <input type="number" name="superficie" id="superficie" class="form-control"
                        min="0" step="0.01"
                        value="{{ old('superficie', $folio->superficie) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="ubicacion">Ubicación Registrada <span class="required">*</span></label>
                <textarea name="ubicacion" id="ubicacion" class="form-control" rows="2"
                    required>{{ old('ubicacion', $folio->ubicacion) }}</textarea>
            </div>

            <div class="form-group">
                <label for="colindancias">Colindancias</label>
                <textarea name="colindancias" id="colindancias" class="form-control" rows="3">{{ old('colindancias', $folio->colindancias) }}</textarea>
            </div>

            <div class="alert" style="background: var(--bg-light); border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; margin-bottom:1.5rem;">
                <strong>⚠️ Nota:</strong> Cualquier cambio será visible inmediatamente para los compradores
                que consulten el folio de este terreno.
            </div>

            <div style="display:flex; gap:1rem;">
                <a href="{{ route('vendedor.terrenos.mis') }}" class="btn btn-secondary" style="flex:1; text-align:center;">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary" style="flex:2;">
                    💾 Actualizar Datos del Folio
                </button>
            </div>
        </form>
        @endif
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