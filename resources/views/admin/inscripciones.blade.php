@extends('layouts.app')

@section('title', 'Inscripciones Derechos Reales')

@section('content')
<style>
    .ins-table { width:100%; border-collapse:collapse; }
    .ins-table th {
        padding:.8rem 1rem; text-align:left; font-size:.68rem;
        font-weight:700; letter-spacing:.1em; text-transform:uppercase;
        color:var(--text-muted); border-bottom:2px solid var(--border-color);
        white-space:nowrap;
    }
    .ins-table td {
        padding:.85rem 1rem; font-size:.85rem;
        border-bottom:1px solid var(--border-color); vertical-align:middle;
    }
    .ins-table tr:hover td { background:var(--bg-light); }
    .badge-pendiente   { padding:.3rem .8rem; background:#fff3cd; border:1px solid #ffeeba; border-radius:100px; font-size:.72rem; font-weight:700; color:#856404; }
    .badge-en_revision { padding:.3rem .8rem; background:#cce5ff; border:1px solid #b8daff; border-radius:100px; font-size:.72rem; font-weight:700; color:#004085; }
    .badge-inscrito    { padding:.3rem .8rem; background:#d4edda; border:1px solid #c3e6cb; border-radius:100px; font-size:.72rem; font-weight:700; color:#155724; }
    .badge-rechazado   { padding:.3rem .8rem; background:#f8d7da; border:1px solid #f5c6cb; border-radius:100px; font-size:.72rem; font-weight:700; color:#721c24; }
</style>

<div class="card">
    <div class="card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <h2 class="card-title">🏛️ Inscripciones Derechos Reales</h2>
            <p style="margin:0; color:var(--text-muted); font-size:.9rem;">
                Revisa y aprueba las inscripciones enviadas por los vendedores
            </p>
        </div>
        <div style="display:flex; gap:1.25rem;">
            <div style="text-align:center;">
                <div style="font-size:1.3rem; font-weight:700; color:#856404;">{{ $inscripciones->where('estado','pendiente')->count() }}</div>
                <div style="font-size:.68rem; color:var(--text-muted); text-transform:uppercase;">Pendientes</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.3rem; font-weight:700; color:#004085;">{{ $inscripciones->where('estado','en_revision')->count() }}</div>
                <div style="font-size:.68rem; color:var(--text-muted); text-transform:uppercase;">En revisión</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.3rem; font-weight:700; color:#155724;">{{ $inscripciones->where('estado','inscrito')->count() }}</div>
                <div style="font-size:.68rem; color:var(--text-muted); text-transform:uppercase;">Inscritos</div>
            </div>
        </div>
    </div>

    <div class="card-body" style="padding:0;">

        @if(session('success'))
            <div class="alert alert-success" style="margin:1.25rem 1.5rem 0;">{{ session('success') }}</div>
        @endif

        @if($inscripciones->isEmpty())
            <div style="padding:3rem; text-align:center; color:var(--text-muted);">
                <p style="font-size:2rem;">📭</p>
                <p>No hay inscripciones registradas aún.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="ins-table">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Terreno</th>
                            <th>Vendedor</th>
                            <th>Matrícula</th>
                            <th>Tasa</th>
                            <th>Comprobante</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inscripciones as $ins)
                        <tr>
                            <td><strong>{{ $ins->folio->numero_folio }}</strong></td>
                            <td>
                                <a href="{{ route('admin.ver_terreno', $ins->folio->terreno->id) }}"
                                   style="color:var(--primary); text-decoration:none;">
                                    #{{ $ins->folio->terreno->id }} — {{ Str::limit($ins->folio->terreno->ubicacion, 25) }}
                                </a>
                            </td>
                            <td>{{ $ins->folio->terreno->vendedor->nombre ?? '—' }}</td>
                            <td>{{ $ins->numero_matricula ?? '—' }}</td>
                            <td>{{ $ins->tasa_pagada ? 'Bs. ' . number_format($ins->tasa_pagada, 2) : '—' }}</td>
                            <td>
                                @if($ins->comprobante_archivo)
                                    <a href="{{ route('admin.inscripcion.archivo', $ins->id) }}"
                                       target="_blank"
                                       style="color:#007bff; font-size:.82rem;">
                                        📎 Ver archivo
                                    </a>
                                @else
                                    <span style="color:var(--text-muted); font-size:.82rem;">Sin archivo</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-{{ $ins->estado }}">
                                    @php
                                        $textos = [
                                            'pendiente'   => '🕐 Pendiente',
                                            'en_revision' => '🔍 En revisión',
                                            'inscrito'    => '✅ Inscrito',
                                            'rechazado'   => '❌ Rechazado',
                                        ];
                                    @endphp
                                    {{ $textos[$ins->estado] ?? $ins->estado }}
                                </span>
                            </td>
                            <td>
                                @if($ins->estado !== 'inscrito')
                                <form action="{{ route('admin.inscripcion.procesar') }}" method="POST"
                                      style="display:flex; flex-direction:column; gap:.4rem; min-width:180px;">
                                    @csrf
                                    <input type="hidden" name="inscripcion_id" value="{{ $ins->id }}">
                                    <select name="estado" class="form-control" style="padding:.35rem .6rem; font-size:.8rem;">
                                        <option value="en_revision" {{ $ins->estado === 'en_revision' ? 'selected' : '' }}>🔍 En revisión</option>
                                        <option value="inscrito">✅ Marcar como Inscrito</option>
                                        <option value="rechazado">❌ Rechazar</option>
                                    </select>
                                    <input type="text" name="observacion" class="form-control"
                                           style="padding:.35rem .6rem; font-size:.8rem;"
                                           placeholder="Observación (opcional)">
                                    <button type="submit" class="btn btn-primary"
                                            style="padding:.4rem .8rem; font-size:.8rem;">
                                        Guardar
                                    </button>
                                </form>
                                @else
                                    <span style="font-size:.8rem; color:#155724;">
                                        Revisado por {{ $ins->revisor->nombre ?? 'Admin' }}
                                    </span>
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