<?php

namespace App\Http\Controllers;

use App\Models\ExamenCuenta;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class ExamenesCuentaController extends Controller
{
    public function index()
    {
        return view('layouts.examenesCuenta.index');
    }

    public function search(Request $request)
    {
        if ($request->ajax())
        {
            $query = ExamenCuenta::join('clientes', 'pagosacuenta.IdEmpresa', '=', 'clientes.Id')
                ->join('pagosacuenta_it', 'pagosacuenta.Id', '=', 'pagosacuenta_it.IdPago')
                ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
                ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->select(
                    'pagosacuenta.Id as IdEx',
                    'clientes.RazonSocial as Empresa',
                    'clientes.Identificacion as Cuit',
                    'clientes.ParaEmpresa as ParaEmpresa',
                    'pagosacuenta.FechaP as FechaPagado',
                    'pagosacuenta.Pagado as Pagado',
                    'pagosacuenta.Fecha as Fecha',
                    'pagosacuenta.Tipo as Tipo',
                    'pagosacuenta.Suc as Sucursal',
                    'pagosacuenta.Nro as Numero',
                    'pacientes.Nombre as NomPaciente',
                    'pacientes.Apellido as ApePaciente'
                );
            
            $FactDesde = explode('-', $request->rangoDesde);
            $FactHasta = empty($request->rangoHasta) ? $FactDesde : explode('-', $request->rangoHasta);

            $query->when(!empty($request->rangoDesde) || !empty($request->rangoHasta), function ($query) use ($FactDesde, $FactHasta) {

                $query->whereBetween('pagosacuenta.Tipo', [$FactDesde[0], $FactHasta[0]])
                    ->whereBetween('pagosacuenta.Suc', [intval($FactDesde[1], 10), intval($FactHasta[1], 10)])
                    ->whereBetween('pagosacuenta.Nro', [intval($FactDesde[2], 10), intval($FactHasta[2], 10)])
                    ->orderBy('pagosacuenta.Tipo', 'ASC')
                    ->orderBy('pagosacuenta.Suc', 'ASC')
                    ->orderBy('pagosacuenta.Nro', 'ASC');
            });

            $query->when(!empty($request->fechaDesde) && !empty($request->fechaHasta), function ($query) use ($request) {
                $query->whereBetween('pagosacuenta.Fecha', [$request->fechaDesde, $request->fechaHasta]);
            });

            $query->when(!empty($request->empresa), function ($query) use ($request) {
                $query->where('clientes.Id', $request->empresa);
            });

            $query->when(!empty($request->examen), function ($query) use ($request) {
                $query->where('examenes.Id', $request->examen);
            });

            $query->when(!empty($request->paciente), function ($query) use ($request) {
                $query->where('pacientes.Id', $request->paciente);
            });

            $result = $query->groupBy('pagosacuenta.Id', 'pagosacuenta.Tipo', 'pagosacuenta.Suc', 'pagosacuenta.Nro', 'pagosacuenta.Pagado');

            return Datatables::of($result)->make(true);
        }

        return view('layouts.examenesCuenta.index');
    }

    public function cambiarPago(Request $request)
    {
        $estados = $request->Id;
        $resultado = [];

        if (!is_array($estados)) {
            $estados = [$estados];
        }

        foreach($estados as $estado) {

            $item = ExamenCuenta::find($estado);

            if ($item) {

                $item->Pagado = $item->Pagado === 0 ? 1 : 0;
                $item->FechaP = $item->FechaP === '0000-00-00' ? now()->format('Y-m-d') : '0000-00-00';
                $item->save();
                $resultado = ['message' => 'Se ha realizado la actualización correctamente', 'estado' => 'success'];
                
            }
        }

        return response()->json($resultado);
    }


}
