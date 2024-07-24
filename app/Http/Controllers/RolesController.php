<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
            ->join('rol_permisos', 'roles.Id', '=', 'rol_permisos.rol_id')
            ->join('permisos', 'rol_permisos.permiso_id', '=', 'permisos.Id')
            ->select(
                'roles.nombre as Nombre',
                DB::raw('GROUP_CONCAT(permisos.Descripcion SEPARATOR ", ") as Descripcion'),
                'user_rol.rol_id as IdRol',
                'user_rol.user_id as IdUser'
            )
            ->where('users.Id', $request->Id)
            ->groupBy(['IdRol', 'Nombre']) 
            ->get();   
    }


    public function add(Request $request)
    {
        $user = User::find($request->user); 
        $role = Rol::find($request->role);
        $contadorPermiso = $role->permiso->count();
        $result = [];

        if($contadorPermiso === 0) {
            return response()->json(['msg' => 'No se puede asignar el rol porque no tiene permisos asociados'], 409);
        }

        if($user->role->contains($request->role))
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

        if($user && $role){
            $user->role()->detach($role);
            return response()->json(['msg' => 'Se ha eliminado el rol del usuario correctamente'], 200);

        }

        

    }


}
