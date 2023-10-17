<?php

namespace App\Traits;

use App\Models\Fichalaboral;
use App\Models\PrestacionAtributo;

trait ObserverPrestaciones
{
    
    public function setPrestacionAtributo($IdPadre, $sinEval)
    {
        $sinEval = ($sinEval === 'true' ? 1 : 0);

        $prestacion = PrestacionAtributo::where('IdPadre', $IdPadre)->first();

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
        $laboral = Fichalaboral::where('IdPaciente', $paciente)->first();

        if($laboral){
            

            $laboral->IdEmpresa = ($laboral->IdEmpresa === '' || $laboral->IdEmpresa === null ? '' : $empresa);
            $laboral->IdART = ($laboral->IdART === '' || $laboral->IdART === null ? '' : $art);
            $laboral->save();
        }

        
    }

}