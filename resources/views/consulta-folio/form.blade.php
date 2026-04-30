@extends('layouts.app')

@section('title', 'Consulta de Folio Real')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Consulta de Folio Real</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <form method="POST" action="{{ route('folio.consultar.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="numero_folio" class="form-label">Número de Folio</label>
                            <input type="text" class="form-control" id="numero_folio" name="numero_folio" required placeholder="Ej: FOL-001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Consulta</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo_consulta" id="rapida" value="rapida" checked>
                                <label class="form-check-label" for="rapida">
                                    Información Rápida (básica)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo_consulta" id="completa" value="completa">
                                <label class="form-check-label" for="completa">
                                    Folio Completo (todos los datos)
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Consultar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection