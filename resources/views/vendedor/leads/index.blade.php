@extends('layouts.app')

@section('title', 'Gestión de Leads')

@section('content')
<style>
    /* Premium Dashboard Styles */
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
        display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;
    }

    .title {
        font-family: 'Cormorant Garamond', serif; font-size: 2.2rem; font-weight: 700; color: var(--text-1); margin:0;
    }

    .stats-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;
    }

    .stat-card {
        background: var(--card); border: 1px solid var(--rim); border-radius: 16px; padding: 1.5rem; text-align: center;
        transition: border-color 0.3s, transform 0.3s; position: relative; overflow: hidden;
    }
    .stat-card:hover { border-color: var(--gold); transform: translateY(-3px); }
    .stat-card .value { font-family: 'Cormorant Garamond', serif; font-size: 2rem; font-weight: 700; color: var(--gold); }
    .stat-card .label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-3); letter-spacing: 0.1em; }

    .leads-table-container {
        background: var(--card); border: 1px solid var(--rim); border-radius: 20px; overflow: hidden;
    }

    .leads-table { width: 100%; border-collapse: collapse; }
    .leads-table th {
        background: rgba(120,160,255,0.05); padding: 1.2rem; text-align: left; font-size: 0.75rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-3); border-bottom: 1px solid var(--rim);
    }
    .leads-table td {
        padding: 1.2rem; border-bottom: 1px solid var(--rim); color: var(--text-2); font-size: 0.9rem; vertical-align: middle;
    }
    .leads-table tr:hover { background: rgba(120,160,255,0.02); }

    .badge {
        padding: 0.4rem 0.8rem; border-radius: 100px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .badge-nuevo { background: rgba(61,126,245,0.15); color: #3d7ef5; border: 1px solid rgba(61,126,245,0.3); }
    .badge-contactado { background: rgba(201,168,76,0.15); color: #c9a84c; border: 1px solid rgba(201,168,76,0.3); }
    .badge-negociacion { background: rgba(156,39,176,0.15); color: #e1bee7; border: 1px solid rgba(156,39,176,0.3); }
    .badge-cerrado { background: rgba(29,186,126,0.15); color: #1dba7e; border: 1px solid rgba(29,186,126,0.3); }

    .action-btn {
        display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px;
        background: var(--cobalt-soft); color: var(--cobalt); border-radius: 10px; text-decoration: none; transition: 0.2s; border: 1px solid transparent;
    }
    .action-btn:hover { background: var(--cobalt); color: white; box-shadow: 0 4px 15px rgba(61,126,245,0.4); }

    .btn-outline-gold {
        border: 1px solid var(--gold); color: var(--gold); background: transparent; padding: 0.5rem 1rem; border-radius: 12px; transition: 0.3s;
    }
    .btn-outline-gold:hover { background: var(--gold); color: var(--deep); }

</style>

<div class="container py-5">
    <div class="header-section">
        <div>
            <h1 class="title">Gestión de Leads</h1>
            <p style="color: var(--text-2); font-size: 0.9rem; margin-top: 0.5rem;">Administra tus prospectos e inicia negociaciones.</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="value">{{ $stats['nuevos'] }}</div>
            <div class="label">Nuevos</div>
        </div>
        <div class="stat-card">
            <div class="value">{{ $stats['negociacion'] }}</div>
            <div class="label">En Negociación</div>
        </div>
        <div class="stat-card">
            <div class="value">{{ $stats['cerrados'] }}</div>
            <div class="label">Cerrados</div>
        </div>
        <div class="stat-card">
            <div class="value">{{ $stats['total'] }}</div>
            <div class="label">Total Leads</div>
        </div>
    </div>

    <div class="leads-table-container">
        @if($leads->count() > 0)
            <table class="leads-table">
                <thead>
                    <tr>
                        <th>Interesado</th>
                        <th>Propiedad</th>
                        <th>Último Mensaje</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                    <tr>
                        <td>
                            <div class="lead-name" style="font-weight: 600; color: inherit;">{{ $lead->nombre }}</div>
                            <div class="lead-phone" style="font-size: 0.8rem;">{{ $lead->telefono }}</div>
                        </td>
                        <td>
                            @if($lead->terreno_id)
                                <span class="badge badge-contactado" style="font-size: 0.6rem; padding: 0.2rem 0.5rem; margin-bottom: 0.3rem; display: inline-block;">Terreno</span><br>
                                <a href="{{ route('catalogo.detalle', $lead->terreno_id) }}" target="_blank" style="color: var(--cobalt); text-decoration: none; font-weight: 500;">
                                    {{ Str::limit($lead->terreno->ubicacion ?? 'Desconocido', 30) }}
                                </a>
                            @elseif($lead->alquiler_id)
                                <span class="badge badge-nuevo" style="font-size: 0.6rem; padding: 0.2rem 0.5rem; margin-bottom: 0.3rem; display: inline-block;">Alquiler</span><br>
                                <a href="{{ route('catalogo.detalle.alquiler', $lead->alquiler_id) }}" target="_blank" style="color: var(--emerald); text-decoration: none; font-weight: 500;">
                                    {{ Str::limit($lead->alquiler->titulo ?? 'Desconocido', 30) }}
                                </a>
                            @endif
                        </td>
                        <td>
                            @if($lead->chat && $lead->chat->ultimoMensaje)
                                <div class="last-message" style="font-size: 0.85rem; color: inherit; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $lead->chat->ultimoMensaje->user_id === auth()->id() ? 'Tú: ' : '' }}{{ $lead->chat->ultimoMensaje->mensaje ?? 'Archivo adjunto' }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-3);">
                                    {{ $lead->chat->ultimoMensaje->created_at->diffForHumans() }}
                                    @if(!$lead->chat->ultimoMensaje->leido && $lead->chat->ultimoMensaje->user_id !== auth()->id())
                                        <span style="display: inline-block; width: 8px; height: 8px; background: var(--cobalt); border-radius: 50%; margin-left: 5px;"></span>
                                    @endif
                                </div>
                            @else
                                <span style="opacity: 0.3; font-style: italic;">Sin mensajes</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $lead->estado }}">{{ ucfirst($lead->estado) }}</span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('chat.show', $lead->chat->id) }}" class="action-btn" title="Abrir Chat">
                                    <i class="fa-solid fa-comments"></i>
                                </a>
                                <form action="{{ route('vendedor.leads.estado', $lead->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <select name="estado" class="form-select form-select-sm" style="background: var(--deep); color: var(--text-2); border: 1px solid var(--rim); width: auto; display: inline-block; border-radius: 8px;" onchange="this.form.submit()">
                                        <option value="nuevo" {{ $lead->estado == 'nuevo' ? 'selected' : '' }}>Nuevo</option>
                                        <option value="contactado" {{ $lead->estado == 'contactado' ? 'selected' : '' }}>Contactado</option>
                                        <option value="negociacion" {{ $lead->estado == 'negociacion' ? 'selected' : '' }}>Negociación</option>
                                        <option value="cerrado" {{ $lead->estado == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                                    </select>
                                </form>
                            </div>
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
                <i class="fa-solid fa-inbox mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                <h5>No tienes leads actualmente</h5>
                <p>Cuando un comprador se interese en tus terrenos, aparecerán aquí.</p>
            </div>
        @endif
    </div>
</div>
@endsection
