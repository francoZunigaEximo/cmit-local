<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Fichalaboral;
use App\Models\PrestacionesTipo;
use Illuminate\Http\Request;

class FichaAltaController extends Controller
{
    public function save(Request $request)
    {

        $fichaLaboral = Fichalaboral::create([
            'Id' => Fichalaboral::max('Id') + 1,
            'IdPaciente' => $request->paciente,
            'IdEmpresa' => $request->cliente ?? 0,
            'IdART' => $request->art ?? 0,
            'TipoPrestacion' => $request->tipoPrestacion ?? '',
            'Tareas' => $request->tareaRealizar ?? '',
            'Jornada' => $request->horario ?? '',
            'Pago' => $request->pago ?? '',
            'TipoJornada' => $request->tipo ?? '',
            'Observaciones' => $request->observaciones ?? '',
            'TareasEmpAnterior' => $request->ultimoPuesto,
            'Puesto' => $request->puestoActual ?? '',
            'Sector' => $request->sectorActual ?? '',
            'CCosto' => $request->ccosto ?? '',
            'AntigPuesto' => $request->antiguedadPuesto ?? '',
            'FechaIngreso' => $request->fechaIngreso ?? '',
            'FechaEgreso' => $request->fechaEgreso ?? '',
            'FechaPreocupacional' => $request->fechaPreocupacional ?? '',
            'FechaUltPeriod' => $request->fechaUltPeriod ?? '',
            'FechaExArt' => $request->fechaExArt ?? ''
        ]);

        if ($fichaLaboral) {
            return response()->json(['msg' => '¡Los datos se han actualizado. Nos redirigimos a la nueva prestación.!'], 200);
        }else{
            return response()->json(['msg' => '¡Ha ocurrido un error al intentar guardar los datos!'], 500);
        }
            

    }

    //Verificamos para emplear la vista full
    public function verificar(Request $request)
    {
        $fichaLaboral = Fichalaboral::with(['empresa','art'])->where('IdPaciente', $request->Id)->orderBy('Id', 'Desc')->first();

        if ($fichaLaboral) {

            return response()->json(['fichaLaboral' => $fichaLaboral, 'clienteArt' => $fichaLaboral->art ?? '', 'cliente' => $fichaLaboral->empresa ?? '']);
        }

    }

    public function checkObs(Request $request): mixed
    {

        $fichaLaboral = Fichalaboral::with(['paciente','art','empresa'])->where('IdPaciente', $request->Id)->orderBy('Id', 'Desc')->first();

        if($fichaLaboral){
            
            return response()->json([
                'obsArt' => $fichaLaboral->art,
                'obsEmpresa' => $fichaLaboral->empresa,
                'obsPaciente' => $fichaLaboral
            ]);
        }
    }

    // Devuelve el listado de prestaciones segun el financiador seleccionado
    public function getTipoPrestacion(Request $request){

        if(!$request->financiador){
            return response()->json(['tiposPrestacion' => []]);
        }

        $tipoCliente = Cliente::where('Id', $request->financiador)->first()->TipoCliente;

        if($tipoCliente == "A"){
            $tiposPrestacion = PrestacionesTipo::select('Id','Nombre')->where('Nombre', 'ART')->get();
        }
        else {
            $tiposPrestacion = PrestacionesTipo::select('Id','Nombre')->where('Nombre', '!=', 'ART')->get();
        }

        $tiposPrestacion = $tiposPrestacion->map(function ($tipoPrestacion) {
            return [
                'id' => $tipoPrestacion->Id,
                'nombre' => $tipoPrestacion->Nombre,
            ];
        });
        
        return response()->json(['tiposPrestacion' => $tiposPrestacion]);
    }

}
