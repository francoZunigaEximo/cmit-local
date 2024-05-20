<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Rol;

class RolesController extends Controller
{
    public function listado(Request $request)
    {
        $buscar = $request->buscar;

        $resultados = Cache::remember('Rol_'.$buscar, 5, function () use ($buscar) {

            $roles = Rol::where('nombre', 'LIKE', '%'.$buscar.'%')->get();

            $resultados = [];

            foreach ($roles as $rol) {
                $resultados[] = [
                    'id' => $rol->Id,
                    'text' => $rol->nombre,
                ];
            }

            return $resultados;

        });

        return response()->json(['result' => $resultados]);
    }

    public function asignados(Request $request)
    {
        return Rol::join('user_rol', 'roles.Id', '=', 'user_rol.rol_id')
            ->join('users', 'user_rol.user_id', '=', 'users.Id')
            ->select(
                'roles.nombre as Nombre',
                'roles.descripcion as Descripcion',
                'roles.Id as IdRol'
            )
            ->where('users.id', $request->Id)
            ->get();

        
    }
}
