@extends('layouts.app')

@section('title', 'Postular Nueva Promoción')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div class="card form-card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h2 class="card-title" style="display:flex; align-items:center; gap:8px; margin:0;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px; height:24px;">
                    <path d="M12 2L2 7l10 5 10-5-10-5z" />
                    <path d="M2 17l10 5 10-5" />
                    <path d="M2 12l10 5 10-5" />
                </svg>
                Postular Campaña de Descuento
            </h2>
            <a href="{{ route('vendedor.promociones.index') }}" class="btn btn-secondary btn-sm">
                Volver al Listado
            </a>
        </div>

        <div class="card-body">
            <p style="margin-bottom: 24px; font-size: 0.9rem; opacity:0.8; line-height:1.5;">
                Seleccione una de sus propiedades aprobadas e ingrese los datos de la oferta. Al enviar, la campaña será enviada a moderación por un administrador. Una vez aprobada, se destacará al inicio del catálogo público del comprador.
            </p>

            <form action="{{ route('vendedor.promociones.store') }}" method="POST">
                @csrf

                <!-- SELECCIÓN DE PROPIEDAD -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="propiedad_tipo_id">Propiedad Elegible <span class="required" style="color:var(--danger);">*</span></label>
                    <select name="propiedad_tipo_id" id="propiedad_tipo_id" class="form-control" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.4); color:#fff;">
                        <option value="">-- Seleccione una Propiedad --</option>
                        
                        @if($terrenos->count() > 0)
                            <optgroup label="Terrenos y Lotes" style="background:#1a1d24; color:var(--accent);">
                                @foreach($terrenos as $t)
                                    <option value="Terreno:{{ $t->id }}" {{ old('propiedad_tipo_id') === "Terreno:{$t->id}" ? 'selected' : '' }}>
                                        [{{ strtoupper($t->tipo) }}] {{ $t->nombre }} - Cod: {{ $t->codigo }} (${{ number_format($t->precio, 2) }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($alquileres->count() > 0)
                            <optgroup label="Alquileres" style="background:#1a1d24; color:var(--accent);">
                                @foreach($alquileres as $a)
                                    <option value="Alquiler:{{ $a->id }}" {{ old('propiedad_tipo_id') === "Alquiler:{$a->id}" ? 'selected' : '' }}>
                                        [ALQUILER] {{ $a->titulo }} - (${{ number_format($a->precio_mensual, 2) }}/mes)
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                    @error('propiedad_tipo_id')
                        <span style="color:var(--danger); font-size:0.8rem; display:block; margin-top:4px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- TÍTULO DE LA PROMOCIÓN -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="titulo">Título de la Promoción / Oferta <span class="required" style="color:var(--danger);">*</span></label>
                    <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ej: Oferta de Invierno, Descuento Especial Fin de Mes" value="{{ old('titulo') }}" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.4); color:#fff;">
                    @error('titulo')
                        <span style="color:var(--danger); font-size:0.8rem; display:block; margin-top:4px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- PORCENTAJE DE DESCUENTO -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="descuento_porcentaje">Porcentaje de Descuento (%) <span class="required" style="color:var(--danger);">*</span></label>
                    <div style="position:relative;">
                        <input type="number" name="descuento_porcentaje" id="descuento_porcentaje" class="form-control" placeholder="Ej: 15" min="1" max="99" value="{{ old('descuento_porcentaje') }}" required style="width: 100%; padding: 0.75rem 2rem 0.75rem 1rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.4); color:#fff;">
                        <span style="position:absolute; right:15px; top:50%; transform:translateY(-50%); font-weight:800; opacity:0.7; color:#fff;">%</span>
                    </div>
                    @error('descuento_porcentaje')
                        <span style="color:var(--danger); font-size:0.8rem; display:block; margin-top:4px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- DESCRIPCIÓN DE LA OFERTA -->
                <div class="form-group" style="margin-bottom: 2rem;">
                    <label for="descripcion">Detalles de la Oferta / Condiciones <span class="required" style="color:var(--danger);">*</span></label>
                    <textarea name="descripcion" id="descripcion" rows="4" class="form-control" placeholder="Describa brevemente las condiciones del descuento, por ejemplo: Valido pagando al contado o transferencia antes de finalizar la semana." required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.4); color:#fff; resize:vertical;">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <span style="color:var(--danger); font-size:0.8rem; display:block; margin-top:4px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- BOTÓN DE ENVÍO -->
                <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; font-size:1.05rem; display:flex; justify-content:center; align-items:center; gap:8px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px; height:20px;">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    Postular Campaña de Promoción
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
