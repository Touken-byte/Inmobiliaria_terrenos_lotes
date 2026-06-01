@extends('layouts.app')

@section('title', 'Editar Categoría')

@section('content')
@include('admin.categorias._form', [
    'titulo'    => 'Editar Categoría',
    'subtitulo' => 'Modificá los datos de la categoría «' . $categoria->nombre . '».',
    'action'    => route('admin.categorias.update', $categoria),
    'method'    => 'PUT',
    'cat'       => $categoria,
])
@endsection