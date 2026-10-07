<?php

namespace App\Modules\Actividades\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Modules\Actividades\Models\Actividad;
use App\Models\Atractivo;
use App\Modules\Rutas\Models\Ruta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RevisionController extends Controller
{
    public function index(): View
    {
        $pendientes = fn ($q) => $q->where('estado_verificacion', '!=', 'aprobado')
            ->orWhereNull('fecha_verificacion')
            ->orWhere('fecha_verificacion', '<', now()->subDays(config('tourism.verification_days')));
        $actividades = Actividad::with('destino')->where($pendientes)->latest()->paginate(15, ['*'], 'actividades_page');
        $rutas = Ruta::with(['destino', 'puntos.atractivo'])->where($pendientes)->latest()->paginate(15, ['*'], 'rutas_page');

        return view('actividades::operador.revision.index', compact('actividades', 'rutas'));
    }

    public function aprobar(Request $request, string $tipo, int $id): RedirectResponse
    {
        $request->validate(['confirmado' => ['accepted']]);
        $registro = $tipo === 'actividades' ? Actividad::with('atractivo')->findOrFail($id)
            : Ruta::with('puntos.atractivo')->findOrFail($id);
        $precio = $registro instanceof Actividad ? $registro->precio : $registro->costo_estimado;
        $data = $registro->getAttributes();
        $data['importe'] = $precio;
        Validator::make($data, [
            'fuente_url' => ['required', 'url:http,https'],
            'imagen_portada' => ['nullable', 'url:http,https'],
            'imagen_fuente_url' => ['required_with:imagen_portada', 'nullable', 'url:http,https'],
            'imagen_autor' => ['required_with:imagen_portada', 'nullable', 'string'],
            'imagen_licencia' => ['required_with:imagen_portada', 'nullable', 'string'],
            'precio_fuente_url' => ['required_with:importe', 'nullable', 'url:http,https'],
            'precio_verificado_en' => ['required_with:importe', 'nullable', 'date', 'before_or_equal:now',
                'after_or_equal:'.now()->subDays(config('tourism.price_days'))->toDateString()],
        ])->validate();
        if ($registro instanceof Actividad && $registro->atractivo_id) {
            abort_unless($registro->atractivo && Atractivo::activos()->aprobados()->whereKey($registro->atractivo_id)->exists()
                && $registro->atractivo->destino_id === $registro->destino_id, 422, 'Revisa primero el atractivo.');
        }
        if ($registro instanceof Ruta) {
            abort_if($registro->puntos->count() < 2, 422, 'La ruta necesita al menos dos paradas.');
            foreach ($registro->puntos as $punto) {
                if ($punto->atractivo_id) {
                    abort_unless($punto->atractivo && Atractivo::activos()->aprobados()->whereKey($punto->atractivo_id)->exists()
                        && $punto->atractivo->destino_id === $registro->destino_id, 422, 'Revisa primero los atractivos de la ruta.');
                }
            }
        }
        $registro->update(['estado_verificacion' => 'aprobado', 'fecha_verificacion' => now()]);

        return back()->with('exito', 'Registro aprobado. Se publicará si está activo y su verificación está vigente.');
    }
}
