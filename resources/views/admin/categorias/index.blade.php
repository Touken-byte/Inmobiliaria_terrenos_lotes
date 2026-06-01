@extends('layouts.app')

@section('title', 'Gestión de Categorías')

@section('content')
<style>
    .cat-mgmt { max-width: 1100px; margin: 0 auto; padding: 2rem 1.5rem; }

    .cat-mgmt-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .cat-mgmt-header h1 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #eef2fc;
    }
    .cat-mgmt-header p { font-size: .85rem; color: #8fa3cc; margin-top: .2rem; }

    .btn-new {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .65rem 1.4rem;
        background: linear-gradient(135deg, #c9a84c, #a8782a);
        border: none;
        border-radius: 12px;
        color: #1a0f00;
        font-size: .82rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        text-decoration: none;
        cursor: pointer;
        transition: filter .2s;
    }
    .btn-new:hover { filter: brightness(1.1); color: #1a0f00; }

    .cat-table-wrap {
        background: #0f1830;
        border: 1px solid rgba(120,160,255,0.10);
        border-radius: 20px;
        overflow: hidden;
    }

    table { width: 100%; border-collapse: collapse; }
    thead th {
        padding: 1rem 1.25rem;
        text-align: left;
        font-size: .62rem;
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: #3d5480;
        border-bottom: 1px solid rgba(120,160,255,0.10);
    }
    tbody tr { border-bottom: 1px solid rgba(120,160,255,0.06); transition: background .2s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: rgba(120,160,255,0.04); }
    tbody td { padding: 1rem 1.25rem; font-size: .85rem; color: #8fa3cc; vertical-align: middle; }

    .color-dot {
        display: inline-block;
        width: 14px; height: 14px;
        border-radius: 50%;
        margin-right: .5rem;
        vertical-align: middle;
        box-shadow: 0 0 6px currentColor;
        flex-shrink: 0;
    }
    .nombre-cell { display: flex; align-items: center; color: #eef2fc; font-weight: 600; }

    .badge-activa {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .3rem .75rem;
        border-radius: 100px;
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
    }
    .badge-activa.on  { background: rgba(29,186,126,0.15); color: #1dba7e; border: 1px solid rgba(29,186,126,0.3); }
    .badge-activa.off { background: rgba(120,160,255,0.08); color: #3d5480; border: 1px solid rgba(120,160,255,0.15); }

    .lotes-count { font-weight: 600; color: #eef2fc; }

    .actions { display: flex; gap: .5rem; }
    .btn-edit, .btn-del {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .9rem;
        border-radius: 8px;
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .05em;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: filter .2s;
    }
    .btn-edit { background: rgba(61,126,245,0.15); color: #3d7ef5; border: 1px solid rgba(61,126,245,0.25); }
    .btn-edit:hover { filter: brightness(1.2); color: #3d7ef5; }
    .btn-del  { background: rgba(239,68,68,0.12); color: #ef4444; border: 1px solid rgba(239,68,68,0.25); }
    .btn-del:hover  { filter: brightness(1.2); }

    .empty-row td {
        text-align: center;
        padding: 4rem 2rem;
        color: #3d5480;
        font-size: .9rem;
    }
</style>

<div class="cat-mgmt">

    <div class="cat-mgmt-header">
        <div>
            <h1>Gestión de Categorías</h1>
            <p>Clasificación del catálogo de terrenos y alquileres — IN-A02</p>
        </div>
        <a href="{{ route('admin.categorias.create') }}" class="btn-new">
            <i class="fa-solid fa-plus"></i>
            Nueva Categoría
        </a>
    </div>

    <div class="cat-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Aplica a</th>
                    <th>Estado</th>
                    <th>Estadísticas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $cat)
                <tr>
                    <td>
                        <div class="nombre-cell">
                            <span class="color-dot" style="background:{{ $cat->color }};"></span>
                            {{ $cat->nombre }}
                        </div>
                    </td>
                    <td>{{ $cat->descripcion ?? '—' }}</td>
                    <td>
                        @if(($cat->tipo_propiedad ?? 'todos') === 'todos')
                            <span class="badge badge-secondary" style="font-size:0.7rem; padding:4px 8px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12); color:#fff; border-radius:100px; text-transform:uppercase; font-weight:600;">Todos</span>
                        @elseif($cat->tipo_propiedad === 'terreno')
                            <span class="badge badge-info" style="font-size:0.7rem; padding:4px 8px; background:rgba(61,126,245,0.15); border:1px solid rgba(61,126,245,0.3); color:#3d7ef5; border-radius:100px; text-transform:uppercase; font-weight:600;">Terrenos</span>
                        @elseif($cat->tipo_propiedad === 'lote')
                            <span class="badge badge-success" style="font-size:0.7rem; padding:4px 8px; background:rgba(29,186,126,0.15); border:1px solid rgba(29,186,126,0.3); color:#1dba7e; border-radius:100px; text-transform:uppercase; font-weight:600;">Lotes</span>
                        @elseif($cat->tipo_propiedad === 'alquiler')
                            <span class="badge badge-warning" style="font-size:0.7rem; padding:4px 8px; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); color:#f59e0b; border-radius:100px; text-transform:uppercase; font-weight:600;">Alquileres</span>
                        @endif
                    </td>
                    <td>
                        @if($cat->activa)
                            <span class="badge-activa on"><i class="fa-solid fa-circle" style="font-size:.4rem;"></i> Activa</span>
                        @else
                            <span class="badge-activa off"><i class="fa-solid fa-circle" style="font-size:.4rem;"></i> Inactiva</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; flex-direction:column; gap:0.25rem;">
                            <span class="lotes-count">
                                {{ $cat->terrenos_activos + $cat->alquileres_activos }} activas
                            </span>
                            <span style="font-size:.72rem;color:#3d5480;">
                                {{ $cat->terrenos_vendidos }} ventas · {{ $cat->alquileres_cerrados }} alquileres
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.categorias.edit', $cat) }}" class="btn-edit">
                                <i class="fa-solid fa-pen"></i> Editar
                            </a>
                            <form action="{{ route('admin.categorias.destroy', $cat) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar la categoría «{{ $cat->nombre }}»? Esta acción no se puede deshacer.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-del">
                                    <i class="fa-solid fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="6">
                        <i class="fa-solid fa-tag" style="font-size:2rem;display:block;margin-bottom:.75rem;"></i>
                        No hay categorías creadas aún. <a href="{{ route('admin.categorias.create') }}" style="color:#c9a84c;">Crear la primera</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection