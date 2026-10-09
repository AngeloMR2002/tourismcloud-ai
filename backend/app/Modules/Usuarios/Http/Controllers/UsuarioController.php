<?php

namespace App\Modules\Usuarios\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::orderBy('id', 'desc')->paginate(10);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:usuarios',
            'password' => 'required|string|min:8',
            'telefono' => 'nullable|string|max:20',
            'rol'      => ['required', Rule::in(['administrador', 'admin', 'operador_turistico', 'proveedor', 'turista'])],
            'estado'   => ['required', Rule::in(['activo', 'inactivo'])],
        ]);

        $rol = $request->rol === 'admin' ? 'administrador' : $request->rol;

        Usuario::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'rol'      => $rol,
            'estado'   => $request->estado,
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    public function edit(Usuario $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('usuarios')->ignore($usuario->id)],
            'telefono' => 'nullable|string|max:20',
            'rol'      => ['required', Rule::in(['administrador', 'admin', 'operador_turistico', 'proveedor', 'turista'])],
            'estado'   => ['required', Rule::in(['activo', 'inactivo'])],
        ]);

        $data = $request->only(['nombre', 'apellido', 'email', 'rol', 'estado']);
        if ($data['rol'] === 'admin') {
            $data['rol'] = 'administrador';
        }
        
        // Solo actualizamos la contraseña si el administrador escribió una nueva
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado del sistema.');
    }
}