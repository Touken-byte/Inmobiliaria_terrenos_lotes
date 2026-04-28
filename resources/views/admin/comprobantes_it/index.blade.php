@extends('layouts.app')

@section('title', 'Comprobantes IT')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Gestión de Impuestos de Transferencia
            </h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #edf2f7; text-align: left;">
                            <th style="padding: 12px;">ID</th>
                            <th style="padding: 12px;">Vendedor</th>
                            <th style="padding: 12px;">N° Recibo</th>
                            <th style="padding: 12px;">Fecha Pago</th>
                            <th style="padding: 12px;">Monto (USD)</th>
                            <th style="padding: 12px;">Estado</th>
                            <th style="padding: 12px; text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comprobantes as $comp)
                            <tr style="border-bottom: 1px solid #edf2f7;">
                                <td style="padding: 12px;">#{{ $comp->id }}</td>
                                <td style="padding: 12px;">
                                    <strong>{{ $comp->usuario->nombre }}</strong><br>
                                    <small style="color: #718096;">{{ $comp->usuario->email }}</small>
                                </td>
                                <td style="padding: 12px;">{{ $comp->numero_recibo }}</td>
                                <td style="padding: 12px;">{{ $comp->fecha_pago->format('d/m/Y') }}</td>
                                <td style="padding: 12px; font-weight: bold; color: var(--primary);">
                                    ${{ number_format($comp->monto, 2) }}
                                </td>
                                <td style="padding: 12px;">
                                    @if($comp->estado === 'pendiente')
                                        <span class="badge badge-warning">Pendiente</span>
                                    @elseif($comp->estado === 'aprobado')
                                        <span class="badge badge-success">Aprobado</span>
                                    @else
                                        <span class="badge badge-danger">Rechazado</span>
                                        @if($comp->observacion)
                                            <br><small style="color: #e53e3e; display:block; max-width:150px; margin-top:4px;">"{{ $comp->observacion }}"</small>
                                        @endif
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: right; display:flex; gap:8px; justify-content: flex-end;">
                                    <a href="{{ route('admin.comprobantes_it.archivo', $comp->id) }}" target="_blank" class="btn btn-secondary btn-sm" title="Ver Comprobante">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    </a>

                                    @if($comp->estado === 'pendiente')
                                        <button type="button" class="btn btn-success btn-sm" title="Aprobar" onclick="abrirModalAprobar({{ $comp->id }}, '{{ $comp->numero_recibo }}')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </button>

                                        <button type="button" class="btn btn-danger btn-sm" title="Rechazar" onclick="abrirModalRechazo({{ $comp->id }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 24px; text-align: center; color: #a0aec0;">No hay comprobantes subidos todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <div style="margin-top: 20px;">
                    {{ $comprobantes->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rechazo -->
    <div id="modalRechazo" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px);">
        <div class="card" style="width: 100%; max-width: 500px; margin: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="card-header">
                <h3 class="card-title">Rechazar Comprobante</h3>
                <button type="button" onclick="cerrarModalRechazo()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>
            <div class="card-body">
                <form id="formRechazo" method="POST" action="">
                    @csrf
                    <div style="margin-bottom: 15px;">
                        <label for="observacion" style="display:block; margin-bottom:5px; font-weight:bold;">Motivo del rechazo <span style="color:red;">*</span></label>
                        <textarea name="observacion" id="observacion" rows="3" style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e0; font-family:inherit;" required placeholder="Ej. El número de recibo no coincide o imagen borrosa..."></textarea>
                    </div>
                    <div style="text-align: right; display:flex; gap:10px; justify-content:flex-end;">
                        <button type="button" class="btn btn-secondary" onclick="cerrarModalRechazo()">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Aprobar -->
    <div id="modalAprobar" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px);">
        <div class="card" style="width: 100%; max-width: 400px; margin: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); text-align: center;">
            <div class="card-body" style="padding: 30px;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: #d1fae5; color: #059669; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:30px;height:30px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 10px;">¿Aprobar Comprobante?</h3>
                <p style="color: #64748b; margin-bottom: 25px;">Estás a punto de aprobar el recibo <strong id="textoAprobarRecibo"></strong>. El vendedor será notificado de inmediato.</p>
                
                <form id="formAprobar" method="POST" action="">
                    @csrf
                    <div style="display:flex; gap:10px; justify-content:center;">
                        <button type="button" class="btn btn-secondary" onclick="cerrarModalAprobar()" style="flex:1;">Cancelar</button>
                        <button type="submit" class="btn btn-success" style="flex:1; justify-content:center;">Sí, Aprobar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalRechazo(id) {
            document.getElementById('formRechazo').action = `/admin/comprobantes-it/${id}/rechazar`;
            document.getElementById('modalRechazo').style.display = 'flex';
        }

        function cerrarModalRechazo() {
            document.getElementById('modalRechazo').style.display = 'none';
        }

        function abrirModalAprobar(id, numeroRecibo) {
            document.getElementById('formAprobar').action = `/admin/comprobantes-it/${id}/aprobar`;
            document.getElementById('textoAprobarRecibo').innerText = numeroRecibo;
            document.getElementById('modalAprobar').style.display = 'flex';
        }

        function cerrarModalAprobar() {
            document.getElementById('modalAprobar').style.display = 'none';
        }
    </script>
@endsection
