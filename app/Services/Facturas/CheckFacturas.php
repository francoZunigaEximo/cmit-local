<?php

namespace App\Services\Facturas;

use App\Models\FacturaDeVenta;
use App\Models\ExamenCuentaIt;
use Illuminate\Support\Facades\DB;

class CheckFacturas
{
    public function facturaDeVenta(int $idPrestacion)
    {
        return FacturaDeVenta::select(
            'Fecha as Fecha',
                DB::raw('CONCAT(
                    Tipo,
                    LPAD(Sucursal,4,"0"),
                    "-",
                    LPAD(NroFactura,8,"0")
                ) as NroFactura')
            )  
            ->where('IdPrestacion', $idPrestacion)
            ->whereNot('Anulada', 1)
            ->first();
    }

    public function examenCuenta(int $idPrestacion, ?int $idExamen)
    {
        return ExamenCuentaIt::join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->select(
                'pagosacuenta.Fecha as Fecha',
                DB::raw('CONCAT(
                    pagosacuenta.Tipo,
                    LPAD(pagosacuenta.Suc,4,"0"),
                    "-",
                    LPAD(pagosacuenta.Nro,8,"0")
                ) as NroFactura')
            )
            ->where('pagosacuenta_it.IdPrestacion', $idPrestacion)
            ->where('pagosacuenta_it.IdExamen', $idExamen)
            ->first();
    }
}