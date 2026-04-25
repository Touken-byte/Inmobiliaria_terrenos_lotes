@extends('layouts.app')

@section('title', 'Publicar Terreno')

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
            <form action="{{ route('vendedor.terrenos.store') }}" method="POST" enctype="multipart/form-data"
                id="terrenoForm">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="precio">Precio (USD) <span class="required">*</span></label>
                        <input type="number" name="precio" id="precio" class="form-control" placeholder="Ej: 50000" min="0"
                            step="0.01" value="{{ old('precio') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="metros_cuadrados">Metros Cuadrados (m²) <span class="required">*</span></label>
                        <input type="number" name="metros_cuadrados" id="metros_cuadrados" class="form-control"
                            placeholder="Ej: 300" min="0" step="0.01" value="{{ old('metros_cuadrados') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ubicacion">Ubicación <span class="required">*</span></label>
                    <input type="text" name="ubicacion" id="ubicacion" class="form-control"
                        placeholder="Ej: Zona Norte, Calle 5" value="{{ old('ubicacion') }}" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción <span class="required">*</span> <small>(Mínimo 50
                            caracteres)</small></label>
                    <textarea name="descripcion" id="descripcion" class="form-control" rows="4"
                        placeholder="Describe detalladamente las características del terreno..."
                        required>{{ old('descripcion') }}</textarea>
                    <div class="char-counter"><span id="charCount">0</span> caracteres</div>
                </div>

                <div class="form-group">
                    <label>Imágenes del Terreno <span class="required">*</span> <small>(Hasta 10 imágenes, JPG/PNG, máx 5MB
                            c/u)</small></label>
                    <div class="dropzone multi-dropzone" id="imagesDropzone">
                        <div class="dropzone-content">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <polyline points="21 15 16 10 5 21" />
                            </svg>
                            <p>Haga clic para seleccionar imágenes</p>
                        </div>
                    </div>
                    <input type="file" name="imagenes[]" id="imagenesInput" accept=".jpg,.jpeg,.png" multiple
                        style="display:none;" required>

                    <div class="images-preview-grid" id="imagesPreviewGrid"></div>

                    <!-- Selector de imagen de portada -->
                    <div class="portada-selector" id="portadaSelector" style="display:none; margin-top: 15px;">
                        <label class="portada-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                style="width:18px;height:18px;">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Seleccionar imagen de portada
                        </label>
                        <div class="portada-options" id="portadaOptions">
                            <!-- Las opciones se generan dinámicamente -->
                        </div>
                        <input type="hidden" name="portada_index" id="portadaIndex" value="0">
                        <small class="form-hint">La imagen de portada será la principal que se mostrará en las tarjetas de
                            presentación.</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary submit-btn" id="submitBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="width:18px;height:18px;margin-right:8px;vertical-align:middle;">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                    Guardar y Enviar para Aprobación
                </button>
            </form>
        </div>
    </div>

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

        /* Texto negro */
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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
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
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.1);
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
        }

        .submit-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Forzar color de texto negro en inputs y selects */
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #ced4da;
            background: #fff;
            color: #000 !important;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
            background: #fff;
        }

        .form-control::placeholder {
            color: #6c757d;
        }

        /* Para los spinners (flechas) de number input */
        input[type="number"] {
            color: #000 !important;
        }

        /* Ajuste para textos dentro del dropzone */
        .dropzone-content p,
        .dropzone-subtext,
        .dropzone-hint {
            color: #000 !important;
        }

        /* Selector de imagen de portada */
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
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
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
        /* Forzar texto blanco en formulario de publicación para vendedor */
        body.theme-vendedor label,
        body.theme-vendedor .form-label,
        body.theme-vendedor .char-counter,
        body.theme-vendedor .dropzone-content p,
        body.theme-vendedor .dropzone-subtext,
        body.theme-vendedor .dropzone-hint,
        body.theme-vendedor .form-hint,
        body.theme-vendedor .portada-label,
        body.theme-vendedor .portada-option span {
            color: #ffffff !important;
        }
        body.theme-vendedor .form-control {
            color: #ffffff !important;
            background-color: rgba(0,0,0,0.4) !important;
            border-color: rgba(255,255,255,0.2) !important;
        }
        body.theme-vendedor .form-control::placeholder {
            color: #cccccc !important;
        }
        body.theme-vendedor .portada-option {
            background: rgba(0,0,0,0.3);
            border-color: rgba(255,255,255,0.2);
        }
        body.theme-vendedor .portada-option.selected {
            background: rgba(139,92,246,0.5);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const descInput = document.getElementById('descripcion');
            const charCount = document.getElementById('charCount');
            const form = document.getElementById('terrenoForm');
            const submitBtn = document.getElementById('submitBtn');

            // Character Counter
            const updateCount = () => {
                const len = descInput.value.length;
                charCount.textContent = len;
                if (len < 50) charCount.style.color = '#ffc107';
                else charCount.style.color = '#28a745';
            };
            descInput.addEventListener('input', updateCount);
            updateCount();

            // Multiple Image Upload Logic
            const dropzone = document.getElementById('imagesDropzone');
            const fileInput = document.getElementById('imagenesInput');
            const previewGrid = document.getElementById('imagesPreviewGrid');

            let selectedFiles = [];

            dropzone.addEventListener('click', () => fileInput.click());

            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.style.borderColor = '#007bff';
                dropzone.style.background = '#e9ecef';
            });

            dropzone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dropzone.style.borderColor = '#ced4da';
                dropzone.style.background = '#f8f9fa';
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.style.borderColor = '#ced4da';
                dropzone.style.background = '#f8f9fa';
                if (e.dataTransfer.files) {
                    handleFiles(e.dataTransfer.files);
                }
            });

            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
            });

            function handleFiles(filesList) {
                const files = Array.from(filesList);

                let filesValid = [];
                let errors = [];

                files.forEach(file => {
                    if (file.size > 5 * 1024 * 1024) {
                        errors.push(`El archivo ${file.name} supera los 5MB.`);
                        return;
                    }
                    if (!file.type.match('image/(jpeg|jpg|png)')) {
                        errors.push(`El archivo ${file.name} no es una imagen válida (JPG/PNG).`);
                        return;
                    }
                    filesValid.push(file);
                });

                if (errors.length > 0) {
                    alert("Observaciones:\n" + errors.join("\n"));
                }

                if (selectedFiles.length + filesValid.length > 10) {
                    alert(`Límite excedido. Solo puede subir hasta 10 imágenes.`);
                    const availableSpaces = 10 - selectedFiles.length;
                    selectedFiles = [...selectedFiles, ...filesValid.slice(0, availableSpaces)];
                } else {
                    selectedFiles = [...selectedFiles, ...filesValid];
                }

                renderPreviews();
                updateFileInput();
            }

            function renderPreviews() {
                previewGrid.innerHTML = '';
                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const div = document.createElement('div');
                        div.className = 'preview-item';
                        div.innerHTML = `
                                    <img src="${e.target.result}" alt="Preview">
                                    <button type="button" class="remove-btn" onclick="window.removeFile(${index})" title="Eliminar">&times;</button>
                                `;
                        previewGrid.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });

                // Actualizar selector de portada
                updatePortadaSelector();

                if (selectedFiles.length > 0) fileInput.removeAttribute('required');
                else fileInput.setAttribute('required', 'required');
            }

            function updatePortadaSelector() {
                const portadaSelector = document.getElementById('portadaSelector');
                const portadaOptions = document.getElementById('portadaOptions');
                const portadaIndex = document.getElementById('portadaIndex');

                if (selectedFiles.length > 0) {
                    portadaSelector.style.display = 'block';
                    portadaOptions.innerHTML = '';

                    selectedFiles.forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const option = document.createElement('div');
                            option.className = `portada-option ${index === 0 ? 'selected' : ''}`;
                            option.onclick = () => selectPortada(index);

                            option.innerHTML = `
                                    <input type="radio" name="portada" id="portada_${index}" value="${index}" ${index === 0 ? 'checked' : ''}>
                                    <label for="portada_${index}" style="margin:0; cursor:pointer; display:flex; align-items:center; gap:8px;">
                                        <img src="${e.target.result}" alt="Miniatura ${index + 1}">
                                        <span>Imagen ${index + 1}</span>
                                    </label>
                                `;
                            portadaOptions.appendChild(option);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    portadaSelector.style.display = 'none';
                    portadaIndex.value = '0';
                }
            }

            function selectPortada(index) {
                const portadaOptions = document.querySelectorAll('.portada-option');
                const portadaIndex = document.getElementById('portadaIndex');

                portadaOptions.forEach(option => option.classList.remove('selected'));
                portadaOptions[index].classList.add('selected');
                portadaIndex.value = index;
            }

            window.removeFile = function (index) {
                selectedFiles.splice(index, 1);
                renderPreviews();
                updateFileInput();
            };

            function updateFileInput() {
                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
            }

            // Manejo del envío del formulario - evitar doble envío y mostrar loading
            form.addEventListener('submit', (e) => {
                if (selectedFiles.length === 0) {
                    e.preventDefault();
                    alert('Debe adjuntar al menos una imagen del terreno.');
                    return false;
                }
                if (descInput.value.length < 50) {
                    e.preventDefault();
                    alert('La descripción debe contener al menos 50 caracteres.');
                    return false;
                }
                // Deshabilitar botón y cambiar texto
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;margin-right:8px;vertical-align:middle;animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10"/><path d="M12 2v4M12 22v-4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M22 12h-4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Procesando...';
                // El formulario se enviará normalmente
                return true;
            });
        });
    </script>

    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        // Forzar que el selector aparezca después de cargar
        window.addEventListener('load', function() {
            console.log('Verificando portadaSelector...');
            var selector = document.getElementById('portadaSelector');
            if (selector) {
                console.log('Selector encontrado, display:', selector.style.display);
                if (selector.style.display === 'none' && document.getElementById('imagesPreviewGrid').children.length > 0) {
                    selector.style.display = 'block';
                    console.log('Selector forzado a block');
                }
            } else {
                console.log('No se encontró #portadaSelector');
            }
        });
    </script>
@endsection