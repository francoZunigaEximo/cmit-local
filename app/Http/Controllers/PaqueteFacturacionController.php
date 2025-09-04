<?php

namespace App\Http\Controllers;

use App\Models\PaqueteFacturacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PaqueteFacturacionController extends Controller
{
    public function paquetes(Request $request): mixed
    {
        $buscar = $request->buscar;

        $resultados = Cache::remember('Paquete_fact_'.$buscar, 5, function () use ($buscar) {

            $paquetes = PaqueteFacturacion::where('Nombre', 'LIKE', '%'.$buscar.'%')->where('Baja', '=', 0)->get();

            $resultados = [];

            foreach ($paquetes as $paquete) {
                $resultados[] = [
                    'id' => $paquete->Id,
                    'text' => $paquete->Nombre,
                ];
            }

            return $resultados;

        });

        return response()->json(['paquete' => $resultados]);
    }

     public function paqueteId(Request $request)
    {
        if(empty($request->IdPaquete)){
            return response()->json(['msg' => 'No se pudo obtener el paquete'], 500);
        }

        $examenes = DB::select('CALL getExamenesPaqueteFac(?)', [$request->IdPaquete]);
            
        return response()->json(['examenes' => $examenes], 200);
         
    }
    
}
