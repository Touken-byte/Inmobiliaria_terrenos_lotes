@extends('layouts.app')
@section('title', 'Gestión Legal Administrativa')

@section('content')
<style>
.legal-table th { padding: 1.25rem 1rem; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.5; font-weight: 800; border-bottom: 2px solid rgba(255,255,255,0.05); }
.legal-table td { padding: 1.5rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.03); vertical-align: middle; }
.legal-card { border-radius: 20px; overflow: hidden; }

.doc-status { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600; }
.dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
.dot-pendiente { background: #f59e0b; box-shadow: 0 0 8px #f59e0b; }
.dot-aprobado  { background: #10b981; box-shadow: 0 0 8px #10b981; }
.dot-rechazado { background: #ef4444; box-shadow: 0 0 8px #ef4444; }

.action-btn-circle {
    width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);
    transition: all 0.2s; cursor: pointer;
}
.action-btn-circle:hover { background: var(--primary); color: #fff; border-color: var(--primary); transform: translateY(-2px); }
.action-btn-circle.btn-success:hover { background: #10b981; border-color: #10b981; }
.action-btn-circle.btn-danger:hover { background: #ef4444; border-color: #ef4444; }
</style>

<div class="legal-card card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 2rem;">
        <div>
            <h2 class="card-title" style="font-size: 1.5rem; font-weight: 800; margin: 0;">⚖️ Gestión de Trámites Legales</h2>
            <p style="margin: 5px 0 0; opacity: 0.5; font-size: 0.9rem;">Revisión y validación de minutas e impuestos de transferencia.</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 1.25rem; font-weight: 800;">{{ $minutas->total() }}</div>
            <div style="font-size: 0.7rem; opacity: 0.4; text-transform: uppercase;">Total Trámites</div>
        </div>
    </div>

    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="legal-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Vendedor / Comprador</th>
                        <th>Terreno</th>
                        <th>Estado Minuta</th>
                        <th>Estado IT</th>
                        <th>Monto Total</th>
                        <th style="text-align: right;">Acciones de Gestión</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($minutas as $minuta)
                    @php $comp = $minuta->comprobante; @endphp
                    <tr onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                        <td>
                            <div style="font-weight: 700;">{{ $minuta->vendedor->nombre ?? 'N/D' }}</div>
                            <div style="font-size: 0.75rem; opacity: 0.5;">Cliente: {{ $minuta->comprador->nombre ?? 'N/D' }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; font-size: 0.9rem;">{{ Str::limit($minuta->terreno->ubicacion ?? 'N/D', 30) }}</div>
                            <div style="font-size: 0.75rem; opacity: 0.5;">ID Terreno: #{{ $minuta->terreno_id }}</div>
                        </td>
                        <td>
                            <div class="doc-status">
                                <span class="dot dot-{{ $minuta->estado === 'aprobada' ? 'aprobado' : ($minuta->estado === 'rechazada' ? 'rechazado' : 'pendiente') }}"></span>
                                {{ ucfirst($minuta->estado) }}
                            </div>
                        </td>
                        <td>
                            @if($comp)
                                <div class="doc-status">
                                    <span class="dot dot-{{ $comp->estado === 'aprobado' ? 'aprobado' : ($comp->estado === 'rechazado' ? 'rechazado' : 'pendiente') }}"></span>
                                    {{ ucfirst($comp->estado) }}
                                </div>
                            @else
                                <span style="opacity: 0.3; font-style: italic; font-size: 0.8rem;">No cargado</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--accent);">${{ number_format($minuta->monto + ($comp->monto ?? 0), 2) }}</div>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                {{-- Sección MINUTA --}}
                                <div style="display: flex; gap: 5px; background: rgba(255,255,255,0.03); padding: 5px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                                    @if($minuta->archivo)
                                        <a href="{{ route('admin.tramites_legales.ver_minuta', $minuta->id) }}" target="_blank" class="action-btn-circle" title="Ver Minuta">📄</a>
                                    @endif
                                    
                                    @if($minuta->estado === 'pendiente')
                                        <button class="action-btn-circle btn-success" title="Aprobar Minuta" onclick="openModal('aprobar-minuta', {{ $minuta->id }}, '{{ addslashes($minuta->vendedor->nombre) }}')">✓</button>
                                        <button class="action-btn-circle btn-danger" title="Rechazar Minuta" onclick="openModal('rechazar-minuta', {{ $minuta->id }})">✗</button>
                                    @endif
                                </div>

                                {{-- Sección IT (Solo aparece si el vendedor ya cargó algo) --}}
                                @if($comp)
                                    <div style="width: 2px; height: 20px; background: rgba(255,255,255,0.1);"></div>
                                    <div style="display: flex; gap: 5px; background: rgba(255,255,255,0.03); padding: 5px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                                        @if($comp->archivo)
                                            <a href="{{ route('admin.comprobantes_it.archivo', $comp->id) }}" target="_blank" class="action-btn-circle" title="Ver Recibo IT">🧾</a>
                                        @endif

                                        @if($comp->estado === 'pendiente')
                                            <button class="action-btn-circle btn-success" title="Aprobar IT" onclick="openModal('aprobar-it', {{ $comp->id }}, '{{ addslashes($minuta->vendedor->nombre) }}')">IT ✓</button>
                                            <button class="action-btn-circle btn-danger" title="Rechazar IT" onclick="openModal('rechazar-it', {{ $comp->id }})">IT ✗</button>
                                        @endif
                                    </div>
                                @endif

                                {{-- Sección Protocolización (Testimonio) --}}
                                @if($minuta->protocolizacion)
                                    <div style="width: 2px; height: 20px; background: rgba(255,255,255,0.1);"></div>
                                    <div style="display: flex; gap: 5px; background: rgba(255,255,255,0.03); padding: 5px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                                        @if($minuta->protocolizacion->archivo_testimonio)
                                            <a href="{{ route('admin.tramites_legales.ver_testimonio', $minuta->protocolizacion->id) }}" target="_blank" class="action-btn-circle" title="Ver Testimonio">🖋️</a>
                                        @endif

                                        @if($minuta->protocolizacion->estado === 'pendiente')
                                            <button class="action-btn-circle btn-success" title="Aprobar Protocolización" onclick="openModal('aprobar-prot', {{ $minuta->protocolizacion->id }}, '{{ addslashes($minuta->vendedor->nombre) }}')">P ✓</button>
                                            <button class="action-btn-circle btn-danger" title="Rechazar Protocolización" onclick="openModal('rechazar-prot', {{ $minuta->protocolizacion->id }})">P ✗</button>
                                        @endif
                                    </div>
                                @endif

                                {{-- Botón Finalizar (Habilitado si la protocolización está aprobada) --}}
                                @if($minuta->protocolizacion && $minuta->protocolizacion->estado === 'aprobado')
                                    <button type="button" class="btn btn-primary btn-sm" 
                                            style="height: 34px; border-radius: 10px; margin-left: 10px; font-weight: 700; background: linear-gradient(135deg, #7c3aed, #4f46e5);"
                                            onclick="openModal('finalizar', {{ $minuta->id }}, '{{ addslashes($minuta->vendedor->nombre) }}')">
                                        🏆 Finalizar Venta
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 4rem; text-align: center;">
                            <div style="font-size: 3rem; opacity: 0.1; margin-bottom: 1rem;">⚖️</div>
                            <p style="opacity: 0.4;">No hay trámites pendientes de revisión.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 2rem;">
            {{ $minutas->links() }}
        </div>
    </div>
</div>

{{-- MODAL SYSTEM --}}
<div id="modal-container" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:9999; backdrop-filter:blur(8px); align-items:center; justify-content:center; padding: 20px;">
    <div class="card" style="max-width: 480px; width: 100%; border-radius: 24px; border-color: rgba(255,255,255,0.1);">
        <div class="card-header"><h3 id="modal-title" class="card-title">Título Modal</h3></div>
        <div class="card-body" style="padding: 2rem;">
            <p id="modal-msg" style="opacity: 0.6; margin-bottom: 1.5rem;"></p>
            <form id="modal-form" method="POST">
                @csrf
                <div id="obs-container" style="display: none; margin-bottom: 1.5rem;">
                    <label class="info-label" style="display: block; margin-bottom: 8px;">Motivo del Rechazo / Observación</label>
                    <textarea name="observacion" rows="4" class="form-control" placeholder="Indique qué debe corregir el vendedor..."></textarea>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="button" class="btn btn-secondary" style="flex:1;" onclick="closeModal()">Cancelar</button>
                    <button type="submit" id="modal-submit" class="btn btn-primary" style="flex:1;">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(type, id, name = '') {
    const container = document.getElementById('modal-container');
    const title = document.getElementById('modal-title');
    const msg = document.getElementById('modal-msg');
    const form = document.getElementById('modal-form');
    const obs = document.getElementById('obs-container');
    const submit = document.getElementById('modal-submit');

    obs.style.display = 'none';
    submit.className = 'btn btn-primary';

    if(type === 'aprobar-minuta') {
        title.innerText = '¿Aprobar Minuta?';
        msg.innerText = `Confirmas que la minuta presentada por ${name} cumple con todos los requisitos legales.`;
        form.action = `/admin/tramites-legales/${id}/aprobar-minuta`;
        submit.innerText = 'Sí, Aprobar';
        submit.className = 'btn btn-success';
    } else if(type === 'rechazar-minuta') {
        title.innerText = 'Rechazar Minuta';
        msg.innerText = 'El vendedor recibirá una notificación con tus observaciones para corregir el documento.';
        form.action = `/admin/tramites-legales/${id}/rechazar-minuta`;
        obs.style.display = 'block';
        submit.innerText = 'Solicitar Corrección';
        submit.className = 'btn btn-danger';
    } else if(type === 'aprobar-it') {
        title.innerText = '¿Aprobar Comprobante IT?';
        msg.innerText = `Confirmas que el comprobante de impuesto IT de ${name} es válido.`;
        form.action = `/admin/tramites-legales/${id}/aprobar-it`;
        submit.innerText = 'Sí, Aprobar IT';
        submit.className = 'btn btn-success';
    } else if(type === 'rechazar-it') {
        title.innerText = 'Rechazar Comprobante IT';
        msg.innerText = 'Indique qué problema encontró en el comprobante IT.';
        form.action = `/admin/tramites-legales/${id}/rechazar-it`;
        obs.style.display = 'block';
        submit.innerText = 'Rechazar Comprobante';
        submit.className = 'btn btn-danger';
    } else if(type === 'aprobar-prot') {
        title.innerText = '¿Aprobar Protocolización?';
        msg.innerText = `Confirmas que el testimonio notarial presentado por ${name} es correcto.`;
        form.action = `/admin/tramites-legales/${id}/aprobar-protocolizacion`;
        submit.innerText = 'Sí, Aprobar Protocolización';
        submit.className = 'btn btn-success';
    } else if(type === 'rechazar-prot') {
        title.innerText = 'Rechazar Protocolización';
        msg.innerText = 'Indique qué problema encontró en el testimonio notarial.';
        form.action = `/admin/tramites-legales/${id}/rechazar-protocolizacion`;
        obs.style.display = 'block';
        submit.innerText = 'Rechazar Testimonio';
        submit.className = 'btn btn-danger';
    } else if(type === 'finalizar') {
        title.innerText = '🏆 Finalizar Venta Oficialmente';
        msg.innerHTML = `Estás a punto de completar el cierre legal de <strong>${name}</strong>.<br><br>El lote se marcará como <strong>VENDIDO</strong> y el trámite quedará archivado como finalizado.`;
        form.action = `/admin/tramites-legales/${id}/finalizar`;
        submit.innerText = 'Confirmar Venta Cerrada';
        submit.className = 'btn btn-primary';
    }

    container.style.display = 'flex';
}

function closeModal() {
    document.getElementById('modal-container').style.display = 'none';
}
</script>
@endsection
