<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AdminLTEServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publicar assets de AdminLTE
        $this->publishes([
            __DIR__.'/../../../public/adminlte' => public_path('adminlte'),
        ], 'adminlte');

        // Registrar componentes de Blade personalizados
        $this->registerComponents();
    }

    /**
     * Registrar componentes de Blade personalizados
     */
    protected function registerComponents(): void
    {
        // Componente de alerta
        Blade::component('components.alert', 'alert');
        
        // Componente de tarjeta
        Blade::component('components.card', 'card');
        
        // Componente de modal
        Blade::component('components.modal', 'modal');
    }
}
