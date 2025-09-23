<?php

namespace App\Services\Llamador;

use App\Models\Llamador;
use App\Models\Profesional;
use App\Models\User;
use App\Models\UserSession;
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
                ->where('users.inactivo', 0)
                ->get();
    }

    public function listadoAdmin()
    {
        return User::join('user_rol', 'users.id', '=', 'user_rol.user_id')
                ->join('roles', 'user_rol.rol_id', '=', 'roles.Id')
                ->join('datos', 'users.datos_id', '=', 'datos.Id')
                ->select(
                    'users.profesional_id as Id',
                    DB::raw("CONCAT(datos.Apellido,' ',datos.Nombre) as NombreCompleto"),
                    'users.name as usuario',   
                    )
                ->whereIn('roles.nombre', ['Administrador', 'Admin SR', 'Recepcion SR'])
                ->where('users.inactivo', 0)
                ->get();
    }

    public function listadoOnline()
    {
        return UserSession::whereNull('logout_at')->get();
    }

    public function desocuparPaciente(int $idUsuario)
    {
        $query = User::find($idUsuario, ['profesional_id']);
        $ids = [];

        if($query) {
            $llamados = Llamador::whereIn('profesional_id', $query)->get();

            foreach($llamados as $paciente) {
                 array_push($ids, $paciente->prestacion_id);
                $paciente->delete();
            }  
        }
        return $ids;
    }

    public function getProfesional(int $idProfesional)
    {
        return User::join('datos', 'users.datos_id', '=', 'datos.Id')
            ->where('profesional_id', $idProfesional)
            ->select(
                DB::raw("CONCAT(datos.Apellido,' ',datos.Nombre) as NombreCompleto"),
            )
            ->first();
    }

}