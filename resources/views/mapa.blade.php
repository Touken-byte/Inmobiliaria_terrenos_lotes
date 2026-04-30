@extends('layouts.app')

@section('title', 'Mapa Interactivo de Lotes')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 600px;
        width: 100%;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 1;
    }
    .mapa-leyenda {
        display: flex;
        gap: 1.5rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }
    .leyenda-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #555;
    }
    .leyenda-circulo {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px rgba(0,0,0,0.2);
    }
    .mapa-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 600px;
        background: #f8f9fa;
        border-radius: 12px;
        font-size: 1rem;
        color: #888;
    }
    .leaflet-popup-content a.btn-ver {
        display: inline-block;
        margin-top: 8px;
        padding: 5px 14px;
        background: #007bff;
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .leaflet-popup-content a.btn-ver:hover {
        background: #0056b3;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="row mb-3">
        <div class="col-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="mb-1" style="font-size:1.6rem; font-weight:700;">
                    🗺️ Mapa Interactivo de Lotes
                </h1>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    Explora los terrenos disponibles. Haz clic en un marcador para ver detalles.
                </p>
            </div>
            <a href="{{ route('catalogo.terrenos') }}" class="btn btn-outline-primary btn-sm">
                Ver catálogo en lista
            </a>
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            {{-- El div del mapa --}}
            <div id="map-loading" class="mapa-loading d-none">
                <span>Cargando mapa...</span>
            </div>
            <div id="map"></div>

            {{-- Leyenda --}}
            <div class="mapa-leyenda mt-3">
                <div class="leyenda-item">
                    <div class="leyenda-circulo" style="background:#4CAF50;"></div>
                    <span>Disponible</span>
                </div>
                <div class="leyenda-item">
                    <div class="leyenda-circulo" style="background:#808080;"></div>
                    <span>Vendido</span>
                </div>
                <div class="leyenda-item">
                    <div class="leyenda-circulo" style="background:#FF9800;"></div>
                    <span>Reservado</span>
                </div>
            </div>

            {{-- Contador de resultados --}}
            <p class="text-muted mt-2 mb-0" style="font-size:0.85rem;" id="mapa-contador">
                Cargando lotes...
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ─── Coordenadas por defecto: Yacuiba, Tarija, Bolivia ───
    var defaultLat = -22.0186;
    var defaultLng = -63.6774;
    var defaultZoom = 13;

    // Inicializar mapa principal
    var map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CartoDB</a>',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    // ─── Cargar lotes desde el controlador (datos embebidos) ───
    var terrenos = @json($terrenos);

    var contadorEl = document.getElementById('mapa-contador');
    var markersValidos = 0;

    terrenos.forEach(function (terreno) {
        var lat = parseFloat(terreno.latitud);
        var lng = parseFloat(terreno.longitud);

        // Solo colocar marcador si tiene coordenadas válidas
        if (isNaN(lat) || isNaN(lng) || lat === 0 || lng === 0) {
            return;
        }

        markersValidos++;

        // Color según estado del lote
        var color = '#4CAF50'; // disponible por defecto
        if (terreno.estado_lote === 'vendido') {
            color = '#808080';
        } else if (terreno.estado_lote === 'reservado') {
            color = '#FF9800';
        }

        var marker = L.circleMarker([lat, lng], {
            radius: 9,
            fillColor: color,
            color: '#ffffff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.85
        }).addTo(map);

        // Precio formateado
        var precioFormateado = terreno.precio
            ? '$' + Number(terreno.precio).toLocaleString('es-BO')
            : 'Precio no especificado';

        // Superficie
        var superficie = terreno.metros_cuadrados
            ? Number(terreno.metros_cuadrados).toLocaleString('es-BO') + ' m²'
            : 'N/A';

        // Imagen de portada si existe
        var imgHtml = '';
        if (terreno.imagenes && terreno.imagenes.length > 0) {
            var portada = terreno.imagenes.find(function(img) {
                return img.es_portada == 1;
            }) || terreno.imagenes[0];

            if (portada) {
                imgHtml = '<img src="/storage/' + portada.nombre_archivo + '" '
                    + 'style="width:100%;height:100px;object-fit:cover;border-radius:6px;margin-bottom:8px;" '
                    + 'onerror="this.style.display=\'none\'" />';
            }
        }

        // Estado del lote legible
        var estadoLabel = terreno.estado_lote || 'disponible';
        var estadoColor = color;

        var popupContent =
            '<div style="min-width:180px; max-width:220px;">' +
                imgHtml +
                '<strong style="font-size:0.95rem;">' + (terreno.nombre_lote || 'Lote #' + terreno.id) + '</strong><br>' +
                '<span style="font-size:0.82rem; color:#555;">' + (terreno.ubicacion || '') + '</span><br><br>' +
                '<span style="font-size:0.88rem;">💰 <strong>' + precioFormateado + '</strong></span><br>' +
                '<span style="font-size:0.88rem;">📐 ' + superficie + '</span><br>' +
                '<span style="font-size:0.83rem; margin-top:4px; display:inline-block;">' +
                    'Estado: <span style="color:' + estadoColor + '; font-weight:600;">' + estadoLabel + '</span>' +
                '</span><br>' +
                '<a href="/catalogo/' + terreno.id + '" class="btn-ver">Ver detalle →</a>' +
            '</div>';

        marker.bindPopup(popupContent, {
            maxWidth: 240,
            className: 'lote-popup'
        });

        // Abrir popup al pasar el mouse (opcional)
        marker.on('mouseover', function () {
            this.openPopup();
        });
    });

    // Actualizar contador
    if (markersValidos === 0) {
        contadorEl.textContent = 'No hay lotes con ubicación disponible en el mapa aún.';
    } else {
        contadorEl.textContent = 'Mostrando ' + markersValidos + ' lote(s) en el mapa.';
    }

    // Si hay marcadores, ajustar el mapa para mostrarlos todos
    if (markersValidos > 0) {
        var bounds = [];
        terrenos.forEach(function (terreno) {
            var lat = parseFloat(terreno.latitud);
            var lng = parseFloat(terreno.longitud);
            if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                bounds.push([lat, lng]);
            }
        });
        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [40, 40] });
        } else if (bounds.length === 1) {
            map.setView(bounds[0], 15);
        }
    }

    // Asegurarse de que el mapa se renderice correctamente al cargar
    setTimeout(function () {
        map.invalidateSize();
    }, 300);
});
</script>
@endpush