@extends('layouts.app')

@section('title', 'Editar Vendedor')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
            Editar Vendedor: {{ $vendedor->nombre }}
        </h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.actualizar_vendedor', $vendedor->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nombre completo *</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $vendedor->nombre) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Correo electrónico *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $vendedor->email) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $vendedor->telefono) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Estado de verificación</label>
                <select name="estado_verificacion" class="form-control">
                    <option value="pendiente" {{ $vendedor->estado_verificacion == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                    <option value="verificado" {{ $vendedor->estado_verificacion == 'verificado' ? 'selected' : '' }}>✅ Verificado</option>
                    <option value="rechazado" {{ $vendedor->estado_verificacion == 'rechazado' ? 'selected' : '' }}>❌ Rechazado</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Activo</label>
                <div class="checkbox-group">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" {{ $vendedor->activo ? 'checked' : '' }}> 
                    <span>Usuario activo (puede iniciar sesión)</span>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Cambiar contraseña (opcional)</label>
                <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
            </div>
            <div class="form-group">
                <label class="form-label">Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Repetir contraseña">
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.panel') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection