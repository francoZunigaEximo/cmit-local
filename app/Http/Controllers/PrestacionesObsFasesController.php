<?php

namespace App\Http\Controllers;

use App\Models\PrestacionObsFase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrestacionesObsFasesController extends Controller
{
    public function comentariosPriv(Request $request)
    {
        $query = PrestacionObsFase::join('prestaciones', 'prestaciones_obsfases.IdEntidad', '=', 'prestaciones.Id')
            ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->join('users', 'prestaciones_obsfases.IdUsuario', '=', 'users.name')
            ->leftJoin('perfiles', 'prestaciones_obsfases.Rol', '=', 'perfiles.Id') 
            ->select('prestaciones_obsfases.*', 'users.Rol as nombre_perfil')
            ->where('mapas.Id', $request->Id)
            ->where('prestaciones_obsfases.obsfases_id', $request->obsfasesid)
            ->orderBy('prestaciones_obsfases.Id', 'DESC')
            ->get();

        return response()->json(['result' => $query]);
    }

    public function addComentario(Request $request): void
    {

        PrestacionObsFase::create([
            'Id' => PrestacionObsFase::max('Id') + 1,
            'IdEntidad' => $request->IdEntidad,
            'Comentario' => $request->Comentario,
            'IdUsuario' => Auth::user()->name,
            'Fecha' => now()->format('Y-m-d'),
            'Rol' => Auth::user()->IdPerfil,
            'obsfases_id' => $request->obsfasesid
        ]);
    }
}
