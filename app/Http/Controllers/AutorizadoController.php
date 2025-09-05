<?php

namespace App\Http\Controllers;

use App\Models\Autorizado;
use Illuminate\Http\Request;

class AutorizadoController extends Controller
{
    public function alta(Request $request)
    {
        Autorizado::create([
            'Id' => Autorizado::max('Id') + 1,
            'Nombre' => $request->Nombre,
            'Apellido' => $request->Apellido,
            'DNI' => $request->DNI,
            'Derecho' => $request->Derecho,
            'TipoEntidad' => $request->TipoEntidad,
            'IdEntidad' => $request->Id,
        ]);
    }

    public function listado(Request $request)
    {
        $autorizados = Autorizado::where('IdEntidad', $request->Id)->orderBy('Id', 'DESC')->get();

        if($autorizados){
            return response()->json($autorizados, 200);
            
        }
        return response()->json(['msg' => 'Hubo un error al obtener los autorizados.'], 500);
    }

    public function delete(Request $request)
    {
        $autorizado = Autorizado::find($request->Id);

        if ($autorizado) {
            return $autorizado->delete();
        }

    }
}
