<?php

namespace App\Helpers;

use App\Models\ExamenCuentaIt;
use App\Models\ItemPrestacion;

trait ToolsReportes
{
    public function AnexosFormulariosPrint(int $id): mixed
    {
        //verifico si hay anexos con formularios a imprimir
	    // $query="Select e.Id From itemsprestaciones ip,examenes e Where e.Id=ip.IdExamen and e.IdReporte <> 0 and ip.Anulado=0 and e.Evaluador=1 and  ip.IdPrestacion=$idprest LIMIT 1";	$rs=mysql_query($query,$conn);

        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->select('examenes.Id as Id')
                ->whereNot('examenes.IdReporte', 0)
                ->where('itemsprestaciones.Anulado', 0)
                ->where('examenes.Evaluador', 1)
                ->where('itemsprestaciones.IdPrestacion', $id)
                ->first();
    }

    public function checkExCtaImpago(int $idPrestacion): mixed
    {
        return ExamenCuentaIt::join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->where('pagosacuenta_it.IdPrestacion', $idPrestacion)->count();
    }
}