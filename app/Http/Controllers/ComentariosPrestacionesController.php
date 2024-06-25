<?php

namespace App\Http\Controllers;

use App\Models\PrestacionComentario;
use Illuminate\Http\Request;

class ComentariosPrestacionesController extends Controller
{

    public function getComentarioPres(Request $request)
    {
        $prestacion = PrestacionComentario::where('IdP', $request->Id)->first();

        $comentario = $prestacion ? $prestacion->Obs : null;

        if ($comentario) {
            return response()->json(['comentario' => $comentario]);
        }

    }

    public function setComentarioPres(Request $request)
    {
        PrestacionComentario::updateOrCreate([
            'IdP' => $request->Id,
        ],[
            'Id' => PrestacionComentario::max('Id') + 1,
            'Obs' => $request->observacion
        ]);

        return response()->json(['msg' => 'Se ha actualizado la observacion correctamente'], 200);
    }
}
