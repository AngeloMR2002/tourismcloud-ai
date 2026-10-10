<?php

namespace App\Modules\Usuarios\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    private const ROLES   = ['administrador', 'operador_turistico', 'proveedor', 'turista'];
    private const ESTADOS = ['activo', 'inactivo'];

    public function index(Request $request)
    {
        $usuarios = Usuario::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%' . $request->q . '%';
                $query->where(fn ($w) => $w
                    ->where('nombre', 'ilike', $q)
                    ->orWhere('apellido', 'ilike', $q)
                    ->orWhere('email', 'ilike', $q));
            })
            ->when($request->filled('rol'), fn ($query) => $query->where('rol', $request->rol))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->estado))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    public function show(Usuario $usuario)
    {
        $organizacion = $usuario->organizacion_id
            ? DB::table('organizaciones')->where('id', $usuario->organizacion_id)->value('nombre')
            : null;

        return view('usuarios.show', compact('usuario', 'organizacion'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:usuarios'],
            'password' => ['required', 'string', 'min:8'],
            'rol'      => ['required', Rule::in(self::ROLES)],
            'estado'   => ['required', Rule::in(self::ESTADOS)],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        Usuario::create($validated);

        return redirect()->route('admin.usuarios.index')->with('exito', 'Usuario registrado correctamente.');
    }

    public function edit(Usuario $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $validated = $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:150', Rule::unique('usuarios')->ignore($usuario->id)],
            'rol'      => ['required', Rule::in(self::ROLES)],
            'estado'   => ['required', Rule::in(self::ESTADOS)],
        ]);

        // El admin no puede quitarse a sí mismo el acceso
        if ($usuario->id === Auth::id()
            && ($validated['rol'] !== $usuario->rol || $validated['estado'] !== 'activo')) {
            return back()->withInput()
                ->with('error', 'No puedes cambiar tu propio rol ni desactivar tu propia cuenta.');
        }

        $usuario->update($validated);

        return redirect()->route('admin.usuarios.index')->with('exito', 'Usuario actualizado correctamente.');
    }

    public function toggleEstado(Usuario $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->update([
            'estado' => $usuario->estado === 'activo' ? 'inactivo' : 'activo',
        ]);

        return back()->with('exito', $usuario->estado === 'activo' ? 'Usuario activado.' : 'Usuario desactivado.');
    }

    public function destroy(Usuario $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('exito', 'Usuario eliminado del sistema.');
    }
}