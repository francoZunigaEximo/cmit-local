<?php

namespace App\Services\Llamador;

use App\Models\Profesional;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Support\Facades\DB;

class Profesionales
{
    public function listado($tipo)
    {
        return User::join('user_rol', 'users.id', '=', 'user_rol.user_id')
                ->join('user_sessions', 'users.id', '=', 'user_sessions.user_id')
                ->join('roles', 'user_rol.rol_id', '=', 'roles.Id')
                ->join('datos', 'users.datos_id', '=', 'datos.Id')
                ->select(
                    'users.profesional_id as Id',
                    DB::raw("CONCAT(datos.Apellido,' ',datos.Nombre) as NombreCompleto"),
                    'users.name as usuario',
                    
                    )
                ->where('roles.nombre', $tipo)
                ->whereNull('user_sessions.logout_at')
                ->get();
    }

    public function listadoOnline()
    {
        return UserSession::whereNull('logout_at')->get();
    }

}