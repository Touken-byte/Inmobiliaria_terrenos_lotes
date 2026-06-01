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

                    @if($alquiler->estado_aprobacion === 'rechazado' && $alquiler->motivo_rechazo)
                        <div class="alert alert-danger mt-2 mb-2" style="padding: 0.5rem; font-size: 0.85rem; border-radius: 8px; background: #fdf2f2; border: 1px solid #fde8e8; color: #9b1c1c;">
                            <strong>Motivo de rechazo:</strong> {{ $alquiler->motivo_rechazo }}
                        </div>
                    @endif
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
                <div class="card-footer bg-white border-top-0" style="padding: 0 1.25rem 1.25rem 1.25rem;">
                    <form action="{{ route('vendedor.alquileres.toggle_estado', $alquiler->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn w-100 {{ $alquiler->estado === 'disponible' ? 'btn-danger' : 'btn-success' }}" style="border-radius: 12px; font-weight: 700; padding: 10px; transition: all 0.2s; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <i class="fas {{ $alquiler->estado === 'disponible' ? 'fa-lock' : 'fa-door-open' }}"></i>
                            {{ $alquiler->estado === 'disponible' ? 'Marcar como Ocupado' : 'Marcar como Disponible' }}
                        </button>
                    </form>
                    <div class="d-flex" style="gap: 10px;">
                        <a href="{{ route('vendedor.alquileres.edit', $alquiler->id) }}" class="btn btn-light w-50" style="border-radius: 12px; font-weight: 600; border: 1px solid #e2e8f0; color: #475569; transition: all 0.2s;">
                            <i class="fas fa-edit" style="color: #f59e0b;"></i> Editar
                        </a>
                        <form action="{{ route('vendedor.alquileres.destroy', $alquiler->id) }}" method="POST" class="w-50" onsubmit="return confirm('¿Seguro que deseas eliminar esta publicación?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn w-100" style="border-radius: 12px; font-weight: 600; transition: all 0.2s; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444;" onmouseover="this.style.background='#ef4444'; this.style.color='#fff';" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'; this.style.color='#ef4444';">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
                @if($alquiler->historialEstados && $alquiler->historialEstados->count() > 0)
                <div class="card-footer border-top-0" style="background: rgba(26, 78, 184, 0.03); padding: 1.25rem;">
                    <div style="font-size: 0.8rem; font-weight: 700; color: #1a4eb8; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.05em;">
                        <i class="fas fa-history" style="color: #d4af37;"></i> Historial de Actividad
                    </div>
                    <div style="max-height: 120px; overflow-y: auto; padding-right: 5px;" class="custom-scrollbar">
                        <ul class="list-unstyled mb-0 position-relative" style="border-left: 2px solid rgba(26, 78, 184, 0.2); margin-left: 6px; padding-left: 15px;">
                            @foreach($alquiler->historialEstados as $historial)
                                <li style="position: relative; margin-bottom: 10px;">
                                    <span style="position: absolute; left: -21px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: #d4af37; border: 2px solid #fff; box-shadow: 0 0 0 1px rgba(26, 78, 184, 0.2);"></span>
                                    <div style="font-size: 0.75rem; color: #475569; font-weight: 600;">
                                        {{ $historial->created_at->format('d/m/Y') }} <span style="opacity:0.7; font-weight:400;">{{ $historial->created_at->format('H:i') }}</span>
                                    </div>
                                    <div style="font-size: 0.85rem; color: #1e293b; background: #fff; padding: 4px 10px; border-radius: 8px; border: 1px solid rgba(26, 78, 184, 0.1); display: inline-block; margin-top: 4px; box-shadow: 0 2px 8px rgba(26, 78, 184, 0.05);">
                                        <span style="text-decoration: line-through; opacity: 0.6;">{{ ucfirst($historial->estado_anterior) }}</span> 
                                        <i class="fas fa-arrow-right mx-2" style="color: #d4af37; font-size: 0.7rem;"></i> 
                                        <span style="font-weight: 700; color: {{ $historial->estado_nuevo === 'disponible' ? '#10b981' : '#1a4eb8' }};">{{ ucfirst($historial->estado_nuevo) }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
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
