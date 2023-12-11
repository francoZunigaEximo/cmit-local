<?php

namespace App\Traits;

use App\Models\ArchivoEfector;
use App\Models\ArchivoInformador;
use App\Models\ItemPrestacion;
use App\Models\ItemPrestacionInfo;
use App\Models\Prestacion;
use App\Models\Paciente;

trait ObserverItemsPrestaciones
{

    public function createItemPrestacionInfo($id, $observacion)
    {

        ItemPrestacionInfo::create([
            'Id' => ItemPrestacionInfo::max('Id') + 1,
            'IdIP' => $id,
            'IdP' => 0,
            'Obs' => $observacion ?? '',
            'C1' => 0,
            'C2' => 0
        ]);
    }

    public function updateItemPrestacionInfo($id, $observacion)
    {

        $query = ItemPrestacionInfo::where('IdIP', $id)->first();
        $query->Obs = $observacion ?? '';
        $query->save();

    }

    public function generarQR($tipo, $prestacionId, $examenId, $pacienteId, $out)
    {
        if($out === 'texto')
        {
            //Tipo: A:efector,B:informador,C:evaluador (1 caracter)
            $prestacionId = str_pad($prestacionId, 9, "0", STR_PAD_LEFT);
	        $examenId = str_pad($examenId, 5, "0", STR_PAD_LEFT);
	        $pacienteId = str_pad($pacienteId, 7, "0", STR_PAD_LEFT);

            $code = strtoupper($tipo).$prestacionId.$examenId.$pacienteId;

            return $code;

        }elseif($out === 'qr'){

            //Codigo
        }
    }

    public function getPaciente($id)
    {
        $query = Prestacion::where('Id', $id)->first(['IdPaciente']);
        $paciente = Paciente::find($query->IdPaciente);
        return $paciente;
    }

    public function updateEstado($tipo, $id)
    {
       
        $item = ItemPrestacion::find($id);
        $efectores = ArchivoEfector::where('IdEntidad', $id)->get();
        $informadores = ArchivoInformador::where('IdEntidad', $id)->get();

        if($item)
        {
            if($tipo === 'efector' && $efectores)
            {

                switch ($item->CAdj) {
                    case 4:
                        $item->CAdj = 5;
                        break;
                    
                    case 1:
                        $item->CAdj = 2;
                        break;
                }
            }elseif($tipo === 'efector' && !($efectores)){
                
                switch($item->CAdj) {
                    case 5:
                        $item->CAdj = 4;
                        break;

                    case 2:
                        $item->CAdj = 1;
                        break;
                }
            }

            if($tipo === 'informador' && $informadores){

                $item->CInfo = 1;
            }else{
                $item->CInfo = 0;
            }

            $item->save();
        }
    }
}