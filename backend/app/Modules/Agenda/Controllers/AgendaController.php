<?php

namespace App\Modules\Agenda\Controllers;

use Illuminate\Routing\Controller;

class AgendaController extends Controller
{
    public function index()
    {
        $path = base_path('app/Modules/Agenda/Views/index.blade.php');
return view()->file($path);
    }
}