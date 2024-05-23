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

        Gate::define('prestaciones_add', function(User $user) {
            foreach ($user->role as $rol) {
                if ($rol->permiso->contains('slug', 'prestaciones_add')) {
                    return true;
                }
            }
            return false;
        });

        Gate::define('etapas_show', function(User $user) {
            foreach ($user->role as $rol) {
                if ($rol->permiso->contains('slug', 'etapas_show')) {
                    return true;
                }
            }
            return false;
        });

        Gate::define('clientes_add', function(User $user) {
            foreach ($user->role as $rol) {
                if ($rol->permiso->contains('slug', 'clientes_add')) {
                    return true;
                }
            }
            return false;
        });

        Gate::define('boton_prestaciones', function(User $user) {
            foreach ($user->role as $rol) {
                if ($rol->permiso->contains('slug', 'boton_prestaciones')) {
                    return true;
                }
            }
            return false;
        });
        
    }
}
