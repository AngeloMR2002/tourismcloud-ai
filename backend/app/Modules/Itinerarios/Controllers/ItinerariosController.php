<?php

namespace App\Modules\Itinerarios\Controllers;

use Illuminate\Routing\Controller;

class ItinerariosController extends Controller
{
    public function index()
    {
        $path = base_path('app/Modules/Itinerarios/Views/index.blade.php');
return view()->file($path);
    }
}