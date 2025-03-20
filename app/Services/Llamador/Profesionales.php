<?php

namespace App\Services\Llamador;

use App\Models\Profesional;
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
                    DB::raw("CONCAT(datos.Apellido,' ',datos.Nombre) as NombreCompleto"),
                    'users.name as usuario',
                    
                    )
                ->where('roles.nombre', $tipo)
                ->get();
    }

    public function getNombreCompleto(int $id)
    {
        return Profesional::join('users', 'profesionales.Id', '=', 'users.profesional_id')
                        ->join('datos', 'users.datos_id', '=', 'datos.Id')
                        ->select(
                            DB::raw("CONCAT(datos.Nombre.' '.datos.Apellido) as NombreCompleto")
                        )
                        ->where('profesionales.Id', $id)
                        ->first();
    }
}