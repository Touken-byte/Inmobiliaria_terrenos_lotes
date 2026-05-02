@extends('layouts.app')
@section('title', 'Historial Legal de Ventas')

@section('content')
<style>
.status-chip {
    padding: 4px 12px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
}
.status-pendiente { background: rgba(245,158,11,0.1); color: #f59e0b; border: 1px solid rgba(245,158,11,0.2); }
.status-aprobado  { background: rgba(16,185,129,0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
.status-rechazado { background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }

.doc-status { display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 600; }
.dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
.dot-pendiente { background: #f59e0b; box-shadow: 0 0 8px #f59e0b; }
.dot-aprobado  { background: #10b981; box-shadow: 0 0 8px #10b981; }
.dot-rechazado { background: #ef4444; box-shadow: 0 0 8px #ef4444; }

.action-btn-circle {
    width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);
    transition: all 0.2s; cursor: pointer; text-decoration: none !important;
}
.action-btn-circle:hover { background: var(--primary); color: #fff; border-color: var(--primary); transform: translateY(-2px); }
</style>

<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="mb-1">Historial de Trámites Legales</h2>
                <p class="text-muted mb-0">Registro completo de todas tus ventas y su documentación legal.</p>
            </div>
        </div>

    <div class="card" style="border-radius: 24px; overflow: hidden; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <th style="padding: 1.5rem; opacity: 0.4; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;">Terreno / Cliente</th>
                            <th style="padding: 1.5rem; opacity: 0.4; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;">Estado Minuta</th>
                            <th style="padding: 1.5rem; opacity: 0.4; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;">Estado IT</th>
                            <th style="padding: 1.5rem; opacity: 0.4; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;">Monto Total</th>
                            <th style="padding: 1.5rem; opacity: 0.4; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Documentos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historial as $h)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1.5rem;">
                                <div style="font-weight: 700; font-size: 1rem;">{{ $h->terreno->ubicacion ?? 'N/D' }}</div>
                                <div style="font-size: 0.8rem; opacity: 0.5; margin-top: 2px;">Comprador: {{ $h->comprador->nombre ?? 'N/D' }}</div>
                            </td>
                            <td style="padding: 1.5rem;">
                                <div class="doc-status">
                                    <span class="dot dot-{{ in_array($h->estado, ['aprobada', 'completada']) ? 'aprobado' : ($h->estado === 'rechazada' ? 'rechazado' : 'pendiente') }}"></span>
                                    {{ ucfirst($h->estado) }}
                                </div>
                                <div style="font-size: 0.75rem; opacity: 0.4; margin-top: 5px;">{{ $h->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td style="padding: 1.5rem;">
                                @if($h->it)
                                    <div class="doc-status">
                                        <span class="dot dot-{{ in_array($h->it->estado, ['aprobado', 'completado']) ? 'aprobado' : ($h->it->estado === 'rechazado' ? 'rechazado' : 'pendiente') }}"></span>
                                        {{ ucfirst($h->it->estado) }}
                                    </div>
                                @else
                                    <span style="opacity: 0.3; font-style: italic; font-size: 0.85rem;">No cargado</span>
                                @endif
                            </td>
                            <td style="padding: 1.5rem;">
                                <div style="font-weight: 800; color: var(--accent); font-size: 1.1rem;">${{ number_format($h->monto + ($h->it->monto ?? 0), 2) }}</div>
                            </td>
                            <td style="padding: 1.5rem; text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                    {{-- Grupo Minuta --}}
                                    <div style="display: flex; gap: 5px; background: rgba(255,255,255,0.03); padding: 5px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                                        @if($h->archivo)
                                            <a href="{{ route('vendedor.minuta.archivo', $h->id) }}" target="_blank" class="action-btn-circle" title="Ver Minuta">📄</a>
                                        @else
                                            <span style="opacity: 0.1; padding: 0 5px;">-</span>
                                        @endif
                                    </div>

                                    @if($h->it && $h->it->archivo)
                                        <div style="width: 1px; height: 15px; background: rgba(255,255,255,0.1);"></div>

                                        {{-- Grupo IT --}}
                                        <div style="display: flex; gap: 5px; background: rgba(255,255,255,0.03); padding: 5px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                                            <a href="{{ route('vendedor.comprobante_it.archivo', $h->it->id) }}" target="_blank" class="action-btn-circle" title="Ver IT">🧾</a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding: 5rem; text-align: center;">
                                <div style="font-size: 3rem; opacity: 0.1; margin-bottom: 1.5rem;">📁</div>
                                <p style="opacity: 0.4; font-size: 1.1rem;">No tienes trámites legales registrados aún.</p>
                                <a href="{{ route('vendedor.proceso_legal') }}" class="btn btn-primary btn-sm" style="margin-top: 1rem; border-radius: 10px;">Iniciar Trámite</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
