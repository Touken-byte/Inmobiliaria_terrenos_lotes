@extends('layouts.app')

@section('title', 'Folio Completo - ' . $folio->numero_folio)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Folio Real Completo: {{ $folio->numero_folio }}</h3>
                </div>
                <div class="card-body">
                    <!-- Datos básicos -->
                    <h5>Datos del Lote</h5>
                    <table class="table table-sm">
                        <tr><th>Superficie:</th><td>{{ number_format($folio->superficie, 2) }} m²</td></tr>
                        <tr><th>Ubicación:</th><td>{{ $folio->ubicacion }}</td></tr>
                        <tr><th>Colindancias:</th><td>{{ $folio->colindancias ?? 'N/A' }}</td></tr>
                    </table>

                    <!-- Propietarios vigentes -->
                    <h5>Propietarios Vigentes</h5>
                    @if($folio->propietarios->where('vigente', true)->count())
                        <table class="table table-sm">
                            <thead><tr><th>Nombre</th><th>Documento</th><th>Desde</th><th>Hasta</th></tr></thead>
                            <tbody>
                                @foreach($folio->propietarios->where('vigente', true) as $prop)
                                <tr>
                                    <td>{{ $prop->nombre_completo }}</td>
                                    <td>{{ $prop->tipo_documento }}: {{ $prop->numero_documento }}</td>
                                    <td>{{ $prop->fecha_desde ?? '-' }}</td>
                                    <td>{{ $prop->fecha_hasta ?? 'Actual' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No hay propietarios vigentes registrados.</p>
                    @endif

                    <!-- Gravámenes -->
                    <h5>Gráva menes</h5>
                    @if($folio->gravamenes->where('activo', true)->count())
                        <table class="table table-sm">
                            <thead><tr><th>Tipo</th><th>Descripción</th><th>Monto</th><th>Fecha Registro</th></tr></thead>
                            <tbody>
                                @foreach($folio->gravamenes->where('activo', true) as $g)
                                <tr>
                                    <td>{{ $g->tipo }}</td>
                                    <td>{{ $g->descripcion }}</td>
                                    <td>{{ $g->monto ? '$'.number_format($g->monto,2) : '-' }}</td>
                                    <td>{{ $g->fecha_registro ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No hay gravámenes activos.</p>
                    @endif

                    <!-- Restricciones -->
                    <h5>Restricciones</h5>
                    @if($folio->restricciones->where('activa', true)->count())
                        <table class="table table-sm">
                            <thead><tr><th>Tipo</th><th>Descripción</th><th>Vigencia</th></tr></thead>
                            <tbody>
                                @foreach($folio->restricciones->where('activa', true) as $r)
                                <tr>
                                    <td>{{ $r->tipo }}</td>
                                    <td>{{ $r->descripcion }}</td>
                                    <td>{{ $r->fecha_inicio ?: 'N/A' }} - {{ $r->fecha_fin ?: 'Indefinida' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No hay restricciones activas.</p>
                    @endif

                    <!-- Trámites pendientes -->
                    <h5>Trámites Pendientes</h5>
                    @if($folio->tramites->where('estado', 'pendiente')->count())
                        <table class="table table-sm">
                            <thead><tr><th>Nombre</th><th>Fecha Solicitud</th><th>Observaciones</th></tr></thead>
                            <tbody>
                                @foreach($folio->tramites->where('estado', 'pendiente') as $t)
                                <tr>
                                    <td>{{ $t->nombre_tramite }}</td>
                                    <td>{{ $t->fecha_solicitud ?? '-' }}</td>
                                    <td>{{ $t->observaciones ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No hay trámites pendientes.</p>
                    @endif

                    <div class="alert alert-info mt-3">
                        <strong>Nota:</strong> Esta consulta ha sido registrada en el historial de accesos.
                    </div>
                    <a href="{{ route('folio.consultar.form') }}" class="btn btn-primary">Nueva Consulta</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection