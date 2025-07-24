<?php 

namespace App\Services\ItemsPrestaciones;

use App\Models\ItemPrestacion;
use App\Models\Examen;
use App\Models\ExamenCuentaIt;
use App\Services\ItemsPrestaciones\Helper;
use Illuminate\Support\Facades\DB;

class Crud 
{
    private $itemsHelper;

    public function __construct(Helper $itemsHelper)
    {
        $this->itemsHelper = $itemsHelper;
    }

    public function create(array $examenes, int $idPrestacion, ?int $idExaCta)
    {
        if(empty($examenes)) {
            return response()->json(['msg' => 'No hay examenes para procesar'], 409);
        }

        $listadoExamenes = ItemPrestacion::where('IdPrestacion', $idPrestacion)->whereIn('IdExamen', $examenes)->pluck('IdExamen')->all();

        $limpiezaExamenes = array_diff($examenes, $listadoExamenes);

        if(empty($limpiezaExamenes)) {
            return response()->json(['msg' => 'No hay datos para procesar'], 409);
        }

        $data = Examen::whereIn('Id', $limpiezaExamenes)->get()->keyBy('Id');

        $insertarExamenes = [];

        foreach ($limpiezaExamenes as $examenId) {
            $examen = $data->get($examenId);
            
            if(!$examen) {
                continue;
            }

            $honorarios = $this->itemsHelper->honorarios($examen->Id, $examen->IdProveedor);

            $insertarExamenes[] = [
                'IdPrestacion' => $idPrestacion,
                'IdExamen' => $examen->Id,
                'Fecha' => now()->format('Y-m-d'),
                'CAdj'=> $examen->Cerrado === 1 ? ($examen->Adjunto === 0 ? 3 : 4) : 1,
                'CInfo'=> $examen->Informe,
                'IdProveedor' => $examen->IdProveedor,
                'VtoItem' => $examen->DiasVencimiento,
                'SinEsc' => $examen->SinEsc,
                'Forma' => $examen->Forma,
                'Ausente' => $examen->Ausente,
                'Devol' => $examen->Devol,
                'IdProfesional' => $examen->Cerrado === 1 ? 26 : 0,
                'IdProfesional2' => 0,
                'Honorarios' => $honorarios == 'true' ? $honorarios : 0
            ];
        }

        if(!empty($insertarExamenes)) {
            DB::table('itemsprestaciones')->insert($insertarExamenes);
        }else{
            return response()->json(['msg' => 'No hay examenes para insertar'], 409);
        }

        foreach ($limpiezaExamenes as $examenId) {

            if (!empty($idExaCta)) { 
                $this->registrarPagoaCuenta($idExaCta, $idPrestacion);
            }
            
            if (!empty($itemsToInsert)) {
                ItemPrestacion::InsertarVtoPrestacion($idPrestacion);
            }
        }
    }

    private function registrarPagoaCuenta(int $IdPagoCtaIt, int $idPrestacion): void
    {
        $query = ExamenCuentaIt::find($IdPagoCtaIt);

        if($query) {
            $query->IdPrestacion = $idPrestacion;
            $query->save();
        }
    }
}