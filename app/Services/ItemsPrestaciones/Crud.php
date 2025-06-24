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

        foreach ($examenes as $examen) {
            
            $itemPrestacion = ItemPrestacion::where('IdPrestacion', $idPrestacion)->where('IdExamen', $examen)->first();

            if(!$itemPrestacion){

                $examen = Examen::find($examen);
                $honorarios = $this->itemsHelper->honorarios($examen->Id, $examen->IdProveedor);

               DB::insert('
                    INSERT INTO itemsprestaciones (
                        Id, IdPrestacion, IdExamen, Fecha, CAdj, CInfo, IdProveedor,
                        VtoItem, SinEsc, Forma, Ausente, Devol, IdProfesional,
                        IdProfesional2, Honorarios
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ', [
                    ItemPrestacion::max('Id') + 1,
                    $idPrestacion,
                    $examen->Id,
                    now()->format('Y-m-d'),
                    $examen->Cerrado === 1 
                        ? ($examen->Adjunto === 0 ? 3 : 4) 
                        : 1,
                    $examen->Informe,
                    $examen->IdProveedor,
                    $examen->DiasVencimiento,
                    $examen->SinEsc,
                    $examen->Forma,
                    $examen->Ausente,
                    $examen->Devol,
                    $examen->Cerrado === 1 ? 26 : 0,
                    0,
                    $honorarios == 'true' ? $honorarios : 0
                ]);

                !empty($request->idExaCta) || is_numeric($idExaCta) <> 0 ? $this->registrarPagoaCuenta($idExaCta, $idPrestacion) : null;

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