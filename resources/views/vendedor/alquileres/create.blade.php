@extends('layouts.app')

@section('title', 'Publicar Habitación')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h3 class="mb-0"><i class="fas fa-home text-primary"></i> Publicar Habitación para Alquiler</h3>
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

                <form action="{{ route('vendedor.alquileres.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="titulo" class="form-label fw-bold">Título de la Publicación</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" value="{{ old('titulo') }}" required placeholder="Ej: Habitación amoblada cerca de la universidad">
                        </div>
                        <div class="col-md-4">
                            <label for="precio_mensual" class="form-label fw-bold">Precio Mensual ($)</label>
                            <input type="number" step="0.01" class="form-control" id="precio_mensual" name="precio_mensual" value="{{ old('precio_mensual') }}" required placeholder="Ej: 150">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="ubicacion" class="form-label fw-bold">Ubicación / Dirección</label>
                        <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="{{ old('ubicacion') }}" required placeholder="Ej: Av. Principal #123, Zona Sur">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="metros_cuadrados" class="form-label fw-bold">Área (m²)</label>
                            <input type="number" step="0.01" class="form-control" id="metros_cuadrados" name="metros_cuadrados" value="{{ old('metros_cuadrados') }}" required placeholder="Ej: 20">
                        </div>
                        <div class="col-md-4">
                            <label for="habitaciones" class="form-label fw-bold">Cantidad de Habitaciones</label>
                            <input type="number" class="form-control" id="habitaciones" name="habitaciones" value="{{ old('habitaciones', 1) }}" required min="1">
                        </div>
                        <div class="col-md-4">
                            <label for="banos" class="form-label fw-bold">Cantidad de Baños</label>
                            <input type="number" class="form-control" id="banos" name="banos" value="{{ old('banos', 1) }}" required min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-bold">Descripción Detallada</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required placeholder="Describe las comodidades, reglas, etc.">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Servicios Incluidos</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servicios_incluidos[]" value="Agua" id="servicio_agua">
                                    <label class="form-check-label" for="servicio_agua">Agua</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servicios_incluidos[]" value="Luz" id="servicio_luz">
                                    <label class="form-check-label" for="servicio_luz">Luz</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servicios_incluidos[]" value="Internet" id="servicio_internet">
                                    <label class="form-check-label" for="servicio_internet">Internet Wi-Fi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servicios_incluidos[]" value="Gas" id="servicio_gas">
                                    <label class="form-check-label" for="servicio_gas">Gas Domiciliario</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servicios_incluidos[]" value="Cable" id="servicio_cable">
                                    <label class="form-check-label" for="servicio_cable">TV Cable</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="disponible_desde" class="form-label fw-bold">Disponible Desde</label>
                            <input type="date" class="form-control" id="disponible_desde" name="disponible_desde" value="{{ old('disponible_desde', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="imagenes" class="form-label fw-bold">Imágenes (Máx 5)</label>
                        <input class="form-control" type="file" id="imagenes" name="imagenes[]" multiple accept="image/*">
                        <small class="text-muted">Formatos permitidos: JPG, PNG, JPEG. Tamaño máximo: 5MB por imagen.</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('vendedor.alquileres.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Publicar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
