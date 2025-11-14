<?php

namespace App\Modules\Inventario\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    /**
     * Muestra el dashboard del módulo de inventario
     */
    public function index()
    {
        return view('inventario::dashboard');
    }
}
