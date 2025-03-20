<?php

namespace App\Services\Roles;

use App\Models\User;

class Utilidades
{
    public function checkTipoRol(string $usuario, array $tipo)
    {
        return User::join('user_rol', 'users.id', '=', 'user_rol.user_id')
                ->join('roles', 'user_rol.rol_id', '=', 'roles.Id')
                ->join('datos', 'users.datos_id', '=', 'datos.Id')
                ->whereIn('roles.nombre', $tipo)
                ->where('users.name', $usuario)
                ->exists();
    }
}