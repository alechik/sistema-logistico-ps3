<?php

namespace App\Modules\Inventario\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class InventarioServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar vistas
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'inventario');
        
        // Registrar rutas
        $this->registerRoutes();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publicar archivos de configuración
        $this->publishes([
            __DIR__.'/../../config/inventario.php' => config_path('inventario.php'),
        ], 'config');
        
        // Cargar migraciones
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }

    /**
     * Registrar las rutas del módulo
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => 'inventario',
            'middleware' => ['web', 'auth'],
            'namespace' => 'App\\Modules\\Inventario\\Http\\Controllers',
            'as' => 'inventario.'
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        });
    }
}
