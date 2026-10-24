<?php

namespace App\Modules\Usuarios\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Usuarios\Models\Favorito;
use App\Modules\Usuarios\Services\FavoritoCatalogo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FavoritoController extends Controller
{
    public function __construct(private FavoritoCatalogo $catalogo) {}

    public function index(Request $request): View
    {
        $this->usuarioActivo($request);
        $datos = $request->validate(['tipo' => ['nullable', Rule::in(FavoritoCatalogo::TIPOS)]]);
        $favoritos = Favorito::query()->where('usuario_id', $request->user()->id)
            ->when($datos['tipo'] ?? null, fn (Builder $q, string $tipo) => $q->where('tipo', $tipo))
            ->where(function (Builder $query): void {
                foreach (FavoritoCatalogo::TIPOS as $tipo) {
                    $visible = $this->catalogo->consulta($tipo);
                    $query->orWhere(fn (Builder $q) => $q->where('tipo', $tipo)
                        ->whereIn('recurso_id', $visible->select($visible->getModel()->qualifyColumn('id'))->reorder()));
                }
            })->latest('id')->paginate(12)->withQueryString();

        foreach ($favoritos->getCollection()->groupBy('tipo') as $tipo => $grupo) {
            $recursos = $this->catalogo->consulta($tipo)->whereKey($grupo->pluck('recurso_id'))->get()->keyBy('id');
            foreach ($grupo as $favorito) {
                $favorito->setRelation('recurso', $recursos->get($favorito->recurso_id));
            }
        }

        return view('usuarios::favoritos.index', compact('favoritos'));
    }

    public function show(Request $request, string $tipo, int $id): View
    {
        $this->usuarioActivo($request);
        $recurso = $this->catalogo->consulta($tipo)->findOrFail($id);

        return view('usuarios::favoritos.confirmar', compact('tipo', 'recurso'));
    }

    public function store(Request $request, string $tipo, int $id): JsonResponse|RedirectResponse
    {
        $this->usuarioActivo($request);
        $this->catalogo->consulta($tipo)->findOrFail($id);
        Favorito::firstOrCreate(['usuario_id' => $request->user()->id, 'tipo' => $tipo, 'recurso_id' => $id]);

        return $request->expectsJson()
            ? response()->json(['saved' => true, 'message' => __('favorites.saved')])
            : redirect()->route('favoritos.index')->with('exito', __('favorites.saved'));
    }

    public function destroy(Request $request, string $tipo, int $id): JsonResponse|RedirectResponse
    {
        $this->usuarioActivo($request);
        abort_unless(in_array($tipo, FavoritoCatalogo::TIPOS, true), 404);
        Favorito::where('usuario_id', $request->user()->id)->where('tipo', $tipo)->where('recurso_id', $id)->delete();

        return $request->expectsJson()
            ? response()->json(['saved' => false, 'message' => __('favorites.removed')])
            : redirect()->route('favoritos.index')->with('exito', __('favorites.removed'));
    }

    private function usuarioActivo(Request $request): void
    {
        abort_unless($request->user()?->estado === 'activo', 403);
    }
}
