<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Event;

class MenuServiceProvider extends ServiceProvider
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
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Cargar el menú personalizado desde el archivo de configuración
            $menu = config('custom_menu.menu', []);
            
            // Agregar cada ítem del menú al constructor de menú de AdminLTE
            foreach ($menu as $item) {
                $event->menu->add($item);
            }
        });
    }
}
