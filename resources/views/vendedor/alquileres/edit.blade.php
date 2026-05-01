@extends('layouts.app')

@section('title', 'Editar Publicación')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h3 class="mb-0"><i class="fas fa-edit text-warning"></i> Editar Publicación de Alquiler</h3>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('vendedor.alquileres.update', $alquiler->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="titulo" class="form-label fw-bold">Título de la Publicación</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" value="{{ old('titulo', $alquiler->titulo) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="precio_mensual" class="form-label fw-bold">Precio Mensual ($)</label>
                            <input type="number" step="0.01" class="form-control" id="precio_mensual" name="precio_mensual" value="{{ old('precio_mensual', $alquiler->precio_mensual) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="ubicacion" class="form-label fw-bold">Ubicación / Dirección</label>
                        <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="{{ old('ubicacion', $alquiler->ubicacion) }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="metros_cuadrados" class="form-label fw-bold">Área (m²)</label>
                            <input type="number" step="0.01" class="form-control" id="metros_cuadrados" name="metros_cuadrados" value="{{ old('metros_cuadrados', $alquiler->metros_cuadrados) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="habitaciones" class="form-label fw-bold">Cantidad de Habitaciones</label>
                            <input type="number" class="form-control" id="habitaciones" name="habitaciones" value="{{ old('habitaciones', $alquiler->habitaciones) }}" required min="1">
                        </div>
                        <div class="col-md-4">
                            <label for="banos" class="form-label fw-bold">Cantidad de Baños</label>
                            <input type="number" class="form-control" id="banos" name="banos" value="{{ old('banos', $alquiler->banos) }}" required min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-bold">Descripción Detallada</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required>{{ old('descripcion', $alquiler->descripcion) }}</textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Servicios Incluidos</label>
                            @php
                                $servicios = old('servicios_incluidos', $alquiler->servicios_incluidos ?? []);
                            @endphp
                            <div class="d-flex flex-wrap gap-3">
                                @foreach(['Agua', 'Luz', 'Internet', 'Gas', 'Cable'] as $servicio)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servicios_incluidos[]" value="{{ $servicio }}" id="servicio_{{ strtolower($servicio) }}" {{ in_array($servicio, $servicios) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="servicio_{{ strtolower($servicio) }}">{{ $servicio }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="disponible_desde" class="form-label fw-bold">Disponible Desde</label>
                            <input type="date" class="form-control" id="disponible_desde" name="disponible_desde" value="{{ old('disponible_desde', $alquiler->disponible_desde ? $alquiler->disponible_desde->format('Y-m-d') : date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Imágenes Actuales</label>
                        @if($alquiler->imagenes->count() > 0)
                            <div class="row mb-3">
                                @foreach($alquiler->imagenes as $imagen)
                                    <div class="col-md-3 mb-2">
                                        <div class="position-relative">
                                            <img src="{{ asset($imagen->ruta_archivo) }}" class="img-thumbnail" alt="Imagen actual">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No hay imágenes publicadas.</p>
                        @endif
                        
                        <label for="imagenes" class="form-label fw-bold">Agregar Nuevas Imágenes (Opcional)</label>
                        <input class="form-control" type="file" id="imagenes" name="imagenes[]" multiple accept="image/*">
                        <small class="text-muted">Formatos permitidos: JPG, PNG, JPEG. Tamaño máximo: 5MB por imagen.</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('vendedor.alquileres.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
