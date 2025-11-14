<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Only run this if we're not in a console command (i.e., not during migrations)
        if (!app()->runningInConsole()) {
            // Create super user if no users exist
            if (app()->environment('local', 'production')) {
                try {
                    \App\Services\UserService::createSuperUserIfNotExists();
                } catch (\Exception $e) {
                    // Log the error but don't crash the application
                    \Log::error('Error creating super user: ' . $e->getMessage());
                }
            }
        }
    }
}
