@extends('layouts.app')

@section('title', 'Inscripción Derechos Reales — Folio ' . $folio->numero_folio)

@section('content')
<div class="card form-card">
    <div class="card-header">
        <h2 class="card-title">🏛️ Inscripción en Derechos Reales</h2>
        <p style="margin:0; color:var(--text-muted); font-size:.9rem;">
            Folio {{ $folio->numero_folio }} — {{ $folio->terreno->ubicacion }}
        </p>
    </div>

    <div class="card-body">

        {{-- Estado actual si ya existe --}}
        @if($inscripcion)
            <div class="alert alert-info" style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem;">
                <div>
                    <strong>📋 Inscripción existente</strong> —
                    enviada el {{ \Carbon\Carbon::parse($inscripcion->created_at)->translatedFormat('d \d\e F \d\e Y') }}
                </div>
                @php
                    $colores = [
                        'pendiente'   => ['bg'=>'#fff3cd','border'=>'#ffeeba','color'=>'#856404','texto'=>'🕐 Pendiente'],
                        'en_revision' => ['bg'=>'#cce5ff','border'=>'#b8daff','color'=>'#004085','texto'=>'🔍 En revisión'],
                        'inscrito'    => ['bg'=>'#d4edda','border'=>'#c3e6cb','color'=>'#155724','texto'=>'✅ Inscrito'],
                        'rechazado'   => ['bg'=>'#f8d7da','border'=>'#f5c6cb','color'=>'#721c24','texto'=>'❌ Rechazado'],
                    ];
                    $c = $colores[$inscripcion->estado] ?? $colores['pendiente'];
                @endphp
                <span style="padding:.3rem .9rem; background:{{ $c['bg'] }}; border:1px solid {{ $c['border'] }}; border-radius:100px; font-size:.8rem; font-weight:700; color:{{ $c['color'] }};">
                    {{ $c['texto'] }}
                </span>
            </div>

            @if($inscripcion->observacion_admin)
                <div class="alert" style="background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; padding:1rem; margin-bottom:1.5rem;">
                    <strong>💬 Observación del admin:</strong> {{ $inscripcion->observacion_admin }}
                </div>
            @endif

            @if($inscripcion->estado === 'inscrito')
                <div class="alert" style="background:#d4edda; border:1px solid #c3e6cb; border-radius:8px; padding:1rem; margin-bottom:1.5rem;">
                    <strong>✅ Inscripción aprobada.</strong> Este terreno ya está inscrito en Derechos Reales.
                </div>
                <a href="{{ route('vendedor.terrenos.mis') }}" class="btn btn-secondary" style="display:block; text-align:center;">
                    ← Volver a mis terrenos
                </a>
                @php return; @endphp
            @endif
        @endif

        <div class="alert" style="background:var(--bg-light); border:1px solid var(--border-color); border-radius:8px; padding:1rem; margin-bottom:1.5rem;">
            <strong>ℹ️ ¿Qué es esto?</strong><br>
            Aquí puedes subir el comprobante de inscripción del terreno en el Registro de Derechos Reales.
            El administrador revisará la documentación y actualizará el estado.
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom:1.25rem;">
                <strong>⚠️ Corrige los siguientes errores:</strong>
                <ul style="margin:.5rem 0 0 1.2rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('vendedor.inscripcion.store', $folio->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="numero_matricula">Número de Matrícula</label>
                <input type="text" name="numero_matricula" id="numero_matricula" class="form-control"
                    placeholder="Ej: 7.01.1.01.0001234"
                    value="{{ old('numero_matricula', $inscripcion->numero_matricula ?? '') }}">
                <small style="color:var(--text-muted);">Número asignado por el Registro de Derechos Reales.</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_entrada">Fecha de Entrada</label>
                    <input type="date" name="fecha_entrada" id="fecha_entrada" class="form-control"
                        value="{{ old('fecha_entrada', isset($inscripcion->fecha_entrada) ? $inscripcion->fecha_entrada->format('Y-m-d') : '') }}">
                </div>
                <div class="form-group">
                    <label for="fecha_salida">Fecha de Salida</label>
                    <input type="date" name="fecha_salida" id="fecha_salida" class="form-control"
                        value="{{ old('fecha_salida', isset($inscripcion->fecha_salida) ? $inscripcion->fecha_salida->format('Y-m-d') : '') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="tasa_pagada">Tasa Pagada (Bs.)</label>
                <input type="number" name="tasa_pagada" id="tasa_pagada" class="form-control"
                    min="0" step="0.01" placeholder="Ej: 150.00"
                    value="{{ old('tasa_pagada', $inscripcion->tasa_pagada ?? '') }}">
            </div>

            <div class="form-group">
                <label for="comprobante">Comprobante de Inscripción</label>
                @if(isset($inscripcion->comprobante_archivo) && $inscripcion->comprobante_archivo)
                    <div style="margin-bottom:.75rem; padding:.75rem 1rem; background:var(--bg-light); border:1px solid var(--border-color); border-radius:8px; display:flex; align-items:center; justify-content:space-between;">
                        <span style="font-size:.85rem;">
                            📎 {{ $inscripcion->comprobante_nombre_original }}
                        </span>
                        <a href="{{ route('vendedor.inscripcion.archivo', $inscripcion->id) }}"
                           target="_blank"
                           style="font-size:.8rem; color:#007bff;">Ver archivo</a>
                    </div>
                @endif
                <input type="file" name="comprobante" id="comprobante" class="form-control"
                    accept=".pdf,.jpg,.jpeg,.png">
                <small style="color:var(--text-muted);">PDF, JPG o PNG. Máximo 5MB.
                    {{ isset($inscripcion->comprobante_archivo) ? 'Sube un nuevo archivo para reemplazar el actual.' : '' }}
                </small>
            </div>

            <div style="display:flex; gap:1rem; margin-top:1.5rem;">
                <a href="{{ route('vendedor.terrenos.mis') }}" class="btn btn-secondary" style="flex:1; text-align:center;">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary" style="flex:2;">
                    📤 {{ $inscripcion ? 'Actualizar Inscripción' : 'Enviar Inscripción' }}
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