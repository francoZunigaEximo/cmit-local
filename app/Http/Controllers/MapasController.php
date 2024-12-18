<?php

namespace App\Http\Controllers;

use App\Models\Auditor;
use App\Models\ItemPrestacion;
use App\Models\Prestacion;
use App\Models\Mapa;
use App\Models\Paciente;
use App\Models\Constanciase;
use App\Models\ConstanciaseIt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\ObserverMapas;
use Illuminate\Support\Facades\Auth;
use App\Traits\CheckPermission;
use App\Traits\ReporteExcel;


class MapasController extends Controller
{

    const TBLMAPA = 5; // cod de Mapas en la tabla auditariatablas

    use ObserverMapas, CheckPermission, ReporteExcel;

    public function index(Request $request): mixed
    {
        if (!$this->hasPermission('mapas_show')) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $Nro = $request->Nro;
        $Art = $request->Art;
        $Empresa = $request->Empresa;
        $Estado = $request->Estado;
        $corteDesde = $request->corteDesde;
        $corteHasta = $request->corteHasta;
        $entregaDesde = $request->entregaDesde;
        $entregaHasta = $request->entregaHasta;
        $Vencimiento = $request->Vencimiento;
        $Ver = $request->Ver;

        if ($request->ajax()) {

            $query = Mapa::leftJoin('prestaciones', 'mapas.Id', '=', 'prestaciones.IdMapa')
            ->leftJoin('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('clientes', 'mapas.IdART', '=', 'clientes.Id')
            ->join('clientes AS clientes2', 'mapas.IdEmpresa', '=', 'clientes2.Id')
            ->select(
                    'mapas.Id AS Id', 
                    'mapas.Nro AS Nro',
                    'mapas.Fecha AS Fecha', 
                    'mapas.FechaE AS FechaE',
                    'mapas.Cmapeados AS Cmapeados',
                    'mapas.Cpacientes AS contadorPacientes',
                    'clientes.RazonSocial AS Art',
                    'clientes.ParaEmpresa AS ParaEmpresa_Art',
                    'clientes.NombreFantasia AS NombreFantasia_Art',   
                    'clientes2.RazonSocial AS Empresa', 
                    'clientes2.ParaEmpresa AS ParaEmpresa_Empresa',
                    'clientes2.ParaEmpresa AS NombreFantasia_Empresa',  
                    'prestaciones.eEnviado AS eEnviado', 
                    'prestaciones.Cerrado AS Cerrado', 
                    'prestaciones.Entregado AS Entregado', 
                    'prestaciones.Finalizado AS Finalizado', 
                    'prestaciones.IdPaciente AS IdPaciente', 
                    'prestaciones.Id AS IdPrestacion'
                    )
                ->selectRaw('COALESCE((SELECT COUNT(*) FROM prestaciones WHERE IdMapa = mapas.Id AND prestaciones.Anulado = 0), 0) AS contadorPrestaciones')
                ->selectRaw("COALESCE((SELECT COUNT(*) FROM prestaciones WHERE IdMapa = mapas.Id AND prestaciones.Anulado = 1), 0) AS cdorPacientesAnulados")
                ->selectRaw("COALESCE((SELECT COUNT(*) FROM prestaciones WHERE IdMapa = mapas.Id AND eEnviado = 1), 0) AS cdorEEnviados")
                ->selectRaw("COALESCE((SELECT COUNT(*) FROM prestaciones WHERE IdMapa = mapas.Id AND Finalizado = 1), 0) AS cdorFinalizados")
                ->selectRaw("COALESCE((SELECT COUNT(*) FROM prestaciones WHERE IdMapa = mapas.Id AND Cerrado = 1), 0) AS cdorCerrados")   
                ->selectRaw("COALESCE((SELECT COUNT(*) FROM prestaciones WHERE IdMapa = mapas.Id AND Entregado = 1), 0) AS cdorEntregados");
            

            $query->when($Nro, function ($query) use ($Nro) {
                $query->where('mapas.Nro', $Nro);
                    
            });

            $query->when($Art, function ($query) use ($Art) {
                $query->where('clientes.Id', $Art);
            });

            $query->when($Empresa, function ($query) use ($Empresa) {
                $query->where('clientes2.Id', $Empresa);
            });

            //Terminado
            $query->when(!empty($Estado) && $Estado === 'terminado', function ($query) {
                $query->havingRaw('contadorPrestaciones > 0')
                    ->havingRaw('contadorPrestaciones = cdorCerrados')
                    ->havingRaw('contadorPrestaciones = cdorFinalizados')
                    ->havingRaw('contadorPrestaciones = cdorEntregados');
            });
            
            //Abierto
            $query->when(!empty($Estado) && $Estado === 'abierto', function ($query) {
                $query->having('contadorPrestaciones', '>', 0)
                ->having('cdorCerrados', 0)
                ->having('cdorFinalizados', 0)
                ->having('cdorEEnviados', 0)
                ->having('cdorEntregados', 0);
            });

            //Cerrado
            $query->when(!empty($Estado) && $Estado === 'cerrado', function ($query){
                $query->havingRaw('contadorPrestaciones > 0')
                ->havingRaw('contadorPrestaciones = cdorCerrados')
                ->havingRaw('cdorFinalizados = 0')
                ->havingRaw('cdorEEnviados = 0')
                ->havingRaw('cdorEntregados = 0');
            });

            //enProceso
            $query->when(!empty($Estado) && $Estado === 'enProceso', function ($query) {
                $query->having('contadorPrestaciones', '>', 0)
                    ->having('cdorFinalizados', 0)
                    ->having('cdorEEnviados', 0)
                    ->having('cdorEntregados', 0);
            
                $query->where(function($query) {
                    $query->having('cdorCerrados', 0)
                          ->orWhere(function ($query) {
                              $query->havingRaw('contadorPrestaciones = cdorCerrados');
                          });
                });
            });
            

            //Todos
            $query->when(empty($Estado) && $Estado === 'todos', function ($query){
                $query->addSelect(DB::raw("'Todos' as estado"));
            });

            //Vacio
            $query->when(!empty($Estado) && $Estado === 'vacio', function ($query){
                $query->having('contadorPrestaciones', 0);
                });

            $query->when(!empty($corteDesde) && ! empty($corteHasta), function ($query) use ($corteDesde, $corteHasta) {
                $query->whereBetween('mapas.Fecha', [$corteDesde, $corteHasta]);
            });

            $query->when(!empty($entregaDesde) && ! empty($entregaHasta), function ($query) use ($entregaDesde, $entregaHasta) {
                $query->whereBetween('mapas.FechaE', [$entregaDesde, $entregaHasta]);
            });

            $query->when(is_array($Vencimiento) && in_array('corteVencido', $Vencimiento), function ($query){
                $query->where('mapas.Fecha', '<', now()->format('Y-m-d'))
                    ->whereNot('mapas.Nro', 0)
                    ->whereNot('mapas.Fecha', '0000-00-00')
                    ->whereNotNull('mapas.Fecha');
            });

            $query->when(is_array($Vencimiento) && in_array('corteVigente', $Vencimiento), function ($query){
                $query->where('mapas.Fecha', '>=', now()->format('Y-m-d'))
                    ->whereNot('mapas.Nro', 0)
                    ->whereNot('mapas.Fecha', '0000-00-00')
                    ->whereNotNull('mapas.Fecha');
            });

            $query->when(is_array($Vencimiento) && in_array('entregaVigente', $Vencimiento), function ($query){
                $query->where('mapas.FechaE', '>=', now()->format('Y-m-d'))
                    ->whereNot('mapas.Nro', 0)
                    ->whereNot('mapas.FechaE', '0000-00-00')
                    ->whereNotNull('mapas.FechaE');
            });

            $query->when(is_array($Vencimiento) && in_array('entregaVencida', $Vencimiento), function ($query){
                $query->where('mapas.FechaE', '<', now()->format('Y-m-d'))
                    ->whereNot('mapas.Nro', 0)
                    ->whereNot('mapas.FechaE', '0000-00-00')
                    ->whereNotNull('mapas.FechaE');
            });

            $query->when($Ver == 'activo', function ($query) {
                $query->where('mapas.Inactivo', 0);
            });

            $query->when($Ver == 'inactivo', function ($query) {
                $query->where('mapas.Inactivo', 1);
            });

            // dd($query->toSql(), $query->getBindings());

            $result = $query->groupBy('mapas.Nro')
                        ->whereNot('mapas.Nro', 0)
                        ->orderByDesc('mapas.Id');

            return Datatables::of($result)->make(true);
        }

        return view('layouts.mapas.index');

    }

    public function create(): mixed
    {
        if (!$this->hasPermission('mapas_add')) {
            abort(403);
        }
        return view('layouts.mapas.create');
    }

    public function edit(Mapa $mapa)
    {
        if (!$this->hasPermission("mapas_edit")) {
            abort(403);
        }

        $cerradas = $this->contadorCerrado($mapa->Id);
        $finalizados = $this->contadorFinalizado($mapa->Id);
        $entregados = $this->contadorEntregado($mapa->Id);
        $conEstado = $this->contadorConEstado($mapa->Id);
        $completas = $this->contadorCompletas($mapa->Id);
        $enProceso = $this->contadorEnProceso($mapa->Id);
        $presentes = Prestacion::where('IdMapa', $mapa->Id)->count();
        $ausentes = (intval($mapa->Cpacientes) ?? 0) - $presentes;
        $auditorias = Auditor::with('auditarAccion')->where('IdTabla', 5)->where('IdRegistro', $mapa->Id)->orderBy('Id', 'Desc')->get();

        return view('layouts.mapas.edit', compact(['mapa', 'cerradas', 'finalizados', 'entregados', 'conEstado', 'presentes', 'completas', 'enProceso', 'ausentes', 'auditorias']));
    }


    public function store(Request $request)
    {
        if(!$this->hasPermission('mapas_add')) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $nuevoId = Mapa::max('Id') + 1;

        Mapa::create([
            'Id' => $nuevoId,
            'Nro' => $request->Nro,
            'IdART' => $request->IdART,
            'IdEmpresa' => $request->IdEmpresa,
            'Fecha' => $request->Fecha ?? '0000-00-00',
            'FechaE' => $request->FechaE ?? '0000-00-00',
            'Estado' => $request->Estado,
            'Cpacientes' => $request->Cpacientes ?? 0,
            'Cmapeados' => $request->Cpacientes ?? 0,
            'Inactivo' => $request->Estado ?? 0,
            'Obs' => $request->Obs ?? '',
            'eEnviado' => 0,
        ]);

        return redirect()->route('mapas.edit', ['mapa' => $nuevoId]);
    }

    public function updateMapa(Request $request): mixed
    {
        if(!$this->hasPermission('mapas_edit')) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $mapa = Mapa::where('Id', $request->Id)->first();
        $totalPrestacion = Prestacion::where('IdMapa', $request->Id)->count();

        if ($request->Cpacientes < $totalPrestacion && $mapa->Cmapeados !== $mapa->Cpacientes) {
            return response()->json(['msg' => 'No se puede actualizar el numero de pacientes porque el numero de pacientes que ya integra el mapa es superior'], 409);
        }

        $data = $this->nuevosPacientesMapeados($mapa->Cpacientes, $mapa->Cmapeados, $request->Cpacientes);

        if ($data['totalMapeados'] < 0 ) {
            return response()->json(['msg' => 'Ya hay prestaciones en el mapa y la cantidad de pacientes no puede puede ser menor a los mapeados'], 409);
        }

        if ($mapa)
        {
            $mapa->Nro = $request->Nro;
            $mapa->IdART = $request->IdART;
            $mapa->IdEmpresa = $request->IdEmpresa;
            $mapa->Fecha = $request->FechaEdicion;
            $mapa->FechaE = $request->FechaEEdicion;
            $mapa->Inactivo = $request->Estado;
            $mapa->Obs = $request->Obs;
            $mapa->Cmapeados = $data['totalMapeados'] ?? $request->Cmapeados;
            $mapa->Cpacientes = $data['pacientes'] ?? $request->Cpacientes;
            $mapa->save();
        
            return response()->json(['msg' => 'Mapa actualizado'], 200);
        } else {
            return response()->json(['msg' => 'Mapa no encontrado'], 404);
        }
    }

    public function delete(Request $request)
    {
        if (!$this->hasPermission('mapas_delete')) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $mapa = Mapa::find($request->Id);
        if ($mapa) {
            $mapa->Inactivo = 1;
            $mapa->save();

            return response()->json(['msg' => 'Mapa eliminado'], 200);
        }

        return response()->json(['msg' => 'Mapa no encontrado'], 404);
    }

    public function prestaciones(Request $request)
    {
        $query = $this->queryPrestaciones($request->mapa);
        $result = $query->distinct()->get();
        return response()->json($result);
    }

    public function searchMapaPres(Request $request)
    {
            $NroPrestacion = $request->NroPrestacion;
            $NroRemito = $request->NroRemito;
            $Etapa = $request->Etapa;
            $Estado = $request->Estado;
            
            $query = $this->queryPrestaciones($request->mapa);
            $query->when($NroPrestacion, function ($query) use ($NroPrestacion) {
                $query->where('prestaciones.Id', $NroPrestacion);
            });
            
            $query->when($NroRemito, function ($query) use ($NroRemito) {
                $query->where('prestaciones.NroCEE', $NroRemito);
            });
            
            $query->when($Estado === 'abierto', function ($query) {
                $query->selectRaw("'Abierto' as estado");
                $query->where('prestaciones.Finalizado', 0)
                    ->where('prestaciones.Cerrado', 0)
                    ->where('prestaciones.eEnviado', 0);
            });

            $query->when($Estado === 'cerrado', function ($query) {
                $query->selectRaw("'Cerrado' as estado");
                $query->where('prestaciones.Cerrado', 1);
            });

            $query->when($Estado === 'finalizado', function ($query) {
                $query->selectRaw("'Finalizado' as estado");
                $query->where('prestaciones.Finalizado', 1);
            });

            $query->when($Estado === 'eEnviado', function ($query) {
                $query->selectRaw("'eEnviado' as estado");
                $query->where('prestaciones.eEnviado', 1);
            });

            $query->when($Estado === 'entregado', function ($query) {
                $query->selectRaw("'Entregado' as estado");
                $query->where('prestaciones.Entregado', 1);
            });

            $query->when($Estado === 'anulado', function ($query) {
                $query->selectRaw("'Anulado' as estado");
                $query->where('prestaciones.Anulado', 1);
            });

            $query->when($Etapa === 'completa', function ($query) {
                $query->where(function ($query) {
                    $query->where('prestaciones.Incompleto', 0);
                });
            });

            $query->when($Etapa === 'incompleta', function ($query) {
                $query->where(function ($query) {
                    $query->where('prestaciones.Incompleto', 1);
                });
            });
              
            $result = $query->distinct()->get();
            
            return response()->json(['result' => $result]);
    }

    public function show($id)
    {
        // Show
    }

    public function export(Request $request)
    {
        $ids = $request->Id;
        if (! is_array($ids)) {
            $ids = [$ids];
        }
        $mapas = $this->queryBase();

        if ($request->archivo === 'csv')
        {

            $mapas->when($request->modulo === 'remito', function ($mapas) use ($ids, $request) {
                $mapas->whereIn('prestaciones.NroCEE', $ids)
                        ->where('mapas.Nro', $request->mapa);
            });

            $mapas->when(empty($request->Tipo), function ($mapas) use ($ids) {
                $mapas->whereIn('mapas.Id', $ids);
            });

            $result = $mapas->orderBy('prestaciones.Id', 'DESC')->get();

            if ($result->isEmpty()) {
                return response()->json(['msg' => 'No se encontraron datos para exportar. Hay un conflicto'], 409);
            }

            return $this->listadoMapa($result);

        } elseif ($request->archivo === 'pdf') {

            $result = $mapas->whereIn('prestaciones.NroCEE', $ids)
            ->where('mapas.Nro', $request->mapa)
            ->orderBy('prestaciones.Id', 'DESC')->get(); 

            if ($result->isEmpty()) {
                return response()->json(['msg' => 'No se encontraron datos para generar el PDF. Hay un conflicto'], 409);
            }
            
            $pdf = PDF::loadView('layouts.mapas.pdf', ['result' => $result]);
            $path = storage_path('app/public/');
            $fileName = time() . '.pdf';
            $pdf->save($path . $fileName);
            
            $filePath = $path . $fileName;
            chmod($filePath, 0777);

            return response()->json(['filePath' => $filePath, 'msg' => 'Reporte generado']);
        }
    }

    //Obtenemos listado mapas
    public function getMapas(Request $request)
    {
        $empresa= $request->empresa;
        $art = $request->art;

        $mapas = Mapa::join('clientes as Art', 'mapas.IdART', '=', 'Art.Id')
            ->join('clientes as Empresa', 'mapas.IdEmpresa', '=', 'Empresa.Id')
            ->select(
                'mapas.Id as Id',
                'mapas.Nro as Nro',
                'Art.RazonSocial as RSArt',
                'Empresa.RazonSocial as RSE')
            ->where('mapas.IdART', $art)
            ->where('mapas.IdEmpresa', $empresa)
            ->where('mapas.Cmapeados', '>=', 1)
            ->whereDate('mapas.Fecha', '>', now())
            ->whereNot('mapas.Inactivo', 1)
            ->get();

        return response()->json(['mapas' => $mapas]);
    }

    public function saveRemitos(Request $request)
    {
        $remitos = Prestacion::where('NroCEE', $request->Id)->get();

        if ($remitos) 
        {

            foreach ($remitos as $remito) {
                $remito->Entregado = 1;
                $remito->FechaEntrega = $request->FechaE;
                $remito->save();
            }
    
            constanciase::obsRemito($request->Id, $request->Obs);
            return response()->json(['msg' => 'Se han registrado las fechas de entrega en los remitos correspondientes'], 200);

        } else {
            return response()->json(['msg' => 'Ha ocurrido un error y no se ha registrado la fecha'], 500);
        }

        
    }

    public function getPacienteMapa(Request $request)
    {
        $prestacion = Prestacion::where('Id', $request->prestacion)->first('IdPaciente');
        $paciente = Paciente::where('Id', $prestacion->IdPaciente)->first(['Nombre', 'Apellido', 'TipoDocumento', 'Documento']);

        return response()->json($paciente);

    }

    public function examenes(Request $request)
    {

        $query = Cache::remember('examenes_'.$request, 5, function () use ($request) {

            $q = ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=','archivosefector.IdEntidad')
                ->leftJoin('proveedores', 'itemsprestaciones.IdProveedor', '=', 'proveedores.Id')
                ->leftJoin('profesionales', 'itemsprestaciones.IdProfesional', '=', 'profesionales.Id')
                ->select(
                    'examenes.Id AS IdExamen',
                    'examenes.Nombre AS NombreExamen',
                    'examenes.Informe AS Informe',
                    'itemsprestaciones.CAdj AS CAdj',
                    'itemsprestaciones.CInfo AS CInfo',
                    'itemsprestaciones.Id AS IdItemPrestacion',
                    'itemsprestaciones.Incompleto AS Incompleto',
                    'examenes.Adjunto AS ExamenAdjunto',
                    'proveedores.Nombre AS NombreProveedor',
                    'profesionales.Nombre AS NombreEfector',
                    'profesionales.Apellido AS ApellidoEfector',
                    DB::raw('(SELECT Nombre FROM profesionales WHERE Id = itemsprestaciones.IdProfesional2) AS NombreInformador'),
                    DB::raw('(SELECT Apellido FROM profesionales WHERE Id = itemsprestaciones.IdProfesional2) AS ApellidoInformador'),
                    DB::raw('(SELECT CASE WHEN COUNT(*) = SUM(CASE WHEN itemsprestaciones.Id = archivosefector.IdEntidad THEN 1 ELSE 0 END) THEN "adjunto" ELSE "sadjunto" END FROM itemsprestaciones WHERE itemsprestaciones.Id = archivosefector.IdEntidad) AS adjuntados')
                )
                ->where('itemsprestaciones.IdPrestacion', $request->prestacion)
                ->get();

                return $q;

            });

         return response()->json($query);
    }

    public function getCerrar(Request $request)
    {
        $query = $this->queryCerrar($request->mapa);
        
        $query->where(function ($query) {
            $query->where('prestaciones.Cerrado', 0)
                ->where('prestaciones.Finalizado', 0)
                ->where('prestaciones.Entregado', 0)
                ->where('prestaciones.eEnviado', 0);
        });

        $result = $query->get();

        return response()->json(['result' => $result]);
    }

    public function serchInCerrar(Request $request): mixed
    {

        $NroPresCerrar = $request->prestacion;
        $EstadoCerrar = $request->estado;

        $query = $this->queryCerrar($request->mapa);

        $query->when($NroPresCerrar, function ($query) use ($NroPresCerrar) {
            $query->where('prestaciones.Id', $NroPresCerrar);
        });

        $query->when($EstadoCerrar === 'abierto', function ($query) {
            $query->where('prestaciones.Finalizado', '=', 0)
                ->where('prestaciones.Cerrado', '=', 0)
                ->where('prestaciones.Entregado', 0)
                ->where('prestaciones.eEnviado', 0);
        });
        
        $query->when($EstadoCerrar === 'cerrado', function ($query) {
            $query->where('prestaciones.Cerrado', 1)
                ->where('prestaciones.Finalizado', 0)
                ->where('prestaciones.Entregado', 0)
                ->where('prestaciones.eEnviado', 0);
        });

        $result = $query->get();

        return response()->json(['result' => $result]);
    }

    public function saveCerrar(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $prestacion = Prestacion::where('Id', $id)->first();
            
            if ($prestacion) {
                $prestacion->Cerrado = 1;
                $prestacion->FechaCierre = now()->format('Y-m-d');
                $prestacion->save();
            } 
        }
    }

    public function saveEstado(Request $request)
    {
        $estados = [
            'Finalizado' => 'FechaFinalizado',
            'Cerrado' => 'FechaCierre'  
        ];
    
        if (array_key_exists($request->estado, $estados)) {
            $estado = $request->estado;
            $fecha = $estados[$estado];
    
            $ids = $request->ids;
            
            $respuestas = [];

            foreach ($ids as $id) {
                $prestacion = Prestacion::where('Id', $id)->first();
                
                if ($prestacion) 
                {
                    $prestacion->$estado = 1;
                    $prestacion->$fecha = now()->format('Y-m-d');
                    $prestacion->save();

                    $respuesta = ['msg' => 'Se ha cerrado la prestación '.$id.' del mapa', 'estado' => 'success'];

                } else {
                    $respuesta = ['msg' => 'No se ha podido cerrar la prestación '.$id.' del mapa', 'estado' => 'warning'];
                }

                $respuestas[] = $respuesta;
            }
            
            return response()->json($respuestas);
        }   
    }

    public function checker(Request $request)
    {
        $mapa = Mapa::where('Nro', $request->Nro)->exists();

        return response()->json($mapa);
    }

    public function getFinalizar(Request $request)
    {
        $query = $this->queryFinalizar($request->mapa);
        
        $query->where('prestaciones.Finalizado', 0)
            ->where('prestaciones.Cerrado', 1)
            ->where('prestaciones.Entregado', 0)
            ->where('prestaciones.eEnviado', 0);

        $result = $query->get();

        return response()->json(['result' => $result]);
    }


    public function searchInFinalizar(Request $request): mixed
    {

        $NroRemito = $request->remito;
        $NroPrestacion = $request->prestacion;
        $estadoFinalizar = $request->estadoFinalizar;

        $query = $this->queryFinalizar($request->mapa);

        $query->when($NroPrestacion, function ($query) use ($NroPrestacion) {
            $query->where('prestaciones.Id', $NroPrestacion);
        });

        $query->when($NroRemito, function ($query) use ($NroRemito) {
            $query->where('prestaciones.NroCEE', $NroRemito);
        });

        $query->when($estadoFinalizar === 'aFinalizar', function ($query) use ($estadoFinalizar) {
            $query->where('prestaciones.Cerrado', 1)
                ->where('prestaciones.Finalizado', 0)
                ->where('prestaciones.eEnviado', 0);
        });

        $query->when($estadoFinalizar === 'finalizados', function ($query) use ($estadoFinalizar) {
            $query->where('prestaciones.Cerrado', 1)
                ->where('prestaciones.Finalizado', 1)
                ->where('prestaciones.eEnviado', 0);
        });

        $query->when($estadoFinalizar === 'finalizadosTotal', function ($query) use ($estadoFinalizar) {
            $query->where('prestaciones.Cerrado', 1)
                ->where('prestaciones.Finalizado', 1)
                ->where('prestaciones.eEnviado', 1);
        });

        $query->when($estadoFinalizar === 'todos', function ($query) {
            $query->where(function ($query) {
                $query->where('prestaciones.Finalizado', 1)
                      ->orWhere('prestaciones.Finalizado', 0);
            });
        });

        $result = $query->get();

        return response()->json(['result' => $result]);
    }

    public function saveFinalizar(Request $request): mixed
    {
        
        $ids = $request->ids;
        $nuevoNroRemito = Constanciase::max('NroC') + 1;
        Constanciase::addRemito($nuevoNroRemito);

        $respuestas = [];

        foreach ($ids as $id) {
            $prestacion = Prestacion::where('Id', $id)->where('Cerrado', 1)->first();

            if ($prestacion) {
                $prestacion->Finalizado = 1;
                $prestacion->FechaFinalizado = now()->format('Y-m-d');
                $prestacion->save();

                ConstanciaseIt::addConstPrestacion($id, Constanciase::max('Id'));
                $this->actualizarRemitoPrestacion($id, $nuevoNroRemito);

                $respuesta = ['msg' => 'Se ha finalizado la prestación '.$id.' del mapa', 'estado' => 'success'];
            }else{

                $respuesta = ['msg' => 'No se ha podido finalizar la prestación '.$id.' del mapa', 'estado' => 'warning'];
            }
            
            $respuestas[] = $respuesta;      
        }

        return response()->json($respuestas);
    }

    public function searchInEnviar(Request $request): mixed
    {
        $desde = $request->desde;
        $hasta = $request->hasta;
        $eEnviado = $request->eEnviado;
        $prestacion = $request->prestacion;
        $mapa = $request->mapa;
        $NroRemito = $request->NroRemito;

        $query = $this->queryEnviar($mapa);

        $query->when($prestacion, function($query) use ($prestacion) {
            $query->where('prestaciones.Id', $prestacion);
        });

        $query->when($NroRemito, function($query) use ($NroRemito) {
            $query->where('prestaciones.NroCEE', $NroRemito);
        });

        $query->when($eEnviado === 'eEnviadas', function($query) {
            $query->where('prestaciones.eEnviado', 1);
        });

        $query->when($eEnviado === 'noEenviadas', function($query) {
            $query->where('prestaciones.eEnviado', 0);
        });

        $query->when(!empty($desde) && !empty($hasta), function ($query) use ($desde, $hasta){
            $query->whereBetween('prestaciones.Fecha', [$desde, $hasta]);
        });

        $resutl = $query->distinct()->orderBy('prestaciones.NroCEE', 'DESC')->orderBy('pacientes.Apellido', 'ASC')->get();

        return response()->json(['result' => $resutl]);
    }

    public function geteEnviar(Request $request)
    {
        $query = $this->queryEnviar($request->mapa);

        $query->where(function ($query) {
            $query->where('prestaciones.eEnviado', 0)
                ->where('prestaciones.Cerrado', 1)
                ->where('prestaciones.Finalizado', 1);
        });
        
        $result = $query->orderBy('prestaciones.NroCEE', 'DESC')->orderBy('pacientes.Apellido', 'ASC')->distinct()->get();

        return response()->json(['result' => $result]);
    }

    public function saveEnviar(Request $request)
    {
        return $request->all();
        $ids = $request->ids;

        $accion = ($request->eTipo === 'eArt' 
                ? 41 
                : ($request->eTipo === 'eEmpresa' 
                    ? 42
                    : null)
                );

        foreach ($ids as $id) {
            $prestacion = Prestacion::with(['art' => function($query){
                $query->select(['SEMail']);
            }])->where('Id', $id)->first();
            
            if ($prestacion) {

                $prestacion->eEnviado = ($request->eTipo === 'eArt' ? 1 : 0);
                $prestacion->FechaEnviado = ($request->eTipo === 'eArt' 
                    ? now()->format('Y-m-d') 
                    : '0000-00-00');
                $prestacion->save();
                
                if ($accion !== null) {
                    Auditor::setAuditoria($id, self::TBLMAPA, $accion, Auth::user()->name);
                }            
                //$request->adjunto && $this->eEstudio($id);
            } 
        }
    }


    public function changeEstado(Request $request)
    {
        $estado = ($request->estado === 'examen' 
            ? ItemPrestacion::find($request->Id) 
            : Prestacion::find($request->Id));
     
        if ($estado) {
            $estado->Incompleto = ($estado->Incompleto === 1 ? 0 : 1);
            $estado->save();

            return response()->json(['result' => $estado]);
        }
    }

    public function getRemito(Request $request)
    {
        $resultados = Cache::remember('remito_'.$request, 5, function () use ($request) {

            $remito = Prestacion::with(['constanciases:Obs'])
            ->select(
                'prestaciones.NroCEE', 
                'prestaciones.Id', 
                'prestaciones.Entregado',
                DB::raw('COUNT(*) as contadorRemitos')
            )
            ->where('prestaciones.IdMapa', $request->Id)
            ->groupBy('prestaciones.NroCEE')
            ->get();

            return $remito; 
        });

        return response()->json(['result' => $resultados]);
    }

    public function reverseRemito(Request $request)
    {
        $remitos = Prestacion::where('NroCEE', $request->Id)->get();

        if ($remitos)
        {
            foreach($remitos as $prestacion)
            {
                $prestacion->Entregado = 0;
                $prestacion->FechaEntrega = '0000-00-00';
                $prestacion->save();
            }

            Constanciase::obsRemito($request->Id, '');
            return response()->json(['msg' => 'Se ha revertido la entrega correctamente'], 200);
        } else {
            return response()->json(['msg' => 'Ha ocurrido un error y no se ha podido revertir la entrega'], 500);
        }
    }

    public function controlPacienteMapa(Request $request)
    {
        $query = Prestacion::with(['mapa', 'paciente'])->where('IdMapa', $request->mapa)->where('IdPaciente', $request->paciente)->count();

        return response()->json($query);
    }

    private function queryBase()
    {
        return Mapa::leftJoin('prestaciones', 'mapas.Id', '=', 'prestaciones.IdMapa')
        ->leftJoin('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->select(
            'mapas.Id as Id',
            'mapas.Nro as Nro',
            DB::raw('(Select RazonSocial from clientes where Id = mapas.IdART) AS Art'),
            DB::raw('(Select RazonSocial from clientes where Id = mapas.IdEmpresa) AS Empresa'),
            'mapas.Fecha as Fecha',
            'mapas.FechaE as FechaE',
            'mapas.Inactivo as Inactivo',
            'mapas.Obs as Obs',
            'prestaciones.eEnviado as eEnviado',
            'prestaciones.Cerrado as Cerrado',
            'prestaciones.Entregado as Entregado',
            'prestaciones.Finalizado as Finalizado',
            'prestaciones.NroCEE as NroCEE',
            'pacientes.Nombre as Nombre',
            'pacientes.Apellido as Apellido',
            'prestaciones.Id as IdPrestacion',
            DB::raw('COALESCE((SELECT COUNT(*) FROM prestaciones WHERE IdMapa = mapas.Id), 0) as contadorPrestaciones'),
            DB::raw('COALESCE((SELECT COUNT(*) FROM pacientes WHERE pacientes.Id = prestaciones.IdPaciente), 0) as contadorPacientes'),
            DB::raw('COALESCE((SELECT COUNT(*) FROM pacientes WHERE pacientes.Id = prestaciones.IdPaciente AND prestaciones.Anulado = 0), 0) as cdorPacientesAnulados'));
    }

    private function queryPrestaciones($idmapa)
    {

        $query = Prestacion::join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
        ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
        ->select(
            'mapas.Id AS Id',
            'mapas.Nro AS Nro',
            'mapas.Fecha AS Fecha',
            'mapas.FechaE AS FechaE',
            'mapas.Cpacientes AS contadorPacientes',
            'prestaciones.eEnviado AS eEnviado',
            'prestaciones.Cerrado AS Cerrado',
            'prestaciones.Entregado AS Entregado',
            'prestaciones.Finalizado AS Finalizado',
            'prestaciones.IdPaciente AS IdPaciente',
            'prestaciones.Id AS IdPrestacion',
            'prestaciones.NroCEE AS NroCEE',
            'prestaciones.Facturado AS Facturado',
            'prestaciones.Incompleto AS Incompleto',
            'pacientes.Apellido AS Apellido',
            'pacientes.Nombre AS Nombre',
            DB::raw('(
                SELECT 
                    CASE 
                    WHEN COUNT(*) = SUM(CASE WHEN (items.CAdj = 5 OR items.CAdj = 3) AND (items.CInfo = 3 OR items.CInfo = 0) THEN 1 ELSE 0 END)
                        THEN "Completo" 
                        ELSE "Incompleto" 
                    END 
                FROM itemsprestaciones AS items 
                WHERE items.IdPrestacion = prestaciones.Id
            ) AS Etapa'))
        ->where('mapas.Nro', '=', $idmapa);

        return $query;
    }

    private function queryCerrar($idmapa)
    {
        $query = Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->select(
            'prestaciones.Id as IdPrestacion',
            'prestaciones.Fecha as Fecha',
            'prestaciones.Finalizado as Finalizado',
            'prestaciones.eEnviado as eEnviado',
            'prestaciones.Cerrado as Cerrado',
            'prestaciones.Entregado AS Entregado',
            'pacientes.Documento AS dni',
            'prestaciones.Anulado AS Anulado',
            'pacientes.Nombre as NombrePaciente',
            'pacientes.Apellido as ApellidoPaciente',)
            ->where('mapas.Nro', '=', $idmapa);

        return $query;
    }

    private function queryFinalizar($idmapa)
    {
        $query = Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
        ->select(
            'prestaciones.Id as IdPrestacion',
            'prestaciones.Fecha as Fecha',
            'prestaciones.NroCEE as NroRemito',
            'pacientes.Nombre as NombrePaciente',
            'pacientes.Apellido as ApellidoPaciente',
            'pacientes.Documento as Documento',
            'prestaciones.Finalizado as Finalizado',
            'prestaciones.eEnviado as eEnviado',
            'prestaciones.Cerrado as Cerrado',
            'prestaciones.Entregado AS Entregado',
            'prestaciones.Anulado AS Anulado')
            ->where('prestaciones.Forma', 0)
            ->where('prestaciones.Devol', 0)
            ->where('prestaciones.RxPreliminar', 0)
            ->where('prestaciones.SinEsc', 0)
            ->where('mapas.Nro', $idmapa);

        return $query;
    }

    public function queryEnviar($idmapa)
    {
        $query = Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
        ->join('clientes as empresa', 'prestaciones.IdART', '=', 'empresa.Id')
        ->join('clientes as art', 'prestaciones.IdEmpresa', '=', 'art.Id')
        ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
        ->select(
            'prestaciones.Id AS IdPrestacion',
            'prestaciones.Fecha AS Fecha',
            'prestaciones.TipoPrestacion AS TipoPrestacion',
            'prestaciones.eEnviado AS eEnviado',
            'prestaciones.Cerrado AS Cerrado',
            'prestaciones.Finalizado AS Finalizado',
            'prestaciones.NroCEE AS NroRemito',
            'pacientes.Nombre AS NombrePaciente',
            'pacientes.Apellido AS ApellidoPaciente',
            'pacientes.Documento AS Documento',
            'empresa.SEMail AS EmpresaSinEnvio',
            'art.SEMail AS ArtSinEnvio',
            DB::raw('(SELECT CASE WHEN COUNT(*) = SUM(CASE WHEN items.Incompleto = 0 THEN 1 ELSE 0 END) THEN "Completo" ELSE "Incompleto" END FROM itemsprestaciones AS items WHERE items.IdPrestacion = prestaciones.Id) AS Etapa'))
            ->where('mapas.Nro', $idmapa);

        return $query;
    }


    private function nuevosPacientesMapeados(int $actual, int $totalMapeados, int $nuevo)
    {
        if ($nuevo === $actual) {
            return ['pacientes' => $actual, 'totalMapeados' => $totalMapeados];
        }

        if ($actual === $totalMapeados && $nuevo <> $actual) {
            return ['pacientes' => $nuevo, 'totalMapeados' => $nuevo];
        }

        if ($nuevo > $actual) {
            // Se suman los nuevos mapeados según la diferencia de pacientes
            $diferencia = $nuevo - $actual;
            return [
                'pacientes' => $nuevo,
                'totalMapeados' => $totalMapeados + $diferencia // Se incrementan los mapeados
            ];
        } else {
            $diferencia = $actual - $nuevo;
            $nuevosMapeados = max(0, $totalMapeados - $diferencia);

            return [
                'pacientes' => $nuevo,
                'totalMapeados' => $nuevosMapeados
            ];
        }
    }





} 

