{{-- ===================== ESTILOS ===================== --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap');

/* Fondo oscuro global */
body, html {
    background: #0b1120 !important;
    min-height: 100vh;
}

/* Neutralizar contenedores del layout */
.container, .content-wrapper, main, .main-content,
.page-content, .wrapper, .app-content, .inner-wrapper {
    background: transparent !important;
    box-shadow: none !important;
}

/* Fondo con gradientes decorativos */
.minuta-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    font-family: 'DM Sans', sans-serif;
    position: relative;
}

.minuta-wrapper::before {
    content: '';
    position: fixed;
    inset: 0;
    background:
        radial-gradient(ellipse 60% 50% at 15% 50%, rgba(30,80,160,0.25) 0%, transparent 70%),
        radial-gradient(ellipse 40% 60% at 85% 20%, rgba(20,50,120,0.2) 0%, transparent 70%);
    pointer-events: none;
    z-index: 0;
}

.minuta-card {
    background: rgba(255,255,255,0.04);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px;
    width: 100%;
    max-width: 660px;
    padding: 2.5rem;
    box-shadow: 0 25px 60px rgba(0,0,0,0.5);
    animation: fadeUp 0.5s ease both;
    position: relative;
    z-index: 1;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ---- Marca ---- */
.minuta-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
}

.minuta-brand-icon {
    width: 44px; height: 44px;
    background: linear-gradient(135deg, #1e50a0, #2563eb);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    box-shadow: 0 4px 15px rgba(37,99,235,0.4);
    flex-shrink: 0;
}

.minuta-brand-name {
    font-family: 'Syne', sans-serif;
    font-weight: 700;
    font-size: 1.3rem;
    color: #fff;
    letter-spacing: -0.02em;
}

.minuta-badge {
    background: rgba(37,99,235,0.2);
    border: 1px solid rgba(37,99,235,0.4);
    color: #60a5fa;
    font-size: 0.6rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 3px 10px;
    border-radius: 20px;
}

.minuta-card h2 {
    font-family: 'Syne', sans-serif;
    font-weight: 600;
    font-size: 1.5rem;
    color: #fff !important;
    margin: 1.5rem 0 0.25rem !important;
    letter-spacing: -0.02em;
}

.minuta-subtitle {
    color: rgba(255,255,255,0.4);
    font-size: 0.85rem;
    margin-bottom: 2rem;
}

.minuta-divider {
    height: 1px;
    background: rgba(255,255,255,0.07);
    margin: 1.5rem 0;
    border: none;
}

/* ---- Alertas ---- */
.minuta-alert-success {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: rgba(16,185,129,0.1);
    border: 1px solid rgba(16,185,129,0.3);
    border-radius: 10px;
    color: #6ee7b7 !important;
    font-size: 0.875rem;
    padding: 0.75rem 1rem;
    margin-bottom: 1.5rem;
}

.minuta-alert-error {
    background: rgba(239,68,68,0.1);
    border: 1px solid rgba(239,68,68,0.3);
    border-radius: 10px;
    color: #fca5a5 !important;
    font-size: 0.875rem;
    padding: 0.75rem 1rem;
    margin-bottom: 1.5rem;
}

.minuta-alert-error ul { list-style: none; padding: 0; margin: 0; }
.minuta-alert-error li { padding: 2px 0; }
.minuta-alert-error li::before { content: '• '; }

/* ---- Layout del form ---- */
.minuta-form {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}

.minuta-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 520px) {
    .minuta-grid-2 { grid-template-columns: 1fr; }
    .minuta-card { padding: 1.5rem; }
}

/* ---- Grupos de campo ---- */
.minuta-form .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.minuta-form label {
    font-size: 0.75rem;
    font-weight: 500;
    color: rgba(255,255,255,0.5) !important;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    font-family: 'DM Sans', sans-serif;
}

/* ---- Inputs y Selects ---- */
.minuta-form select,
.minuta-form input[type="number"],
.minuta-form input[type="date"] {
    width: 100%;
    background: rgba(255,255,255,0.06) !important;
    border: 1px solid rgba(255,255,255,0.12) !important;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    color: #fff !important;
    color-scheme: dark;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem;
    transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    appearance: none;
    -webkit-appearance: none;
    outline: none;
    box-shadow: none;
}

.minuta-form select:focus,
.minuta-form input[type="number"]:focus,
.minuta-form input[type="date"]:focus {
    border-color: rgba(37,99,235,0.7) !important;
    background: rgba(255,255,255,0.09) !important;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.18) !important;
}

.minuta-form select option {
    background: #1a2540;
    color: #fff;
}

.minuta-form input[type="number"]::placeholder {
    color: rgba(255,255,255,0.25);
}

/* Flecha custom para select */
.minuta-select-wrap {
    position: relative;
}

.minuta-select-wrap::after {
    content: '';
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 0; height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 6px solid rgba(255,255,255,0.4);
    pointer-events: none;
}

/* ---- File upload ---- */
.minuta-file-label {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    background: rgba(255,255,255,0.04);
    border: 1.5px dashed rgba(255,255,255,0.14);
    border-radius: 12px;
    padding: 1rem 1.2rem;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    color: rgba(255,255,255,0.4);
    font-size: 0.875rem;
    font-family: 'DM Sans', sans-serif;
    position: relative;
    overflow: hidden;
}

.minuta-file-label:hover {
    border-color: rgba(37,99,235,0.55);
    background: rgba(37,99,235,0.08);
    color: rgba(255,255,255,0.75);
}

.minuta-file-label input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
    border: none !important;
    background: none !important;
    padding: 0 !important;
}

.minuta-file-icon {
    width: 36px; height: 36px;
    background: rgba(37,99,235,0.2);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
}

.minuta-file-text strong {
    display: block;
    color: rgba(255,255,255,0.65);
    font-size: 0.875rem;
    font-weight: 500;
}

.minuta-file-text span {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.3);
}

/* ---- Botón guardar ---- */
.btn-guardar-minuta {
    width: 100%;
    padding: 0.9rem 1rem;
    background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    border: none;
    border-radius: 12px;
    color: #fff !important;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
    box-shadow: 0 4px 20px rgba(37,99,235,0.45);
    letter-spacing: 0.01em;
    margin-top: 0.25rem;
    text-decoration: none;
}

.btn-guardar-minuta:hover {
    filter: brightness(1.12);
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(37,99,235,0.55);
}

.btn-guardar-minuta:active {
    transform: translateY(0);
    filter: brightness(0.97);
}

/* ---- Nota de seguridad ---- */
.minuta-secure-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    color: rgba(255,255,255,0.22);
    font-size: 0.73rem;
    margin-top: 0.75rem;
    font-family: 'DM Sans', sans-serif;
}
</style>

{{-- ===================== HTML ===================== --}}
<div class="minuta-wrapper">
    <div class="minuta-card">

        {{-- Marca --}}
        <div class="minuta-brand">
            <div class="minuta-brand-icon">🏔</div>
            <span class="minuta-brand-name">TerrenoSur</span>
            <span class="minuta-badge">MÓDULO IN-A01</span>
        </div>

        <h2>Registro de Minuta de Compraventa</h2>
        <p class="minuta-subtitle">Complete los campos para registrar la minuta</p>

        {{-- Alerta de éxito --}}
        @if(session('success'))
            <div class="minuta-alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="minuta-alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <hr class="minuta-divider">

        <form action="{{ route('admin.minutas.store') }}" method="POST"
              enctype="multipart/form-data" class="minuta-form">
            @csrf

            {{-- Terreno --}}
            <div class="form-group">
                <label>Terreno</label>
                <div class="minuta-select-wrap">
                    <select name="terreno_id" required>
                        <option value="">Seleccionar terreno</option>
                        @foreach($terrenos as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->id }} – {{ $t->ubicacion ?? 'Sin datos' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Comprador / Vendedor --}}
            <div class="minuta-grid-2">
                <div class="form-group">
                    <label>Comprador</label>
                    <div class="minuta-select-wrap">
                        <select name="comprador_id" required>
                            <option value="">Seleccionar</option>
                            @foreach($compradores as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Vendedor</label>
                    <div class="minuta-select-wrap">
                        <select name="vendedor_id" required>
                            <option value="">Seleccionar</option>
                            @foreach($vendedores as $v)
                                <option value="{{ $v->id }}">{{ $v->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Monto / Fecha --}}
            <div class="minuta-grid-2">
                <div class="form-group">
                    <label>Monto</label>
                    <input type="number" step="0.01" name="monto"
                           required placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" name="fecha" required>
                </div>
            </div>

            {{-- Archivo --}}
            <div class="form-group">
                <label>Archivo (PDF o imagen)</label>
                <label class="minuta-file-label">
                    <div class="minuta-file-icon">📄</div>
                    <div class="minuta-file-text">
                        <strong>Haga clic o arrastre el archivo aquí</strong>
                        <span>PDF, JPG, PNG — máx. 10 MB</span>
                    </div>
                    <input type="file" name="archivo" accept=".pdf,image/*">
                </label>
            </div>

            <hr class="minuta-divider" style="margin: 0">

            {{-- Botón --}}
            <button type="submit" class="btn-guardar-minuta">
                ✓ &nbsp;Guardar Minuta
            </button>

            <div class="minuta-secure-note">
                🔒 Sistema seguro con autenticación CSRF
            </div>

        </form>
    </div>
</div>
