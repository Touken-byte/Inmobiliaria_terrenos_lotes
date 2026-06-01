@extends('layouts.app')

@section('title', 'Cuentas de Administrador')

@section('content')

<style>
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th {
        padding: .85rem 1rem;
        text-align: left;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--text-muted);
        border-bottom: 2px solid var(--border-color);
    }
    .admin-table td {
        padding: .9rem 1rem;
        font-size: .88rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .admin-table tr:hover td { background: var(--bg-light); }

    .role-badge {
        padding: .25rem .7rem;
        border-radius: 100px;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        background: #ede9fe;
        color: #5b21b6;
        border: 1px solid #d8b4fe;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .modal-overlay.active {
        display: flex;
        opacity: 1;
    }
    .modal-box {
        background: var(--card-bg, #fff);
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--border-color);
        transform: scale(0.95);
        transition: transform 0.3s ease;
        overflow: hidden;
    }
    .modal-overlay.active .modal-box {
        transform: scale(1);
    }
    .modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-muted);
        line-height: 1;
        padding: 0;
    }
    .modal-body {
        padding: 1.5rem;
    }
    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
        gap: .75rem;
        background: var(--bg-light);
    }

    /* Password Validation Feedback */
    .validation-rules {
        margin-top: .5rem;
        padding: .75rem;
        background: var(--bg-light);
        border-radius: 8px;
        font-size: .8rem;
    }
    .rule-item {
        display: flex;
        align-items: center;
        gap: .4rem;
        color: var(--text-muted);
        margin-bottom: .25rem;
    }
    .rule-item:last-child {
        margin-bottom: 0;
    }
    .rule-item.valid {
        color: #28a745;
    }
    .rule-item.invalid {
        color: #dc3545;
    }
</style>

<div class="card">
    <div class="card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <h2 class="card-title">👥 Cuentas de Administrador</h2>
            <p style="margin:0; color:var(--text-muted); font-size:.9rem;">
                Administradores con acceso al panel de control de TerrenoSur
            </p>
        </div>
        <button onclick="openModal()" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:.5rem;">
            ➕ Agregar Administrador
        </button>
    </div>

    <div class="card-body" style="padding:0;">
        @if($administradores->isEmpty())
            <div style="padding:3rem; text-align:center; color:var(--text-muted);">
                <p style="font-size:2rem;">📭</p>
                <p>No hay administradores registrados.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo electrónico</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Estado Verificación</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($administradores as $admin)
                        <tr>
                            <td><strong>{{ $admin->nombre }}</strong></td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->telefono ?? '—' }}</td>
                            <td><span class="role-badge">🛡️ Admin</span></td>
                            <td>
                                <span style="color:#155724; font-weight: 600;">
                                    ✅ Verificado
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($admin->fecha_registro)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal para Crear Administrador -->
<div class="modal-overlay" id="createAdminModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Agregar Administrador</h3>
            <button onclick="closeModal()" class="modal-close">&times;</button>
        </div>
        <form action="{{ route('admin.administradores.store') }}" method="POST" id="adminForm">
            @csrf
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display:block; margin-bottom:.35rem;">Nombre Completo</label>
                    <input type="text" name="nombre" class="form-control" style="width:100%;" required value="{{ old('nombre') }}">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display:block; margin-bottom:.35rem;">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" style="width:100%;" required value="{{ old('email') }}">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display:block; margin-bottom:.35rem;">Teléfono / Celular</label>
                    <input type="text" name="telefono" class="form-control" style="width:100%;" value="{{ old('telefono') }}">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display:block; margin-bottom:.35rem;">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" style="width:100%;" required>
                    
                    {{-- Validaciones de seguridad en tiempo real --}}
                    <div class="validation-rules">
                        <div class="rule-item" id="rule-length">
                            <span class="status-icon">⚪</span> Mínimo 8 caracteres
                        </div>
                        <div class="rule-item" id="rule-case">
                            <span class="status-icon">⚪</span> Mayúscula y minúscula
                        </div>
                        <div class="rule-item" id="rule-number">
                            <span class="status-icon">⚪</span> Al menos un número
                        </div>
                        <div class="rule-item" id="rule-symbol">
                            <span class="status-icon">⚪</span> Al menos un símbolo (ej. @, $, !, %, *, #, ?, &)
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" style="display:block; margin-bottom:.35rem;">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" style="width:100%;" required>
                    <span id="password-match-error" style="color:#dc3545; font-size:.8rem; display:none;">Las contraseñas no coinciden.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Guardar Administrador</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal() {
        const modal = document.getElementById('createAdminModal');
        modal.classList.add('active');
    }

    function closeModal() {
        const modal = document.getElementById('createAdminModal');
        modal.classList.remove('active');
    }

    // Cerrar si se hace click afuera del modal box
    document.getElementById('createAdminModal').addEventListener('click', function(e) {
        if(e.target === this) {
            closeModal();
        }
    });

    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submitBtn');
    const matchError = document.getElementById('password-match-error');

    const rules = {
        length: { reg: /.{8,}/, el: document.getElementById('rule-length') },
        case: { reg: /(?=.*[a-z])(?=.*[A-Z])/, el: document.getElementById('rule-case') },
        number: { reg: /(?=.*\d)/, el: document.getElementById('rule-number') },
        symbol: { reg: /(?=.*[@$!%*#?&])/, el: document.getElementById('rule-symbol') }
    };

    function validatePassword() {
        const val = passwordField.value;
        let allValid = true;

        for (const [key, rule] of Object.entries(rules)) {
            const isValid = rule.reg.test(val);
            if (isValid) {
                rule.el.classList.remove('invalid');
                rule.el.classList.add('valid');
                rule.el.querySelector('.status-icon').textContent = '✅';
            } else {
                rule.el.classList.remove('valid');
                if (val.length > 0) {
                    rule.el.classList.add('invalid');
                    rule.el.querySelector('.status-icon').textContent = '❌';
                } else {
                    rule.el.classList.remove('invalid');
                    rule.el.querySelector('.status-icon').textContent = '⚪';
                }
                allValid = false;
            }
        }

        // Confirmación
        const matches = val === confirmField.value;
        if (confirmField.value.length > 0) {
            if (matches) {
                matchError.style.display = 'none';
            } else {
                matchError.style.display = 'block';
                allValid = false;
            }
        }

        submitBtn.disabled = !allValid;
    }

    passwordField.addEventListener('input', validatePassword);
    confirmField.addEventListener('input', validatePassword);

    // Si hay errores de validación de Laravel, volver a abrir el modal
    @if ($errors->any())
        openModal();
    @endif
</script>
@endpush

@endsection
