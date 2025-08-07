<?php

namespace App\Traits;

use App\Models\ArchivoEfector;
use App\Models\ArchivoInformador;
use App\Models\ItemPrestacion;
use App\Models\ItemPrestacionInfo;
use App\Models\Prestacion;
use App\Models\Profesional;
use Illuminate\Support\Facades\DB;

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

    public function updateItemPrestacionInfo(int $id, string $observacion)
    {
        $query = ItemPrestacionInfo::where('IdIP', $id)->first();
        $query->Obs = $observacion;
        $query->save();

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

    public function adjunto(?int $id, string $tipo): bool
    {
        return $tipo === 'Efector'
            ? ArchivoEfector::where('IdEntidad', $id)->exists()
            : ArchivoInformador::where('IdEntidad', $id)->exists();
    }


    public function getDatosProfesional(int $id)
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

    public function multiEfector(int $idPrestacion, int $idProfesional, int $idProveedor): mixed
    {
        //$itemsprestacione->examenes->IdProveedor
        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
        ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
        ->select(
            'itemsprestaciones.Id as Id',
            DB::raw('(SELECT COUNT(*) FROM archivosefector WHERE archivosefector.IdEntidad = itemsprestaciones.Id) as archivos_count'),
            'examenes.Nombre as NombreExamen',
            'itemsprestaciones.IdPrestacion as IdPrestacion'
            )
        ->where('itemsprestaciones.IdPrestacion', $idPrestacion)
        ->whereIn('itemsprestaciones.IdProfesional', [$idProfesional, 0])
        ->where('examenes.IdProveedor', $idProveedor)
        ->where('proveedores.Multi', 1)
        ->whereNot('itemsprestaciones.Anulado', 1)
        ->orderBy('proveedores.Nombre', 'DESC')
        ->get();
    }

    public function multiInformador(int $idPrestacion, int $idProfesional, int $idProveedor): mixed
    {
        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
        ->join('proveedores', 'examenes.IdProveedor2', '=', 'proveedores.Id')
        ->join('profesionales', 'itemsprestaciones.IdProfesional2', '=', 'profesionales.Id')
        ->select('itemsprestaciones.Id', 
            DB::raw('(SELECT COUNT(*) FROM archivosinformador WHERE archivosinformador.IdEntidad = itemsprestaciones.Id) as archivos_count'),
            'examenes.Nombre as NombreExamen',
            'proveedores.Nombre as NombreProveedor'
        )
        ->where('itemsprestaciones.IdPrestacion', $idPrestacion)
        ->whereIn('itemsprestaciones.IdProfesional2', [$idProfesional, 0])
        ->where('examenes.IdProveedor2', $idProveedor)
        ->where('proveedores.MultiE', 1)
        ->where('profesionales.InfAdj', 1)
        ->whereNot('itemsprestaciones.Anulado', 1)
        ->whereNot('itemsprestaciones.CInfo', 0)
        ->orderBy('proveedores.Nombre', 'DESC')
        ->get();
    }
        
    
}