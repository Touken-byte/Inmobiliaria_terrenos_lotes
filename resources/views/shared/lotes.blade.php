@extends('layouts.app')

@section('title', 'Control de Lotes')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h2 class="card-title" style="margin: 0;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px; margin-right: 12px; color: var(--accent);">
                <rect x="3" y="3" width="7" height="7" />
                <rect x="14" y="3" width="7" height="7" />
                <rect x="14" y="14" width="7" height="7" />
                <rect x="3" y="14" width="7" height="7" />
            </svg>
            Control de Disponibilidad de Lotes
            <span class="badge badge-secondary">{{ count($terrenos) }}</span>
        </h2>
        @if(Auth::check() && Auth::user()->rol === 'admin')
            <button class="btn btn-primary" onclick="abrirReporteModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; margin-right: 8px;">
                    <path d="M21.21 15.89A10 10 0 1 1 8 2.83" />
                    <path d="M22 12A10 10 0 0 0 12 2v10z" />
                </svg>
                Reporte de Inventario
            </button>
        @endif
    </div>
    <div class="card-body no-padding">
        @if (count($terrenos) === 0)
            <div class="empty-state">
                <p>No se encontraron lotes registrados.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="data-table" id="lotesTable">
                    <thead>
                        <tr>
                            <th>ID Lote</th>
                            <th>Ubicación</th>
                            <th>Vendedor</th>
                            <th>Estado Actual</th>
                            <th>Último Cambio</th>
                            <th>Actualizar Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($terrenos as $t)
                            <tr class="terreno-row">
                                <td class="id-cell">#{{ $t->id }}</td>
                                <td>
                                    <span class="ubicacion-text" title="{{ $t->ubicacion }}">{{ Str::limit($t->ubicacion, 40) }}</span>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <span class="user-name-text">{{ $t->vendedor->nombre ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = 'secondary';
                                        if($t->estado_lote === 'disponible') $badgeClass = 'success';
                                        if($t->estado_lote === 'reservado') $badgeClass = 'warning';
                                        if($t->estado_lote === 'vendido') $badgeClass = 'danger';
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">
                                        {{ ucfirst($t->estado_lote) }}
                                    </span>
                                </td>
                                <td class="date-cell">
                                    <div class="date-multi">
                                        <span class="date-main" style="display: block; font-weight: 500;">{{ $t->actualizado_en ? \Carbon\Carbon::parse($t->actualizado_en)->locale('es')->diffForHumans() : 'Fecha no disponible' }}</span>
                                        @if($t->actualizado_en)
                                            <span class="date-time" style="font-size: 0.85em; opacity: 0.7;">{{ \Carbon\Carbon::parse($t->actualizado_en)->timezone('America/La_Paz')->translatedFormat('d M Y, H:i') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($t->estado === 'aprobado')
                                        @if(Auth::user() && Auth::user()->rol === 'vendedor')
                                            <form action="{{ route('vendedor.lotes.estado', $t->id) }}" method="POST" style="display: flex; gap: 4px; align-items: center; flex-wrap: wrap;">
                                                @csrf
                                                <button type="submit" name="estado_lote" value="disponible" 
                                                    class="btn btn-sm {{ $t->estado_lote === 'disponible' ? 'btn-primary' : '' }}" 
                                                    style="padding: 4px 8px; font-size: 0.8em; border: 1px solid rgba(255,255,255,0.2); background: {{ $t->estado_lote === 'disponible' ? 'rgba(34, 197, 94, 0.2)' : 'transparent' }}; color: {{ $t->estado_lote === 'disponible' ? '#4ade80' : '#ccc' }}; border-radius: 6px;">
                                                    Disponible
                                                </button>
                                                <button type="submit" name="estado_lote" value="reservado" 
                                                    class="btn btn-sm {{ $t->estado_lote === 'reservado' ? 'btn-primary' : '' }}" 
                                                    style="padding: 4px 8px; font-size: 0.8em; border: 1px solid rgba(255,255,255,0.2); background: {{ $t->estado_lote === 'reservado' ? 'rgba(234, 179, 8, 0.2)' : 'transparent' }}; color: {{ $t->estado_lote === 'reservado' ? '#facc15' : '#ccc' }}; border-radius: 6px;">
                                                    Reservado
                                                </button>
                                                <button type="submit" name="estado_lote" value="vendido" 
                                                    class="btn btn-sm {{ $t->estado_lote === 'vendido' ? 'btn-primary' : '' }}" 
                                                    style="padding: 4px 8px; font-size: 0.8em; border: 1px solid rgba(255,255,255,0.2); background: {{ $t->estado_lote === 'vendido' ? 'rgba(239, 68, 68, 0.2)' : 'transparent' }}; color: {{ $t->estado_lote === 'vendido' ? '#f87171' : '#ccc' }}; border-radius: 6px;">
                                                    Vendido
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge badge-secondary">Modificable por el Vendedor de la propiedad</span>
                                        @endif
                                    @elseif($t->estado === 'rechazado')
                                        <span class="badge badge-danger">Rechazado por moderación</span>
                                    @else
                                        <span class="badge badge-warning" style="color: #000;">Pendiente de aprobación</span>
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

@if(Auth::check() && Auth::user()->rol === 'admin')
<!-- Reporte de Inventario Modal -->
<div id="reporteModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 1050; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(4px);">
    <div class="card" style="width: 100%; max-width: 1000px; max-height: 90vh; overflow-y: auto; background: var(--bg-color, #1e1e2f); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px;">
            <h3 style="margin: 0; font-size: 1.5rem; color: var(--text-color, #fff);">📊 Reporte de Inventario</h3>
            <button onclick="cerrarReporteModal()" style="background: none; border: none; font-size: 28px; cursor: pointer; color: #a1a1aa; line-height: 1;">&times;</button>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div style="display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                <div style="flex: 1; min-width: 150px;">
                    <label style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 0.9em; color: #a1a1aa;">Fecha Desde:</label>
                    <input type="date" id="filtroDesde" class="form-control" style="width: 100%;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 0.9em; color: #a1a1aa;">Fecha Hasta:</label>
                    <input type="date" id="filtroHasta" class="form-control" style="width: 100%;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 0.9em; color: #a1a1aa;">Ubicación (Filtro):</label>
                    <input type="text" id="filtroUbicacion" class="form-control" placeholder="Ej. Zona Norte..." style="width: 100%;">
                </div>
                <div style="flex: 1; min-width: 180px;">
                    <label style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 0.9em; color: #a1a1aa;">Vendedor:</label>
                    <select id="filtroVendedor" class="form-control" style="width: 100%;">
                        <option value="">Todos los vendedores</option>
                        @isset($vendedores)
                            @foreach($vendedores as $v)
                                <option value="{{ $v->id }}">{{ $v->nombre }} {{ $v->apellido }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div style="display: flex; align-items: flex-end;">
                    <button onclick="cargarDatosReporte()" class="btn btn-primary" style="height: 42px; display: flex; align-items: center; gap: 8px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        Filtrar
                    </button>
                </div>
            </div>

            <!-- KPIs -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
                <div style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); text-align: center;">
                    <h4 style="margin: 0 0 10px 0; color: #a1a1aa; font-size: 0.9em; font-weight: 500;">Ventas del Mes</h4>
                    <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <span id="kpiVentasMes" style="font-size: 2em; font-weight: bold; color: #fff;">0</span>
                        <span id="kpiCrecimiento" style="font-size: 0.85em; padding: 2px 6px; border-radius: 4px; background: rgba(255,255,255,0.1);">-</span>
                    </div>
                </div>
                <div style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); text-align: center;">
                    <h4 style="margin: 0 0 10px 0; color: #a1a1aa; font-size: 0.9em; font-weight: 500;">Lotes Disponibles</h4>
                    <span id="kpiDisponibles" style="font-size: 2em; font-weight: bold; color: #22c55e;">0</span>
                </div>
                <div style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); text-align: center;">
                    <h4 style="margin: 0 0 10px 0; color: #a1a1aa; font-size: 0.9em; font-weight: 500;">Tiempo Promedio de Venta</h4>
                    <div style="display: flex; align-items: baseline; justify-content: center; gap: 5px;">
                        <span id="kpiPromedio" style="font-size: 2em; font-weight: bold; color: #fff;">0</span>
                        <span style="color: #a1a1aa; font-size: 0.9em;">días</span>
                    </div>
                </div>
            </div>

            <!-- Financials -->
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 15px; margin-bottom: 25px; padding: 15px; background: rgba(34, 197, 94, 0.05); border-radius: 8px; border: 1px solid rgba(34, 197, 94, 0.2);">
                <div style="text-align: center; flex: 1; min-width: 150px;">
                    <span style="display: block; color: #a1a1aa; font-size: 0.85em; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Ingresos (Vendidos)</span>
                    <span id="finVendidos" style="font-size: 1.5em; font-weight: bold; color: #ef4444;">$0</span>
                </div>
                <div style="text-align: center; flex: 1; min-width: 150px; border-left: 1px solid rgba(255,255,255,0.1); border-right: 1px solid rgba(255,255,255,0.1);">
                    <span style="display: block; color: #a1a1aa; font-size: 0.85em; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Proyección (Reservados)</span>
                    <span id="finReservados" style="font-size: 1.5em; font-weight: bold; color: #eab308;">$0</span>
                </div>
                <div style="text-align: center; flex: 1; min-width: 150px;">
                    <span style="display: block; color: #a1a1aa; font-size: 0.85em; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Capital Estancado (Disp.)</span>
                    <span id="finDisponibles" style="font-size: 1.5em; font-weight: bold; color: #22c55e;">$0</span>
                </div>
            </div>

            <!-- Contenedor PDF -->
            <div id="pdfContainer" style="background: var(--bg-color, #1e1e2f); padding: 15px; border-radius: 8px;">
                <div style="text-align: center; margin-bottom: 20px; display: none;" id="pdfHeader">
                    <h2 style="color: var(--text-color, #fff); margin-bottom: 5px;">Reporte de Inventario de Lotes</h2>
                    <p id="pdfFechas" style="color: #a1a1aa; font-size: 0.9em;"></p>
                </div>
                
                <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                    <!-- Gráfico Barras -->
                    <div style="flex: 2; min-width: 350px; height: 350px; position: relative;">
                        <canvas id="inventarioChart"></canvas>
                    </div>
                    <!-- Gráfico Dona -->
                    <div style="flex: 1; min-width: 250px; height: 350px; position: relative; display: flex; flex-direction: column; align-items: center;">
                        <h4 style="color: #a1a1aa; font-size: 1em; font-weight: 500; margin-bottom: 10px;">Distribución Global</h4>
                        <div style="flex: 1; width: 100%; position: relative;">
                            <canvas id="donaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exportar -->
            <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <button onclick="exportarExcel()" class="btn btn-outline" style="display: flex; align-items: center; gap: 8px; border-color: #22c55e; color: #22c55e;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Exportar Excel
                </button>
                <button onclick="exportarPDF()" class="btn btn-outline" style="display: flex; align-items: center; gap: 8px; border-color: #ef4444; color: #ef4444;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Exportar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    let chartInstance = null;
    let donaChartInstance = null;
    let rawData = [];

    const formatMoney = (amount) => {
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 0 }).format(amount);
    };

    function abrirReporteModal() {
        document.getElementById('reporteModal').style.display = 'flex';
        const hoy = new Date();
        const seisMeses = new Date();
        seisMeses.setMonth(hoy.getMonth() - 5); 
        
        if (!document.getElementById('filtroDesde').value) {
            document.getElementById('filtroDesde').value = seisMeses.toISOString().split('T')[0];
            document.getElementById('filtroHasta').value = hoy.toISOString().split('T')[0];
        }
        cargarDatosReporte();
    }

    function cerrarReporteModal() {
        document.getElementById('reporteModal').style.display = 'none';
    }

    async function cargarDatosReporte() {
        const desde = document.getElementById('filtroDesde').value;
        const hasta = document.getElementById('filtroHasta').value;
        const ubicacion = document.getElementById('filtroUbicacion').value;
        const vendedorId = document.getElementById('filtroVendedor').value;

        const url = new URL("{{ route('admin.api.inventario_stats') }}", window.location.origin);
        if (desde) url.searchParams.append('fecha_desde', desde);
        if (hasta) url.searchParams.append('fecha_hasta', hasta);
        if (ubicacion) url.searchParams.append('ubicacion', ubicacion);
        if (vendedorId) url.searchParams.append('vendedor_id', vendedorId);

        try {
            const response = await fetch(url);
            const data = await response.json();
            rawData = data;
            
            actualizarUI(data);
            dibujarGrafico(data);
        } catch (error) {
            console.error("Error cargando datos:", error);
            alert("Hubo un error al cargar los datos del reporte.");
        }
    }

    function actualizarUI(data) {
        document.getElementById('kpiVentasMes').innerText = data.kpis.ventas_mes;
        document.getElementById('kpiDisponibles').innerText = data.kpis.disponibles_total;
        document.getElementById('kpiPromedio').innerText = data.kpis.promedio_dias;
        
        const crecEl = document.getElementById('kpiCrecimiento');
        if (data.kpis.crecimiento > 0) {
            crecEl.innerText = `↑ ${data.kpis.crecimiento}%`;
            crecEl.style.color = '#4ade80';
            crecEl.style.background = 'rgba(74, 222, 128, 0.1)';
        } else if (data.kpis.crecimiento < 0) {
            crecEl.innerText = `↓ ${Math.abs(data.kpis.crecimiento)}%`;
            crecEl.style.color = '#f87171';
            crecEl.style.background = 'rgba(248, 113, 113, 0.1)';
        } else {
            crecEl.innerText = `- 0%`;
            crecEl.style.color = '#a1a1aa';
            crecEl.style.background = 'rgba(255, 255, 255, 0.1)';
        }

        document.getElementById('finVendidos').innerText = formatMoney(data.financials.vendido);
        document.getElementById('finReservados').innerText = formatMoney(data.financials.reservado);
        document.getElementById('finDisponibles').innerText = formatMoney(data.financials.disponible);
    }

    function dibujarGrafico(data) {
        const ctx = document.getElementById('inventarioChart').getContext('2d');
        const ctxDona = document.getElementById('donaChart').getContext('2d');
        
        if (chartInstance) chartInstance.destroy();
        if (donaChartInstance) donaChartInstance.destroy();

        Chart.defaults.color = '#a1a1aa';
        Chart.defaults.font.family = 'Inter, system-ui, sans-serif';

        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Disponibles',
                        data: data.disponibles,
                        backgroundColor: 'rgba(34, 197, 94, 0.6)',
                        borderColor: '#22c55e',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Vendidos',
                        data: data.vendidos,
                        backgroundColor: 'rgba(239, 68, 68, 0.6)',
                        borderColor: '#ef4444',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Reservados',
                        data: data.reservados,
                        backgroundColor: 'rgba(234, 179, 8, 0.6)',
                        borderColor: '#eab308',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { padding: 15, usePointStyle: true, pointStyle: 'rectRounded' } },
                    tooltip: { mode: 'index', intersect: false, backgroundColor: 'rgba(0, 0, 0, 0.8)', titleColor: '#fff', bodyColor: '#fff', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1 }
                },
                scales: {
                    x: { grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false } },
                    y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false } }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false }
            }
        });

        donaChartInstance = new Chart(ctxDona, {
            type: 'doughnut',
            data: {
                labels: ['Disponibles', 'Vendidos', 'Reservados'],
                datasets: [{
                    data: [data.global.disponible, data.global.vendido, data.global.reservado],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(234, 179, 8, 0.8)'
                    ],
                    borderColor: 'var(--bg-color, #1e1e2f)',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, pointStyle: 'circle' } },
                    tooltip: { backgroundColor: 'rgba(0, 0, 0, 0.8)', titleColor: '#fff', bodyColor: '#fff', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1 }
                }
            }
        });
    }

    function exportarPDF() {
        const element = document.getElementById('pdfContainer');
        const header = document.getElementById('pdfHeader');
        const fechas = document.getElementById('pdfFechas');
        
        const desde = document.getElementById('filtroDesde').value;
        const hasta = document.getElementById('filtroHasta').value;
        fechas.innerText = `Periodo: ${desde ? desde : 'Inicio'} a ${hasta ? hasta : 'Hoy'}`;
        
        const originalBg = element.style.background;
        element.style.background = '#ffffff';
        header.style.display = 'block';
        header.querySelector('h2').style.color = '#000000';
        
        Chart.defaults.color = '#333';
        chartInstance.update();
        donaChartInstance.update();

        const opt = {
            margin:       10,
            filename:     'Reporte_Inventario_Dashboard.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, backgroundColor: '#ffffff' },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            element.style.background = originalBg;
            header.style.display = 'none';
            header.querySelector('h2').style.color = 'var(--text-color, #fff)';
            Chart.defaults.color = '#a1a1aa';
            chartInstance.update();
            donaChartInstance.update();
        });
    }

    function exportarExcel() {
        if (!rawData || !rawData.labels || rawData.labels.length === 0) {
            alert('No hay datos para exportar.');
            return;
        }

        const ws_data = [['Mes', 'Disponibles', 'Vendidos', 'Reservados', 'Total', 'Ingresos Venta ($)']];
        
        for (let i = 0; i < rawData.labels.length; i++) {
            const disponibles = rawData.disponibles[i];
            const vendidos = rawData.vendidos[i];
            const reservados = rawData.reservados[i];
            const total = disponibles + vendidos + reservados;
            const dinero = rawData.dinero_vendido[i];
            ws_data.push([rawData.labels[i], disponibles, vendidos, reservados, total, dinero]);
        }

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(ws_data);
        
        const wscols = [{wch: 15}, {wch: 15}, {wch: 15}, {wch: 15}, {wch: 15}, {wch: 20}];
        ws['!cols'] = wscols;

        XLSX.utils.book_append_sheet(wb, ws, "Inventario");
        
        const desde = document.getElementById('filtroDesde').value || 'inicio';
        const hasta = document.getElementById('filtroHasta').value || 'hoy';
        XLSX.writeFile(wb, `Reporte_Inventario_Dashboard_${desde}_a_${hasta}.xlsx`);
    }

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('reporteModal');
        if (e.target === modal) cerrarReporteModal();
    });
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') cerrarReporteModal();
    });
</script>
@endif
@endsection
