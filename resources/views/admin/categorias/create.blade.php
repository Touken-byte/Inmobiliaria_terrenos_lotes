@extends('layouts.app')

@section('title', 'Nueva Categoría')

@section('content')
@include('admin.categorias._form', [
    'titulo'  => 'Nueva Categoría',
    'subtitulo' => 'Completá los datos para agregar una nueva categoría al catálogo.',
    'action'  => route('admin.categorias.store'),
    'method'  => 'POST',
    'cat'     => null,
])
@endsection