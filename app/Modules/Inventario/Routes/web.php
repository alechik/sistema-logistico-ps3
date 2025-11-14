<?php

use Illuminate\Support\Facades\Route;

// Ruta principal del módulo de inventario
Route::get('/', 'InventarioController@index')->name('dashboard');

// Aquí puedes agregar más rutas específicas del módulo de inventario
