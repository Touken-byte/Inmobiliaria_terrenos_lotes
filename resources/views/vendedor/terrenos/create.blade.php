@extends('layouts.app')

@section('title', 'Publicar Terreno')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .form-card {
        max-width: 800px;
        margin: 0 auto;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-row {
        display: flex;
        gap: 1.5rem;
    }
    .form-row .form-group {
        flex: 1;
    }
    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        font-size: 0.95rem;
        color: #000 !important;
    }
    .required {
        color: #dc3545;
    }
    .char-counter {
        font-size: 0.8rem;
        color: #6c757d;
        text-align: right;
        margin-top: 0.25rem;
        font-family: monospace;
    }
    .images-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .preview-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        height: 130px;
        border: 1px solid #dee2e6;
        background: #f8f9fa;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.9;
        transition: 0.3s;
    }
    .preview-item:hover img {
        opacity: 1;
        transform: scale(1.05);
    }
    .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.7);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        width: 26px;
        height: 26px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        backdrop-filter: blur(4px);
        transition: 0.2s;
    }
    .remove-btn:hover {
        background: #dc3545;
        border-color: #dc3545;
        transform: scale(1.1);
    }
    .multi-dropzone {
        cursor: pointer;
        padding: 2.5rem;
        border: 2px dashed #ced4da;
        text-align: center;
        border-radius: 12px;
        transition: 0.3s;
        background: #f8f9fa;
    }
    .multi-dropzone:hover {
        border-color: #007bff;
        background: #e9ecef;
        box-shadow: 0 0 15px rgba(0,123,255,0.1);
    }
    .multi-dropzone svg {
        width: 48px;
        height: 48px;
        margin-bottom: 1rem;
        opacity: 0.5;
        color: #007bff;
        transition: 0.3s;
    }
    .multi-dropzone:hover svg {
        opacity: 1;
        transform: translateY(-5px);
    }
    .submit-btn {
        width: 100%;
        padding: 1rem;
        font-size: 1.1rem;
        border-radius: 8px;
        margin-top: 1rem;
        background: #007bff;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .submit-btn:hover {
        background: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,123,255,0.3);
    }
    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 1px solid #ced4da;
        background: #fff;
        color: #000 !important;
        font-size: 1rem;
        transition: all 0.3s;
        box-sizing: border-box;
    }
    .form-control:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.2);
        background: #fff;
    }
    .form-control::placeholder {
        color: #6c757d;
    }
    input[type="number"] {
        color: #000 !important;
    }
    .dropzone-content p {
        color: #555 !important;
    }
    .portada-selector {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 16px;
        margin-top: 16px;
    }
    .portada-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 12px;
        font-size: 0.95rem;
    }
    .portada-options {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .portada-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.9rem;
    }
    .portada-option:hover {
        border-color: #007bff;
        background: #e9ecef;
    }
    .portada-option.selected {
        border-color: #007bff;
        background: #e3f2fd;
        box-shadow: 0 2px 8px rgba(0,123,255,0.2);
    }
    .portada-option input[type="radio"] {
        margin: 0;
    }
    .portada-option img {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        object-fit: cover;
        border: 1px solid #dee2e6;
    }
    @keyframes spin {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div class="card form-card">
    <div class="card-header">
        <h2 class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Publicar Nuevo Terreno
        </h2>
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('vendedor.terrenos.store') }}" method="POST"
              enctype="multipart/form-data" id="terrenoForm">
            @csrf

            {{-- Precio y Metros --}}
            <div class="form-row">
                <div class="form-group">
                    <label for="precio">Precio (USD) <span class="required">*</span></label>
                    <input type="number" name="precio" id="precio" class="form-control"
                           placeholder="Ej: 50000" min="0" step="0.01"
                           value="{{ old('precio') }}" required>
                </div>
                <div class="form-group">
                    <label for="metros_cuadrados">Metros Cuadrados (m²) <span class="required">*</span></label>
                    <input type="number" name="metros_cuadrados" id="metros_cuadrados"
                           class="form-control" placeholder="Ej: 300" min="0" step="0.01"
                           value="{{ old('metros_cuadrados') }}" required>
                </div>
            </div>

            {{-- Ubicación texto --}}
            <div class="form-group">
                <label for="ubicacion">Ubicación <span class="required">*</span></label>
                <input type="text" name="ubicacion" id="ubicacion" class="form-control"
                       placeholder="Ej: Zona Norte, Calle 5"
                       value="{{ old('ubicacion') }}" required>
            </div>

            {{-- Mapa selector --}}
            <div class="form-group">
                <label>
                    Ubicación en el Mapa <span class="required">*</span>
                    <small>(Arrastra el pin a la ubicación exacta del terreno)</small>
                </label>
                <div id="mapaSelector" style="height:350px; border-radius:10px; border:2px solid #ced4da; margin-bottom:8px;"></div>
                <div style="display:flex; gap:12px; margin-top:6px;">
                    <div class="form-group" style="flex:1; margin-bottom:0;">
                        <label for="latitud" style="font-size:0.85rem;">Latitud</label>
                        <input type="text" name="latitud" id="latitud" class="form-control"
                               value="{{ old('latitud') }}"
                               placeholder="Se completa al mover el pin" readonly>
                    </div>
                    <div class="form-group" style="flex:1; margin-bottom:0;">
                        <label for="longitud" style="font-size:0.85rem;">Longitud</label>
                        <input type="text" name="longitud" id="longitud" class="form-control"
                               value="{{ old('longitud') }}"
                               placeholder="Se completa al mover el pin" readonly>
                    </div>
                </div>
                <small style="color:#6c757d;">
                    💡 Haz clic en el mapa o arrastra el marcador para ajustar la ubicación exacta.
                </small>
            </div>

            {{-- Descripción --}}
            <div class="form-group">
                <label for="descripcion">
                    Descripción <span class="required">*</span>
                    <small>(Mínimo 50 caracteres)</small>
                </label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="4"
                          placeholder="Describe detalladamente las características del terreno..."
                          required>{{ old('descripcion') }}</textarea>
                <div class="char-counter"><span id="charCount">0</span> caracteres</div>
            </div>

            {{-- Imágenes --}}
            <div class="form-group">
                <label>
                    Imágenes del Terreno <span class="required">*</span>
                    <small>(Hasta 10 imágenes, JPG/PNG, máx 5MB c/u)</small>
                </label>

                <div class="dropzone multi-dropzone" id="imagesDropzone">
                    <div class="dropzone-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <p>Haga clic para seleccionar imágenes</p>
                    </div>
                </div>

                <input type="file" name="imagenes[]" id="imagenesInput"
                       accept=".jpg,.jpeg,.png" multiple style="display:none;" required>

                <div class="images-preview-grid" id="imagesPreviewGrid"></div>

                {{-- Selector de portada --}}
                <div class="portada-selector" id="portadaSelector" style="display:none; margin-top:15px;">
                    <div class="portada-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             style="width:18px;height:18px;">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Seleccionar imagen de portada
                    </div>
                    <div class="portada-options" id="portadaOptions"></div>
                    <input type="hidden" name="portada_index" id="portadaIndex" value="0">
                    <small class="form-hint" style="color:#6c757d; margin-top:8px; display:block;">
                        La imagen de portada será la principal que se mostrará en las tarjetas de presentación.
                    </small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary submit-btn" id="submitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     style="width:18px;height:18px;margin-right:8px;vertical-align:middle;">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
                Guardar y Enviar para Aprobación
            </button>
        </form>
    </div>
</div>
@endsection

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SCRIPTS — fuera del @section, correctamente en @push       --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── 1. Contador de caracteres ──────────────────────────────
    var descInput = document.getElementById('descripcion');
    var charCount = document.getElementById('charCount');

    function actualizarContador() {
        var len = descInput.value.length;
        charCount.textContent = len;
        charCount.style.color = len < 50 ? '#ffc107' : '#28a745';
    }
    descInput.addEventListener('input', actualizarContador);
    actualizarContador();

    // ── 2. Mini mapa selector de ubicación ────────────────────
    var defaultLat = -22.0186;   // Yacuiba, Tarija
    var defaultLng = -63.6774;

    var mapaSelector = L.map('mapaSelector').setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OSM &copy; CartoDB',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(mapaSelector);

    var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(mapaSelector);

    function actualizarCoordenadas(lat, lng) {
        document.getElementById('latitud').value  = lat.toFixed(8);
        document.getElementById('longitud').value = lng.toFixed(8);
    }

    // Inicializar con coordenadas por defecto
    actualizarCoordenadas(defaultLat, defaultLng);

    marker.on('dragend', function (e) {
        var pos = e.target.getLatLng();
        actualizarCoordenadas(pos.lat, pos.lng);
    });

    mapaSelector.on('click', function (e) {
        marker.setLatLng(e.latlng);
        actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
    });

    // ── 3. Subida múltiple de imágenes ────────────────────────
    var dropzone   = document.getElementById('imagesDropzone');
    var fileInput  = document.getElementById('imagenesInput');
    var previewGrid = document.getElementById('imagesPreviewGrid');

    var selectedFiles = [];

    dropzone.addEventListener('click', function () { fileInput.click(); });

    dropzone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropzone.style.borderColor = '#007bff';
        dropzone.style.background  = '#e9ecef';
    });

    dropzone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        dropzone.style.borderColor = '#ced4da';
        dropzone.style.background  = '#f8f9fa';
    });

    dropzone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropzone.style.borderColor = '#ced4da';
        dropzone.style.background  = '#f8f9fa';
        if (e.dataTransfer.files) {
            handleFiles(e.dataTransfer.files);
        }
    });

    fileInput.addEventListener('change', function (e) {
        handleFiles(e.target.files);
        // Limpiar el input para permitir volver a seleccionar los mismos archivos
        fileInput.value = '';
    });

    function handleFiles(filesList) {
        var files  = Array.from(filesList);
        var errors = [];
        var valid  = [];

        files.forEach(function (file) {
            if (file.size > 5 * 1024 * 1024) {
                errors.push('• ' + file.name + ' supera los 5MB.');
                return;
            }
            if (!file.type.match('image/(jpeg|jpg|png)')) {
                errors.push('• ' + file.name + ' no es JPG/PNG válido.');
                return;
            }
            valid.push(file);
        });

        if (errors.length > 0) {
            alert('Observaciones:\n' + errors.join('\n'));
        }

        var espacioDisponible = 10 - selectedFiles.length;
        if (valid.length > espacioDisponible) {
            alert('Límite de 10 imágenes. Solo se agregarán las primeras ' + espacioDisponible + '.');
            valid = valid.slice(0, espacioDisponible);
        }

        selectedFiles = selectedFiles.concat(valid);
        renderPreviews();
        syncFileInput();
    }

    function renderPreviews() {
        previewGrid.innerHTML = '';

        selectedFiles.forEach(function (file, index) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML =
                    '<img src="' + e.target.result + '" alt="Preview ' + (index + 1) + '">' +
                    '<button type="button" class="remove-btn" ' +
                        'onclick="window.removeFile(' + index + ')" title="Eliminar">&times;</button>';
                previewGrid.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        updatePortadaSelector();

        if (selectedFiles.length > 0) {
            fileInput.removeAttribute('required');
        } else {
            fileInput.setAttribute('required', 'required');
        }
    }

    function updatePortadaSelector() {
        var portadaSelector = document.getElementById('portadaSelector');
        var portadaOptions  = document.getElementById('portadaOptions');
        var portadaIndex    = document.getElementById('portadaIndex');

        if (selectedFiles.length === 0) {
            portadaSelector.style.display = 'none';
            portadaIndex.value = '0';
            return;
        }

        portadaSelector.style.display = 'block';
        portadaOptions.innerHTML = '';

        var currentPortada = parseInt(portadaIndex.value) || 0;
        if (currentPortada >= selectedFiles.length) {
            currentPortada = 0;
            portadaIndex.value = '0';
        }

        selectedFiles.forEach(function (file, index) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var option = document.createElement('div');
                option.className = 'portada-option' + (index === currentPortada ? ' selected' : '');
                option.setAttribute('data-index', index);
                option.onclick = function () { selectPortada(index); };
                option.innerHTML =
                    '<input type="radio" name="portada_radio" value="' + index + '" ' +
                        (index === currentPortada ? 'checked' : '') + '>' +
                    '<img src="' + e.target.result + '" alt="Imagen ' + (index + 1) + '">' +
                    '<span>Imagen ' + (index + 1) + '</span>';
                portadaOptions.appendChild(option);
            };
            reader.readAsDataURL(file);
        });
    }

    function selectPortada(index) {
        document.getElementById('portadaIndex').value = index;
        document.querySelectorAll('.portada-option').forEach(function (opt, i) {
            opt.classList.toggle('selected', i === index);
            var radio = opt.querySelector('input[type="radio"]');
            if (radio) radio.checked = (i === index);
        });
    }

    // Exponer removeFile globalmente para los onclick inline
    window.removeFile = function (index) {
        selectedFiles.splice(index, 1);
        // Ajustar portada si el índice eliminado era la portada actual
        var portadaIndex = document.getElementById('portadaIndex');
        var currentPortada = parseInt(portadaIndex.value) || 0;
        if (currentPortada >= selectedFiles.length && selectedFiles.length > 0) {
            portadaIndex.value = selectedFiles.length - 1;
        } else if (selectedFiles.length === 0) {
            portadaIndex.value = '0';
        }
        renderPreviews();
        syncFileInput();
    };

    function syncFileInput() {
        try {
            var dt = new DataTransfer();
            selectedFiles.forEach(function (file) { dt.items.add(file); });
            fileInput.files = dt.files;
        } catch (e) {
            // DataTransfer no soportado en algunos navegadores viejos
            console.warn('DataTransfer no disponible:', e);
        }
    }

    // ── 4. Envío del formulario ────────────────────────────────
    var form      = document.getElementById('terrenoForm');
    var submitBtn = document.getElementById('submitBtn');
    var enviando  = false;

    form.addEventListener('submit', function (e) {
        // Prevenir doble envío
        if (enviando) {
            e.preventDefault();
            return false;
        }

        if (selectedFiles.length === 0) {
            e.preventDefault();
            alert('Debe adjuntar al menos una imagen del terreno.');
            return false;
        }

        if (descInput.value.trim().length < 50) {
            e.preventDefault();
            alert('La descripción debe contener al menos 50 caracteres.');
            return false;
        }

        // Sincronizar archivos al input antes de enviar
        syncFileInput();

        enviando = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML =
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" ' +
                'style="width:18px;height:18px;margin-right:8px;vertical-align:middle;' +
                'animation:spin 1s linear infinite;">' +
                '<circle cx="12" cy="12" r="10"/>' +
                '<path d="M12 2v4M12 22v-4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83' +
                'M2 12h4M22 12h-4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>' +
            '</svg> Procesando...';

        return true;
    });

    // Seguridad: re-habilitar el botón si el usuario navega de vuelta
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            enviando = false;
            submitBtn.disabled = false;
            submitBtn.innerHTML =
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" ' +
                    'style="width:18px;height:18px;margin-right:8px;vertical-align:middle;">' +
                    '<path d="M5 12h14M12 5l7 7-7 7"/>' +
                '</svg> Guardar y Enviar para Aprobación';
        }
    });

});
</script>
@endpush