<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaqueteEstudio;
use Illuminate\Support\Facades\Cache;
use App\Traits\ObserverExamenes;
use Illuminate\Support\Facades\DB;

class PaqueteEstudioController extends Controller
{
    use ObserverExamenes;

    //Listado de Paquete de estudios
    public function paquetes(Request $request): mixed
    {

        $buscar = $request->buscar;

        $resultados = Cache::remember('Paquete'.$buscar, 5, function () use ($buscar) {

            $paquetes = PaqueteEstudio::where('Nombre', 'LIKE', '%'.$buscar.'%')->get();

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

        $examenes = DB::select('CALL getExamenesPaquete(?)', [$request->IdPaquete]);
            
        return response()->json(['examenes' => $examenes], 200);
         
    }
    
}
