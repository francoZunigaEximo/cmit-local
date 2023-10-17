<?php

namespace App\Http\Controllers;

use App\Models\Autorizado;
use Illuminate\Http\Request;

class AutorizadoController extends Controller
{
    public function alta(Request $request)
    {

        $autorizado = Autorizado::create([
            'Id' => Autorizado::max('Id') + 1,
            'Nombre' => $request->Nombre,
            'Apellido' => $request->Apellido,
            'DNI' => $request->DNI,
            'Derecho' => $request->Derecho,
            'TipoEntidad' => $request->TipoEntidad,
            'IdEntidad' => $request->Id,
        ]);

        $autorizado->save();
    }

    public function getAut(Request $request)
    {
        $autorizados = Autorizado::where('IdEntidad', $request->Id)->orderBy('Id', 'DESC')->get();

        return response()->json($autorizados);
    }

    public function delete(Request $request)
    {
        $autorizado = Autorizado::find($request->Id);

        if ($autorizado) {
            return $autorizado->delete();
        }

    }
}
