<?php

use App\Modules\Actividades\Models\Actividad;
use App\Modules\Destinos\Models\Destino;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

Route::post('/preferencias/idioma', function (Request $request) {
    $validated = $request->validate([
        'locale' => ['required', Rule::in(['es', 'en'])],
    ]);

    $request->session()->put('interface_locale', $validated['locale']);

    return response()->json(['locale' => $validated['locale']])
        ->cookie('tc_locale', $validated['locale'], 60 * 24 * 365, '/', null, $request->isSecure(), false, false, 'lax');
})->name('interface.locale');

Route::get('/api/exchange-rate/pen-usd', function (ExchangeRateService $exchangeRates) {
    $quote = $exchangeRates->latest();

    if ($quote === null) {
        return response()->json([
            'message' => __('ui.rate_unavailable'),
        ], 503);
    }

    return response()->json([
        'base' => 'USD',
        'quote' => 'PEN',
        'rate' => $quote['rate'],
        'as_of' => $quote['as_of'],
        'stale' => $quote['stale'],
        'source' => 'BCRP / SBS, serie PD04640PD (venta)',
    ]);
})->name('exchange-rate.pen-usd');

// ─── Página de inicio ─────────────────────────────────────────────────────────
Route::get('/', function () {
    $destinosActivos = Destino::query()
        ->where('estado', 'activo')
        ->get(['id', 'nombre'])
        ->keyBy(fn (Destino $destino) => mb_strtolower($destino->nombre));

    $ciudadesDestacadas = collect([
        ['nombre' => 'Lima', 'region' => __('home.lima_region'), 'resumen' => __('home.lima_summary'), 'imagen' => 'images/destinations/lima.jpg'],
        ['nombre' => 'Cusco', 'region' => __('home.cusco_region'), 'resumen' => __('home.cusco_summary'), 'imagen' => 'images/destinations/cusco.jpg'],
        ['nombre' => 'Arequipa', 'region' => __('home.arequipa_region'), 'resumen' => __('home.arequipa_summary'), 'imagen' => 'images/home-hero/arequipa-atardecer.png'],
        ['nombre' => 'Puno', 'region' => __('home.puno_region'), 'resumen' => __('home.puno_summary'), 'imagen' => 'images/destinations/puno.jpg'],
        ['nombre' => 'Trujillo', 'region' => __('home.trujillo_region'), 'resumen' => __('home.trujillo_summary'), 'imagen' => 'images/destinations/trujillo.jpg'],
        ['nombre' => 'Iquitos', 'region' => __('home.iquitos_region'), 'resumen' => __('home.iquitos_summary'), 'imagen' => 'images/destinations/iquitos.jpg'],
    ])->map(function (array $ciudad) use ($destinosActivos): array {
        $ciudad['destino_id'] = $destinosActivos->get(mb_strtolower($ciudad['nombre']))?->id;

        return $ciudad;
    });

    $actividadesDestacadas = Actividad::query()
        ->activas()
        ->with('destino:id,nombre,estado')
        ->whereHas('destino', fn ($consulta) => $consulta->where('estado', 'activo'))
        ->latest('updated_at')
        ->limit(6)
        ->get();

    return view('welcome', compact('ciudadesDestacadas', 'actividadesDestacadas'));
})->name('inicio');

// ─── Rutas Globales / Vistas Base ─────────────────────────────────────────────
Route::redirect('/usuarios', '/admin/usuarios')->name('principal.usuarios');
Route::redirect('/organizaciones', '/admin/usuarios')->name('principal.organizaciones');
Route::redirect('/permisos', '/admin/usuarios')->name('principal.permisos');
