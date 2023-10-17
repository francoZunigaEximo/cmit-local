<?php

namespace App\Http\Controllers;

use App\Models\PrestacionComentario;
use Illuminate\Http\Request;

class ComentariosPrestacionesController extends Controller
{
    public function setComentarioPres(Request $request)
    {

        $comentario = PrestacionComentario::where('IdP', $request->IdP)->first();

        if ($comentario) {

            $comentario->Obs = $request->Obs;
            $comentario->save();
        } else {

            PrestacionComentario::create([
                'Id' => PrestacionComentario::max('Id') + 1,
                'IdP' => $request->IdP,
                'Obs' => $request->Obs,
            ]);

        }

    }

    public function getComentarioPres(Request $request)
    {
        $prestacion = PrestacionComentario::where('IdP', $request->Id)->first();

        $comentario = $prestacion ? $prestacion->Obs : null;

        if ($comentario) {
            return response()->json(['comentario' => $comentario]);
        }

    }
}
