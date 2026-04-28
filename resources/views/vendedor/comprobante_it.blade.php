@extends('layouts.app')

@section('title', 'Impuesto de Transferencia')

@section('content')
<div style="display:flex; flex-wrap:wrap; gap:24px;">

    <!-- LADO IZQUIERDO: FORMULARIO -->
    <div class="card form-card" style="flex:1; min-width: 350px;">
        <div class="card-header">
            <h2 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Cargar Comprobante IT
            </h2>
        </div>

        <div class="card-body">
            <p style="margin-bottom: 20px; font-size: 0.9rem; opacity:0.9;">
                Registre el pago del Impuesto de Transferencia (IT) correspondiente. Un administrador revisará la documentación.
            </p>

            <form action="{{ route('vendedor.comprobante_it.store') }}" method="POST" enctype="multipart/form-data" id="itForm">
                @csrf

                <div class="form-group">
                    <label for="numero_recibo">Número de Recibo <span class="required">*</span></label>
                    <input type="text" name="numero_recibo" id="numero_recibo" class="form-control" placeholder="Ej: REC-12345678" value="{{ old('numero_recibo') }}" required>
                </div>

                <div class="form-row" style="display:flex; gap:16px; margin-bottom:1.5rem;">
                    <div class="form-group" style="flex:1; margin-bottom:0;">
                        <label for="fecha_pago">Fecha de Pago <span class="required">*</span></label>
                        <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" value="{{ old('fecha_pago') }}" max="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group" style="flex:1; margin-bottom:0;">
                        <label for="monto">Monto (USD) <span class="required">*</span></label>
                        <input type="number" name="monto" id="monto" class="form-control" placeholder="Ej: 450.50" min="0.01" step="0.01" value="{{ old('monto') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Archivo Comprobante <span class="required">*</span> <small>(PDF, JPG, PNG - Máx 5MB)</small></label>
                    <div class="dropzone" id="fileDropzone" style="border: 2px dashed rgba(255,255,255,0.2); border-radius: 12px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s; background: rgba(0,0,0,0.1);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 40px; height: 40px; opacity: 0.6; margin-bottom: 10px;">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <p id="fileName" style="font-weight: 600;">Haga clic o arrastre el archivo aquí</p>
                    </div>
                    <input type="file" name="archivo" id="archivoInput" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="submitBtn" style="margin-top: 20px; width:100%; display:flex; justify-content:center; font-size:1.05rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;margin-right:8px;">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    Enviar Comprobante
                </button>
            </form>
        </div>
    </div>

    <!-- LADO DERECHO: HISTORIAL / ESTADO -->
    <div style="flex:1; min-width: 350px; display:flex; flex-direction:column; gap:24px;">
        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0;">Mi Historial de Comprobantes</h3>
        
        @forelse($comprobantes as $comp)
            <div class="card" style="margin-bottom:0; {{ $loop->first ? 'border-left: 4px solid var(--primary);' : 'opacity: 0.8;' }}">
                <div class="card-body">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 15px;">
                        <div>
                            <span style="font-size:0.8rem; text-transform:uppercase; letter-spacing:1px; opacity:0.7;">Recibo</span>
                            <h4 style="font-size: 1.1rem; font-weight: 800; margin:0;">{{ $comp->numero_recibo }}</h4>
                        </div>
                        @if($comp->estado === 'pendiente')
                            <span class="badge badge-warning" style="padding: 6px 12px; font-size: 0.8rem;">En Revisión</span>
                        @elseif($comp->estado === 'aprobado')
                            <span class="badge badge-success" style="padding: 6px 12px; font-size: 0.8rem;">Aprobado</span>
                        @else
                            <span class="badge badge-danger" style="padding: 6px 12px; font-size: 0.8rem;">Rechazado</span>
                        @endif
                    </div>
                    
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; padding: 15px; background: rgba(0,0,0,0.2); border-radius: 8px;">
                        <div>
                            <span style="font-size:0.75rem; opacity:0.7;">Fecha Pago</span>
                            <div style="font-weight:600;">{{ $comp->fecha_pago->format('d M, Y') }}</div>
                        </div>
                        <div>
                            <span style="font-size:0.75rem; opacity:0.7;">Monto Total</span>
                            <div style="font-weight:600; color:var(--accent);">${{ number_format($comp->monto, 2) }}</div>
                        </div>
                    </div>

                    @if($comp->estado === 'rechazado' && $comp->observacion)
                        <div class="alert alert-danger" style="margin-bottom: 0; border-radius:8px; padding:12px;">
                            <strong style="display:block; font-size:0.8rem; margin-bottom:4px;">Motivo del rechazo:</strong>
                            <p style="font-size: 0.9rem; margin:0; line-height:1.4;">{{ $comp->observacion }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="card" style="background: rgba(0,0,0,0.1); border: 2px dashed rgba(255,255,255,0.1); box-shadow:none;">
                <div class="card-body" style="text-align:center; padding: 40px 20px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 48px; height: 48px; opacity: 0.3; margin-bottom: 15px; margin-left:auto; margin-right:auto; display:block;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    </svg>
                    <h4 style="opacity:0.7;">No hay registros</h4>
                    <p style="opacity:0.5; font-size:0.9rem;">Aún no ha subido comprobantes IT.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .form-group { margin-bottom: 1.5rem; }
    label { display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem; color: #fff !important; }
    .required { color: #dc3545; }
    .form-control { width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.4); color: #fff !important; font-size: 1rem; transition: all 0.3s; }
    .form-control:focus { outline: none; border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2); }
    .dropzone:hover { border-color: var(--primary-light) !important; background: rgba(124,58,237,0.1) !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropzone = document.getElementById('fileDropzone');
        const fileInput = document.getElementById('archivoInput');
        const fileName = document.getElementById('fileName');
        const form = document.getElementById('itForm');
        const submitBtn = document.getElementById('submitBtn');

        dropzone.addEventListener('click', () => fileInput.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = '#7c3aed';
            dropzone.style.background = 'rgba(124,58,237,0.1)';
        });

        dropzone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = 'rgba(255,255,255,0.2)';
            dropzone.style.background = 'rgba(0,0,0,0.1)';
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                updateFileName();
            }
        });

        fileInput.addEventListener('change', updateFileName);

        function updateFileName() {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    alert('El archivo no puede pesar más de 5MB');
                    fileInput.value = '';
                    fileName.innerHTML = 'Haga clic o arrastre el archivo aquí';
                    return;
                }
                fileName.innerHTML = `<span style="color:var(--accent);">${file.name}</span> <br><small>(${Math.round(file.size/1024)} KB)</small>`;
                dropzone.style.borderColor = '#10b981';
            }
        }

        form.addEventListener('submit', (e) => {
            if (fileInput.files.length === 0) {
                e.preventDefault();
                alert('Debe adjuntar el comprobante.');
                return false;
            }
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Enviando...';
            return true;
        });
    });
</script>
@endsection
