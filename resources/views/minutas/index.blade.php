{{-- ===================== ESTILOS ===================== --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap');

body, html {
    background: #0b1120 !important;
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
}

.container, .content-wrapper, main, .main-content,
.page-content, .wrapper, .app-content, .inner-wrapper {
    background: transparent !important;
    box-shadow: none !important;
}

.minutas-wrapper {
    min-height: 100vh;
    padding: 2.5rem 2rem;
    position: relative;
    font-family: 'DM Sans', sans-serif;
}

.minutas-wrapper::before {
    content: '';
    position: fixed;
    inset: 0;
    background:
        radial-gradient(ellipse 60% 50% at 10% 40%, rgba(30,80,160,0.22) 0%, transparent 70%),
        radial-gradient(ellipse 40% 50% at 90% 10%, rgba(20,50,120,0.18) 0%, transparent 70%);
    pointer-events: none;
    z-index: 0;
}

.minutas-inner {
    position: relative;
    z-index: 1;
    max-width: 1100px;
    margin: 0 auto;
}

/* ---- Header ---- */
.minutas-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
}

.minutas-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.minutas-brand-icon {
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

.minutas-brand-name {
    font-family: 'Syne', sans-serif;
    font-weight: 700;
    font-size: 1.3rem;
    color: #fff;
    letter-spacing: -0.02em;
}

.minutas-badge {
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

.minutas-title-block h2 {
    font-family: 'Syne', sans-serif;
    font-weight: 600;
    font-size: 1.6rem;
    color: #fff !important;
    margin: 0 0 0.2rem !important;
    letter-spacing: -0.02em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.minutas-title-block p {
    color: rgba(255,255,255,0.38);
    font-size: 0.85rem;
    margin: 0;
}

/* ---- Botón nueva minuta ---- */
.btn-nueva-minuta {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.7rem 1.3rem;
    background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    border-radius: 10px;
    color: #fff !important;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none !important;
    transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
    box-shadow: 0 4px 18px rgba(37,99,235,0.4);
    white-space: nowrap;
}

.btn-nueva-minuta:hover {
    filter: brightness(1.12);
    transform: translateY(-2px);
    box-shadow: 0 8px 26px rgba(37,99,235,0.5);
}

/* ---- Alerta éxito ---- */
.minutas-alert-success {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: rgba(16,185,129,0.1);
    border: 1px solid rgba(16,185,129,0.28);
    border-radius: 10px;
    color: #6ee7b7 !important;
    font-size: 0.875rem;
    padding: 0.75rem 1rem;
    margin-bottom: 1.5rem;
}

/* ---- Empty state ---- */
.minutas-empty {
    background: rgba(255,255,255,0.03);
    border: 1px dashed rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 3.5rem 2rem;
    text-align: center;
    color: rgba(255,255,255,0.3);
    margin-top: 1rem;
}

.minutas-empty-icon {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
    opacity: 0.5;
}

.minutas-empty p {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.35) !important;
    margin: 0;
}

/* ---- Tabla contenedor ---- */
.minutas-table-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(0,0,0,0.35);
    animation: fadeUp 0.45s ease both;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ---- Tabla ---- */
.minutas-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.minutas-table thead tr {
    background: rgba(37,99,235,0.15);
    border-bottom: 1px solid rgba(37,99,235,0.25);
}

.minutas-table th {
    padding: 1rem 1.1rem;
    color: rgba(255,255,255,0.5) !important;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    text-align: left;
    white-space: nowrap;
    background: transparent !important;
}

.minutas-table td {
    padding: 0.9rem 1.1rem;
    color: rgba(255,255,255,0.8) !important;
    border-bottom: 1px solid rgba(255,255,255,0.05) !important;
    vertical-align: middle;
}

.minutas-table tbody tr {
    transition: background 0.15s;
}

.minutas-table tbody tr:hover {
    background: rgba(255,255,255,0.04) !important;
}

.minutas-table tbody tr:last-child td {
    border-bottom: none !important;
}

/* ID pill */
.id-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px; height: 28px;
    background: rgba(37,99,235,0.2);
    border: 1px solid rgba(37,99,235,0.3);
    border-radius: 8px;
    color: #60a5fa !important;
    font-size: 0.78rem;
    font-weight: 700;
}

/* Monto */
.monto-cell {
    font-family: 'Syne', sans-serif;
    font-weight: 600;
    color: #fff !important;
}

.monto-currency {
    font-size: 0.7rem;
    color: rgba(255,255,255,0.35);
    margin-right: 2px;
}

/* Fecha */
.fecha-cell {
    color: rgba(255,255,255,0.5) !important;
    font-size: 0.82rem;
}

/* Badges */
.badge-ver {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.75rem;
    background: rgba(16,185,129,0.15);
    border: 1px solid rgba(16,185,129,0.3);
    border-radius: 8px;
    color: #6ee7b7 !important;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none !important;
    transition: background 0.15s, border-color 0.15s;
}

.badge-ver:hover {
    background: rgba(16,185,129,0.25);
    border-color: rgba(16,185,129,0.5);
}

.badge-sin-archivo {
    display: inline-block;
    padding: 0.3rem 0.75rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    color: rgba(255,255,255,0.3) !important;
    font-size: 0.75rem;
}

/* Contador */
.minutas-count {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 8px;
    padding: 0.3rem 0.75rem;
    color: rgba(255,255,255,0.4);
    font-size: 0.75rem;
    margin-bottom: 1rem;
}

.minutas-count strong {
    color: rgba(255,255,255,0.7);
}

/* Divider */
.minutas-divider {
    height: 1px;
    background: rgba(255,255,255,0.07);
    border: none;
    margin: 1.5rem 0;
}
</style>

{{-- ===================== HTML ===================== --}}
<div class="minutas-wrapper">
    <div class="minutas-inner">

        {{-- Header --}}
        <div class="minutas-header">
            <div style="display:flex; flex-direction:column; gap:0.6rem;">
                <div class="minutas-brand">
                    <div class="minutas-brand-icon">🏔</div>
                    <span class="minutas-brand-name">TerrenoSur</span>
                    <span class="minutas-badge">MÓDULO IN-A01</span>
                </div>
                <div class="minutas-title-block">
                    <h2>📄 Lista de Minutas</h2>
                    <p>Registro de minutas de compraventa del sistema</p>
                </div>
            </div>

            <a href="{{ route('admin.minutas.create') }}" class="btn-nueva-minuta">
                ＋ &nbsp;Nueva Minuta
            </a>
        </div>

        {{-- Alerta éxito --}}
        @if(session('success'))
            <div class="minutas-alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        <hr class="minutas-divider">

        {{-- Contenido --}}
        @if($minutas->isEmpty())
            <div class="minutas-empty">
                <div class="minutas-empty-icon">📭</div>
                <p>No hay minutas registradas aún.</p>
            </div>
        @else
            <div class="minutas-count">
                Total: <strong>{{ $minutas->count() }}</strong> minuta{{ $minutas->count() !== 1 ? 's' : '' }}
            </div>

            <div class="minutas-table-card">
                <table class="minutas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Terreno</th>
                            <th>Comprador</th>
                            <th>Vendedor</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Documento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($minutas as $m)
                        <tr>
                            <td><span class="id-pill">{{ $m->id }}</span></td>
                            <td>{{ $m->terreno->ubicacion ?? 'Sin datos' }}</td>
                            <td>{{ $m->comprador->nombre ?? 'N/A' }}</td>
                            <td>{{ $m->vendedor->nombre ?? 'N/A' }}</td>
                            <td class="monto-cell">
                                <span class="monto-currency">Bs</span>
                                {{ number_format($m->monto, 2) }}
                            </td>
                            <td class="fecha-cell">
                                {{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($m->archivo)
                                    <a href="{{ asset('storage/' . $m->archivo) }}"
                                       target="_blank" class="badge-ver">
                                        📎 Ver
                                    </a>
                                @else
                                    <span class="badge-sin-archivo">Sin archivo</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>
