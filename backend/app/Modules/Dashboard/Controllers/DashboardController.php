<?php

namespace App\Modules\Dashboard\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $path = base_path('app/Modules/Dashboard/Views/index.blade.php');

        return view()->file($path);
    }
}