@extends('layouts.app')

@section('title', 'Información Rápida - Folio ' . $folio->numero_folio)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Información Rápida del Folio</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Número de Folio</th>
                            <td>{{ $folio->numero_folio }}</td>
                        </tr>
                        <tr>
                            <th>Superficie</th>
                            <td>{{ number_format($folio->superficie, 2) }} m²</td>
                        </tr>
                        <tr>
                            <th>Ubicación</th>
                            <td>{{ $folio->ubicacion }}</td>
                        </tr>
                        <tr>
                            <th>Colindancias</th>
                            <td>{{ $folio->colindancias ?? 'No registradas' }}</td>
                        </tr>
                    </table>
                    <div class="text-center mt-3">
                        <a href="{{ route('folio.completo', $folio->id) }}" class="btn btn-info">Ver Folio Completo</a>
                        <a href="{{ route('folio.consultar.form') }}" class="btn btn-secondary">Nueva Consulta</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection