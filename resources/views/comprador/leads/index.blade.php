@extends('layouts.comprador')

@section('title', 'Mis Intereses')

@section('content')
<style>
    :root {
        --void:         #050810;
        --deep:         #080d1a;
        --surface:      #0c1326;
        --card:         #0f1830;
        --rim:          rgba(120,160,255,0.10);
        --rim-gold:     rgba(201,168,76,0.22);
        --gold:         #c9a84c;
        --cobalt:       #3d7ef5;
        --cobalt-soft:  rgba(61,126,245,0.12);
        --emerald:      #1dba7e;
        --text-1:       #eef2fc;
        --text-2:       #8fa3cc;
        --text-3:       #3d5480;
    }

    body { background: var(--void); color: var(--text-1); font-family: 'Outfit', sans-serif; }

    .header-section {
        margin-bottom: 2rem;
    }

    .title {
        font-family: 'Cormorant Garamond', serif; font-size: 2.2rem; font-weight: 700; color: var(--text-1); margin:0;
    }

    .lead-card {
        background: var(--card); border: 1px solid var(--rim); border-radius: 20px; overflow: hidden; display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem; margin-bottom: 1.5rem; transition: border-color 0.3s, transform 0.3s;
    }
    .lead-card:hover { border-color: var(--cobalt); transform: translateY(-3px); }

    .img-box {
        width: 120px; height: 120px; border-radius: 12px; background: var(--surface); flex-shrink: 0; overflow: hidden;
    }
    .img-box img { width: 100%; height: 100%; object-fit: cover; }

    .info-box { flex: 1; }
    .lead-title { font-size: 1.2rem; font-weight: 700; color: var(--text-1); margin-bottom: 0.3rem; }
    .lead-location { font-size: 0.9rem; color: var(--text-2); margin-bottom: 1rem; }

    .badge { padding: 0.3rem 0.8rem; border-radius: 100px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-right: 0.5rem; }
    .badge-nuevo { background: rgba(61,126,245,0.15); color: #3d7ef5; }
    .badge-negociacion { background: rgba(156,39,176,0.15); color: #e1bee7; }
    .badge-cerrado { background: rgba(29,186,126,0.15); color: #1dba7e; }

    .btn-chat {
        display: inline-flex; align-items: center; gap: 0.5rem; background: var(--cobalt); color: white; padding: 0.6rem 1.2rem; border-radius: 12px; font-weight: 600; text-decoration: none; font-size: 0.9rem; transition: 0.2s;
    }
    .btn-chat:hover { background: #2b60cc; color: white; box-shadow: 0 4px 15px rgba(61,126,245,0.4); }

    .date { font-size: 0.8rem; color: var(--text-3); text-align: right; }
</style>

<div class="container py-5">
    <div class="header-section">
        <h1 class="title">Mis Intereses</h1>
        <p style="color: var(--text-2); font-size: 0.9rem; margin-top: 0.5rem;">Haz seguimiento a los terrenos que te interesan y negocia con los vendedores.</p>
    </div>

    <div>
        @forelse($leads as $lead)
        <div class="lead-card">
            <div class="img-box">
                @if($lead->terreno && $lead->terreno->imagenes->count() > 0)
                    <img src="{{ asset($lead->terreno->imagenes->first()->ruta_archivo) }}" alt="Terreno">
                @elseif($lead->alquiler && $lead->alquiler->imagenes->count() > 0)
                    <img src="{{ asset($lead->alquiler->imagenes->first()->ruta_archivo) }}" alt="Alquiler">
                @else
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-3);">
                        <i class="fa-regular fa-image fa-2x"></i>
                    </div>
                @endif
            </div>
            
            <div class="info-box">
                @if($lead->terreno)
                    <div class="lead-title">Lote en {{ Str::words($lead->terreno->ubicacion, 4) }}</div>
                    <div class="lead-location"><i class="fa-solid fa-location-dot" style="color: var(--cobalt); margin-right: 5px;"></i>{{ $lead->terreno->ubicacion }}</div>
                @elseif($lead->alquiler)
                    <div class="lead-title">Alquiler: {{ Str::words($lead->alquiler->titulo, 4) }}</div>
                    <div class="lead-location"><i class="fa-solid fa-location-dot" style="color: var(--emerald); margin-right: 5px;"></i>{{ $lead->alquiler->ubicacion }}</div>
                @endif
                <div>
                    <span class="badge badge-{{ in_array($lead->estado, ['nuevo','contactado']) ? 'nuevo' : $lead->estado }}">{{ ucfirst($lead->estado) }}</span>
                    <span style="font-size: 0.8rem; color: var(--text-3);">Propietario/Vendedor: {{ $lead->vendedor->nombre }}</span>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 1rem;">
                <div class="date">{{ $lead->fecha_contacto->diffForHumans() }}</div>
                <a href="{{ route('chat.show', $lead->chat->id) }}" class="btn-chat">
                    <i class="fa-solid fa-comment-dots"></i> Ver Conversación
                </a>
            </div>
        </div>
        @empty
            <div style="padding: 4rem 2rem; text-align: center; color: var(--text-3); background: var(--card); border: 1px solid var(--rim); border-radius: 20px;">
                <i class="fa-solid fa-handshake mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                <h5>Aún no tienes intereses activos</h5>
                <p>Explora el catálogo y contacta a los vendedores de los terrenos que te gusten.</p>
                <a href="{{ route('catalogo.terrenos') }}" class="btn-chat mt-3" style="display: inline-block;">Ir al Catálogo</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
