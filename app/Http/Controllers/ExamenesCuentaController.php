<?php

namespace App\Http\Controllers;

use App\Models\ExamenCuenta;
use App\Models\ExamenCuentaIt;
use App\Models\Relpaqest;
use App\Models\Relpaqfact;
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
                    ->groupBy('clientes.Id')
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

    public function update(Request $request)
    {
        $examen = ExamenCuenta::find($request->Id);

        if($examen)
        {
            $examen->IdEmpresa = $request->IdEmpresa;
            $examen->Fecha = $request->Fecha;
            $examen->Tipo = $request->Tipo;
            $examen->Suc = $request->Suc;
            $examen->Nro = $request->Nro;
            $examen->FechaP = $request->FechaP ?? '0000-00-00';
            $examen->Pagado = $request->FechaP !== null ? 1 : 0;
            $examen->Obs = $request->Obs;
            $examen->save();

        }
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

    public function deleteItem(Request $request)
    {
        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach($examenes as $examen) {

            $item = ExamenCuentaIt::find($examen);
            $item && $item->delete();
        }
    }

    public function liberarItem(Request $request)
    {
        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach($examenes as $examen) {

            $item = ExamenCuentaIt::find($examen);
            if($item) 
            {
                $item->Obs = '-';
                $item->save();
            }
        }
    }

    public function precarga(Request $request)
    {

        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach($examenes as $examen) {

            $item = ExamenCuentaIt::find($examen);
            if($item) 
            {
                $item->Obs = $request->Obs;
                $item->save();
            }
        }
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
            ->where('pagosacuenta_it.IdPago', $request->Id)
            ->whereNot('pagosacuenta_it.Obs', 'provisorio')
            ->orderBy('pagosacuenta_it.Obs', 'Desc')
            ->get();

        return response()->json($query);


    }

    public function saveEx(Request $request)
    {

        switch($request->Tipo) {

            case 'examen':

                $obs = $request->Obs ?? '-';
                $obsAgregar = false;
                for ($i=0; $i < $request->cantidad; $i++) { 
                    ExamenCuentaIt::create([
                        'Id' => ExamenCuentaIt::max('Id') + 1,
                        'IdPago' => $request->Id,
                        'IdExamen' => $request->examen,
                        'IdPrestacion' => 0,
                        'Obs' => !$obsAgregar ? $obs : '',
                        'Obs2' => ''
                    ]);

                    if(!$obsAgregar) {
                        $obsAgregar = true;
                    }
                }
                break;

            case 'paquete':
                
                $obs = $request->Obs ?? '-';
                $obsAgregar = false;

                for ($i=0; $i < $request->cantidad; $i++) {
                    
                    $examenes = Relpaqest::where('IdPaquete', $request->examen)->get();

                    foreach($examenes as $e) {

                        ExamenCuentaIt::create([
                            'Id' => ExamenCuentaIt::max('Id') + 1,
                            'IdPago' => $request->Id,
                            'IdExamen' => $e->IdExamen,
                            'IdPrestacion' => 0,
                            'Obs' => !$obsAgregar ? $obs : '',
                            'Obs2' => ''
                        ]);  
                    }

                    if(!$obsAgregar) {
                        $obsAgregar = true;
                    }
                }
                break;
            
            case 'facturacion':
               
                $obs = $request->Obs ?? '-';
                $obsAgregar = false;

                for ($i=0; $i < $request->cantidad; $i++) {
                    
                    $examenes = Relpaqfact::where('IdPaquete', $request->examen)->get();

                    foreach($examenes as $e) {

                        ExamenCuentaIt::create([
                            'Id' => ExamenCuentaIt::max('Id') + 1,
                            'IdPago' => $request->Id,
                            'IdExamen' => $e->IdExamen,
                            'IdPrestacion' => 0,
                            'Obs' => !$obsAgregar ? $obs : '',
                            'Obs2' => ''
                        ]);  
                    }

                    if(!$obsAgregar) {
                        $obsAgregar = true;
                    }
                }
                break;
            }       
    }

    public function lstClientes(Request $request)
    {
        $clientes = ExamenCuenta::join('pagosacuenta_it', 'pagosacuenta.Id', '=', 'pagosacuenta_it.IdPago')
            ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->select(
                'pagosacuenta.Id as Id',
                'pagosacuenta.Fecha as Fecha',
                'pagosacuenta.Tipo as Tipo',
                'pagosacuenta.Suc as Suc',
                'pagosacuenta.Nro as Nro',
                'pagosacuenta.Obs as Obs',
                DB::raw('(SELECT COUNT(*) FROM pagosacuenta_it WHERE IdPago = pagosacuenta.Id AND IdExamen = examenes.Id AND pagosacuenta_it.IdPrestacion <> 0) as Cantidad')
            )
            ->where('IdEmpresa', $request->Id)
            ->whereNot('pagosacuenta_it.IdExamen', 0)
            //->whereNot('pagosacuenta_it.IdPrestacion', 0)
            ->orderBy('pagosacuenta.Id', 'Desc')
            ->orderBy('pagosacuenta.Fecha', 'Desc')
            ->groupBy('pagosacuenta.Id')
            ->get();

        return response()->json($clientes);
    }

    public function listadoDni(Request $request)
    {
        $clientes = ExamenCuentaIt::join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->select(
                'pacientes.Documento as Documento',
                'pagosacuenta_it.IdPrestacion as IdPrestacion',
                'pagosacuenta_it.IdPago as IdPago'
            )
            ->where('pagosacuenta_it.IdPago', $request->Id)
            ->orderBy('pagosacuenta_it.Id', 'Desc')
            ->groupBy('pagosacuenta_it.IdPrestacion')
            ->get();

        return response()->json($clientes);
    }
    
    public function listadoEx(Request $request)
    {
        $clientes = ExamenCuentaIt::join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->select(
                'examenes.Nombre as NombreExamen',
                'pagosacuenta.Tipo as Tipo',
                'pagosacuenta.Suc as Suc',
                'pagosacuenta.Nro as Nro',
                'pagosacuenta.Pagado as Pagado',
                DB::raw('(SELECT COUNT(*) FROM pagosacuenta_it WHERE IdPago = pagosacuenta.Id AND IdExamen = examenes.Id AND pagosacuenta_it.IdPrestacion = '.$request->Id.') as Cantidad')
            )
            ->where('pagosacuenta_it.IdPrestacion', $request->Id)
            ->where('pagosacuenta_it.IdPago', $request->IdPago)
            ->groupBy('pagosacuenta_it.IdExamen')
            ->orderBy('pagosacuenta_it.IdPrestacion', 'ASC')
            ->get();
        
        return response()->json($clientes);
        
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
}
