<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
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

    public function create()
    {
        return view('layouts.usuarios.create');
    }

    public function edit(User $usuario)
    {
        $provincias = Provincia::all();

        $query = User::join('datos', 'users.datos_id', '=', 'datos.Id')
            ->join('localidades', 'datos.IdLocalidad', '=', 'localidades.Id')
            ->select(
                "users.id as UserId",
                "users.name as Name",
                "users.email as EMail",
                "users.inactivo as Inactivo",
                "datos.Id as IdDatos",
                "datos.Telefono as Telefono",
                "datos.TipoIdentificacion as TipoIdentificacion",
                "datos.Identificacion as Identificacion",
                "datos.TipoDocumento as TipoDocumento",
                "datos.Documento as Documento",
                "datos.Nombre as Nombre",
                "datos.Apellido as Apellido",
                "datos.FechaNacimiento as FechaNacimiento",
                "datos.Direccion as Direccion",
                "datos.IdLocalidad as ILocalidad",
                "datos.Provincia as Provincia",
                "datos.CP as CP",
                "datos.Id as Id",
                "localidades.Nombre as NombreLocalidad"
            )->find($usuario->id);

        return view('layouts.usuarios.edit', compact(['query', 'provincias']));
    }

    public function buscar(Request $request) 
    {
        if ($request->ajax()) {

            $query = User::leftJoin('user_rol', 'users.id', '=', 'user_rol.user_id')
                ->leftJoin('roles', 'user_rol.rol_id', '=', 'roles.Id')
                ->leftJoin('datos', 'users.datos_id', '=', 'datos.Id')
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

    public function NombreUsuario(Request $request): mixed
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

    public function Usuario(Request $request): mixed
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

    public function checkUsuario(Request $request): mixed
    {
        $query = User::where('name', $request->usuario)->doesntExist();;
        return response()->json($query);       
    }

    public function checkCorreo(Request $request): mixed
    {
        $query = User::where('email', $request->email)->doesntExist();;
        return response()->json($query);         
    }

    public function checkEmailUpdate(Request $request)
    {
        $response = [];

        $verificar = User::where('email', $request->email)->first();

        if ($verificar) {
            $response = ['msg' => 'El correo ya está en uso por otro usuario.', 'estado' => 'false', 'correo' => $request->email];
        }else{
            $response = ['msg' => 'El correo se encuentra disponible.', 'estado' => 'true', 'correo' => $request->email];
        }

        return response()->json($response);
    }

}

