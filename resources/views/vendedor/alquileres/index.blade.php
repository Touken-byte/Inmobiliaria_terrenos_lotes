@extends('layouts.app')

@section('title', 'Mis Alquileres')

@section('content')
<div class="dashboard-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2><i class="fas fa-bed"></i> Mis Publicaciones de Alquiler</h2>
        <a href="{{ route('vendedor.alquileres.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Publicar Habitación
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row">
    @forelse($alquileres as $alquiler)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                @if($alquiler->imagenes->count() > 0)
                    <img src="{{ asset($alquiler->imagenes->first()->ruta_archivo) }}" class="card-img-top" alt="Imagen del alquiler" style="height: 200px; object-fit: cover;">
                @else
                    <div class="bg-secondary text-white d-flex justify-content-center align-items-center" style="height: 200px;">
                        <span>Sin imagen</span>
                    </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $alquiler->titulo }}</h5>
                    <p class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ $alquiler->ubicacion }}</p>
                    <h6 class="text-primary font-weight-bold">${{ number_format($alquiler->precio_mensual, 2) }} / mes</h6>
                    <p class="card-text text-truncate">{{ $alquiler->descripcion }}</p>
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <span class="badge {{ $alquiler->estado === 'disponible' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($alquiler->estado) }}
                            </span>
                            @if($alquiler->estado_aprobacion === 'pendiente')
                                <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Pendiente</span>
                            @elseif($alquiler->estado_aprobacion === 'rechazado')
                                <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Rechazado</span>
                            @else
                                <span class="badge bg-primary"><i class="fas fa-check-circle"></i> Aprobado</span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $alquiler->habitaciones }} Hab | {{ $alquiler->banos }} Baños</small>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                    <form action="{{ route('vendedor.alquileres.toggle_estado', $alquiler->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $alquiler->estado === 'disponible' ? 'btn-outline-danger' : 'btn-outline-success' }}" title="Cambiar Estado">
                            <i class="fas {{ $alquiler->estado === 'disponible' ? 'fa-times' : 'fa-check' }}"></i>
                            {{ $alquiler->estado === 'disponible' ? 'Marcar Alquilado' : 'Marcar Disponible' }}
                        </button>
                    </form>
                    <div>
                        <a href="{{ route('vendedor.alquileres.edit', $alquiler->id) }}" class="btn btn-sm btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('vendedor.alquileres.destroy', $alquiler->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta publicación?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted mb-3">
                <i class="fas fa-home fa-3x"></i>
            </div>
            <h4>Aún no tienes publicaciones</h4>
            <p>Comienza a publicar habitaciones para alquilar.</p>
            <a href="{{ route('vendedor.alquileres.create') }}" class="btn btn-primary">
                Publicar Ahora
            </a>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $alquileres->links() }}
</div>
@endsection
