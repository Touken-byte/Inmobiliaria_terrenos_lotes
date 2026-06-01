<style>
    .form-wrap { max-width: 640px; margin: 2rem auto; padding: 0 1.5rem 4rem; }
    .form-wrap h1 { font-size: 1.6rem; font-weight: 700; color: #eef2fc; margin-bottom: .35rem; }
    .form-wrap p  { font-size: .85rem; color: #8fa3cc; margin-bottom: 2rem; }

    .form-card {
        background: #0f1830;
        border: 1px solid rgba(120,160,255,0.10);
        border-radius: 20px;
        padding: 2rem;
    }

    .form-group { margin-bottom: 1.5rem; }
    .form-group label {
        display: block;
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: #3d5480;
        margin-bottom: .6rem;
    }
    .form-group input[type="text"],
    .form-group textarea {
        width: 100%;
        padding: .75rem 1rem;
        background: #0c1326;
        border: 1px solid rgba(120,160,255,0.12);
        border-radius: 12px;
        color: #eef2fc;
        font-size: .9rem;
        font-family: 'Outfit', system-ui, sans-serif;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        box-sizing: border-box;
    }
    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #c9a84c;
        box-shadow: 0 0 0 3px rgba(201,168,76,0.12);
    }
    .form-group textarea { resize: vertical; min-height: 90px; }

    .color-row { display: flex; align-items: center; gap: 1rem; }
    .color-preview {
        width: 44px; height: 44px;
        border-radius: 10px;
        border: 2px solid rgba(120,160,255,0.15);
        flex-shrink: 0;
        transition: background .2s;
    }
    .color-text {
        flex: 1;
        padding: .75rem 1rem;
        background: #0c1326;
        border: 1px solid rgba(120,160,255,0.12);
        border-radius: 12px;
        color: #eef2fc;
        font-size: .9rem;
        font-family: monospace;
        outline: none;
        transition: border-color .2s;
    }
    .color-text:focus { border-color: #c9a84c; }

    .toggle-row { display: flex; align-items: center; gap: 1rem; }
    .toggle-label { font-size: .85rem; color: #8fa3cc; }
    .toggle-switch { position: relative; width: 48px; height: 26px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; inset: 0;
        background: #0c1326;
        border: 1px solid rgba(120,160,255,0.15);
        border-radius: 100px;
        cursor: pointer;
        transition: background .25s;
    }
    .toggle-slider::before {
        content: '';
        position: absolute;
        width: 18px; height: 18px;
        left: 3px; top: 50%;
        transform: translateY(-50%);
        background: #3d5480;
        border-radius: 50%;
        transition: transform .25s, background .25s;
    }
    .toggle-switch input:checked + .toggle-slider { background: rgba(29,186,126,0.2); border-color: rgba(29,186,126,0.4); }
    .toggle-switch input:checked + .toggle-slider::before { transform: translate(22px, -50%); background: #1dba7e; }

    .form-error { font-size: .75rem; color: #ef4444; margin-top: .4rem; }

    .form-actions { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; }
    .btn-cancel {
        padding: .65rem 1.4rem;
        background: transparent;
        border: 1px solid rgba(120,160,255,0.15);
        border-radius: 12px;
        color: #8fa3cc;
        font-size: .82rem;
        font-weight: 600;
        text-decoration: none;
        transition: border-color .2s;
    }
    .btn-cancel:hover { border-color: rgba(120,160,255,0.35); color: #eef2fc; }
    .btn-save {
        padding: .65rem 1.75rem;
        background: linear-gradient(135deg, #c9a84c, #a8782a);
        border: none;
        border-radius: 12px;
        color: #1a0f00;
        font-size: .82rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        cursor: pointer;
        transition: filter .2s;
    }
    .btn-save:hover { filter: brightness(1.1); }
</style>

<div class="form-wrap">
    <h1>{{ $titulo }}</h1>
    <p>{{ $subtitulo }}</p>

    <div class="form-card">
        <form action="{{ $action }}" method="POST">
            @csrf
            @if($method === 'PUT') @method('PUT') @endif

            {{-- Nombre --}}
            <div class="form-group">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre"
                       value="{{ old('nombre', $cat?->nombre) }}"
                       placeholder="Ej: Urbano, Comercial, Residencial…"
                       maxlength="50" required>
                @error('nombre')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descripción --}}
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion"
                          placeholder="Descripción breve de la categoría (opcional)…"
                          maxlength="255">{{ old('descripcion', $cat?->descripcion) }}</textarea>
                @error('descripcion')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Color --}}
            <div class="form-group">
                <label>Color identificador *</label>
                <div class="color-row">
                    <div class="color-preview" id="colorPreview"
                         style="background: {{ old('color', $cat?->color ?? '#3d7ef5') }}"></div>
                    <input type="text" class="color-text" id="colorInput" name="color"
                           value="{{ old('color', $cat?->color ?? '#3d7ef5') }}"
                           placeholder="#3d7ef5" maxlength="7" required>
                </div>
                @error('color')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipo de Propiedad --}}
            <div class="form-group">
                <label for="tipo_propiedad">Tipo de Propiedad *</label>
                <select id="tipo_propiedad" name="tipo_propiedad" required style="width:100%; padding:.75rem 1rem; background:#0c1326; border:1px solid rgba(120,160,255,0.12); border-radius:12px; color:#eef2fc; font-size:.9rem; font-family:'Outfit', system-ui, sans-serif; outline:none; transition:border-color .2s, box-shadow .2s; box-sizing:border-box;">
                    <option value="todos" {{ old('tipo_propiedad', $cat?->tipo_propiedad) === 'todos' ? 'selected' : '' }}>Aplica a Todos</option>
                    <option value="terreno" {{ old('tipo_propiedad', $cat?->tipo_propiedad) === 'terreno' ? 'selected' : '' }}>Aplica a Terrenos</option>
                    <option value="lote" {{ old('tipo_propiedad', $cat?->tipo_propiedad) === 'lote' ? 'selected' : '' }}>Aplica a Lotes</option>
                    <option value="alquiler" {{ old('tipo_propiedad', $cat?->tipo_propiedad) === 'alquiler' ? 'selected' : '' }}>Aplica a Alquileres</option>
                </select>
                @error('tipo_propiedad')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Activa --}}
            <div class="form-group">
                <label>Estado</label>
                <div class="toggle-row">
                    <label class="toggle-switch">
                        <input type="checkbox" name="activa" value="1"
                               {{ old('activa', $cat?->activa ?? true) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Categoría activa (visible en filtros del catálogo)</span>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.categorias.index') }}" class="btn-cancel">Cancelar</a>
                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ $cat ? 'Guardar cambios' : 'Crear categoría' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const colorInput   = document.getElementById('colorInput');
    const colorPreview = document.getElementById('colorPreview');

    colorInput.addEventListener('input', () => {
        const val = colorInput.value;
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
            colorPreview.style.background = val;
        }
    });
</script>