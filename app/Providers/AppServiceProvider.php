<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

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
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '256M');

        Gate::define('boton_usuarios', function(User $user) {
        
            foreach ($user->role as $rol) {
                if ($rol->permiso->contains('slug', 'boton_usuarios')) {
                    return true;
                }
            }
        
            return false;
        });

        Gate::define('boton_usuarios', function(User $user) {
        
            foreach ($user->role as $rol) {
                if ($rol->permiso->contains('slug', 'boton_usuarios')) {
                    return true;
                }
            }
        
            return false;
        });
        
    }
}
