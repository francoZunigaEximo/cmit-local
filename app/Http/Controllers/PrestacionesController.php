<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Mapa;
use App\Models\Paciente;
use App\Models\PaqueteEstudio;
use App\Models\Prestacion;
use App\Models\PrestacionesTipo;
use App\Traits\ObserverFacturasVenta;
use App\Traits\ObserverPrestaciones;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PrestacionesController extends Controller
{

    use ObserverPrestaciones, ObserverFacturasVenta;

    public function index(Request $request): mixed
    {

        if ($request->ajax()) {

            $query = Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
                ->select(
                    DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdART) AS Art'),
                    DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdEmpresa) AS RazonSocial'),
                    'clientes.ParaEmpresa as ParaEmpresa',
                    'clientes.Identificacion as Identificacion',
                    'prestaciones.Fecha as FechaAlta',
                    'prestaciones.Id as Id',
                    'pacientes.Nombre as Nombre',
                    'pacientes.Apellido as Apellido',
                    'prestaciones.TipoPrestacion as Tipo',
                    'prestaciones.Anulado as Anulado',
                    'prestaciones.Pago as Pago',
                    'prestaciones.Ausente as Ausente',
                    'prestaciones.Incompleto as Incompleto',
                    'prestaciones.Devol as Devol',
                    'prestaciones.Forma as Forma',
                    'prestaciones.SinEsc as SinEsc',
                    'prestaciones.Estado as Estado',
                    'prestaciones.Facturado as Facturado'
                )
                ->where('prestaciones.Estado', '=', '1')
                ->where('prestaciones.Fecha', '=', Carbon::now()->format('Y-m-d'))
                ->orderBy('prestaciones.Id', 'DESC');

            return Datatables::of($query)->make(true);

        }

        return view('layouts.prestaciones.index');
    }


    public function search(Request $request): mixed
    {
        if ($request->ajax()) {
            $query = $this->buildQuery($request);
            return Datatables::of($query)->make(true);
        }
    
        return view('layouts.prestaciones.index');
    }
    
    private function buildQuery(Request $request)
    {
        $query = Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
            ->select(
                DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdART) AS Art'),
                DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdEmpresa) AS empresa'),
                DB::raw("CONCAT(pacientes.Apellido,pacientes.Nombre) AS nombreCompleto"),
                'clientes.ParaEmpresa as ParaEmpresa',
                'clientes.Identificacion as Identificacion',
                'prestaciones.Fecha as FechaAlta',
                'prestaciones.Id as Id',
                'pacientes.Nombre as Nombre',
                'pacientes.Apellido as Apellido',
                'prestaciones.Anulado as Anulado',
                'prestaciones.Pago as Pago',
                'prestaciones.FechaVto as FechaVencimiento',
                'prestaciones.Ausente as Ausente',
                'prestaciones.Incompleto as Incompleto',
                'prestaciones.Devol as Devol',
                'prestaciones.Forma as Forma',
                'prestaciones.SinEsc as SinEsc',
                'prestaciones.TipoPrestacion as TipoPrestacion',
                'prestaciones.eEnviado as eEnviado',
                'prestaciones.Estado as Estado'
            )
            ->where('prestaciones.Estado', 1);
    
        if (!empty($request->nroprestacion)) {
            $query->where('prestaciones.Id', '=', $request->nroprestacion);
        } else {
            $query = $this->applyBasicFilters($query, $request);
            $query = $this->applyAdvancedFilters($query, $request);
        }
    
        return $query;
    }
    
    private function applyBasicFilters($query, $request)
    {
        if(!empty($request->pacempart)) {
            $query->where(function ($query) use ($request) {
                $query->orwhere('clientes.RazonSocial', 'LIKE', '%'. $request->pacempart .'%')
                    ->orWhere('clientes.Identificacion', 'LIKE', '%'. $request->pacempart .'%')
                    ->orWhere('clientes.ParaEmpresa', 'LIKE', '%'. $request->pacempart .'%')
                    ->orWhere('clientes.NombreFantasia', 'LIKE', '%'. $request->pacempart .'%')
                    ->orWhere('pacientes.Nombre', 'LIKE', '%'. $request->pacempart .'%')
                    ->orWhere('pacientes.Apellido', 'LIKE', '%'. $request->pacempart .'%')
                    ->orWhere('pacientes.Documento', 'LIKE', '%'. $request->pacempart .'%')
                    ->orWhere('pacientes.Identificacion', 'LIKE', '%'. $request->pacempart .'%');
            });
        }

        if (!empty($request->tipoPrestacion)) {
            $query->where('prestaciones.TipoPrestacion', $request->tipoPrestacion);
        }
    
        if (!empty($request->pago)) {
            $query->where('prestaciones.Pago', '=', $request->pago);
        }
    
        if (!empty($request->formaPago)) {
            $query->where('prestaciones.SPago', '=', $request->formaPago);
        }

        if(!empty($request->eEnviado)){
            $query->where('prestaciones.eEnviado', '=', $request->eEnviado);
        }

        if (!empty($request->fechaDesde) && (!empty($request->fechaHasta))) {
            $fechaDesde = Carbon::parse($request->fechaDesde); // Creamos un objeto para poder manipular la
            $fechaDesde->addDay(); //Se agrega metodo addDay de Carbon para fixear los dias
            $query->whereBetween('prestaciones.Fecha', [$fechaDesde, $request->fechaHasta]);
        }

        if (is_array($request->estado) && in_array('Incompleto', $request->estado)) {
            $query->where('prestaciones.Incompleto', '1');
        }

        if (is_array($request->estado) && in_array('Anulado', $request->estado)) {
            $query->where('prestaciones.Anulado', '1');
        }

        if (is_array($request->estado) && in_array('Ausente', $request->estado)) {
            $query->where('prestaciones.Ausente', '1');
        }

        if (is_array($request->estado) && in_array('Forma', $request->estado)) {
            $query->where('prestaciones.Forma', '1');
        }

        if (is_array($request->estado) && in_array('SinEsc', $request->estado)) {
            $query->where('prestaciones.SinEsc', '1');
        }

        if (is_array($request->estado) && in_array('Devol', $request->estado)) {
            $query->where('prestaciones.Devol', '1');
        }
    
        if (is_array($request->estado) && in_array('RxPreliminar', $request->estado)) {
            $query->where('prestaciones.RxPreliminar', '1');
        }

        if (is_array($request->estado) && in_array('Cerrado', $request->estado)) {
            $query->where('prestaciones.Cerrado', '1');
        }

        if (is_array($request->estado) && in_array('Abierto', $request->estado)) {
            $query->where('prestaciones.Cerrado', '0')
                ->where('prestaciones.Finalizado', '0');
        }

        if (is_array($request->estado) && in_array('Cerrado', $request->estado)) {
            $query->where('prestaciones.Cerrado', '1');
        }
    
        return $query;
    }
    
    private function applyAdvancedFilters($query, $request)
    {

        if (!empty($request->finalizado)) {
            $query->where('prestaciones.Finalizado', '=', $request->finalizado);
        }
    
        if (!empty($request->facturado)) {
            $query->where('prestaciones.Facturado', '=', $request->facturado);
        }
    
        if (!empty($request->entregado)) {
            $query->where('prestaciones.Entregado', '=', $request->entregado);
        }
    
        return $query;
    }
    

    public function create()
    {
        $tipoPrestacion = PrestacionesTipo::all();
        $paquetes = PaqueteEstudio::all();

        return view('layouts.prestaciones.create', compact(['tipoPrestacion', 'paquetes']));
    }

    public function edit(Prestacion $prestacione)
    {

        $tipoPrestacion = PrestacionesTipo::all();
        $financiador = Cliente::find($prestacione->Financiador, ['RazonSocial', 'Id', 'Identificacion']);

        return view('layouts.prestaciones.edit', compact(['tipoPrestacion', 'prestacione', 'financiador']));

    }

    public function estados(Request $request): mixed
    {

        $estado = Prestacion::find($request->Id);


        switch ($request->Tipo) {
            case 'cerrar':
                if ($estado->Finalizado !== 1) {
                    $estado->Cerrado = ($estado->Cerrado === 0 && $estado->Entregado === 0 && $estado->eEnviado === 0 ? 1 : 0);
                }
                
                if ($estado->Cerrado === 1) {
                    $estado->FechaCierre = now()->format('Y-m-d');
                } else {
                    $estado->FechaCierre = null; 
                }
                break;

            case 'finalizar':
                if($estado->Entregado !== 1){
                    $estado->Finalizado = ($estado->Finalizado === 0 && $estado->Cerrado === 1 && $estado->Entregado === 0 && $estado->eEnviado === 0 ? 1 : 0);
                }
                
                if ($estado->Finalizado === 1) {
                    $estado->FechaFinalizado = now()->format('Y-m-d');
                } else {
                    $estado->FechaFinalizado = null; 
                }
                break;

            case 'entregar':
                if($estado->eEnviado !== 1){
                    $estado->Entregado = ($estado->Finalizado === 1 && $estado->Cerrado === 1 && $estado->Entregado === 0 && $estado->eEnviado === 0 ? 1 : 0);
                }
                
                if ($estado->Entregado === 1) {
                    $estado->FechaEntrega = now()->format('Y-m-d');
                } else {
                    $estado->FechaEntrega = null; 
                }
                break;

            case 'eEnviar':
                $estado->eEnviado = ($estado->Cerrado === 1 && $estado->eEnviado === 0 ? 1 : 0);
                if ($estado->eEnviado === 1) {
                    $estado->FechaEnviado= now()->format('Y-m-d');
                } else {
                    $estado->FechaEnviado = null; 
                }
                break;
        }

        $estado->save();

        return response()->json(['tipo' => $request->Tipo, 'estado' => $estado]);

    }

    public function down(Request $request): void
    {

        $prestaciones = Prestacion::find($request->Id);

        if($prestaciones)
        {
            $prestaciones->update(['Estado' => '0']);
        }
        
    }

    public function blockPrestacion(Request $request)
    {
        $prestaciones = Prestacion::find($request->Id);

        if($prestaciones){

            $prestaciones->Anulado = 1; // 0 => Habilitado, 1 => Anulado
            $prestaciones->save();

        }
        
    }

    public function verifyBlock(Request $request)
    {

        $cliente = Cliente::find($request->cliente);

        if ($cliente->Bloqueado == 1) {

            return response()->json(['RazonSocial' => $cliente->RazonSocial, 'Identificacion' => $cliente->Identificacion, 'Motivo' => $cliente->Motivo, 'Bloqueado' => $cliente->Bloqueado]);
        } else {

            return response()->json(['Bloqueado' => $cliente->Bloqueado]);
        }
    }

    public function getPago(Request $request)
    {
        $cliente = Cliente::where('Id', $request->financiador)->first();

        $formaPago = $cliente ? $cliente->FPago : '';

        return response()->json(['option' => $formaPago]);
    }

    public function savePrestacion(Request $request)
    {
        $nuevoId = Prestacion::max('Id') + 1;

        Prestacion::create([
            'Id' => $nuevoId,
            'IdPaciente' => $request->paciente,
            'TipoPrestacion' => $request->tipoPrestacion,
            'Fecha' => $request->fecha,
            'IdMapa' => $request->mapas ?? '0',
            'Pago' => $request->pago,
            'SPago' => $request->spago ?? '',
            'Observaciones' => $request->observaciones ??  '',
            'IdEmpresa' => $request->IdEmpresa,
            'IdART' => $request->IdART,
            'Fecha' => date('Y-m-d'),
            'Financiador' => $request->financiador,
        ]);

        $empresa = ($request->tipoPrestacion === 'ART' ? $request->IdART : $request->IdEmpresa);

        if($request->mapas)
        {
            $this->updateMapeados($request->mapas);
        }

        if($request->tipo && $request->sucursal && $request->nroFactura && $nuevoId)
        {
            $this->addFactura($request->tipo, $request->sucursal, $request->nroFactura, $empresa, $request->tipoPrestacion, $nuevoId);
        }
        

        return response()->json(['nuevoId' => $nuevoId]);
    }

    public function updatePrestacion(Request $request)
    {
   
        $prestacion = Prestacion::find($request->Id);
        $prestacion->IdEmpresa = $request->Empresa ?? 0;
        $prestacion->IdART = $request->Art ?? 0;
        $prestacion->Fecha = $request->Fecha ?? '';
        $prestacion->TipoPrestacion = $request->TipoPrestacion ?? '';
        $prestacion->IdMapa = $request->Mapas ?? 0;
        $prestacion->Pago = $request->Pago ?? '';
        $prestacion->SPago = $request->SPago ?? '';
        $prestacion->Financiador = ($request->TipoPrestacion == 'ART' ? $request->Art : $request->Empresa) ?? 0;
        $prestacion->Observaciones = $request->Observaciones ?? '';
        $prestacion->NumeroFacturaVta = $request->NumeroFacturaVta ?? 0;
        $prestacion->IdEvaluador = $request->IdEvaluador ?? 0;
        $prestacion->Evaluacion = $request->Evaluacion ?? 0;
        $prestacion->Calificacion = $request->Calificacion ?? 0;
        $prestacion->RxPreliminar = $request->RxPreliminar === 'true' ? 1 : 0;
        $prestacion->ObsExamenes = $request->ObsExamenes ?? '';
        $prestacion->save();
        
        if($request->SinEval){
            $this->setPrestacionAtributo($request->Id, $request->SinEval);
        }

        $empresa = ($request->tipoPrestacion === 'ART' ? $request->ART : $request->Empresa);
        
        $this->updateFichaLaboral($request->IdPaciente, $request->Art, $request->Empresa);
        $this->addFactura($request->tipo, $request->sucursal, $request->nroFactura, $empresa, $request->tipoPrestacion, $request->Id);

    }

    public function vencimiento(Request $request): void
    {
        $prestacion = Prestacion::find($request->Id);
        if($prestacion){
            $prestacion->update(['Vto' => 1]);
        }  
    }

    public function getParaEmpresas(Request $request): mixed
    {

        $buscar = $request->buscar;
        $tipo = $request->tipo;
        $searchCliente = Cliente::find($request->IdCliente);

        $resultados = Cache::remember('ParaEmpreas_'.$buscar, 5, function () use ($buscar, $tipo, $searchCliente) {

            $clientes = Cliente::where(function ($query) use ($buscar) {
                $query->where('ParaEmpresa', 'LIKE', '%'.$buscar.'%')
                    ->orWhere('Identificacion', 'LIKE', '%'.$buscar.'%');
            })
                ->where('TipoCliente', '=', $tipo)
                ->where('Identificacion', '=', $searchCliente->Identificacion)
                ->get();

            $resultados = [];

            foreach ($clientes as $cliente) {
                $resultados[] = [
                    'id' => $cliente->Id,
                    'text' => $cliente->RazonSocial.' - '.$cliente->Identificacion,
                ];
            }

            return $resultados;

        });

        return response()->json(['paraEmpresas' => $resultados]);
    }

    public function getPresPaciente(Request $request): mixed
    {

        $prestacion = Prestacion::find($request->Id);
        $mapa = Mapa::join('clientes', 'mapas.IdART', '=', 'clientes.Id')
            ->select(
                'clientes.Id as Id',
                'clientes.RazonSocial as RazonSocial',
                'clientes.Identificacion as Identificacion')
            ->where('clientes.Id', '=', $prestacion->IdMapa)
            ->first();

        if ($mapa) {

            $resultados[] = [
                'id' => $mapa->Id,
                'text' => $mapa->RazonSocial.' - '.$mapa->Identificacion,
            ];

            return response()->json(['resultado' => $prestacion, 'mapa' => $resultados ?? '']);

        } 
    }

    public function verifyWizard(Request $request)
    {
        $query = Paciente::where('Documento', $request->Documento)->first();
        $existe = $query !== null;

        return response()->json(['existe' => $existe, 'paciente' => $query]);
    }

}
