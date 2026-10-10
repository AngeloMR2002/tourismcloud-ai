<?php

namespace App\Modules\Usuarios\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Mostrar formulario de Login
    public function showLogin()
    {
        return view('usuarios.auth.login');
    }

    // Procesar Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 'estado' => 'activo' bloquea a los usuarios inactivos
        if (Auth::attempt([...$credentials, 'estado' => 'activo'], $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var Usuario $usuario */
            $usuario = Auth::user();

            return redirect()->intended($usuario->homeUrl());
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    // Mostrar formulario de Registro
    public function showRegister()
    {
        return view('usuarios.auth.register');
    }

    // Procesar Registro
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:usuarios'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'turista', // Por defecto los que se registran por la web son turistas
            'estado' => 'activo'
        ]);

        Auth::login($usuario);

        return redirect($usuario->homeUrl());
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}