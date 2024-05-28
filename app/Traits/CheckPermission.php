<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait CheckPermission
{
    public function hasPermission($permissionSlug)
    {
        $hasPermission = false;

        foreach (Auth::user()->role as $role) {
            if ($role->permiso->contains('slug', $permissionSlug)) {
                $hasPermission = true;
                break;
            }
        }

        return $hasPermission;
    }
}
