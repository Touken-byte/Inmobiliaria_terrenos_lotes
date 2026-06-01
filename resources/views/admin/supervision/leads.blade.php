@extends('layouts.app')

@section('title', 'Supervisión de Leads | Admin')

@section('content')
<style>
    /* Admin Premium Styles */
    :root {
        --void:         #050810;
        --deep:         #080d1a;
        --surface:      #0c1326;
        --card:         #0f1830;
        --rim:          rgba(120,160,255,0.10);
        --rim-gold:     rgba(201,168,76,0.22);
        --gold:         #c9a84c;
        --cobalt:       #3d7ef5;
        --emerald:      #1dba7e;
        --text-1:       #eef2fc;
        --text-2:       #8fa3cc;
        --text-3:       #3d5480;
    }

    body { background: var(--void); color: var(--text-1); font-family: 'Outfit', sans-serif; }

    .header-section { margin-bottom: 2rem; }
    .title { font-family: 'Cormorant Garamond', serif; font-size: 2.2rem; font-weight: 700; color: var(--text-1); margin:0; }

    .admin-table-container { background: var(--card); border: 1px solid var(--rim); border-radius: 20px; overflow: hidden; }
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th { background: rgba(120,160,255,0.05); padding: 1.2rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-3); border-bottom: 1px solid var(--rim); }
    .admin-table td { padding: 1.2rem; border-bottom: 1px solid var(--rim); color: var(--text-2); font-size: 0.9rem; vertical-align: middle; }
    .admin-table tr:hover { background: rgba(120,160,255,0.02); }

    .badge { padding: 0.4rem 0.8rem; border-radius: 100px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
    .badge-nuevo { background: rgba(61,126,245,0.15); color: #3d7ef5; }
    .badge-contactado { background: rgba(201,168,76,0.15); color: #c9a84c; }
    .badge-negociacion { background: rgba(156,39,176,0.15); color: #e1bee7; }
    .badge-cerrado { background: rgba(29,186,126,0.15); color: #1dba7e; }

    .btn-supervise { display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(201,168,76,0.15); color: var(--gold); border: 1px solid rgba(201,168,76,0.3); padding: 0.5rem 1rem; border-radius: 12px; font-size: 0.8rem; font-weight: 600; text-decoration: none; transition: 0.2s; }
    .btn-supervise:hover { background: var(--gold); color: var(--deep); }

    .user-info { display: flex; flex-direction: column; }
    .user-name { font-weight: 600; color: var(--text-1); }
    .user-role { font-size: 0.75rem; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.05em; }
</style>

<div class="container py-5">
    <div class="header-section">
        <h1 class="title">Auditoría de Negociaciones</h1>
        <p style="color: var(--text-2); font-size: 0.9rem; margin-top: 0.5rem;">Supervisa los leads y conversaciones entre compradores y vendedores.</p>
    </div>

    <div class="admin-table-container">
        @if($leads->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Comprador</th>
                    <th>Vendedor</th>
                    <th>Propiedad</th>
                    <th>Estado</th>
                    <th>Último Mensaje</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leads as $lead)
                <tr>
                    <td>
                        <div class="user-info">
                            <span class="user-name">{{ $lead->comprador->nombre }}</span>
                            <span class="user-role">{{ $lead->telefono }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="user-info">
                            <span class="user-name">{{ $lead->vendedor->nombre }}</span>
                        </div>
                    </td>
                    <td>
                        @if($lead->terreno_id)
                            <a href="{{ route('admin.ver_terreno', $lead->terreno_id) }}" target="_blank" style="color: var(--cobalt); text-decoration: none; font-weight: 500;">
                                <i class="fa-solid fa-map"></i> {{ Str::limit($lead->terreno->ubicacion ?? 'N/D', 20) }}
                            </a>
                        @elseif($lead->alquiler_id)
                            <a href="{{ route('admin.ver_alquiler', $lead->alquiler_id) }}" target="_blank" style="color: var(--cobalt); text-decoration: none; font-weight: 500;">
                                <i class="fa-solid fa-house"></i> {{ Str::limit($lead->alquiler->ubicacion ?? 'N/D', 20) }}
                            </a>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $lead->estado }}">{{ ucfirst($lead->estado) }}</span>
                    </td>
                    <td>
                        @if($lead->chat && $lead->chat->ultimoMensaje)
                            <div style="font-size: 0.8rem; color: var(--text-2);">
                                {{ Str::limit($lead->chat->ultimoMensaje->mensaje ?? 'Archivo adjunto', 30) }}
                                <br><span style="font-size: 0.7rem; color: var(--text-3);">{{ $lead->chat->ultimoMensaje->created_at->diffForHumans() }}</span>
                            </div>
                        @else
                            <span style="color: var(--text-3); font-size: 0.8rem;">Sin mensajes</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.supervision.chat', $lead->chat->id) }}" class="btn-supervise">
                            <i class="fa-solid fa-eye"></i> Auditar Chat
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4" style="border-top: 1px solid var(--rim);">
            {{ $leads->links('pagination::bootstrap-5') }}
        </div>
        @else
        <div style="padding: 4rem 2rem; text-align: center; color: var(--text-3);">
            <i class="fa-solid fa-shield-halved mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
            <h5>No hay negociaciones en el sistema</h5>
        </div>
        @endif
    </div>
</div>
@endsection
