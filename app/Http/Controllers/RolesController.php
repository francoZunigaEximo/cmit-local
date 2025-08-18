<?php

namespace App\Http\Controllers;

use App\Models\Profesional;
use App\Models\ProfesionalProv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Llamador\Profesionales;
use App\Services\Roles\Utilidades;

use App\Events\LstProfesionalesEvent;
use App\Events\LstProfInformadorEvent;

class RolesController extends Controller
{
    private array $lstRoles;
    protected $profesionales;
    protected $utilidades;

    const ADMIN = ['Administrador', 'Admin SR', 'Recepcion SR'];
    const TIPOS = ['Efector', 'Informador'];

    public function __construct(Profesionales $profesionales, Utilidades $utilidades)
    {
        $this->lstRoles = [
            "Efector" => "T1", 
            "Informador" => "T2", 
            "Evaluador" => "T3", 
            "Combinado" => "T4", 
            "Evaluador ART" => "T5"
        ];

        $this->profesionales = $profesionales;
        $this->utilidades = $utilidades;
    }

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
            ->leftJoin('users', 'user_rol.user_id', '=', 'users.Id')
            ->leftJoin('rol_permisos', 'roles.Id', '=', 'rol_permisos.rol_id')
            ->leftJoin('permisos', 'rol_permisos.permiso_id', '=', 'permisos.Id')
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
        
        if(!Auth::user()->role->contains('nombre', 'Administrador')) {
            return response()->json(['msg' => 'No se puede asignar el rol porque no tiene permisos asociados'], 409);
        }

        if(!in_array($user->role->contains($request->role), [null, false]))
        {
            return response()->json(['msg' => 'El usuario ya tiene ese rol asignado', 'estado' => 'false']);
            
        }else{

            $user->role()->attach($role);
            $this->addRolProfesionales($role, $user);


            return response()->json(['msg' => 'Se ha asignado el rol al usuario correctamente', 'estado' => 'true']);

        }   
    }

    public function delete(Request $request)
    {
        $user = User::find($request->user); 
        $role = Rol::find($request->role); 

        if($user && $role){
            $user->role()->detach($role);
            $this->removeRolProfesionales($role, $user);
            return response()->json(['msg' => 'Se ha eliminado el rol del usuario correctamente'], 200);

        }
    }

    private function addRolProfesionales($roles, $user)
    {
        $buscar = $this->listadoRoles($roles->nombre);

        if(count($buscar) === 1 && $user->profesional_id === 0)
        {
            $id = Profesional::max('Id') + 1;
            Profesional::create([
                'Id' => $id,
                'Documento' => '',
                'Apellido' => '',
                'Nombre' => '',
                'EMail' => '',
                'Direccion' => '',
                'Provincia' => '',
                'Firma' => '',
                'Identificacion' => '',
                'CP' => '',
                'Inactivo' => 0,
                'Foto' => $fileName ?? '',
                'RegHis' => 0,
                'T1' => in_array("Efector", $buscar) === true ? 1 : 0,
                'T2' => in_array("Informador", $buscar) === true ? 1 : 0,
                'T3' => in_array("Evaluador", $buscar) === true ? 1 : 0,
                'T4' => in_array("Combinado", $buscar) === true ? 1 : 0,
                'T5' => in_array("Evaluador ART", $buscar) === true ? 1 : 0,
            ]);
            $user->update(['profesional_id' => $id]);
            
        }elseif(count($buscar) === 1 && $user->profesional_id !== 0) {

            foreach ($this->lstRoles as $key => $value) {
                $user->profesional->$value = in_array($key, $buscar) ? 1 : 0;
            }

            $efectores = $this->profesionales->listado('Efector');
            $informadores = $this->profesionales->listado('Informador');
            event(new LstProfesionalesEvent($efectores));
            event(new LstProfInformadorEvent($informadores));

            $user->profesional->save();
        }
    }

    private function removeRolProfesionales($roles, $user)
    {
        $buscar = $this->listadoRoles($roles->nombre);

        if(!empty($buscar)) {

            foreach ($this->lstRoles as $key => $value) {
                $user->profesional->$value = in_array($key, $buscar) === true ? 0 : $user->profesional->$value;
            }
            $user->profesional->save();
            ProfesionalProv::where('IdProf', $user->profesional_id)->delete();

            foreach(SELF::TIPOS as $tipos) {
                event(new LstProfesionalesEvent($tipos));
            }
        }
    }

    private function listadoRoles($rol)
    {
        $arrList = explode(',', trim($rol));
        $arrKey = array_keys($this->lstRoles);

        return array_intersect($arrList, $arrKey);
    }


}
