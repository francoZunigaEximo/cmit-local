<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Rol;
use App\Models\User;

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
                'user_rol.rol_id as IdRol',
                'user_rol.user_id as IdUser'
            )
            ->where('users.id', $request->Id)
            ->get();  
    }

    public function add(Request $request)
    {
        $user = User::find($request->user); 
        $role = Rol::find($request->role); 
        $result = [];

        if ($user->role->contains($request->role))
        {
            $result = ['msg' => 'El usuario ya tiene ese rol asignado', 'estado' => 'false'];
            
        }else{
            $user->role()->attach($role); 
            $result = ['msg' => 'Se ha asignado el rol al usuario correctamente', 'estado' => 'true'];
        }

        return response()->json($result);
        
    }

    public function delete(Request $request)
    {
        $user = User::find($request->user); 
        $role = Rol::find($request->role); 
        $user->role()->detach($role);

    }


}
