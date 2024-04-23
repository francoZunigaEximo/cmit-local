<?php

namespace App\Traits;

use App\Models\ArchivoEfector;
use App\Models\ArchivoInformador;
use App\Models\ItemPrestacion;
use App\Models\ItemPrestacionInfo;
use App\Models\Prestacion;
use App\Models\Paciente;
use App\Models\Profesional;

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

    public function generarQR($tipo, $prestacionId, $examenId, $pacienteId, $out): mixed
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

    public function getPaciente(int $id): mixed
    {
        return Prestacion::where('Id', $id)->with('paciente')->first();
    
    }

    public function updateEstado(string $tipo, ?int $idItemPrestacion, ?int $idEfector, ?int $idInformador, ?string $multi, ?int $profesional)
    {
       
        $item = ItemPrestacion::with('examenes')->where('Id', $idItemPrestacion)->first();
        $efectores = ArchivoEfector::where('Id', $idEfector)->first();
        $informadores = ArchivoInformador::where('Id', $idInformador)->first();

        if($item)
        {

            if(in_array($tipo, ['efector', 'multiefector']) && $efectores)
            {
     
                switch ($item->CAdj) {
                    
                    case 0:
                        $item->CAdj = $multi === 'multi' ? 5 : 1; 
                        break;
                    
                    case 1:
                        $item->CAdj = $multi === 'multi' ? 5 : 2;
                        break;

                    case 3:
                        $item->CAdj = $multi === 'multi' ? 5 : 4;
                        break;
                    
                    case 4:
                        $item->CAdj = 5;
                        break;
                }

               

            }elseif(in_array($tipo, ['efector', 'multiefector']) && !($efectores)){
                
                switch($item->CAdj) {
                    
                    case 1:
                        $item->CAdj= $multi === 'multi' ? 5 : 0;
                        break;
                    
                    case 4:
                        $item->CAdj = $multi === 'multi' ? 5 : 3;
                        break;
                    
                    case 5:
                        $item->CAdj = $multi === 'multi' ? 5 : 4;
                        break;
                }
            }

            if(in_array($tipo, ['informador', 'multiInformador']) && $informadores)
            {

                switch($item->CInfo) {

                    case 0:
                    case 1:
                        $item->CInfo = 2;
                        break;
                    
                    case 2:
                        $item->CInfo = 3;
                        break;
                }

            }elseif(in_array($tipo, ['informador', 'multiInformador']) && !($informadores)){
                
               
                switch($item->CInfo) {

                    case 3:
                        $item->CInfo = 2;
                        break;
                    
                    case 2:
                        $item->CInfo = 1;
                        break;
                }
            }
            $item->IdProfesional2 = $tipo === 'multiInformador' && $item->IdProfesional2 === 0 ? $profesional : $item->IdProfesional2;
            $item->IdProfesional = $tipo === 'multiefector' && $item->IdProfesional === 0 ? $profesional : $item->IdProfesional;

            $item->save();
        }
    }

    public function adjuntoEfector(int $id): ?int 
    {
        
        if (empty($id)) return null;

        $archivo = ArchivoEfector::where('IdEntidad', $id)->first();
        
        return $archivo ? 1 : 0;
    }

    public function adjuntoInformador(int $id): ?int 
    {
        
        if (empty($id)) return null;

        $archivo = ArchivoInformador::where('IdEntidad', $id)->first();
        
        return $archivo ? 1 : 0;
    }

    public function getDatosProfesional(int $id): string
    {
        $profesional = Profesional::find($id);

        if ($profesional)
        {
            return $profesional->Nombre . " " . $profesional->Apellido;
        }
    }

    public function registarArchivo(?int $id, string $entidad, ?string $descripcion, string $ruta, int $prestacion, string $tipo): void
    {
        if(in_array($tipo, ['efector','multiefector']))
        {
            ArchivoEfector::create([
                'Id' => empty($id) ? ArchivoEfector::max('Id') + 1 : $id,
                'IdEntidad' => $entidad,
                'Descripcion' => $descripcion ?? '',
                'Ruta' => $ruta,
                'IdPrestacion' => $prestacion,
                'Tipo' => '0'
            ]);
        
        } elseif(in_array($tipo, ['informador', 'multiInformador'])) {

            ArchivoInformador::create([
                'Id' => empty($id) ? ArchivoInformador::max('Id') + 1 : $id,
                'IdEntidad' => $entidad,
                'Descripcion' => $descripcion ?? '',
                'Ruta' => $ruta,
                'IdPrestacion' => $prestacion
            ]);
        }

    }
        
    
}