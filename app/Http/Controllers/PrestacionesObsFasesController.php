<?php

namespace App\Http\Controllers;

use App\Models\PrestacionObsFase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrestacionesObsFasesController extends Controller
{
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

    public function addComentario(Request $request): void
    {

        PrestacionObsFase::create([
            'Id' => PrestacionObsFase::max('Id') + 1,
            'IdEntidad' => $request->IdEntidad,
            'Comentario' => $request->Comentario,
            'IdUsuario' => Auth::user()->name,
            'Fecha' => now()->format('Y-m-d'),
            'Rol' => Auth::user()->role->first()->nombre,
            'obsfases_id' => $request->obsfasesid
        ]);
    }

    private function queryBasic()
    {
        $query = PrestacionObsFase::join('prestaciones', 'prestaciones_obsfases.IdEntidad', '=', 'prestaciones.Id')
        ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
        ->join('users', 'prestaciones_obsfases.IdUsuario', '=', 'users.name')
        ->select('prestaciones_obsfases.*', 'prestaciones_obsfases.Rol as nombre_perfil')
        ->orderBy('prestaciones_obsfases.Id', 'DESC');

        return $query;
    }
}
