<?php

namespace App\Http\Controllers;

use App\Models\Localidad;
use App\Models\Provincia;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    //Obtenemos localidades de la selección de Provincia
    public function getLocalidades(Request $request)
    {
        $provincia = Provincia::where('Nombre', $request->provincia)->first(['Id']);
        $localidades = Localidad::where('IdPcia', $provincia->Id)->get();

        if ($localidades) {
            $localidades = $localidades->map(function ($localidad) {
                return [
                    'id' => $localidad->Id,
                    'nombre' => $localidad->Nombre,
                ];
            });

            return response()->json(['localidades' => $localidades]);
        }

        return response()->json(['localidades' => []]);
    }

    //Obtenemos automaticamente los codigos postales tomando la localidad
    public function getCodigoPostal(Request $request)
    {
        $localidadId = $request->localidadId;
        $localidad = Localidad::find($localidadId);

        if ($localidad) {
            $codigoPostal = $localidad->CP;
        } else {
            $codigoPostal = '';
        }

        return response()->json(['codigoPostal' => $codigoPostal]);
    }

    //Autocompletamos la provincia en base a la localidad
    public function checkProvincia(Request $request)
    {
        $localidad = Localidad::where('Nombre', '=', $request->localidad)->orWhere('Id', '=', $request->localidad)->first();
        $provincia = Provincia::where('Id', $localidad->IdPcia)->first();

        if ($localidad->Nombre) {
            return response()->json(['fillProvincia' => $provincia->Nombre]);
        }

        return response()->json(['msg' => 'Debe elegir la provincia manualmente.'], 409);
    }
}
