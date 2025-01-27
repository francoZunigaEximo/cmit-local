<?php

namespace App\Services\Llamador;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class Profesionales
{
    public function listado($tipo)
    {
        return User::join('user_rol', 'users.id', '=', 'user_rol.user_id')
                ->join('roles', 'user_rol.rol_id', '=', 'roles.Id')
                ->join('datos', 'users.datos_id', '=', 'datos.Id')
                ->select(
                    'users.profesional_id as Id',
                    DB::raw("CONCAT(datos.Apellido,' ',datos.Nombre) as NombreCompleto")
                    )
                ->where('roles.nombre', $tipo)
                ->get();
    }
}