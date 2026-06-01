<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegistroController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // ── Sanitización básica de inputs ──────────────────────────────────
        $request->merge([
            'nombre'   => strip_tags(trim($request->nombre ?? '')),
            'email'    => strtolower(trim($request->email ?? '')),
            'telefono' => strip_tags(trim($request->telefono ?? '')),
        ]);

        // ── Validación robusta ─────────────────────────────────────────────
        $validator = Validator::make($request->all(), [
            'nombre'   => 'required|string|max:100|regex:/^[\pL\s\-\'\.]+$/u',
            'email'    => [
                'required',
                'string',
                'email:rfc,dns',
                'max:150',
                'unique:usuarios,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'telefono' => 'nullable|string|max:20|regex:/^[\d\+\-\s\(\)]*$/',
        ], [
            // Nombre
            'nombre.required'    => 'El nombre completo es obligatorio.',
            'nombre.max'         => 'El nombre no puede exceder 100 caracteres.',
            'nombre.regex'       => 'El nombre solo puede contener letras y espacios.',
            // Email
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'El formato del correo no es válido.',
            'email.unique'       => 'Este correo ya está registrado en el sistema.',
            'email.max'          => 'El correo no puede exceder 150 caracteres.',
            // Contraseña
            'password.required'  => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            // Teléfono
            'telefono.regex'     => 'El teléfono solo puede contener números, +, - y paréntesis.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        // ── Crear usuario ──────────────────────────────────────────────────
        $usuario = Usuario::create([
            'nombre'              => $request->nombre,
            'email'               => $request->email,
            'password'            => Hash::make($request->password),
            'telefono'            => $request->telefono ?: null,
            'rol'                 => 'comprador',
            'estado_verificacion' => 'verificado',
            'activo'              => true,
        ]);

        // ── Disparar evento Registered (activa verificación de correo) ─────
        event(new Registered($usuario));

        return redirect()->route('login')
            ->with('success', '¡Cuenta creada! Revisa tu correo para verificar tu cuenta antes de iniciar sesión.');
    }
}