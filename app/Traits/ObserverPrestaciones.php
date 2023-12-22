<?php

namespace App\Traits;

use App\Models\Fichalaboral;
use App\Models\PrestacionAtributo;
use App\Models\PrestacionComentario;
use App\Models\Mapa;

trait ObserverPrestaciones
{
    
    public function setPrestacionAtributo($IdPadre, $sinEval)
    {
        $sinEval = ($sinEval === 'true' ? 1 : 0);

        $prestacion = PrestacionAtributo::where('IdPadre', $IdPadre)->first(['SinEval']);

        if($prestacion) 
        {
            $prestacion->SinEval = $sinEval;
            $prestacion->save();
        }else{

            PrestacionAtributo::create([
                'Id' => PrestacionAtributo::max('Id') + 1,
                'IdPadre' => $IdPadre,
                'SinEval' => $sinEval
            ]);
        }
    }

    public function setPrestacionComentario($id, $observacion)
    {
        $comentario = PrestacionComentario::where('IdP', $id)->first();

        if($comentario) 
        {
            $comentario->Obs = $observacion;
            $comentario->save();
        }else{

            PrestacionComentario::create([
                'Id' => PrestacionComentario::max('Id') + 1,
                'IdP' => $id,
                'Obs' => $observacion,
            ]);

        }
    }

    public function updateFichaLaboral($paciente, $art, $empresa)
    {
        $laboral = Fichalaboral::where('IdPaciente', $paciente)->first(['IdEmpresa', 'IdART']);

        if($laboral){
            
            $laboral->IdEmpresa = (empty($laboral->IdEmpresa) || $laboral->IdEmpresa === null ? '' : $empresa);
            $laboral->IdART = (empty($laboral->IdART) || $laboral->IdART === null ? '' : $art);
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

    

}