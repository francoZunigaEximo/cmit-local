<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParametroReporte;

class ParamDescripcionesController extends Controller
{
    public function listado(Request $request)
    {
        $query = new ParametroReporte();
        $result = $query->getListado($request->IdEntidad, $request->modulo);

        if(empty($result)) {
            return response()->json(['message' => 'No hay listado disponible'], 500);
        }

        return response()->json($result);
    }

    public function eliminar(Request $request) 
    {
        if(empty($request->id)) return;

        $query = $this->eliminar($request->id);

        return response()->json($query);
    }


    
}
