<?php

namespace App\Modules\Destinos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Destinos\Http\Requests\DestinoRequest;
use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Http\Request;

class DestinoController extends Controller
{
    public function index()
    {
        $destinos = Destino::with('operador')->orderBy('created_at', 'desc')->paginate(10);
        return view('destinos.index', compact('destinos'));
    }

    public function create()
    {
        // Traemos solo a los usuarios que son operadores turísticos para asignarlos
        $operadores = Usuario::where('rol', 'operador_turistico')->where('estado', 'activo')->get();
        return view('destinos.create', compact('operadores'));
    }

    public function store(DestinoRequest $request)
    {
        Destino::create($request->validated());
        return redirect()->route('admin.destinos.index')->with('exito', 'Destino creado correctamente.');
    }

    public function edit(Destino $destino)
    {
        $operadores = Usuario::where('rol', 'operador_turistico')->where('estado', 'activo')->get();
        return view('destinos.edit', compact('destino', 'operadores'));
    }

    public function update(DestinoRequest $request, Destino $destino)
    {
        $destino->update($request->validated());
        return redirect()->route('admin.destinos.index')->with('exito', 'Destino actualizado correctamente.');
    }

    public function toggleEstado(Destino $destino)
    {
        $destino->estado = $destino->estado === 'activo' ? 'inactivo' : 'activo';
        $destino->save();
        
        return back()->with('exito', 'Estado del destino actualizado.');
    }
}