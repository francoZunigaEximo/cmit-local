<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaqueteEstudio;
use Illuminate\Support\Facades\Cache;
use App\Traits\ObserverExamenes;
use Illuminate\Support\Facades\DB;
use App\Services\ItemsPrestaciones\Crud;

class PaqueteEstudioController extends Controller
{
    private $itemCrud;

    use ObserverExamenes;

    public function __construct(Crud $itemCrud)
    {
       $this->itemCrud = $itemCrud;     
    }

    //Listado de Paquete de estudios
    public function paquetes(Request $request): mixed
    {

        $buscar = $request->buscar;

        $resultados = Cache::remember('Paquete'.$buscar, 5, function () use ($buscar) {

            $paquetes = PaqueteEstudio::where('Nombre', 'LIKE', '%'.$buscar.'%')
            ->where('Baja', '=', 0)
            ->get();

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

        $examenes = DB::select('CALL getExamenesPaquete(?)', [intval($request->IdPaquete)]);
        $ids = collect($examenes)->pluck('Id')->toArray();
        $this->itemCrud->create($ids, intval($request->IdPrestacion), null);
         
    }
    
}
