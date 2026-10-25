<?php

namespace App\Modules\Recomendaciones\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Recomendaciones\Services\RecomendacionService;
use Illuminate\View\View;

class RecomendacionController extends Controller
{
    public function __construct(private readonly RecomendacionService $recomendacionService) {}

    public function index(): View
    {
        $resultado = $this->recomendacionService->paraTurista(auth()->id());

        return view('recomendaciones::index', $resultado);
    }
}
