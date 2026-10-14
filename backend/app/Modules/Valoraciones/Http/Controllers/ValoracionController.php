<?php

namespace App\Modules\Valoraciones\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Valoraciones\Http\Requests\ValoracionRequest;
use App\Modules\Valoraciones\Models\Valoracion;
use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ValoracionController extends Controller
{
    /**
     * Registra o actualiza una valoración polimórfica (Idempotente y Anti-Spam).
     *
     * Si el usuario ya había valorado el recurso, se actualiza;
     * de lo contrario, se crea una nueva reseña.
     */
    public function store(ValoracionRequest $request): RedirectResponse
    {
        $tipo = $request->validated('valorable_type');
        $id   = (int) $request->validated('valorable_id');

        // Validar que el modelo polimórfico destino realmente exista
        $modelClass = Relation::getMorphedModel($tipo);
        if (!$modelClass || !$modelClass::where('id', $id)->exists()) {
            return back()->with('error', 'El lugar o establecimiento al que intentas calificar no existe.');
        }

        $comentario = $request->validated('comentario');

        $valoracion = Valoracion::updateOrCreate(
            [
                'usuario_id'     => Auth::id(),
                'valorable_type' => $tipo,
                'valorable_id'   => $id,
            ],
            [
                'puntuacion' => (int) $request->validated('puntuacion'),
                'comentario' => $comentario ? trim($comentario) : null,
            ]
        );

        $mensaje = $valoracion->wasRecentlyCreated
            ? '¡Gracias! Tu reseña ha sido publicada con éxito.'
            : 'Tu reseña ha sido actualizada con éxito.';

        return back()->with('exito', $mensaje);
    }

    /**
     * Permite al usuario eliminar su propia reseña.
     */
    public function destroy(Valoracion $valoracion): RedirectResponse
    {
        /** @var Usuario|null $user */
        $user = Auth::user();

        // Solo el autor o un administrador pueden eliminar la valoración
        if (!$user || ($user->id !== $valoracion->usuario_id && !$user->isAdmin())) {
            abort(403, 'No tienes permiso para eliminar esta valoración.');
        }

        $valoracion->delete();

        return back()->with('exito', 'Tu reseña ha sido eliminada correctamente.');
    }
}
