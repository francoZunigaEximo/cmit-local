<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

trait AccessPolicy {

    public function gateAccess($slug)
    {
        Gate::define($slug, function(User $user) use ($slug) {
            foreach ($user->role as $rol) {
                if ($rol->permiso->contains('slug', $slug)) {
                    return true;
                }
            }
            return false;
        });
    }
    
}