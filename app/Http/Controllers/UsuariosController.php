<?php

namespace App\Http\Controllers;

use App\Models\Profesional;
use App\Models\Proveedor;
use App\Models\Provincia;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Traits\CheckPermission;
use Illuminate\Support\Facades\Storage;

class UsuariosController extends Controller
{
    const password = "cmit1234";
    use CheckPermission;

    public function index()
    {
        if(!$this->hasPermission("usuarios_show")) {
            abort(403);
        } 

        return view('layouts.usuarios.index');
    }

    public function create()
    {
        if(!$this->hasPermission("usuarios_add")){
            abort(403);
        }

        return view('layouts.usuarios.create');
    }

    public function edit(User $usuario)
    {
        if(!$this->hasPermission("usuarios_edit")){
            abort(403);
        }

        $lstRoles = ['Efector', 'Informador', 'Evaluador', 'EvaluadorART'];

        $provincias = Provincia::all();

        $roles = Rol::orderBy('nombre', 'asc')->get();

        $lstProveedor = Proveedor::where('Inactivo', 0)->whereNot('Id', 0)->OrderBy('Nombre', 'ASC')->get(['Id', 'Nombre']);

        $query = User::join('datos', 'users.datos_id', '=', 'datos.Id')
            ->join('localidades', 'datos.IdLocalidad', '=', 'localidades.Id')
            ->join('profesionales', 'users.profesional_id', '=', 'profesionales.Id')
            ->leftJoin('user_rol', 'users.id', '=', 'user_rol.user_id')
            ->leftJoin('roles', 'user_rol.rol_id', '=', 'roles.Id')
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
                "localidades.Nombre as NombreLocalidad",
                "profesionales.Id as IdProfesional",
                "profesionales.Firma as Firma",
                "profesionales.Foto as Foto",
                "profesionales.wImage as wImage",
                "profesionales.hImage as hImage",
                "profesionales.InfAdj as InfAdj",
                "profesionales.Pago as Pago",
                "profesionales.MN as MN",
                "profesionales.SeguroMP as SeguroMP",
                "profesionales.MP as MP",
                DB::raw("GROUP_CONCAT(roles.nombre SEPARATOR ',') as NombreRol")
            )->find($usuario->id);

        $listado = explode(',', $query->NombreRol);
        $contador = count(array_intersect($lstRoles, $listado));

        return view('layouts.usuarios.edit', compact(['query', 'provincias', 'roles', 'lstProveedor', 'contador']));
    }

    public function buscar(Request $request) 
    {
        if(!$this->hasPermission("usuarios_show")){
            return response()->json(['msg' => 'No tiene acceso'], 403);
        }

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
                ->where('users.Anulado', 0)
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
        if(!$this->hasPermission("usuarios_show")) {
            return response()->json(['msg' => 'No tiene permisos', 403]);
        }

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
        $query = User::where('name', $request->usuario)->exists();
        return response()->json(['exists' => $query]);       
    }

    public function checkMail(Request $request): mixed
    {
        $query = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $query]);
    }

    public function checkCorreo(Request $request)
    {
        $verificar = User::where('email', $request->email)->first();

        if ($verificar && $verificar->name !== $request->name) {
            $response = ['msg' => 'El correo ya está en uso por otro usuario.', 'estado' => 'false'];
        
        }elseif($verificar && $verificar->name === $request->name) {

            $response = ['msg' => 'El correo es el que usa actualmente. Se procede actualizar.', 'estado' => 'true'];
        }else{

            $response = ['msg' => 'Correo habilitado para actualizar', 'estado' => 'true'];
        }
        return response()->json($response);         
    }

    public function checkTelefono(Request $request): mixed
    {
        if(!$this->hasPermission("usuarios_edit")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }

        $query = User::has('personal')->whereHas('personal', function($query) use ($request) {
            $query->where('Telefono', $request->telefono)->doesntExist();
        });
        return response()->json($query);  
    }

    public function checkEmailUpdate(Request $request)
    {
        if(!$this->hasPermission("usuarios_edit") || !$this->hasPermission("profesionales_edit")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }

        $verificar = User::where('email', $request->email)->first();

        if ($verificar && $verificar->name !== $request->name) {
            $response = ['msg' => 'El correo ya está en uso por otro usuario.', 'estado' => 'false'];
        
        }elseif($verificar && $verificar->name === $request->name) {

            $response = ['msg' => 'El correo es el que usa actualmente.', 'estado' => 'false'];
        }else{

            $q = User::where('name', $request->name)->first();
            $q->email = $request->email;
            $q->save();

            $response = ['msg' => 'Se ha actualizado el email correctamente.', 'estado' => 'true'];
        }
        return response()->json($response);
    }

    public function baja(Request $request)
    {
        
        if(!$this->hasPermission("usuarios_delete")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }

        $query = User::with('role')->find($request->Id);

        if($query) 
        {
            if($query->role->contains('nombre', 'Administrador')) {
                return response()->json(['msg' => 'No se puede eliminar un rol Administrador'], 409);
            }

            if(Auth::user()->name === $query->name) {
                return response()->json(['msg' => 'No puedes hacer una autoeliminación'], 409);
            }

            $query->Anulado = $query->Anulado === 1 ? 0 : 1;
            $query->inactivo = 0;
            $query->save();

            return response()->json(['msg' => 'Se ha dado de baja al usuario correctamente', 'estado' => 'success'], 200);
        }else{
            return response()->json(['msg' => 'No se ha podido dar de baja el usuario'], 500);
        }
    }

    public function bloquear(Request $request)
    {
        if(!$this->hasPermission("usuarios_delete")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }

        $query = User::find($request->Id);

        if($query) 
        {
            if($query->role->contains('nombre', 'Administrador')) {
                return response()->json(['msg' => 'No se puede bloquear un rol Administrador'], 409);
            }

            if(Auth::user()->name === $query->name) {
                return response()->json(['msg' => 'No puedes hacer una bloquear la cuenta que usas'], 409);
            }

            $query->inactivo = $query->inactivo === 1 ? 0 : 1;
            $query->save();
        }

        $nQuery = User::find($request->Id, ['inactivo']);
        $result = $nQuery->inactivo === 1 ? ['msg' => 'Se ha desactivado correctamente al usuario'] : ['msg' => 'Se ha activado al usuario'];

        return response()->json($result);

    }

    public function cambiarPassword(Request $request)
    {
        
        $query = User::find($request->Id);

        if($query) 
        {
            $query->password = Hash::make(SELF::password);
            $query->save();

            return response()->json(['msg' => 'Se ha cambiado la contraseña correctamente'], 200);
        }
    }

    public function updateProfesional(Request $request) 
    {

        $query = Profesional::find($request->Id);

        if($query) {
            $query->Pago = $request->Pago;
            $query->InfAdj = $request->InfAdj;
            $query->Firma = $request->Firma;
            $query->wImage = $request->wImage;
            $query->hImage = $request->hImage;

            if ($request->hasFile('Foto')) {
   
                $fotoExistente = 'public/profesionales/' . $request->Foto;
                if (Storage::exists($fotoExistente)) {
                    Storage::delete($fotoExistente);
                }
    
                $fileName = 'PROF' . $request->Id . '.' . $request->Foto->extension();
                $request->Foto->storeAs('public/profesionales', $fileName);
    
                $query->Foto = $fileName;
            }

            $query->save();

            return response()->json(['msg' => 'Se han cargado los cambios correctamente.'], 200);

        }else{
            return response()->json(['msg' => 'No se ha podido realizar la actualización.'], 500);
        }
    }

    public function checkRoles(Request $request)
    {
        $query = User::with('role')->where('profesional_id', $request->Id)->first();

        return response()->json($query->role);
    }

}

