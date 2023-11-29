<?php

namespace App\Traits;

use App\Models\FacturaDeVenta;
use Carbon\Carbon;

trait ObserverFacturasVenta
{

    public function addFactura($tipo, $sucursal, $factura, $idempresa, $tipocliente, $Id)
    {

        $query = FacturaDeVenta::where('IdPrestacion', $Id)->first();
        
        if($query){

            $query->Tipo = $tipo;
            $query->Sucursal = $sucursal;
            $query->NroFactura = $factura;
            $query->IdEmpresa = $idempresa;
            $query->save();

        }else {

            $nuevoId = FacturaDeVenta::max('Id') + 1;

            FacturaDeVenta::create([
                'Id'=> $nuevoId,
                'Tipo' => $tipo ?? '',
                'Sucursal' => $sucursal ?? '',
                'NroFactura' => $factura ?? '',
                'Fecha' => Carbon::now()->format('Y-m-d'),
                'Anulada' => '0',
                'FechaAnulada' => '0000-00-00',
                'IdEmpresa' => $idempresa ?? 0,
                'TipoCliente' => ($tipocliente === 'ART' ? 'ART' : 'EMPRESA'),
                'ObsAnulado' => '',
                'EnvioFacturaF' => '0000-00-00 00:00:00',
                'Obs' => '',
                'IdPrestacion' => $Id
            ]);
        }
    }

}