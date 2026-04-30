@extends('layouts.app')

@section('title', 'Editar Terreno')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="card form-card">
    <div class="card-header">
        <h2 class="card-title">Editar Terreno #{{ $terreno->id }}</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('vendedor.terrenos.update', $terreno->id) }}" method="POST" enctype="multipart/form-data"
            id="editForm">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="precio">Precio (USD) *</label>
                    <input type="number" name="precio" id="precio" class="form-control"
                        value="{{ old('precio', $terreno->precio) }}" required>
                </div>
                <div class="form-group">
                    <label for="metros_cuadrados">Metros Cuadrados *</label>
                    <input type="number" name="metros_cuadrados" id="metros_cuadrados" class="form-control"
                        value="{{ old('metros_cuadrados', $terreno->metros_cuadrados) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="ubicacion">Ubicación *</label>
                <input type="text" name="ubicacion" id="ubicacion" class="form-control"
                    value="{{ old('ubicacion', $terreno->ubicacion) }}" required>
            </div>

            <div class="form-group">
                <label>Ubicación en el Mapa <small>(Arrastra el pin para ajustar la ubicación exacta)</small></label>
                <div id="mapaSelector" style="height: 350px; border-radius: 10px; border: 2px solid #ced4da; margin-bottom: 8px;"></div>
                <div style="display:flex; gap:12px; margin-top:6px;">
                    <div class="form-group" style="flex:1; margin-bottom:0;">
                        <label style="font-size:0.85rem;">Latitud</label>
                        <input type="text" name="latitud" id="latitud" class="form-control"
                            value="{{ old('latitud', $terreno->latitud) }}" placeholder="Se completa al mover el pin" readonly>
                    </div>
                    <div class="form-group" style="flex:1; margin-bottom:0;">
                        <label style="font-size:0.85rem;">Longitud</label>
                        <input type="text" name="longitud" id="longitud" class="form-control"
                            value="{{ old('longitud', $terreno->longitud) }}" placeholder="Se completa al mover el pin" readonly>
                    </div>
                </div>
                <small style="color:#6c757d;">💡 Haz clic en el mapa o arrastra el marcador para ajustar la ubicación exacta.</small>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción * <small>(Mínimo 50 caracteres)</small></label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="4"
                    required>{{ old('descripcion', $terreno->descripcion) }}</textarea>
                <div class="char-counter"><span id="charCount">{{ strlen($terreno->descripcion) }}</span> caracteres
                </div>
            </div>

            <!-- Imágenes existentes con selector de portada -->
            @if($terreno->imagenes->count() > 0)
            <div class="form-group">
                <label>Imágenes actuales</label>
                <div class="existing-images" id="existingImages">
                    @foreach($terreno->imagenes as $img)
                    <div class="existing-img" data-id="{{ $img->id }}" data-url="{{ $img->url }}">
                        <img src="{{ $img->url }}" alt="Imagen {{ $img->orden }}">
                        <button type="button" class="remove-img" data-id="{{ $img->id }}" title="Eliminar">✖</button>
                        <div class="portada-radio">
                            <input type="radio" name="portada_existente" value="{{ $img->id }}"
                                {{ $terreno->portada_id == $img->id ? 'checked' : '' }}>
                            <label>Portada</label>
                        </div>
                    </div>
                    @endforeach
                </div>
                <small>Selecciona cuál imagen será la portada. También puedes eliminar imágenes.</small>
            </div>
            @endif

            <!-- Subir nuevas imágenes -->
            <div class="form-group">
                <label>Agregar más imágenes <small>(Hasta 10 en total, JPG/PNG, máx 5MB c/u)</small></label>
                <div class="dropzone multi-dropzone" id="imagesDropzone">
                    <div class="dropzone-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <polyline points="21,15 16,10 5,21" />
                        </svg>
                        <p>Haga clic para seleccionar imágenes</p>
                    </div>
                </div>
                <input type="file" name="imagenes[]" id="imagenesInput" accept=".jpg,.jpeg,.png" multiple
                    style="display:none;">
                <div class="images-preview-grid" id="imagesPreviewGrid"></div>
            </div>

            <!-- Selector de portada para nuevas imágenes -->
            <div class="portada-selector" id="portadaSelector" style="display:none; margin-top: 15px;">
                <label class="portada-label">Seleccionar imagen de portada (nuevas)</label>
                <div class="portada-options" id="portadaOptions"></div>
                <input type="hidden" name="portada_index" id="portadaIndex" value="0">
            </div>

            <button type="submit" class="btn btn-primary submit-btn">Actualizar Terreno</button>
        </form>
    </div>
</div>

<style>
    .form-card {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-row {
        display: flex;
        gap: 1.5rem;
    }

    .form-row .form-group {
        flex: 1;
    }

    .form-control {
        background: white;
        color: black;
        border: 1px solid #ccc;
        padding: 0.75rem;
        border-radius: 8px;
        width: 100%;
    }

    .existing-images {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 10px;
    }

    .existing-img {
        position: relative;
        width: 120px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #ddd;
        background: #f8f9fa;
    }

    .existing-img img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        display: block;
    }

    .remove-img {
        position: absolute;
        top: 4px;
        right: 4px;
        background: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        font-size: 14px;
        line-height: 1;
        z-index: 2;
    }

    .portada-radio {
        padding: 6px;
        text-align: center;
        background: #f1f1f1;
        font-size: 0.8rem;
    }

    .portada-radio input {
        margin-right: 4px;
    }

    .multi-dropzone {
        cursor: pointer;
        padding: 2rem;
        border: 2px dashed #ccc;
        text-align: center;
        border-radius: 12px;
        background: #f9f9f9;
        margin-top: 10px;
    }

    .images-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 10px;
        margin-top: 15px;
    }

    .preview-item {
        position: relative;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #ddd;
    }

    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .remove-btn {
        position: absolute;
        top: 2px;
        right: 2px;
        background: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .portada-selector {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 16px;
        margin-top: 16px;
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
    }

    .portada-option.selected {
        border-color: #007bff;
        background: #e3f2fd;
    }

    .portada-option img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
    }

    .submit-btn {
        margin-top: 20px;
        width: 100%;
        padding: 12px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: bold;
    }
</style>

<script>
    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endpush
    document.addEventListener('DOMContentLoaded', () => {
        // Contador de caracteres
        const descInput = document.getElementById('descripcion');
        const charCount = document.getElementById('charCount');
        if (descInput && charCount) {
            descInput.addEventListener('input', () => charCount.textContent = descInput.value.length);
        }

        // Eliminar imágenes existentes con fetch a la ruta correcta
        const removeBtns = document.querySelectorAll('.remove-img');
        removeBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const imgId = this.getAttribute('data-id');
                if (confirm('¿Eliminar esta imagen permanentemente?')) {
                    fetch(`/vendedor/terreno-imagen/${imgId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(response => {
                        if (response.ok) {
                            this.closest('.existing-img').remove();
                            alert('Imagen eliminada');
                            // Recargar para actualizar portadas (opcional)
                            location.reload();
                        } else {
                            alert('Error al eliminar imagen. Código: ' + response.status);
                        }
                    }).catch(err => {
                        alert('Error de red: ' + err);
                    });
                }
            });
        });

        // Subir nuevas imágenes
        const dropzone = document.getElementById('imagesDropzone');
        const fileInput = document.getElementById('imagenesInput');
        const previewGrid = document.getElementById('imagesPreviewGrid');
        let selectedFiles = [];

        if (dropzone && fileInput) {
            dropzone.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', (e) => handleFiles(e.target.files));
        }

        function handleFiles(filesList) {
            const files = Array.from(filesList);
            let errors = [];
            files.forEach(file => {
                if (file.size > 5 * 1024 * 1024) {
                    errors.push(`${file.name} excede 5MB`);
                } else if (!file.type.match('image/(jpeg|jpg|png)')) {
                    errors.push(`${file.name} no es JPG/PNG`);
                } else {
                    selectedFiles.push(file);
                }
            });
            if (errors.length) alert(errors.join('\n'));
            renderPreviews();
            updateFileInput();
        }

        function renderPreviews() {
            if (!previewGrid) return;
            previewGrid.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'preview-item';
                    div.innerHTML = `<img src="${e.target.result}"><button type="button" class="remove-btn" onclick="removeNewFile(${index})">✖</button>`;
                    previewGrid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
            updatePortadaSelector();
        }

        function updatePortadaSelector() {
            const selectorDiv = document.getElementById('portadaSelector');
            const optionsDiv = document.getElementById('portadaOptions');
            if (selectedFiles.length > 0) {
                selectorDiv.style.display = 'block';
                optionsDiv.innerHTML = '';
                selectedFiles.forEach((file, idx) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const opt = document.createElement('div');
                        opt.className = `portada-option ${idx === 0 ? 'selected' : ''}`;
                        opt.innerHTML = `<img src="${e.target.result}"><span>Nueva ${idx+1}</span>`;
                        opt.onclick = () => {
                            document.querySelectorAll('#portadaOptions .portada-option').forEach(o => o.classList.remove('selected'));
                            opt.classList.add('selected');
                            document.getElementById('portadaIndex').value = idx;
                        };
                        optionsDiv.appendChild(opt);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                selectorDiv.style.display = 'none';
            }
        }

        window.removeNewFile = (index) => {
            selectedFiles.splice(index, 1);
            renderPreviews();
            updateFileInput();
        };

        function updateFileInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            fileInput.files = dt.files;
        }

        // Validación antes de enviar
        const form = document.getElementById('editForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                if (descInput && descInput.value.length < 50) {
                    e.preventDefault();
                    alert('La descripción debe tener al menos 50 caracteres.');
                }
            });
        }
    });

    // ── Mini mapa selector de ubicación ──
        var savedLat = {{ $terreno->latitud ?? -34.6037 }};
        var savedLng = {{ $terreno->longitud ?? -58.3816 }};

        var mapaSelector = L.map('mapaSelector').setView([savedLat, savedLng], 15);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OSM &copy; CartoDB',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(mapaSelector);

        var marker = L.marker([savedLat, savedLng], { draggable: true }).addTo(mapaSelector);

        function actualizarCoordenadas(lat, lng) {
            document.getElementById('latitud').value = lat.toFixed(8);
            document.getElementById('longitud').value = lng.toFixed(8);
        }

        // Si ya tenía coordenadas guardadas, las mostramos
        @if($terreno->latitud && $terreno->longitud)
            actualizarCoordenadas(savedLat, savedLng);
        @endif

        marker.on('dragend', function(e) {
            var pos = e.target.getLatLng();
            actualizarCoordenadas(pos.lat, pos.lng);
        });

        mapaSelector.on('click', function(e) {
            marker.setLatLng(e.latlng);
            actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
        });
</script>
@endsection