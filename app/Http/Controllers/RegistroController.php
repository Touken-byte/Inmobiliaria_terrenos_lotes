<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegistroController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'   => 'required|string|max:100',
            'email'    => 'required|email|unique:usuarios,email|max:150',
            'password' => 'required|string|min:6|confirmed',
            'telefono' => 'nullable|string|max:20',
        ], [
            'nombre.required'   => 'El nombre es obligatorio.',
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'El formato del correo no es válido.',
            'email.unique'      => 'Este correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed'=> 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Usuario::create([
            'nombre'               => $request->nombre,
            'email'                => strtolower(trim($request->email)),
            'password'             => Hash::make($request->password),
            'telefono'             => $request->telefono,
            'rol'                  => 'comprador',
            'estado_verificacion'  => 'verificado',
            'activo'               => true,
        ]);

        return redirect()->route('login')
            ->with('success', '¡Cuenta creada exitosamente! Ya puedes iniciar sesión.');
    }
}