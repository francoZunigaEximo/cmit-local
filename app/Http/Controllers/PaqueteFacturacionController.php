<?php

namespace App\Http\Controllers;

use App\Models\PaqueteFacturacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PaqueteFacturacionController extends Controller
{
    public function paquetes(Request $request): mixed
    {
        $buscar = $request->buscar;

        $resultados = Cache::remember('Paquete_fact_'.$buscar, 5, function () use ($buscar) {

            $paquetes = PaqueteFacturacion::where('Nombre', 'LIKE', '%'.$buscar.'%')->get();

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
}
