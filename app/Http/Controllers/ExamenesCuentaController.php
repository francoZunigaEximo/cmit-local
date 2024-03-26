<?php

namespace App\Http\Controllers;

use App\Models\ExamenCuenta;
use App\Models\ExamenCuentaIt;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Traits\ObserverExamenesCuenta;
use Illuminate\Support\Facades\DB;

class ExamenesCuentaController extends Controller
{
    use ObserverExamenesCuenta;

    public function index(Request $request)
    {
        if ($request->ajax())
        {
            $query = $this->queryBasico();

            $result = $query->groupBy('pagosacuenta.Id', 'pagosacuenta.Tipo', 'pagosacuenta.Suc', 'pagosacuenta.Nro', 'pagosacuenta.Pagado')->limit(7)->orderBy('pagosacuenta.Id', 'DESC');

            return Datatables::of($result)->make(true);
        }

        return view('layouts.examenesCuenta.index');
    }

    public function search(Request $request)
    {
        if ($request->ajax())
        {
            $query = $this->queryBasico();
            
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

            $query->when(empty($request->estado), function ($query) {
                $query->where('pagosacuenta.Pagado', 0);
            });

            $query->when(!empty($request->estado) && $request->estado === 'pago', function ($query) {
                $query->where('pagosacuenta.Pagado', 1)
                      ->whereNot('pagosacuenta.Pagado', 0);
            });

            $query->when(!empty($request->estado) && $request->estado === 'todos', function ($query) {
                $query->whereIn('pagosacuenta.Pagado', [0,1]);
            });

            $result = $query->groupBy('pagosacuenta.Id', 'pagosacuenta.Tipo', 'pagosacuenta.Suc', 'pagosacuenta.Nro', 'pagosacuenta.Pagado');

            return Datatables::of($result)->make(true);
        }

        return view('layouts.examenesCuenta.index');
    }

    public function saldo(Request $request)
    {
        if ($request->ajax())
        {
            $query = ExamenCuenta::join('pagosacuenta_it', 'pagosacuenta.Id', '=', 'pagosacuenta_it.IdPago')
            ->join('clientes', 'pagosacuenta.IdEmpresa', '=', 'clientes.Id')
            ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->select(
                'clientes.RazonSocial as Empresa',
                'examenes.Nombre as Examen'
            );

            $query->selectRaw('COUNT(CASE WHEN pagosacuenta_it.IdPrestacion = 0 THEN 1 END) AS contadorSaldos');

            $query->when(!empty($request->examen) && empty($request->empresa), function ($query) use ($request) {
                $query->where('examenes.Id', $request->examen)
                        ->groupBy('examenes.Id')
                        ->groupBy('clientes.Id');
            });

            $query->when(!empty($request->empresa) && empty($request->examen), function ($query) use ($request) {
                $query->where('clientes.Id', $request->empresa)
                        ->groupBy('examenes.Nombre');
            });

            $query->when(empty($request->empresa) && empty($request->examen), function ($query) {
                $query->groupBy('clientes.RazonSocial')
                        ->groupBy('clientes.ParaEmpresa')
                        ->groupBy('clientes.Identificacion')
                        ->groupBy('examenes.Nombre');
            });

            $query->when(!empty($request->empresa) && !empty($request->examen), function ($query) use ($request){
                $query->where('examenes.Id', $request->examen)
                    ->where('clientes.Id', $request->empresa)
                    ->groupBy('pagosacuenta.Id')
                    ->groupBy('pagosacuenta.Fecha')
                    ->groupBy('pagosacuenta.Tipo')
                    ->groupBy('pagosacuenta.Suc')
                    ->groupBy('pagosacuenta.Nro')
                    ->groupBy('clientes.RazonSocial')
                    ->groupBy('clientes.ParaEmpresa')
                    ->groupBy('clientes.Identificacion')
                    ->groupBy('examenes.Nombre');
            });

            $result = $query->havingRaw('contadorSaldos > 0')
                ->whereNot('pagosacuenta_it.Obs', 'provisorio')
                ->orderBy('clientes.RazonSocial')
                ->orderBy('examenes.Nombre');

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

    public function create()
    {
        return view('layouts.examenesCuenta.create');
    }

    public function edit(ExamenCuenta $examenesCuentum)
    {
        return view('layouts.examenesCuenta.edit', compact(['examenesCuentum']));
    }

    public function save(Request $request)
    {
        $nuevoId = ExamenCuenta::max('Id') + 1;

        ExamenCuenta::create([
            'Id' => $nuevoId,
            'IdEmpresa' => $request->IdEmpresa,
            'Fecha' => $request->Fecha ?? now()->format('Y-m-d'),
            'Tipo' => $request->Tipo,
            'Suc' => $request->Suc,
            'Nro' => $request->Nro,
            'FechaP' => $request->FechaP ?? '0000-00-00',
            'Pagado' => $request->FechaP !== null ? 1 : 0,
            'Obs' => $request->Obs ?? ''
        ]);

        $nuevoId && $this->examenProvisorio($nuevoId);

        return response()->json(['id' => $nuevoId]);
    }

    public function detalles(Request $request)
    {

        $detalle = ExamenCuenta::join('pagosacuenta_it', 'pagosacuenta.Id', '=', 'pagosacuenta_it.IdPago')
            ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->select(
                'prestaciones.Id as IdPrestacion',
                'examenes.Nombre as NombreExamen',
                'pacientes.Nombre as NombrePaciente',
                'pacientes.Apellido as ApellidoPaciente'
            )
            ->where('pagosacuenta.Id', $request->Id)
            ->orderBy('pacientes.Apellido', 'ASC')
            ->get();

        return response()->json(['result' => $detalle]);   
    }

    public function delete(Request $request)
    {
        $examen = ExamenCuenta::find($request->Id);
        $resultado = [];

        if ($examen) 
        {
            $examen->delete();
            $resultado = ['message' => 'Se ha eliminado el examen a cuenta de manera correcta', 'estado' => 'success'];

        } else {

            $resultado = ['message' => 'Ha ocurrido un error en el ID de eliminación. Verifique por favor', 'estado' => 'fail'];
        }

        return response()->json($resultado);
    }

    private function queryBasico()
    {
        return ExamenCuenta::join('clientes', 'pagosacuenta.IdEmpresa', '=', 'clientes.Id')
        ->join('pagosacuenta_it', 'pagosacuenta.Id', '=', 'pagosacuenta_it.IdPago')
        ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
        ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
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
            'pacientes.Apellido as ApePaciente',
            'examenes.Nombre as Examen'
        );
    }

    public function listado(Request $request)
    {
        $query = ExamenCuentaIt::join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->select(
                'pagosacuenta_it.Obs as Precarga',
                'examenes.Nombre as Examen',
                'prestaciones.Id as Prestacion',
                'pacientes.Nombre as NombrePaciente',
                'pacientes.Apellido as ApellidoPaciente',
                'pagosacuenta_it.Id as IdEx'
            )
            ->whereNot('pagosacuenta_it.Obs', 'provisorio')
            ->whereNot('pagosacuenta_it.Obs2', 'provisorio')
            ->where('pagosacuenta_it.IdPago', $request->Id)
            ->get();

        return response()->json($query);


    }

    public function addExamen(Request $request)
    {
        $examenes = $request->Id;
    }
}
