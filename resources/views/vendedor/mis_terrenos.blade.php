@extends('layouts.app')

@section('title', 'Mis Terrenos')

@section('content')
    <div class="page-header">
        <h1>Mis Terrenos</h1>
        <a href="{{ route('vendedor.terrenos.create') }}" class="btn btn-primary">+ Publicar nuevo terreno</a>
    </div>

    @if($terrenos->isEmpty())
        <div class="empty-state">
            <p>No has publicado ningún terreno aún.</p>
            <a href="{{ route('vendedor.terrenos.create') }}" class="btn btn-primary">Publicar mi primer terreno</a>
        </div>
    @else
        <div class="terrenos-grid">
            @foreach($terrenos as $terreno)
                <div class="card terreno-card">
                    <div class="terreno-imagen">
                        @if($terreno->portada)
                            <img src="{{ $terreno->portada->url }}" alt="Portada">
                        @elseif($terreno->imagenes->first())
                            <img src="{{ $terreno->imagenes->first()->url }}" alt="Terreno">
                        @else
                            <div class="sin-imagen">📷 Sin imagen</div>
                        @endif
                    </div>
                    <div class="terreno-info">
                        <h3>{{ $terreno->ubicacion }}</h3>
                        <p class="precio">${{ number_format($terreno->precio, 2) }} USD</p>
                        <p>{{ $terreno->metros_cuadrados }} m²</p>
                        <span class="badge badge-{{ $terreno->estado === 'aprobado' ? 'success' : ($terreno->estado === 'rechazado' ? 'danger' : 'warning') }}">
                            {{ ucfirst($terreno->estado) }}
                        </span>
                        <div class="acciones">
                            <a href="{{ route('vendedor.terrenos.edit', $terreno->id) }}" class="btn btn-sm btn-secondary">Editar</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

<style>
    .terrenos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .terreno-card {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .terreno-imagen {
        height: 200px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .terreno-imagen img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sin-imagen {
        font-size: 2rem;
        color: #ccc;
    }

    .terreno-info {
        padding: 1rem;
    }

    .precio {
        font-size: 1.2rem;
        font-weight: bold;
        color: #007bff;
    }

    .acciones {
        margin-top: 1rem;
        display: flex;
        gap: 0.5rem;
    }
</style>