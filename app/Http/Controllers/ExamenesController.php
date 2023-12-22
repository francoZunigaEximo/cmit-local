<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\ItemPrestacion;
use App\Models\PaqueteEstudio;
use App\Models\Relpaqest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExamenesController extends Controller
{
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

        $query = Relpaqest::where('IdPaquete', $request->IdPaquete)->get();
        $idExamenes = $query->pluck('IdExamen')->toArray();
        $examenes = Examen::whereIn('Id', $idExamenes)->get();

        return response()->json(['examenes' => $examenes]);
        
    }

    public function save(Request $request): void
    {

        $examenes = $request->idExamen;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach ($examenes as $examen) {
            
            $itemPrestacion = ItemPrestacion::where('IdPrestacion', $request->idPrestacion)->where('IdExamen', $examen)->first();

            if(!$itemPrestacion){

                ItemPrestacion::create([
                    'Id' => ItemPrestacion::max('Id') + 1,
                    'IdPrestacion' => $request->idPrestacion,
                    'IdExamen' => $examen,
                ]);
            }   
        }
    }

    public function check(Request $request): mixed
    {

        $examenes = ItemPrestacion::where('IdPrestacion', '=', $request->Id)->get() ?? '';

        $idExamenes = [];

        foreach ($examenes as $examen) {
            $idExamenes[] = $examen->IdExamen;
        }

        return response()->json(['respuesta' => ! $examenes->isEmpty(), 'examenes' => $idExamenes]);
    }

    public function search(Request $request): mixed
    {

        $buscar = $request->buscar;

        $resultados = Cache::remember('Examen'.$buscar, 5, function () use ($buscar) {

            $examenes = Examen::where(function ($query) use ($buscar) {
                $query->where('Nombre', 'LIKE', '%'.$buscar.'%');
            })->get();

            $resultados = [];

            foreach ($examenes as $examen) {
                $resultados[] = [
                    'id' => $examen->Id,
                    'text' => $examen->Nombre,
                ];
            }

            return $resultados;

        });

        return response()->json(['examen' => $resultados]);
    }

    public function getExamenes(Request $request): mixed
    {
        $resultados = Cache::remember('itemsprestaciones', 5, function () use ($request) {

            $query = ItemPrestacion::join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
                ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->join('profesionales as efector', 'itemsprestaciones.IdProfesional', '=','efector.Id')
                ->join('profesionales as informador', 'itemsprestaciones.IdProfesional2', '=', 'informador.Id')
                ->select(
                    'examenes.Nombre as Nombre',
                    'examenes.Id as IdExamen',
                    'efector.Nombre as NombreE',
                    'efector.Apellido as ApellidoE',
                    'informador.Nombre as NombreI',
                    'informador.Apellido as ApellidoI',
                    'itemsprestaciones.Ausente as Ausente',
                    'itemsprestaciones.Forma as Forma',
                    'itemsprestaciones.Incompleto as Incompleto',
                    'itemsprestaciones.SinEsc as SinEsc',
                    'itemsprestaciones.Devol as Devol',
                    'itemsprestaciones.CAdj as CAdj',
                    'itemsprestaciones.CInfo as CInfo',
                    'itemsprestaciones.Id as IdItem',
                    'itemsprestaciones.Anulado as Anulado'
                );                

            if ($request->tipo === 'listado' && is_array($request->IdExamen)) {

                    $query->whereIn('examenes.Id', $request->IdExamen)
                            ->where('itemsprestaciones.IdPrestacion', $request->Id);
            } 

            return $query->orderBy('examenes.Nombre', 'ASC')->get();
        });
 
        return response()->json(['examenes' => $resultados]);
    }

    public function itemExamen(Request $request): void
    {
        $item = ItemPrestacion::find($request->Id);
        
        if($item){
          
            switch ($request->opcion) {
                case 'Incompleto':
                    $estado = $item->Incompleto;
                    $cambio = ($estado == 0 || $estado == null ? $item->Incompleto = 1 : $item->Incompleto = 0);
                    $item->Incompleto = $cambio;
                    break;
                
                case 'Ausente':
                    $estado = $item->Ausente;
                    $cambio = ($estado == 0 || $estado == null ? $item->Ausente = 1 : $item->Ausente = 0);
                    $item->Ausente = $cambio;
                    break;
               
                case 'Forma':
                    $estado = $item->Forma;
                    $cambio = ($estado == 0 || $estado == null ? $item->Forma = 1 : $item->Forma = 0);
                    $item->Forma = $cambio;
                    break;

                case 'SinEsc':
                    $estado = $item->SinEsc;
                    $cambio = ($estado == 0 || $estado == null ? $item->SinEsc = 1 : $item->SinEsc = 0);
                    $item->SinEsc = $cambio;
                    break;
                
                case 'Devol':
                    $estado = $item->Devol;
                    $cambio = ($estado == 0 || $estado == null ? $item->Devol = 1 : $item->Devol = 0);
                    $item->Devol = $cambio;
                    break;
            }

            $item->save();
        }

    }

    public function getId(Request $request): mixed
    {

        $getId = Examen::find($request->IdExamen);
        
        if($getId){
            return response()->json(['examenes' => $getId]);
        }
    }

    public function deleteEx(Request $request): void
    {

        $item = ItemPrestacion::find($request->Id);

        if ($item) {
            $item->delete();
        }
    }

    public function bloquearEx(Request $request)
    {

        $item = ItemPrestacion::find($request->Id);

        if ($item) {
            $item->update(['Anulado' => 1]);
        }
    }


}
