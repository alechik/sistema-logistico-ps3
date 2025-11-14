<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Product Permissions
        Gate::define('products.index', function ($user) {
            return $user->hasPermission('products.index');
        });

        Gate::define('products.create', function ($user) {
            return $user->hasPermission('products.create');
        });

        Gate::define('products.edit', function ($user) {
            return $user->hasPermission('products.edit');
        });

        Gate::define('products.destroy', function ($user) {
            return $user->hasPermission('products.destroy');
        });

        // Add more permission checks as needed for other modules
    }
}
