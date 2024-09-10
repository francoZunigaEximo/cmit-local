<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Fichalaboral;
use App\Models\Prestacion;
use App\Models\PrestacionesTipo;
use Illuminate\Http\Request;

class FichaAltaController extends Controller
{
    public function edit(Fichalaboral $fichalaboral)
    {
        $tiposPrestacionPrincipales = ['ART', 'INGRESO', 'PERIODICO', 'OCUPACIONAL', 'EGRESO', 'OTRO'];

        $tiposPrestacionOtros = PrestacionesTipo::whereNotIn('Nombre', $tiposPrestacionPrincipales)->get();

        return view('layouts.fichalaboral.edit', compact(['fichalaboral', 'tiposPrestacionOtros']));
    }

    public function save(Request $request)
    {
        if(!in_array($request->Id, ['', null, 0])) {

            $fichaLaboral = Fichalaboral::find($request->Id);
            $fichaLaboral->IdPaciente = $request->paciente;
            $fichaLaboral->IdEmpresa = $request->cliente ?? 0;
            $fichaLaboral->IdART = $request->art ?? 0;
            $fichaLaboral->TipoPrestacion = $request->tipoPrestacion ?? '';
            $fichaLaboral->Tareas = $request->tareaRealizar ?? '';
            $fichaLaboral->Jornada = $request->horario ?? '';
            $fichaLaboral->Pago = $request->pago ?? '';
            $fichaLaboral->TipoJornada = $request->tipo ?? '';
            $fichaLaboral->Observaciones = $request->observaciones ?? '';
            $fichaLaboral->TareasEmpAnterior = $request->ultimoPuesto ?? '';
            $fichaLaboral->Puesto = $request->puestoActual ?? '';
            $fichaLaboral->Sector = $request->sectorActual ?? '';
            $fichaLaboral->CCosto = $request->ccosto ?? '';
            $fichaLaboral->AntigPuesto = $request->antiguedadPuesto ?? '';
            $fichaLaboral->FechaIngreso = $request->fechaIngreso ?? '';
            $fichaLaboral->FechaEgreso = $request->fechaEgreso ?? '';
            $fichaLaboral->FechaPreocupacional = $request->fechaPreocupacional ?? '';
            $fichaLaboral->FechaUltPeriod = $request->fechaUltPeriod ?? '';
            $fichaLaboral->FechaExArt = $request->fechaExArt ?? '';
            $fichaLaboral->SPago = $request->Spago ?? '';
            $fichaLaboral->Tipo = $request->TipoF ?? '';
            $fichaLaboral->Sucursal = $request->SucursalF ?? '';
            $fichaLaboral->NroFactura = $request->NumeroF ?? '';
            $fichaLaboral->NroFactProv = $request->NumeroProvF ?? '';
            $fichaLaboral->Autorizado = $request->Autoriza ?? '';
            $fichaLaboral->save();

            if(!empty($request->idPrestacion)) {

                $prestacion = Prestacion::find($request->idPrestacion); 
                
                if($prestacion->Cerrado === 1) {

                    return response()->json(['msg' => 'No se ha actualizado la prestación porque la misma se encuentra cerrada'], 405);

                }else{
                    
                    $prestacion->IdEmpresa = in_array($request->cliente, ['', null, 0]) ? null : $request->cliente;
                    $prestacion->IdART = in_array($request->art, ['', null, 0]) ? null : $request->art;
                    $prestacion->TipoPrestacion = $request->tipoPrestacion ?? $prestacion->TipoPrestacion;
                    $prestacion->Pago = $request->pago ?? $prestacion->Pago;
                    $prestacion->save();
                }
            }
            return response()->json(['msg' => 'Se ha actualizado el registro correctamente'], 200);

        }else{

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
                'TareasEmpAnterior' => $request->ultimoPuesto ?? '',
                'Puesto' => $request->puestoActual ?? '',
                'Sector' => $request->sectorActual ?? '',
                'CCosto' => $request->ccosto ?? '',
                'AntigPuesto' => $request->antiguedadPuesto ?? '',
                'FechaIngreso' => $request->fechaIngreso ?? '',
                'FechaEgreso' => $request->fechaEgreso ?? '',
                'FechaPreocupacional' => $request->fechaPreocupacional ?? '',
                'FechaUltPeriod' => $request->fechaUltPeriod ?? '',
                'FechaExArt' => $request->fechaExArt ?? '',
                'SPago' => $request->Spago ?? '',
                'Tipo' => $request->TipoF ?? '',
                'Sucursal' => $request->SucursalF ?? '',
                'NroFactura' => $request->NumeroF ?? '',
                'NroFactProv' => $request->NumeroProvF ?? '',
                'Autorizado' => $request->Autoriza ?? ''
            ]);

            return response()->json(['msg' => 'Se ha creado el registro correctamente'], 200);
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
    public function getTipoPrestacion()
    {

        $tiposPrestacion = PrestacionesTipo::all();

        $tiposPrestacion = $tiposPrestacion->map(function ($tipoPrestacion) {
            return [
                'id' => $tipoPrestacion->Id,
                'nombre' => $tipoPrestacion->Nombre,
            ];
        });
        
        return response()->json(['tiposPrestacion' => $tiposPrestacion]);
    }

    public function verFicha(Request $request)
    {
        $query = Fichalaboral::where('IdPaciente', $request->Id)->orderBy('Id', 'DESC')->first();
        return redirect()->route('fichalaboral.edit', ['fichalaboral' => $query->Id]);
    }

}
