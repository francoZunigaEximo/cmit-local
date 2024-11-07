<?php

namespace App\Services\Reportes\Cuerpos;

use App\Services\Reportes\Reporte;
use FPDF;
use Illuminate\Support\Facades\DB;

class AdjuntosDigitales extends Reporte
{

    public function render(FPDF $fpdf, $datos = ['idPrestacion']):void
    {
        $querys = $this->queryCombinado($datos['idPrestacion']);

        foreach($querys as $query) {

            
        }
    }

    private function queryCombinado(int $id):mixed
    {
        return DB::table(DB::raw('(
            SELECT 
                a.Id as Id,
                a.IdIP as IdEntidad,
                "" as Ruta,
                i.IdProveedor as IdProveedor,
                1 as Tipo,
                0 as NoImprime
            FROM itemsprestaciones_info a
            JOIN itemsprestaciones i ON a.IdIP = i.Id
            WHERE a.C1 = 0 AND a.IdP = ?
            
            UNION
            
            SELECT 
                a.Id as Id,
                a.IdEntidad as IdEntidad,
                a.Ruta as Ruta,
                i.IdProveedor as IdProveedor,
                2 as Tipo,
                0 as NoImprime
            FROM archivosinformador a
            JOIN itemsprestaciones i ON a.IdEntidad = i.Id
            WHERE a.IdPrestacion = ?
            
            UNION
            
            SELECT 
                a.Id as Id,
                a.IdEntidad as IdEntidad,
                a.Ruta as Ruta,
                i.IdProveedor as IdProveedor,
                3 as Tipo,
                e.NoImprime as NoImprime
            FROM archivosefector a
            JOIN itemsprestaciones i ON a.IdEntidad = i.Id
            JOIN examenes e ON i.IdExamen = e.Id
            WHERE e.Evaluador = 0 AND a.IdPrestacion = ?
        ) AS combined'))
        ->addBinding($id, 'int')
        ->addBinding($id, 'int')
        ->addBinding($id, 'int')
        ->get();
    }
        

}
