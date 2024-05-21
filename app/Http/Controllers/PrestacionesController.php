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
use App\Models\Auditor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Exports\PrestacionesExport;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PrestacionesController extends Controller
{

    use ObserverPrestaciones, ObserverFacturasVenta;

    public function index(Request $request): mixed
    {

        if ($request->ajax()) {

            $query = Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->join('clientes as emp', 'prestaciones.IdEmpresa', '=', 'emp.Id')
                ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
                ->join('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
                ->select(
                    DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdART) AS Art'),
                    DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdEmpresa) AS Empresa'),
                    DB::raw('COALESCE(COUNT(itemsprestaciones.IdPrestacion), 0) as Total'),
                    DB::raw('COALESCE(COUNT(CASE WHEN (itemsprestaciones.CAdj = 5 OR itemsprestaciones.CAdj = 3) AND itemsprestaciones.CInfo = 3 THEN itemsprestaciones.IdPrestacion END), 0) as CerradoAdjunto'),
                    'emp.ParaEmpresa as ParaEmpresa',
                    'emp.Identificacion as Identificacion',
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
                ->where('prestaciones.Fecha', '=', now()->format('Y-m-d'))
                ->orderBy('prestaciones.Id', 'DESC')
                ->groupBy('prestaciones.Id');

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
            ->join('clientes as emp', 'prestaciones.IdEmpresa', '=', 'emp.Id')
            ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
            ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->select(
                DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdART) AS Art'),
                DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdEmpresa) AS Empresa'),
                DB::raw('COALESCE(COUNT(itemsprestaciones.IdPrestacion), 0) as Total'),
                DB::raw('COALESCE(COUNT(CASE WHEN (itemsprestaciones.CAdj = 5 OR itemsprestaciones.CAdj = 3) AND itemsprestaciones.CInfo = 3 THEN itemsprestaciones.IdPrestacion END), 0) as CerradoAdjunto'),
                'emp.ParaEmpresa as ParaEmpresa',
                'emp.Identificacion as Identificacion',
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
                'prestaciones.TipoPrestacion as Tipo',
                'prestaciones.eEnviado as eEnviado',
                'prestaciones.Estado as Estado',
                'prestaciones.Facturado as Facturado',
                'prestaciones.Cerrado as Cerrado',
                'prestaciones.Finalizado as Finalizado',
                'prestaciones.Entregado as Entregado'
            )
            ->where('prestaciones.Estado', 1)
            ->groupBy('prestaciones.Id');
    
        if (!empty($request->nroprestacion)) {
            $query->where('prestaciones.Id', '=', $request->nroprestacion);
        } else {
            $query = $this->applyFilters($query, $request);
        }
    
        return $query;
    }
    
    private function applyFilters($query, $request)
    {
        if(!empty($request->pacienteSearch)) {
            $query->where(function ($query) use ($request) {
                $query->orWhere('pacientes.Nombre', 'LIKE', '%'. $request->pacienteSearch .'%')
                    ->orWhere('pacientes.Apellido', 'LIKE', '%'. $request->pacienteSearch .'%')
                    ->orWhere('pacientes.Documento', 'LIKE', '%'. $request->pacienteSearch .'%')
                    ->orWhere('pacientes.Identificacion', 'LIKE', '%'. $request->pacienteSearch .'%');
            });
        }

        if(!empty($request->empresaSearch)) {
            $query->where(function($query) use ($request) {
                $query->orWhere('emp.Identificacion', 'LIKE', '%'. $request->empresaSearch .'%')
                    ->orWhere('emp.ParaEmpresa', 'LIKE', '%'. $request->empresaSearch .'%')
                    ->orWhere('emp.NombreFantasia', 'LIKE', '%'. $request->empresaSearch .'%')
                    ->orWhere('emp.RazonSocial', 'LIKE', '%'. $request->empresaSearch .'%');
            });
        }

        if(!empty($request->artSearch)) {
            $query->where(function($query) use ($request) {
                $query->orwhere('art.RazonSocial', 'LIKE', '%'. $request->artSearch .'%')
                    ->orWhere('art.Identificacion', 'LIKE', '%'. $request->artSearch .'%')
                    ->orWhere('art.ParaEmpresa', 'LIKE', '%'. $request->artSearch .'%')
                    ->orWhere('art.NombreFantasia', 'LIKE', '%'. $request->artSearch .'%');
            });
        }

        if(!empty($request->pacienteSelect2)) {
            $query->where(function($query) use ($request) {
                $query->where('paciente.Id', $request->pacienteSelect2);
            });
        }

        if(!empty($request->empresaSelect2)) {
            $query->where(function($query) use ($request) {
                $query->where('emp.Id', $request->empresaSelect2);
            });
        }

        if(!empty($request->artSelect2)) {
            $query->where(function($query) use ($request) {
                $query->where('art.Id', $request->artSelect2);
            });
        }

        if (!empty($request->tipoPrestacion)) {
            $query->where('prestaciones.TipoPrestacion', $request->tipoPrestacion);
        }

        if (!empty($request->fechaDesde) && (!empty($request->fechaHasta))) {
            $query->whereBetween('prestaciones.Fecha', [$request->fechaDesde, $request->fechaHasta]);
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

        if (is_array($request->estado) && in_array('Finalizado', $request->estado)) {
            $query->where('prestaciones.Finalizado', '1');
        }

        if (is_array($request->estado) && in_array('Entregado', $request->estado)) {
            $query->where('prestaciones.Entregado', '1');
        }

        if (is_array($request->estado) && in_array('eEnviado', $request->estado)) {
            $query->where('prestaciones.eEnviado', '1');
        }

        if (is_array($request->estado) && in_array('Facturado', $request->estado)) {
            $query->where('prestaciones.Facturado', '1');
        }

        if (is_array($request->estado) && in_array('Pago-C', $request->estado)) {
            $query->where('prestaciones.Pago', 'C');
        }

        if (is_array($request->estado) && in_array('Pago-P', $request->estado)) {
            $query->where('prestaciones.Pago', 'P');
        }

        if (is_array($request->estado) && in_array('Pago-B', $request->estado)) {
            $query->where('prestaciones.Pago', 'B');
        }

        if (is_array($request->estado) && in_array('SPago-G', $request->estado)) {
            $query->where('prestaciones.SPago', 'G');
        }

        if (is_array($request->estado) && in_array('SPago-F', $request->estado)) {
            $query->where('prestaciones.SPago', 'F');
        }

        if (is_array($request->estado) && in_array('SPago-E', $request->estado)) {
            $query->where('prestaciones.SPago', 'E');
        }
    
        return $query;
    }
    
    public function create()
    {
        $hasPermission = false;
        foreach(Auth::user()->role as $rol) {
            if($rol->permiso->contains('slug', 'prestaciones_add')){
                $hasPermission = true;
                break;
            }
        }
        if(!$hasPermission){
            abort(403);
        }

        $tipoPrestacion = PrestacionesTipo::all();
        $paquetes = PaqueteEstudio::all();

        return view('layouts.prestaciones.create', compact(['tipoPrestacion', 'paquetes']));
    }

    public function edit(Prestacion $prestacione)
    {

        $hasPermission = false;
        foreach(Auth::user()->role as $rol) {
            if($rol->permiso->contains('slug', 'prestaciones_show')){
                $hasPermission = true;
                break;
            }
        }
        if(!$hasPermission){
            abort(403);
        }

        $tipoPrestacion = PrestacionesTipo::all();
        $financiador = Cliente::find($prestacione->Financiador, ['RazonSocial', 'Id', 'Identificacion']);
        $auditorias = Auditor::with('auditarAccion')->where('IdTabla', 1)->where('IdRegistro', $prestacione->Id)->orderBy('Id', 'Desc')->get();

        return view('layouts.prestaciones.edit', compact(['tipoPrestacion', 'prestacione', 'financiador', 'auditorias']));

    }

    public function estados(Request $request): mixed
    {

        $estado = Prestacion::find($request->Id);

        switch ($request->Tipo) {
            case 'cerrar':

                if ($estado->Cerrado === 0 && $estado->Finalizado === 0 && $estado->Entregado === 0) {
                    $estado->Cerrado = 1;
                    $estado->FechaCierre = now()->format('Y-m-d');
                }elseif($estado->Cerrado === 1 && $estado->Finalizado === 0 && $estado->Entregado === 0){
                    $estado->Cerrado = 0;
                    $estado->FechaCierre = null;
                } 
                break;

            case 'finalizar':
                if($estado->Cerrado === 1 && $estado->Finalizado === 0 && $estado->Entregado === 0){
                    $estado->Finalizado = 1;
                    $estado->FechaFinalizado = now()->format('Y-m-d');
                }elseif($estado->Cerrado === 1 && $estado->Finalizado === 1 && $estado->Entregado === 0){
                    $estado->Finalizado = 0;
                    $estado->FechaFinalizado = null; 
                }
                break;

            case 'entregar':
                if($estado->Cerrado === 1 && $estado->Finalizado === 1 && $estado->Entregado === 0){
                    $estado->Entregado = 1;
                    $estado->FechaEntrega = now()->format('Y-m-d');
                }elseif($estado->Cerrado === 1 && $estado->Finalizado === 1 && $estado->Entregado === 1){
                    $estado->Entregado = 0;
                    $estado->FechaEntrega = null; 
                }
                break;

            case 'eEnviar':
                if($estado->Cerrado === 1 && $estado->eEnviado === 0) {
                    $estado->eEnviado = 1;
                    $estado->FechaEnviado= now()->format('Y-m-d');
                    
                }elseif($estado->Cerrado === 1 && $estado->eEnviado === 1){
                    $estado->eEnviado = 0;
                    $estado->FechaEnviado = null; 
                }
                break;
        }

        $estado->save();

        return response()->json(['tipo' => $request->Tipo, 'estado' => $estado]);

    }

    public function down(Request $request): mixed
    {
        $hasPermission = false;
        foreach (Auth::user()->role as $rol) {
            if ($rol->permiso->contains('slug', 'prestaciones_delete')) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            return response()->json(['msg' => 'No tienes permisos para realizar esta acción'], 403);
        }

        $prestaciones = Prestacion::find($request->Id);

        if ($prestaciones) {
            $prestaciones->update(['Estado' => '0']);
            Auditor::setAuditoria($request->Id, 1, 14, Auth::user()->name);
            return response()->json(['msg' => 'Se ha dado de baja la prestación', 'estado' => 'true'], 200);
        }

        return response()->json(['msg' => 'No se encontró la prestación', 'estado' => 'false'], 404);
    }


    public function blockPrestacion(Request $request)
    {
        $hasPermission = false;
        foreach(Auth::user()->role as $rol) {
            if($rol->permiso->contains('slug', 'prestaciones_block')){
                $hasPermission = true;
                break;
            }
        }
        if(!$hasPermission){
            return response()->json(['msg' => 'No tienes permisos para realizar esta acción'], 403);
        }

        $prestaciones = Prestacion::find($request->Id);

        if($prestaciones)
        {
            $prestaciones->update(['Anulado' => '1']); // 0 => Habilitado, 1 => Anulado
            Auditor::setAuditoria($request->Id, 1, 3, Auth::user()->name);
            return response()->json(['msg' => 'Se ha bloqueado la prestación', 'estado' => 'true'], 200);
        }

        return response()->json(['msg' => 'No se encontró la prestación', 'estado' => 'false'], 404);
        
    }

    public function verifyBlock(Request $request)
    {

        $cliente = Cliente::find($request->cliente);

        if ($cliente) {

            return response()->json(['cliente' => $cliente]);
        } 
    }

    public function savePrestacion(Request $request)
    {
        $nuevoId = Prestacion::max('Id') + 1;

        Prestacion::create([
            'Id' => $nuevoId,
            'IdPaciente' => $request->paciente,
            'TipoPrestacion' => $request->tipoPrestacion,
            'Fecha' => $request->fecha,
            'IdMapa' => $request->tipoPrestacion <> 'ART' ? 0 : ($request->mapas ?? 0),
            'Pago' => $request->pago,
            'SPago' => $request->spago ?? '',
            'Observaciones' => $request->observaciones ??  '',
            'IdEmpresa' => $request->IdEmpresa,
            'IdART' => $request->IdART,
            'Fecha' => now()->format('Y-m-d'),
            'Financiador' => $request->financiador,
        ]);

        Auditor::setAuditoria($nuevoId, 1, 44, Auth::user()->name);

        $empresa = ($request->tipoPrestacion === 'ART' ? $request->IdART : $request->IdEmpresa);

        $request->mapas && $this->updateMapeados($request->mapas);

        if (!in_array($request->examenCuenta, [0, null, ''])) {
            $examenes = $this->registrarExamenCta($request->examenCuenta, $nuevoId);
        } 
    
        if (is_array($examenes) && !in_array($examenes, [0, null, ''])) {
            $this->registrarExamenes($examenes, $nuevoId);
        }

        if($request->tipo && $request->sucursal && $request->nroFactura && $nuevoId)
        {
            $this->addFactura($request->tipo, $request->sucursal, $request->nroFactura, $empresa, $request->tipoPrestacion, $nuevoId);
        }
    
        return response()->json(['nuevoId' => $nuevoId]);
    }

    public function updatePrestacion(Request $request)
    {

        $hasPermission = false;
        foreach(Auth::user()->role as $rol) {
            if($rol->permiso->contains('slug', 'prestaciones_edit')){
                $hasPermission = true;
                break;
            }
        }
        if(!$hasPermission){
            abort(403);
        }

        $prestacion = Prestacion::find($request->Id);
        $prestacion->IdEmpresa = $request->Empresa ?? 0;
        $prestacion->IdART = $request->Art ?? 0;
        $prestacion->Fecha = $request->Fecha ?? '';
        $prestacion->TipoPrestacion = $request->TipoPrestacion ?? '';
        $prestacion->IdMapa = ($request->Art === null || $request->Art === 0 ? 0 : ($request->TipoPrestacion <> 'ART' ? 0 : $request->Mapas));
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
        
        $request->SinEval && $this->setPrestacionAtributo($request->Id, $request->SinEval);
        $request->Obs && $this->setPrestacionComentario($request->Id, $request->Obs);
        $empresa = ($request->tipoPrestacion === 'ART' ? $request->ART : $request->Empresa);
        
        $this->updateFichaLaboral($request->IdPaciente, $request->Art, $request->Empresa);
        $this->addFactura($request->tipo, $request->sucursal, $request->nroFactura, $empresa, $request->tipoPrestacion, $request->Id);
        Auditor::setAuditoria($request->Id, 1, 2, Auth::user()->name);
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

    public function exportExcel(Request $request)
    {
        $ids        = $request->ids ? explode(",", $request->ids) : []; 
        $filters    = $request->filters ? explode(",", $request->filters) : [];
        $tipo       = $request->tipo;

        if($filters){
            $filtersAux = new stdClass() ;
            foreach ($filters as $filter) {
                $value = explode(":", $filter);
                $filtersAux->{$value[0]} = isset($value[1]) ? $value[1] : "";
            }
            $filters = $filtersAux;
        }
        
        return Excel::download(new PrestacionesExport($ids, $filters, $tipo), 'prestaciones.xlsx');
    }

    public function getBloqueo(Request $request)
    {
        $prestacion = Prestacion::where('Id', $request->Id)->first(['Anulado']);
        
        if($prestacion->Anulado === 1)
        {
            return response()->json(['prestacion' => true]);
        }
    }



}
