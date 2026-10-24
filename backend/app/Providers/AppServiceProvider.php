<?php

namespace App\Providers;

use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Establecimientos\Models\Establecimiento;
use App\Modules\Usuarios\Models\Favorito;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.favorite-button', function ($view): void {
            $request = request();
            $key = 'catalog_favorite_keys';
            if (! $request->attributes->has($key)) {
                $keys = $request->user()
                    ? Favorito::where('usuario_id', $request->user()->id)
                        ->get(['tipo', 'recurso_id'])->mapWithKeys(fn ($item) => [$item->tipo.':'.$item->recurso_id => true])->all()
                    : [];
                $request->attributes->set($key, $keys);
            }
            $view->with('favoriteKeys', $request->attributes->get($key));
        });
        // Permite a Laravel buscar plantillas en los directorios Resources/views de cada módulo
        // para que llamadas como view('usuarios.auth.login') o view('destinos.index') funcionen directamente.
        if (is_dir(app_path('Modules'))) {
            foreach (glob(app_path('Modules/*/Resources/views')) as $moduleViews) {
                View::addLocation($moduleViews);
            }
        }

        // Mapeo polimórfico limpio para tablas compartidas (valoraciones, etc.)
        Relation::morphMap([
            'atractivo' => Atractivo::class,
            'establecimiento' => Establecimiento::class,
        ]);
    }
}
