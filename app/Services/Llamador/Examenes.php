<?php 

namespace App\Services\Llamador;

use App\Models\ArchivoEfector;
use App\Models\ArchivoInformador;
use App\Models\ItemPrestacion;
use Illuminate\Support\Facades\DB;

class Examenes 
{
    public function getAllItemsprestaciones(int $id, array $especialidades):mixed
    {
        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
            ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
            ->select(
                'itemsprestaciones.Id as IdItem',
                'itemsprestaciones.CAdj as CAdj',
                'itemsprestaciones.CInfo as CInfo',
                'proveedores.Nombre as NombreEspecialidad',
                'examenes.Nombre as NombreExamen',
                'examenes.Adjunto as Adjunto',
                'examenes.NoImprime as NoImprime',
                DB::raw('(SELECT COUNT(*) FROM archivosefector WHERE IdEntidad = itemsprestaciones.Id) as Archivo'),
                'itemsprestaciones.ObsExamen as ObsExamen'
            )
            ->where('itemsprestaciones.IdPrestacion', $id)
            ->whereIn('examenes.IdProveedor', $especialidades)
            ->get();
    }

    public function checkArchivo(string $tipo, int $idPrestacion)
    {
        if($tipo === 'efector') {
            return ArchivoEfector::where('IdEntidad', $idPrestacion)->count();
        
        }elseif($tipo === 'informador') {
            return ArchivoInformador::where('IdEntidad', $idPrestacion)->count();
        }
    }
}