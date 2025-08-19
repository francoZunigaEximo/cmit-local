<?php

namespace App\Http\Controllers;

use App\Models\PrestacionObsFase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Roles\Checker;
use Illuminate\Support\Facades\DB;

class PrestacionesObsFasesController extends Controller
{
    protected $roles;

    public function __construct(Checker $roles)
    {
        $this->roles = $roles;
    }

    public function comentariosPriv(Request $request)
    {
        
        $query = $this->queryBasic();
        
        if($request->tipo === 'mapa')
        {
            $query->where('mapas.Id', $request->Id)
                ->where('prestaciones_obsfases.obsfases_id', $request->obsfasesid);

        } elseif($request->tipo === 'prestacion') {

            $query->where('prestaciones.Id', $request->Id);
        }

        $result = $query->get();
            

        return response()->json(['result' => $result]);
    }

    public function addComentario(Request $request): mixed
    {

        $save = PrestacionObsFase::create([
            'Id' => PrestacionObsFase::max('Id') + 1,
            'IdEntidad' => $request->IdEntidad,
            'Comentario' => $request->Comentario,
            'IdUsuario' => Auth::user()->name,
            'Fecha' => now()->format('Y-m-d H:i:s'),
            'Rol' => $request->Rol,
            'obsfases_id' => $request->obsfasesid
        ]);

        if($save)
        {
            return response()->json(['msg' => 'Se ha generado la observación correctamente'], 200);
        }
        return response()->json(['msg' => 'No se ha podido guardar el comentario'], 500);
    }

    public function editComentario(Request $request)
    {
        $query = PrestacionObsFase::find($request->Id);

        if(Auth::user()->name !== $query->IdUsuario && $this->roles->userAdmin(Auth::user()->id) === false) {
            return response()->json(['msg' => 'No puedes realizar la operación. Debes ser el usuario creador del comentario o un administrador'], 409);
        }

        if($query) {

                $query->Comentario = $request->Comentario;
                $query->Fecha = now()->format('Y-m-d H:i:s');
                $query->save();
                $query->refresh();

            return response()->json(['msg' => 'Se ha modificado el comentario correctamente'], 200);
        }  
    }

    public function deleteComentario(Request $request)
    {
        $query = PrestacionObsFase::find($request->Id);

        if(Auth::user()->name !== $query->IdUsuario && !$this->roles->userAdmin(Auth::user()->id) === false) {
            return response()->json(['msg' => 'No puedes realizar la operación. Debes ser el usuario creador del comentario o un administrador'], 409);
        }
        if($query) {
            $query->delete();
            return response()->json(['msg' => 'Se ha eliminado el comentario correctamente'], 200);
        } 
    }

    public function getComentario(Request $request)
    {
        $query = PrestacionObsFase::join('prestaciones', 'prestaciones_obsfases.IdEntidad', '=', 'prestaciones.Id')
                                ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                                ->select(
                                    'prestaciones_obsfases.IdEntidad',
                                    'prestaciones_obsfases.Comentario',
                                    'prestaciones_obsfases.Id',
                                    DB::raw("CONCAT(pacientes.Apellido,' ',pacientes.Nombre) as NombreCompleto")
                                    )
                                ->where('prestaciones_obsfases.Id', $request->Id)
                                ->get();
        return response()->json($query);
    }

    public function listadoRoles(Request $request)
    {
        return $this->roles->roleList(null, $request->Nombre);
    }

    private function queryBasic()
    {
        return PrestacionObsFase::join('prestaciones', 'prestaciones_obsfases.IdEntidad', '=', 'prestaciones.Id')
        ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
        ->join('users', 'prestaciones_obsfases.IdUsuario', '=', 'users.name')
        ->select('prestaciones_obsfases.*', 'prestaciones_obsfases.Rol as nombre_perfil')
        ->orderBy('prestaciones_obsfases.Id', 'DESC');
    }

    
}
