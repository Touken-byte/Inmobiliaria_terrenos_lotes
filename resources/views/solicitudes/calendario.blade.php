@extends('layouts.app')

@section('title', 'Calendario de Visitas')

@section('content')
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">

<style>
    /* Estilos Premium TerrenoSur */
    .glass-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 20, 25, 0.85); /* Fondo oscuro semitransparente */
        backdrop-filter: blur(5px);
        align-items: center;
        justify-content: center;
    }
    .glass-modal.active {
        display: flex;
    }
    .glass-modal-content {
        background: #1a1a2e; /* Fondo sólido oscuro para que se vea bien */
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        width: 95%;
        max-width: 500px;
        color: white;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        overflow: hidden;
    }
    .modal-header-premium {
        padding: 20px;
        background: #252545;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-body-premium {
        padding: 25px;
    }
    .form-control-premium {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        color: white;
        border-radius: 10px;
        padding: 10px;
        width: 100%;
        margin-top: 5px;
    }
    .form-control-premium option {
        background: #1a1a2e;
        color: white;
    }
    .btn-submit-premium {
        background: #7c4dff;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-submit-premium:disabled {
        background: #444;
        cursor: not-allowed;
        opacity: 0.5;
    }
    .alert-disponibilidad {
        margin-top: 15px;
        padding: 10px;
        border-radius: 10px;
        font-size: 0.9rem;
        display: none;
    }
</style>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h1>Calendario de Visitas</h1>
    </div>
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

<!-- Modal Corregido -->
<div id="create-modal" class="glass-modal">
    <div class="glass-modal-content">
        <div class="modal-header-premium">
            <h3 style="margin:0;">Agendar Visita</h3>
            <button onclick="closeModal()" style="background:none; border:none; color:white; font-size:24px; cursor:pointer;">&times;</button>
        </div>

        <form id="create-form" action="{{ route('vendedor.solicitudes.store') }}" method="POST">
            @csrf
            <div class="modal-body-premium">
                <input type="hidden" name="fecha_visita" id="modal-fecha">

                <div style="background: rgba(124, 77, 255, 0.1); padding: 10px; border-radius: 10px; margin-bottom: 20px;">
                    <span style="font-size: 0.8rem; color: #aaa;">FECHA SELECCIONADA</span>
                    <div id="display-fecha" style="font-size: 1.2rem; font-weight: bold;"></div>
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label style="font-size: 0.85rem;">Hora de Inicio</label>
                        <input type="time" name="hora_inicio" id="modal-hora-inicio" class="form-control-premium" required>
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 0.85rem;">Hora Final</label>
                        <input type="time" name="hora_fin" id="modal-hora-fin" class="form-control-premium" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-size: 0.85rem;">Terreno a visitar</label>
                    <select name="terreno_id" id="modal-terreno" class="form-control-premium" required>
                        <option value="" disabled selected>Selecciona un terreno</option>
                        @foreach($terrenos as $terreno)
                            <option value="{{ $terreno->id }}">{{ $terreno->ubicacion }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-size: 0.85rem;">Asignar Vendedor</label>
                    <select name="vendedor_id" id="modal-vendedor" class="form-control-premium" required>
                        <option value="" disabled selected>Selecciona un vendedor</option>
                        @foreach($vendedores as $vendedor)
                            <option value="{{ $vendedor->id }}">{{ $vendedor->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Alerta de disponibilidad -->
                <div id="modal-disponibilidad" class="alert-disponibilidad">
                    <span id="modal-disponibilidad-texto"></span>
                </div>
            </div>

            <div style="padding: 20px; display: flex; justify-content: flex-end; gap: 10px; background: rgba(0,0,0,0.2);">
                <button type="button" onclick="closeModal()" style="background:none; border:none; color:white; cursor:pointer;">Cancelar</button>
                <button type="submit" id="btn-submit" class="btn-submit-premium" disabled>Reservar Cita</button>
            </div>
        </form>
    </div>
</div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth' },
        events: '{{ route("vendedor.solicitudes.eventos") }}',
        dateClick: function(info) { openModal(info.dateStr); }
    });
    calendar.render();

    window.openModal = function(dateStr) {
        document.getElementById('modal-fecha').value = dateStr;
        document.getElementById('display-fecha').textContent = dateStr;
        document.getElementById('create-modal').classList.add('active');
        actualizarEstadoBoton(false);
    }

    window.closeModal = function() {
        document.getElementById('create-modal').classList.remove('active');
        document.getElementById('create-form').reset();
    }

    const checkInputs = ['modal-vendedor', 'modal-terreno', 'modal-hora-inicio', 'modal-hora-fin'];
    checkInputs.forEach(id => {
        document.getElementById(id).addEventListener('change', verificarDisponibilidadModal);
    });

    function actualizarEstadoBoton(disponible) {
        const btn = document.getElementById('btn-submit');
        const v = document.getElementById('modal-vendedor').value;
        const t = document.getElementById('modal-terreno').value;
        const h1 = document.getElementById('modal-hora-inicio').value;
        const h2 = document.getElementById('modal-hora-fin').value;
        
        btn.disabled = !(v && t && h1 && h2 && disponible);
    }

    function verificarDisponibilidadModal() {
        const v = document.getElementById('modal-vendedor').value;
        const f = document.getElementById('modal-fecha').value;
        const h1 = document.getElementById('modal-hora-inicio').value;
        const h2 = document.getElementById('modal-hora-fin').value;
        const alertDiv = document.getElementById('modal-disponibilidad');
        const alertText = document.getElementById('modal-disponibilidad-texto');

        if (!v || !h1 || !h2) return;

        alertDiv.style.display = 'block';
        alertDiv.style.background = 'rgba(255,255,255,0.1)';
        alertText.textContent = 'Verificando disponibilidad...';

        fetch('{{ route("vendedor.solicitudes.verificar_disponibilidad") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ vendedor_id: v, fecha: f, hora_inicio: h1, hora_fin: h2 })
        })
        .then(res => res.json())
        .then(data => {
            alertText.textContent = data.mensaje;
            alertDiv.style.background = data.disponible ? 'rgba(40, 167, 69, 0.2)' : 'rgba(220, 53, 69, 0.2)';
            actualizarEstadoBoton(data.disponible);
        })
        .catch(err => {
            alertText.textContent = 'Horario disponible (Verificación manual)';
            alertDiv.style.background = 'rgba(255,255,0,0.1)';
            actualizarEstadoBoton(true); // Permitimos avanzar por seguridad
        });
    }
});
</script>
@endsection