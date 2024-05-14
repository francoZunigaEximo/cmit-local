<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class UsuariosController extends Controller
{
    public function index()
    {
        return view('layouts.usuarios.index');
    }

    public function buscar(Request $request) 
    {
        if ($request->ajax()) {

            $query = User::join('user_rol', 'users.id', '=', 'user_rol.user_id')
                ->join('roles', 'user_rol.rol_id', '=', 'roles.Id')
                ->join('datos', 'users.datos_id', '=', 'datos.Id')
                ->select(
                    'users.id as IdUser',
                    'users.name as usuario',
                    'datos.Nombre as Nombre',
                    'datos.Apellido as Apellido',
                    DB::raw("GROUP_CONCAT(roles.nombre SEPARATOR ', ') as RolUsuario"),
                    'users.inactivo as Inactivo',
                )
                ->groupBy('users.id');
        
            $query->when(!empty($request->nombre), function ($query) use ($request) {
                $query->where('datos.Id', $request->nombre);
            });
        
            $query->when(!empty($request->usuario), function ($query) use ($request){
                $query->where('users.id', $request->usuario);
            });
               
            $query->when(!empty($request->rol), function ($query) use ($request) {
                $query->where('roles.Id', $request->rol);
            });
        
            $result = $query->get();
        
            return Datatables::of($result)->make(true);
        }        

        return view('layouts.prestaciones.index');
    }

    public function NombreUsuario(Request $request)
    {
        $buscar = $request->buscar;

        $resultados = Cache::remember('Nombre_usuario_'.$buscar, 5, function () use ($buscar) {

            $nombreApellidos = User::join('datos', 'users.datos_id', '=', 'datos.Id')
                ->whereRaw("CONCAT(datos.Apellido, ' ', datos.Nombre) LIKE ?", ['%'.$buscar.'%'])
                ->orWhere('datos.Apellido', 'LIKE', '%'.$buscar.'%')
                ->orWhere('datos.Nombre', 'LIKE', '%'.$buscar.'%')
                ->get();

            $resultados = [];

            foreach ($nombreApellidos as $nombre) {
                $resultados[] = [
                    'id' => $nombre->Id,
                    'text' => $nombre->Apellido . ' ' .$nombre->Nombre,
                ];
            }

            return $resultados;

        });

        return response()->json(['result' => $resultados]);
    }

    public function Usuario(Request $request)
    {
        $buscar = $request->buscar;

        $resultados = Cache::remember('Usuario_'.$buscar, 5, function () use ($buscar) {

            $usuarios = User::where('name', 'LIKE', '%'.$buscar.'%')->get();

            $resultados = [];

            foreach ($usuarios as $usuario) {
                $resultados[] = [
                    'id' => $usuario->id,
                    'text' => $usuario->name,
                ];
            }

            return $resultados;

        });

        return response()->json(['result' => $resultados]);
    }

}

