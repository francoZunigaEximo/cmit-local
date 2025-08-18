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
            ->join('users as efector', 'itemsprestaciones.IdProfesional', '=', 'efector.profesional_id')
            ->join('users as informador', 'itemsprestaciones.IdProfesional2', '=', 'informador.profesional_id')
            ->join('datos as datosEfector', 'efector.datos_id', '=', 'datosEfector.Id')
            ->join('datos as datosInformador', 'informador.datos_id', '=', 'datosInformador.Id')
            ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
            ->join('profesionales as hisEfector', 'itemsprestaciones.IdProfesional' , '=', 'hisEfector.Id')
            ->join('profesionales as hisInformador', 'itemsprestaciones.IdProfesional2' , '=', 'hisInformador.Id')
            ->select(
                'itemsprestaciones.Id as IdItem',
                'itemsprestaciones.CAdj as CAdj',
                'itemsprestaciones.IdProfesional as efectorId',
                'itemsprestaciones.IdProfesional2 as informadorId',
                'itemsprestaciones.CInfo as CInfo',
                'proveedores.Nombre as NombreEspecialidad',
                'examenes.Nombre as NombreExamen',
                'examenes.Adjunto as Adjunto',
                'examenes.NoImprime as NoImprime',
                'examenes.Id as IdExamen',
                DB::raw("CONCAT(hisEfector.Apellido,' ',hisEfector.Nombre) as EfectorHistorico"),
                DB::raw("CONCAT(hisInformador.Apellido,' ',hisEfector.Nombre) as InformadorHistorico"),
                DB::raw("CONCAT(datosEfector.Apellido, ' ', datosEfector.Nombre) as Efector"),
                DB::raw("CONCAT(datosInformador.Apellido, ' ', datosInformador.Nombre) as Informador"),
                DB::raw('(SELECT COUNT(*) FROM archivosefector WHERE IdEntidad = itemsprestaciones.Id) as Archivo'),
                'itemsprestaciones.ObsExamen as ObsExamen',
            )->where('itemsprestaciones.IdPrestacion', $id)
                ->where('itemsprestaciones.IdProfesional2', 0)
                ->whereIn('itemsprestaciones.IdProveedor', $especialidades)
                ->groupBy('examenes.Nombre')
                ->get();
    }

    public function checkArchivo(string $tipo, int $idPrestacion)
    {

        $modelos = [
            'efector' => ArchivoEfector::class,
            'informador' => ArchivoInformador::class
        ];

        if(!isset($modelos[$tipo])) {
            return response()->json(['msg' => 'Ha ocurrido un error. No se encuentra el modelo correspondiente en el conteo de archivos'], 404);
        }

        return $modelos[$tipo]::where('IdEntidad', $idPrestacion)->count();

    }

}