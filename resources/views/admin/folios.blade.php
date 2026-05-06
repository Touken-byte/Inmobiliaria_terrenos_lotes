@extends('layouts.app')

@section('title', 'Gestión de Folios')

@section('content')

<style>
    .badge-pendiente {
        padding: .3rem .85rem;
        background: #fff3cd;
        border: 1px solid #ffeeba;
        border-radius: 100px;
        font-size: .75rem;
        font-weight: 700;
        color: #856404;
    }
    .badge-verificado {
        padding: .3rem .85rem;
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 100px;
        font-size: .75rem;
        font-weight: 700;
        color: #155724;
    }
    .badge-rechazado {
        padding: .3rem .85rem;
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 100px;
        font-size: .75rem;
        font-weight: 700;
        color: #721c24;
    }
    .folio-table { width: 100%; border-collapse: collapse; }
    .folio-table th {
        padding: .85rem 1rem;
        text-align: left;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--text-muted);
        border-bottom: 2px solid var(--border-color);
    }
    .folio-table td {
        padding: .9rem 1rem;
        font-size: .88rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .folio-table tr:hover td { background: var(--bg-light); }
    .btn-verificar {
        padding: .4rem .9rem;
        background: #28a745;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: .8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
    }
    .btn-verificar:hover { background: #218838; }
    .btn-rechazar {
        padding: .4rem .9rem;
        background: #dc3545;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: .8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
        margin-left: .4rem;
    }
    .btn-rechazar:hover { background: #c82333; }
</style>

<div class="card">
    <div class="card-header" style="display:flex; align-items:center; justify-content:space-between;">
        <div>
            <h2 class="card-title">📋 Gestión de Folios</h2>
            <p style="margin:0; color:var(--text-muted); font-size:.9rem;">
                Revisa y verifica los folios registrados por los vendedores
            </p>
        </div>
        {{-- Contadores rápidos --}}
        <div style="display:flex; gap:1rem;">
            <div style="text-align:center;">
                <div style="font-size:1.4rem; font-weight:700; color:#856404;">
                    {{ $folios->where('estado','pendiente')->count() }}
                </div>
                <div style="font-size:.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em;">Pendientes</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.4rem; font-weight:700; color:#155724;">
                    {{ $folios->where('estado','verificado')->count() }}
                </div>
                <div style="font-size:.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em;">Verificados</div>
            </div>
        </div>
    </div>

    <div class="card-body" style="padding:0;">

        @if(session('success'))
            <div class="alert alert-success" style="margin:1.25rem 1.5rem 0;">
                {{ session('success') }}
            </div>
        @endif

        @if($folios->isEmpty())
            <div style="padding:3rem; text-align:center; color:var(--text-muted);">
                <p style="font-size:2rem;">📭</p>
                <p>No hay folios registrados aún.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="folio-table">
                    <thead>
                        <tr>
                            <th>N° Folio</th>
                            <th>Terreno</th>
                            <th>Vendedor</th>
                            <th>Superficie</th>
                            <th>Fecha registro</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($folios as $folio)
                        <tr>
                            <td><strong>{{ $folio->numero_folio }}</strong></td>
                            <td>
                                <a href="{{ route('admin.ver_terreno', $folio->terreno->id) }}"
                                   style="color:var(--primary); text-decoration:none;">
                                    #{{ $folio->terreno->id }} — {{ Str::limit($folio->terreno->ubicacion, 30) }}
                                </a>
                            </td>
                            <td>{{ $folio->terreno->vendedor->nombre ?? '—' }}</td>
                            <td>{{ number_format($folio->superficie, 2) }} m²</td>
                            <td>{{ \Carbon\Carbon::parse($folio->created_at)->format('d/m/Y') }}</td>
                            <td>
                                @if($folio->estado === 'pendiente')
                                    <span class="badge-pendiente">🕐 Pendiente</span>
                                @elseif($folio->estado === 'verificado')
                                    <span class="badge-verificado">✅ Verificado</span>
                                @else
                                    <span class="badge-rechazado">❌ Rechazado</span>
                                @endif
                            </td>
                            <td>
                                @if($folio->estado === 'pendiente')
                                    {{-- Botón Verificar --}}
                                    <form action="{{ route('admin.folio.verificar') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="folio_id" value="{{ $folio->id }}">
                                        <input type="hidden" name="accion" value="verificado">
                                        <button type="submit" class="btn-verificar"
                                            onclick="return confirm('¿Verificar el folio {{ $folio->numero_folio }}?')">
                                            ✅ Verificar
                                        </button>
                                    </form>
                                    {{-- Botón Rechazar --}}
                                    <form action="{{ route('admin.folio.verificar') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="folio_id" value="{{ $folio->id }}">
                                        <input type="hidden" name="accion" value="rechazado">
                                        <button type="submit" class="btn-rechazar"
                                            onclick="return confirm('¿Rechazar el folio {{ $folio->numero_folio }}?')">
                                            ❌ Rechazar
                                        </button>
                                    </form>
                                @elseif($folio->estado === 'verificado')
                                    <span style="font-size:.8rem; color:#155724;">
                                        Verificado por {{ $folio->adminVerificador->nombre ?? 'Admin' }}
                                    </span>
                                @else
                                    {{-- Permitir re-verificar si fue rechazado --}}
                                    <form action="{{ route('admin.folio.verificar') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="folio_id" value="{{ $folio->id }}">
                                        <input type="hidden" name="accion" value="verificado">
                                        <button type="submit" class="btn-verificar"
                                            onclick="return confirm('¿Verificar este folio rechazado?')">
                                            ↩️ Re-verificar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>

@endsection