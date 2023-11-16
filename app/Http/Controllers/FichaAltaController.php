<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Fichalaboral;
use App\Models\Paciente;
use Illuminate\Http\Request;

class FichaAltaController extends Controller
{
    public function save(Request $request): void
    {

        $ficha = Fichalaboral::where('IdPaciente', $request->paciente)->first();
        if ($ficha) {
            $ficha->IdEmpresa = $request->cliente ?? 0;
            $ficha->IdART = $request->art ?? 0;
            $ficha->Tareas = $request->tareaRealizar ?? '';
            $ficha->Pago = $request->pago ?? '';
            $ficha->TipoPrestacion = $request->tipoPrestacion ?? '';
            $ficha->Jornada = $request->horario ?? '';
            $ficha->TipoJornada = $request->tipo ?? '';
            $ficha->Observaciones = $request->observaciones ?? '';
            $ficha->TareasEmpAnterior = $request->ultimoPuesto;
            $ficha->Puesto = $request->puestoActual ?? '';
            $ficha->Sector = $request->sectorActual ?? '';
            $ficha->CCosto = $request->ccosto ?? '';
            $ficha->AntigPuesto = $request->antiguedadPuesto ?? '';
            $ficha->FechaIngreso = $request->fechaIngreso ?? '';
            $ficha->FechaEgreso = $request->fechaEgreso ?? '';
            $ficha->save();

        } else {

            Fichalaboral::create([
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
            ]);
        }

    }

    //Verificamos para emplear la vista full
    public function verificar(Request $request)
    {
        $fichaLaboral = Fichalaboral::where('IdPaciente', $request->Id)->first();

        if ($fichaLaboral) {

            $clienteArt = Cliente::where('Id', $fichaLaboral->IdART)->first();
            $cliente = Cliente::where('Id', $fichaLaboral->IdEmpresa)->first();

            return response()->json(['fichaLaboral' => $fichaLaboral, 'clienteArt' => $clienteArt, 'cliente' => $cliente]);

        }

    }

    public function checkObs(Request $request): mixed
    {

        $fichaLaboral = Fichalaboral::where('IdPaciente', $request->Id)->first(['IdArt', 'IdEmpresa']);

        if($fichaLaboral){

            $obsArt = Cliente::where('Id', $fichaLaboral->IdArt)->first(['Motivo', 'Observaciones']);
            $obsEmpresa = Cliente::where('Id', $fichaLaboral->IdEmpresa)->first(['Motivo', 'Observaciones']);
            $obsPaciente = Paciente::where('Id', $request->Id)->first(['Observaciones']);

            return response()->json([
                'obsArt' => $obsArt,
                'obsEmpresa' => $obsEmpresa,
                'obsPaciente' => $obsPaciente
            ]);
        }
    }

}
