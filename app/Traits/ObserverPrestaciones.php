<?php

namespace App\Traits;

use App\Models\FacturaDeVenta;
use App\Models\Fichalaboral;
use App\Models\PrestacionAtributo;
use App\Models\Mapa;
use Carbon\Carbon;

trait ObserverPrestaciones
{
    
    public function setPrestacionAtributo($IdPadre, $sinEval)
    {
        $sinEval = ($sinEval === 'true' ? 1 : 0);

        $prestacion = PrestacionAtributo::where('IdPadre', $IdPadre)->first(['SinEval']);

        if ($prestacion) {
            $prestacion->SinEval = $sinEval;
            $prestacion->save();
        } else {

            PrestacionAtributo::create([
                'Id' => PrestacionAtributo::max('Id') + 1,
                'IdPadre' => $IdPadre,
                'SinEval' => $sinEval
            ]);
        }
    }

    public function updateFichaLaboral($paciente, $art, $empresa)
    {
        $laboral = Fichalaboral::where('IdPaciente', $paciente)->first(['IdEmpresa', 'IdART']);

        if($laboral){
            
            $laboral->IdEmpresa = ($laboral->IdEmpresa === '' || $laboral->IdEmpresa === null ? '' : $empresa);
            $laboral->IdART = ($laboral->IdART === '' || $laboral->IdART === null ? '' : $art);
            $laboral->save();
        }  
    }

    public function updateMapeados($mapa)
    {
        $mapeado = Mapa::find($mapa);
        
        if($mapeado){
            $mapeado->Cmapeados -= 1;
            $mapeado->save();
        }
    }

    public function facturaVenta($tipo, $sucursal, $factura, $idempresa, $tipocliente)
    {

        if(empty($tipo) || empty($sucursal) || empty($factura) || empty($idempresa) || empty($tipocliente)) return;

        $nuevoId = FacturaDeVenta::max('Id') + 1;

        FacturaDeVenta::create([
            'Id'=> $nuevoId,
            'Tipo' => $tipo ?? '',
            'Sucursal' => $sucursal ?? '',
            'NroFactura' => $factura ?? '',
            'Fecha' => Carbon::now()->format('Y-m-d'),
            'Anulada' => '0',
            'FechaAnulada' => '0000-00-00',
            'IdEmpresa' => $idempresa,
            'TipoCliente' => ($tipocliente === 'ART' ? 'ART' : 'EMPRESA'),
            'ObsAnulado' => '',
            'EnvioFacturaF' => '0000-00-00 00:00:00',
            'Obs' => ''
        ]);
    }

}