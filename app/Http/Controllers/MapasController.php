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
use App\Traits\ObserverMapas;
use Illuminate\Support\Facades\Auth;
use App\Traits\CheckPermission;
use App\Services\Reportes\ReporteService;
use App\Helpers\Tools;
use App\Helpers\ToolsEmails;
use Carbon\Carbon;

use App\Services\Reportes\Titulos\EEstudio;
use App\Services\Reportes\Cuerpos\EvaluacionResumen;
use App\Enum\ListadoReportes;
use App\Helpers\ToolsReportes;
use App\Services\Reportes\Cuerpos\AdjuntosGenerales;
use App\Services\Reportes\Cuerpos\AdjuntosAnexos;
use App\Services\Reportes\Cuerpos\AdjuntosDigitales;
use App\Services\Reportes\Cuerpos\Remito;

use App\Jobs\ReporteMapasJob;
use App\Helpers\FileHelper;
use App\Models\AuditorAcciones;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use App\Services\ReportesExcel\ReporteExcel;


class MapasController extends Controller
{
    protected $reporteService;
    protected $outputPath;
    protected $sendPath;
    protected $fileNameExport;
    private $tempFile;
    protected $reporteExcel;

    const TBLMAPA = 5; // cod de Mapas en la tabla auditariatablas
    const FOLDERTEMP = 'temp/MAPA';
    const BASETEMP = 'app/public/temp/';
    const EMPRESA_SEND = 'ENVIO E-ESTUDIO EMPRESA';
    const ART_SEND = 'ENVIO E-ESTUDIO ART';

    public $helper = '
        <div class="d-flex flex-column gap-1">
            <span class="custom-badge amarillo"> Entregas entre 15 a 11 días </span>
            <span class="custom-badge naranja">Entregas entre 10 a 1 día</span>
            <span class="custom-badge rojo">Entregas con 0 en adelante mientras no este eEnviado</span>
            <span class="custom-badge verde">Entregas eEnviadas</span>
        </div>
    ';

    public $helperEdit = '
        <ul>
            <li>La fecha de <b class="negrita_verde">Corte</b> impide que se sigan asociando Prestaciones</li>
            <li>Los <b class="negrita_verde">Remitos</b> podran eliminarse y modificarse desde la Consulta de Constancias</li>
        </ul>
    ';

    use ObserverMapas, CheckPermission, ToolsReportes, ToolsEmails;

    public function __construct(
        ReporteService $reporteService,
        ReporteExcel $reporteExcel
    ) {
        $this->reporteService = $reporteService;
        $this->outputPath = storage_path('app/public/temp/fusionar-' . Tools::randomCode(15) . '.pdf');
        $this->sendPath = storage_path('app/public/temp/cmit-' . Tools::randomCode(15) . '-informe.pdf');
        $this->fileNameExport = 'reporte-' . Tools::randomCode(15);
        $this->tempFile = 'app/public/temp/file-';
        $this->reporteExcel = $reporteExcel;
    }

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
        
            $query = Mapa::query()
                ->leftJoin('prestaciones', 'mapas.Id', '=', 'prestaciones.IdMapa')
                ->join('clientes as art', 'mapas.IdART', '=', 'art.Id')
                ->join('clientes as empresa', 'mapas.IdEmpresa', '=', 'empresa.Id')
                ->select(
                    'mapas.Id',
                    'mapas.Nro',
                    'mapas.Fecha',
                    'mapas.FechaE',
                    'mapas.Cmapeados',
                    'mapas.Cpacientes',
                    'art.RazonSocial as Art',
                    'empresa.RazonSocial as Empresa',
                    DB::raw("SUM(CASE WHEN prestaciones.Anulado = 0 THEN 1 ELSE 0 END) as contadorPrestaciones"),
                    DB::raw("SUM(CASE WHEN prestaciones.Anulado = 1 THEN 1 ELSE 0 END) as cdorPacientesAnulados"),
                    DB::raw("SUM(CASE WHEN prestaciones.eEnviado = 1 THEN 1 ELSE 0 END) as cdorEEnviados"),
                    DB::raw("SUM(CASE WHEN prestaciones.Finalizado = 1 THEN 1 ELSE 0 END) as cdorFinalizados"),
                    DB::raw("SUM(CASE WHEN prestaciones.Cerrado = 1 THEN 1 ELSE 0 END) as cdorCerrados"),
                    DB::raw("SUM(CASE WHEN prestaciones.Entregado = 1 THEN 1 ELSE 0 END) as cdorEntregados")
                );

            $query->groupBy('mapas.Id', 'mapas.Nro', 'mapas.Fecha', 'mapas.FechaE', 'mapas.Cmapeados', 'mapas.Cpacientes', 'art.RazonSocial', 'empresa.RazonSocial');

            if (!empty($Art)) {
                $query->where('art.Id', $Art);
            }
            if (!empty($Empresa)) {
                $query->where('empresa.Id', $Empresa);
            }
            if (!empty($Nro)) {
                $query->where('mapas.Nro', $Nro);
            }

            if (!empty($Estado)) {
                switch ($Estado) {
                    case 'terminado':
                        $query->havingRaw('contadorPrestaciones > 0')
                            ->havingRaw('contadorPrestaciones = cdorCerrados')
                            ->havingRaw('contadorPrestaciones = cdorFinalizados')
                            ->havingRaw('contadorPrestaciones = cdorEntregados');
                        break;
                    case 'abierto':
                        $query->havingRaw('contadorPrestaciones > 0 AND cdorCerrados = 0 AND cdorEEnviados = 0 AND cdorEntregados = 0');
                        break;
                    case 'eEnviado':
                        $query->havingRaw('contadorPrestaciones > 0')
                            ->havingRaw('(contadorPrestaciones = cdorEEnviados OR (cdorEEnviados = 1 AND contadorPrestaciones <> cdorEEnviados))')
                            ->havingRaw('contadorPrestaciones = cdorCerrados')
                            ->havingRaw('contadorPrestaciones = cdorFinalizados')
                            ->havingRaw('contadorPrestaciones = cdorEntregados');
                        break;
                    case 'cerrado':
                        $query->havingRaw('contadorPrestaciones > 0 AND contadorPrestaciones = cdorCerrados AND cdorFinalizados = 0 AND cdorEEnviados = 0 AND cdorEntregados = 0');
                        break;
                    case 'enProceso':
                        $query->havingRaw('contadorPrestaciones > 0 AND cdorFinalizados = 0 AND cdorCerrados = 0 AND cdorEEnviados = 0 AND cdorEntregados = 0');
                        break;
                    case 'vacio':
                        $query->havingRaw('contadorPrestaciones = 0');
                        break;
                    case 'todos':
                        $query->addSelect(DB::raw("'Todos' as estado_label"));
                        break;
                }
            }

            if (!empty($corteDesde) && !empty($corteHasta)) {
                $query->whereBetween('mapas.Fecha', [$corteDesde, $corteHasta]);
            }
            
            if (!empty($entregaDesde) && !empty($entregaHasta)) {
                $query->whereBetween('mapas.FechaE', [$entregaDesde, $entregaHasta]);
            }

            $hoy = now()->format('Y-m-d');
            
            if (!empty($Vencimiento) && is_array($Vencimiento)) {
                $query->where(function($q) use ($Vencimiento, $hoy) {
                    if (in_array('corteVencido', $Vencimiento)) $q->orWhere('mapas.Fecha', '<', $hoy);
                    if (in_array('corteVigente', $Vencimiento)) $q->orWhere('mapas.Fecha', '>=', $hoy);
                    if (in_array('entregaVencida', $Vencimiento)) $q->orWhere('mapas.FechaE', '<', $hoy);
                    if (in_array('entregaVigente', $Vencimiento)) $q->orWhere('mapas.FechaE', '>=', $hoy);
                });
            }

            if ($Ver == 'activo') $query->where('mapas.Inactivo', 0);
            if ($Ver == 'inactivo') $query->where('mapas.Inactivo', 1);

            $query->whereNotNull('mapas.Fecha')
                ->where('mapas.Fecha', '<>', '0000-00-00')
                ->orderByDesc('mapas.Id');

            return Datatables::of($query)->make(true);
        }

    return view('layouts.mapas.index', ['helper' => $this->helper]);
    }

    public function create(): mixed
    {
        if (!$this->hasPermission('mapas_add')) {
            abort(403);
        }
        return view('layouts.mapas.create',['helper' => $this->helperEdit]);
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

        return view('layouts.mapas.edit', compact(['mapa', 'cerradas', 'finalizados', 'entregados', 'conEstado', 'presentes', 'completas', 'enProceso', 'ausentes']), ['helper' => $this->helperEdit]);
    }


    public function store(Request $request)
    {
        if (!$this->hasPermission('mapas_add')) {
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
            'FechaAsignacion' => $request->FechaAsignacion ?? '0000-00-00',
        ]);
        return redirect()->route('mapas.edit', ['mapa' => $nuevoId]);
    }

    public function updateMapa(Request $request): mixed
    {
        if (!$this->hasPermission('mapas_edit')) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $mapa = Mapa::where('Id', $request->Id)->first();

        if(empty($mapa)) {
            return response()->json(['msg' => 'Mapa no encontrado'], 404);
        }

        $totalPrestacion = Prestacion::where('IdMapa', $request->Id)->count();

        if ($request->Cpacientes < $totalPrestacion && $mapa->Cmapeados !== $mapa->Cpacientes) {
            return response()->json(['msg' => 'No se puede actualizar el numero de pacientes porque el numero de pacientes que ya integra el mapa es superior'], 409);
        }

        $data = $this->nuevosPacientesMapeados($mapa->Cpacientes, $mapa->Cmapeados, $request->Cpacientes);

        if ($data['totalMapeados'] < 0) {
            return response()->json(['msg' => 'Ya hay prestaciones en el mapa y la cantidad de pacientes no puede puede ser menor a los mapeados'], 409);
        }

        $mapa->Nro = $request->Nro;
        $mapa->IdART = $request->IdART;
        $mapa->IdEmpresa = $request->IdEmpresa;
        $mapa->Fecha = $request->FechaEdicion;
        $mapa->FechaE = $request->FechaEEdicion;
        $mapa->Inactivo = $request->Estado;
        $mapa->Obs = $request->Obs;
        $mapa->Cmapeados = $data['totalMapeados'] ?? $request->Cmapeados;
        $mapa->Cpacientes = $data['pacientes'] ?? $request->Cpacientes;
        $mapa->FechaAsignacion = $request->FechaAsignacion;
        $mapa->save();

        return response()->json(['msg' => 'Mapa actualizado'], 200);

    }

    public function delete(Request $request)
    {
        if (!$this->hasPermission('mapas_delete')) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $mapa = Mapa::find($request->Id);

        if(empty($mapa)) {
            return response()->json(['msg' => 'Mapa no encontrado'], 404);
        }

        $mapa->update(['Inactivo' => 1]);
        return response()->json(['msg' => 'Mapa eliminado'], 200);
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

    public function show() {}

    public function export(Request $request)
    {
        if ($request->archivo === 'xls') {
            $reporte = $this->reporteExcel->crear('mapas');
            $remito = $this->reporteExcel->crear('remitos');

            $datos = ['IdMapa' => $request->mapa, 'nroRemito' => $request->Id];
            return $request->modulo === 'remito'
                ? $remito->generar($datos)
                : $reporte->generar($request->Id);

        }
        
        if ($request->archivo === 'pdf') {
            $examenes = Prestacion::where('NroCEE', $request->Id)->get();

            if (empty($examenes)) {
                return response()->json(['msg' => 'No se encontraron datos para generar el PDF. Hay un conflicto'], 404);
            }

            return response()->json([
                'filePath' => $this->remitoPdf($request->Id),
                'name' => $this->fileNameExport.'.pdf',
                'msg' => 'Imprimiendo Remito',
            ]); 
        }
    }

    //Obtenemos listado mapas
    public function getMapas(Request $request)
    {
        $empresa = $request->empresa;
        $art = $request->art;

        $mapas = Mapa::join('clientes as Art', 'mapas.IdART', '=', 'Art.Id')
            ->join('clientes as Empresa', 'mapas.IdEmpresa', '=', 'Empresa.Id')
            ->select(
                'mapas.Id as Id',
                'mapas.Nro as Nro',
                'Art.RazonSocial as RSArt',
                'Empresa.RazonSocial as RSE'
            )
            ->where('mapas.IdART', $art)
            ->where('mapas.IdEmpresa', $empresa)
            ->where('mapas.Cmapeados', '>=', 1)
            ->whereDate('mapas.Fecha', '>', now())
            ->whereNot('mapas.Inactivo', 1)
            ->get();

        return response()->json(['mapas' => $mapas]);
    }

    public function getMapaPrestacion(Request $request)
    {
        return Prestacion::with(['mapa', 'mapa.artMapa', 'mapa.empresaMapa'])->find($request->Id);
    }

    public function saveRemitos(Request $request)
    {
        $remitos = Prestacion::where('NroCEE', $request->Id)->get();

        if(empty($remitos)) {
            return response()->json(['msg' => 'Ha ocurrido un error y no se ha registrado la fecha'], 500);
        }

        foreach ($remitos as $remito) {
            $remito->Entregado = 1;
            $remito->FechaEntrega = $request->FechaE;
            $remito->save();
        }

        constanciase::obsRemito($request->Id, $request->Obs);
        return response()->json(['msg' => 'Se han registrado las fechas de entrega en los remitos correspondientes'], 200);
        
    }

    public function getPacienteMapa(Request $request)
    {
        $prestacion = Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->where('prestaciones.Id', $request->prestacion)
            ->select(
                'pacientes.Nombre',
                'pacientes.Apellido',
                'pacientes.TipoDocumento',
                'pacientes.Documento'
            )->first();

        return response()->json($prestacion);
    }

    public function examenes(Request $request)
    {
        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
            ->leftJoin('archivosinformador', 'itemsprestaciones.Id', '=', 'archivosinformador.IdEntidad')
            ->leftJoin('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
            ->leftJoin('profesionales as profEfector', 'itemsprestaciones.IdProfesional', '=', 'profEfector.Id')
            ->leftJoin('profesionales as profInformador', 'itemsprestaciones.IdProfesional2', '=', 'profInformador.Id')
            ->leftJoin('users as userEfector', 'profEfector.Id', '=', 'userEfector.profesional_id')
            ->leftJoin('users as userInformador', 'profInformador.Id', '=', 'userInformador.profesional_id')
            ->leftJoin('datos as DatosEfector', 'userEfector.datos_id', '=', 'DatosEfector.Id')
            ->leftJoin('datos as DatosInformador', 'userInformador.datos_id', '=', 'DatosInformador.Id')
            ->select(
                'examenes.Id AS IdExamen',
                'examenes.Nombre AS NombreExamen',
                'examenes.Informe AS Informe',
                'itemsprestaciones.CAdj AS CAdj',
                'itemsprestaciones.CInfo AS CInfo',
                'itemsprestaciones.Id AS IdItemPrestacion',
                'itemsprestaciones.Incompleto AS Incompleto',
                'itemsprestaciones.Anulado AS Anulado',
                'examenes.Adjunto AS ExamenAdjunto',
                'proveedores.Nombre AS NombreProveedor',
                'profEfector.RegHis AS RegHisEfector',
                'profInformador.RegHis AS RegHisInformador',
                'userEfector.profesional_id AS IdProfesionalEfector',
                'userInformador.profesional_id AS IdProfesionalInformador',
                DB::raw('CONCAT(profEfector.Nombre, " ", profEfector.Apellido) AS fullNameEfector'),
                DB::raw('CONCAT(profInformador.Nombre, " ", profInformador.Apellido) AS fullNameInformador'),
                DB::raw('CONCAT(DatosEfector.Nombre, " ", DatosEfector.Apellido) AS fullNameDatosEfector'),
                DB::raw('CONCAT(DatosInformador.Nombre, " ", DatosInformador.Apellido) AS fullNameDatosInformador'),
                DB::raw('(CASE 
                        WHEN EXISTS(SELECT 1 FROM archivosefector WHERE itemsprestaciones.Id = archivosefector.IdEntidad) THEN "adjunto" 
                        ELSE "sadjunto"
                    END) AS adjEfector'),
                DB::raw('(CASE 
                    WHEN EXISTS(SELECT 1 FROM archivosinformador WHERE itemsprestaciones.Id = archivosinformador.IdEntidad) THEN "adjunto" 
                    ELSE "sadjunto"
                END) AS adjInformador')
            )
            ->where('itemsprestaciones.IdPrestacion', $request->prestacion)
            ->groupBy('itemsprestaciones.Id')
            ->get();
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

    public function serchCerrados(Request $request): mixed
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
        $ids = (array) $request->ids;
        $prestaciones = Prestacion::whereIn('Id', $ids)->get();

        if (empty($prestaciones)) {
            return response()->json(['msg' => 'Prestacion no encontrada'], 404);
        }

        foreach ($prestaciones as $prestacion) {

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

            $ids = (array) $request->ids;
            $respuestas = [];

            $prestaciones = Prestacion::whereIn('Id', $ids)->get();

            if(empty($prestaciones)){
                return response()->json(['msg' => 'No hay prestaciones para cerrar'], 404);
            }

            foreach ($prestaciones as $prestacion) {
                $prestacion->$estado = 1;
                $prestacion->$fecha = now()->format('Y-m-d');
                $prestacion->save();

                $respuesta = ['msg' => 'Se ha cerrado la prestación ' . $prestacion->Id . ' del mapa', 'estado' => 'success'];
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


    public function searchFinalizados(Request $request): mixed
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

        $query->when($estadoFinalizar === 'aFinalizar', function ($query) {
            $query->where('prestaciones.Cerrado', 1)
                ->where('prestaciones.Finalizado', 0)
                ->where('prestaciones.eEnviado', 0);
        });

        $query->when($estadoFinalizar === 'finalizados', function ($query) {
            $query->where('prestaciones.Cerrado', 1)
                ->where('prestaciones.Finalizado', 1)
                ->where('prestaciones.eEnviado', 0);
        });

        $query->when($estadoFinalizar === 'finalizadosTotal', function ($query) {
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
        $ids = (array) $request->ids;
        $nuevoNroRemito = Constanciase::max('NroC') + 1;
        Constanciase::addRemito($nuevoNroRemito);
        $respuestas = [];

        $prestaciones = Prestacion::whereIn('Id', $ids)->where('Cerrado', 1)->get();

        foreach ($prestaciones as $prestacion) {
            
            $prestacion->Finalizado = 1;
            $prestacion->FechaFinalizado = now()->format('Y-m-d');
            $prestacion->save();

            ConstanciaseIt::addConstPrestacion($prestacion->Id, Constanciase::max('Id'));
            $this->actualizarRemitoPrestacion($prestacion->Id, $nuevoNroRemito);

            $respuesta = ['msg' => 'Se ha finalizado la prestación '.$prestacion->Id.' del mapa', 'estado' => 'success'];

            $respuestas[] = $respuesta;      
        }

        return response()->json($respuestas);
    }

    public function searchEnviados(Request $request): mixed
    {
        $desde = $request->desde;
        $hasta = $request->hasta;
        $eEnviado = $request->eEnviado;
        $prestacion = $request->prestacion;
        $mapa = $request->mapa;
        $NroRemito = $request->NroRemito;

        $query = $this->queryEnviar($mapa);

        $query->when($prestacion, function ($query) use ($prestacion) {
            $query->where('prestaciones.Id', $prestacion);
        });

        $query->when($NroRemito, function ($query) use ($NroRemito) {
            $query->where('prestaciones.NroCEE', $NroRemito);
        });

        $query->when($eEnviado === 'eEnviadas', function ($query) {
            $query->where('prestaciones.eEnviado', 1);
        });

        $query->when($eEnviado === 'noEenviadas', function ($query) {
            $query->where('prestaciones.eEnviado', 0);
        });

        $query->when(!empty($desde) && !empty($hasta), function ($query) use ($desde, $hasta) {
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
        $ids = $request->ids;
        $respuestas = [];

        $accion = ($request->eTipo === 'eArt'
            ? $this->getAuditoriaId(SELF::ART_SEND)
            : ($request->eTipo === 'eEmpresa'
                ? $this->getAuditoriaId(SELF::EMPRESA_SEND)
                : null)
        );

        $prestaciones = Prestacion::with(['empresa', 'art', 'paciente'])->whereIn('Id', $ids)->get();

        foreach ($prestaciones as $prestacion) {
            
            if ($prestacion &&  $this->checkExCtaImpago($prestacion->Id) === 0) {

                $nombreCompleto = $prestacion->paciente->Apellido . ' ' . $prestacion->paciente->Nombre;
                $cuerpo = [
                    'paciente' => $nombreCompleto,
                    'Fecha' => Carbon::parse($prestacion->Fecha)->format("d/m/Y"),
                    'TipoDocumento' => $prestacion->paciente->TipoDocumento,
                    'Documento' => $prestacion->paciente->Documento,
                    'RazonSocial' => $prestacion->empresa->RazonSocial,
                    'ParaEmpresa' => $prestacion->empresa->ParaEmpresa,
                    'IdMapa' => $prestacion->IdMapa,
                    'Id' => $prestacion->Id,
                    'Tipo' => $prestacion->TipoPrestacion,
                    'TipoPrestacion' => $prestacion->TipoPrestacion,
                ];

                if (($request->eTipo === 'eArt' || $request->eTipo === 'eEmpresa') && $request->exportarInforme == 'true') {

                    $listado = [
                        $this->eEstudio($prestacion->Id, "si"),
                        $this->adjDigitalFisico($prestacion->Id, 2),
                        $this->adjAnexos($prestacion->Id),
                        $this->adjGenerales($prestacion->Id),
                    ];

                    $estudios = $this->AnexosFormulariosPrint($prestacion->Id); //obtiene los ids en un array

                    if ($estudios) {
                        foreach ($estudios as $examen) {
                            $estudio = $this->addEstudioExamen($prestacion->Id, $examen);
                            $listado[] = $estudio;
                        }
                    }

                    //Usamos una salida alternativa para poder controlar el flujo de archivos
                    $outputPath = storage_path('app/public/temp/fusionar-' . Tools::randomCode(15) . '.pdf');

                    $this->reporteService->fusionarPDFs($listado, $outputPath);

                    $respuestas[] = [
                        'filePath' => $outputPath,
                        'name' => 'MAPA_' . $prestacion->paciente->Apellido . '_' . $prestacion->paciente->Nombre . '_' . $prestacion->paciente->Documento . '_PRESTACION.pdf',
                        'msg' => 'Se imprime todo el reporte art.',
                        'icon' => $request->eTipo === 'eArt' ? 'art-impresion' : 'empresa-impresion'
                    ];
                }
                
                if ($request->eTipo === 'eArt' && $request->enviarMail == 'true') {

                    $emails = $this->getEmailsReporte($prestacion->art->EMailInformes);
                    $estudios = $this->AnexosFormulariosPrint($prestacion->Id); //obtiene los ids en un array

                    foreach ($emails as $email) {

                        $file1 = [
                            $this->eEstudio($prestacion->Id, "no"),
                            $this->adjDigitalFisico($prestacion->Id, 2),
                        ];
                        
                        $file3 = [$this->adjAnexos($prestacion->Id)];
                        $file4 = [$this->adjGenerales($prestacion->Id)];

                        $file2 = [];
                        if ($estudios) {
                            foreach ($estudios as $examen) {
                                $estudio = $this->addEstudioExamen($prestacion->Id, $examen);
                                $file2[] = $estudio;
                            }
                        }

                        //Rutas de los archivos
                        $ruta = $this->rutasTempReportes($prestacion->Id);

                        $this->reporteService->fusionarPDFs($file1, $ruta['eEstudioSend']);
                        $this->reporteService->fusionarPDFs($file3, $ruta['eAdjuntoSend']);
                        $this->reporteService->fusionarPDFs($file4, $ruta['eGeneralSend']);
                        $this->reporteService->fusionarPDFs($file2, $ruta['estudiosCheck']);

                        $attachments = [$ruta['eEstudioSend'], $ruta['eAdjuntoSend'], $ruta['eGeneralSend']];
                        $estudios !== null ? array_push($attachments, $ruta['estudiosCheck']) : null;

                        $asunto = 'Mapa ' . $nombreCompleto . ' - ' . $prestacion->paciente->TipoDocumento . ' ' . $prestacion->paciente->Documento;

                        ReporteMapasJob::dispatch($email, $asunto, $cuerpo, $attachments)->onQueue('correos'); //Enviamos el correo al CronJob y Redis

                        $this->copiasRegistroEEnvio($prestacion->Id); //Enviamos las copias de los archivos creados a las carpetas correspondientes del sistema

                        $this->registrarEEnvio($prestacion->Id); //Confirmamos el eEnvio registrando fecha y campo eEnviar

                        Auditor::setAuditoria($prestacion->Id, self::TBLMAPA, $accion, Auth::user()->name); //Generamos auditoria
                        $this->folderTempClean(); //Limpiamos la carpeta Temp

                        $respuestas[] = ['msg' => 'Se ha enviado el eEstudio al cliente ' . $prestacion->art->RazonSocial . ' correctamente. ' . $prestacion->Id, 'icon' => 'eArt'];
                    }
                } 
                
                if ($request->eTipo === 'eEmpresa' && $request->enviarMail == 'true') {

                    $emails = $this->getEmailsReporte($prestacion->empresa->EMailInformes);
                    $estudios = $this->AnexosFormulariosPrint($prestacion->Id); //obtiene los ids en un array

                    foreach ($emails as $email) {

                        $file1 = [
                            $this->eEstudio($prestacion->Id, "no"),
                            $this->adjDigitalFisico($prestacion->Id, 2),
                        ];
                        
                        $file3 = [
                            $this->adjAnexos($prestacion->Id),
                        ];
                        $file4 = [
                            $this->adjGenerales($prestacion->Id),
                        ];

                        $file2 = [];
                        if ($estudios) {
                            foreach ($estudios as $examen) {
                                $estudio = $this->addEstudioExamen($prestacion->Id, $examen);
                                $file2[] = $estudio;
                            }
                        }

                        //Rutas de los archivos
                        $ruta = $this->rutasTempReportes($prestacion->Id);

                        $this->reporteService->fusionarPDFs($file1, $ruta['eEstudioSend']);
                        $this->reporteService->fusionarPDFs($file3, $ruta['eAdjuntoSend']);
                        $this->reporteService->fusionarPDFs($file4, $ruta['eGeneralSend']);
                        $this->reporteService->fusionarPDFs($file2, $ruta['estudiosCheck']);

                        $attachments = [$ruta['eEstudioSend'], $ruta['eAdjuntoSend'], $ruta['eGeneralSend']];
                        $estudios !== null ? array_push($attachments, $ruta['estudiosCheck']) : null;

                        $asunto = 'Mapa ' . $nombreCompleto . ' - ' . $prestacion->paciente->TipoDocumento . ' ' . $prestacion->paciente->Documento;

                        ReporteMapasJob::dispatch($email, $asunto, $cuerpo, $attachments)->onQueue('correos');
                        $this->copiasRegistroEEnvio($prestacion->Id); //Enviamos las copias de los archivos creados a las carpetas correspondientes del sistema

                        Auditor::setAuditoria($prestacion->Id, self::TBLMAPA, $accion, Auth::user()->name);

                        $respuestas[] = ['msg' => 'Se ha enviado el eEstudio al cliente ' . $prestacion->art->RazonSocial . ' correctamente. ' . $prestacion->Id, 'icon' => 'eEmpresa'];
                    }
                }
            } else {

                $respuestas[] = ['msg' => 'El cliente ' . $prestacion->empresa->RazonSocial . ' presenta examenes a cuenta impagos. No se ha realizado el envio.', 'icon' => 'warning'];
            }
        }

        $this->folderTempClean(); //Limpia la carpeta temporal
        return response()->json($respuestas);
    }

    public function vistaPreviaReporte(Request $request)
    {
        $listado = [
            'eEstudio' => $this->eEstudio($request->Id, "si"),
            'adjDigitalFisico' => $this->adjDigitalFisico($request->Id, 2),
            'adjAnexos' => $this->adjAnexos($request->Id),
            'adjGenerales' => $this->adjGenerales($request->Id),
        ];

        $estudios = $this->AnexosFormulariosPrint($request->Id); //obtiene los ids en un array

        if (!empty($estudios)) {
            foreach ($estudios as $examen) {
                $estudio = $this->addEstudioExamen($request->Id, $examen);
                $listado[] = $estudio;
            }
        }

        $filePath = SELF::FOLDERTEMP . $request->Id . '.pdf';

        $this->reporteService->fusionarPDFs($listado, $this->outputPath);
        File::copy($this->outputPath, storage_path(SELF::BASETEMP . $filePath));

        $fileUrl = Storage::disk('public')->url($filePath);
        $fileUrl = str_replace('storage/', 'public/storage/', $fileUrl);

        return response()->json($fileUrl);
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
        $resultados = Cache::remember('remito_' . $request, 5, function () use ($request) {

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

        if ($remitos) {
            foreach ($remitos as $prestacion) {
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

    public function listadoAuditorias(Request $request)
    {
        //listado de prestaciones que componen el mapa
        $listado = Prestacion::where('IdMapa', $request->Id)->pluck('Id');
        return Auditor::with('auditarAccion')->where('IdTabla', 5)->whereIn('IdRegistro', $listado)->orderBy('Id', 'Desc')->get();
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
                DB::raw('COALESCE((SELECT COUNT(*) FROM pacientes WHERE pacientes.Id = prestaciones.IdPaciente AND prestaciones.Anulado = 0), 0) as cdorPacientesAnulados')
            );
    }

    private function queryPrestaciones($idmapa)
    {
        return Prestacion::join('mapas', function($join) use ($idmapa){
            $join->on('prestaciones.IdMapa', '=', 'mapas.Id')
                ->where('mapas.Nro', $idmapa);
        })
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
            ) AS Etapa')
            );
    }

    private function queryCerrar($idmapa)
    {
        return Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('mapas', function($join) use ($idmapa){
                $join->on('prestaciones.IdMapa', '=', 'mapas.Id')
                    ->where('mapas.Nro', $idmapa);
            })
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
                'pacientes.Apellido as ApellidoPaciente',
            );
    }

    private function queryFinalizar(string $idmapa): mixed
    {
        return Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('mapas', function($join) use ($idmapa){
                $join->on('prestaciones.IdMapa', '=', 'mapas.Id')
                    ->where('mapas.Nro', $idmapa);
            })
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
                'prestaciones.Anulado AS Anulado'
            )
            ->where('prestaciones.Forma', 0)
            ->where('prestaciones.Devol', 0)
            ->where('prestaciones.RxPreliminar', 0)
            ->where('prestaciones.SinEsc', 0);
    }

    private function queryEnviar($idmapa)
    {
        return Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->join('clientes as empresa', 'prestaciones.IdART', '=', 'empresa.Id')
            ->join('clientes as art', 'prestaciones.IdEmpresa', '=', 'art.Id')
            ->join('mapas', function($join) use ($idmapa){
                $join->on('prestaciones.IdMapa', '=', 'mapas.Id')
                    ->where('mapas.Nro', $idmapa);
            })
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
                DB::raw('(SELECT CASE WHEN COUNT(*) = SUM(CASE WHEN items.Incompleto = 0 THEN 1 ELSE 0 END) THEN "Completo" ELSE "Incompleto" END FROM itemsprestaciones AS items WHERE items.IdPrestacion = prestaciones.Id) AS Etapa')
            );
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

    private function eEstudio(int $idPrestacion, string $opciones): mixed
    {
        return $this->reporteService->generarReporte(
            EEstudio::class,
            EvaluacionResumen::class,
            null,
            null,
            'guardar',
            storage_path($this->tempFile . Tools::randomCode(15) . '-' . Auth::user()->name . '.pdf'),
            null,
            ['id' => $idPrestacion],
            ['id' => $idPrestacion, 'firmaeval' => 0, 'opciones' => $opciones, 'eEstudio' => 'si'],
            [],
            [],
            null
        );
    }

    private function addEstudioExamen(int $idPrestacion, int $idExamen): mixed
    {
        return $this->reporteService->generarReporte(
            ListadoReportes::getReporte($idExamen),
            null,
            null,
            null,
            'guardar',
            storage_path($this->tempFile . Tools::randomCode(15) . '-' . Auth::user()->name . '.pdf'),
            null,
            ['id' => $idPrestacion, 'idExamen' => $idExamen],
            [],
            [],
            [],
            null
        );
    }

    private function adjGenerales(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            AdjuntosGenerales::class,
            null,
            null,
            null,
            'guardar',
            null,
            null,
            ['id' => $idPrestacion],
            [],
            [],
            [],
            storage_path('app/public/temp/merge_adjGenerales_' . $idPrestacion . '.pdf')
        );
    }

    private function adjAnexos(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            AdjuntosAnexos::class,
            null,
            null,
            null,
            'guardar',
            null,
            null,
            ['id' => $idPrestacion],
            [],
            [],
            [],
            storage_path('app/public/temp/merge_adjAnexos_' . $idPrestacion . '.pdf')
        );
    }

    private function adjDigitalFisico(int $idPrestacion, int $tipo): mixed // 1 es Digital, 2 es Fisico,Digital
    {
        return $this->reporteService->generarReporte(
            AdjuntosDigitales::class,
            null,
            null,
            null,
            'guardar',
            null,
            null,
            ['id' => $idPrestacion, 'tipo' => $tipo],
            [],
            [],
            [],
            storage_path('app/public/temp/merge_adjDigitales_' . $idPrestacion . '.pdf')
        );
    }

    private function remitoPdf(int $idRemito): mixed
    {
        return $this->reporteService->generarReporte(
            Remito::class,
            null,
            null,
            null,
            'guardar',
            storage_path($this->tempFile . Tools::randomCode(15) . '-' . Auth::user()->name . '.pdf'),
            null,
            ['id' => $idRemito],
            [],
            [],
            [],
            null
        );
    }

    private function registrarEEnvio(int $id): void
    {
        $prestacion = Prestacion::find($id);

        if ($prestacion) {
            $prestacion->update([
                'eEnviado' => 1,
                'FechaEnviado' => now()->format('Y-m-d')
            ]);
        }
    }

    private function copiasRegistroEEnvio(int $id): void
    {
        File::copy($this->eEstudio($id, "no"), FileHelper::getFileUrl('escritura') . '/Enviar/eEstudio' . $id . '.pdf');
        File::copy($this->adjDigitalFisico($id, 2), FileHelper::getFileUrl('escritura') . '/Enviar/eAdjuntos' . $id . '.pdf');
        File::copy($this->adjAnexos($id), FileHelper::getFileUrl('escritura') . '/Enviar/eAnexos' . $id . '.pdf');
    }

    private function rutasTempReportes(int $id)
    {
        $prestacion = Prestacion::with('paciente')->find($id);

        if ($prestacion) {

            return [
                'eEstudioSend' => storage_path('app/public/temp/eEstudio' . $prestacion->Id . '.pdf'),
                'eAdjuntoSend' => storage_path('app/public/temp/eAdjuntos_' . $prestacion->paciente->Apellido . '_' . $prestacion->paciente->Nombre . '_' . $prestacion->paciente->Documento . '_' . Carbon::parse($prestacion->Fecha)->format('d-m-Y') . '.pdf'),
                'eGeneralSend' => storage_path('app/public/temp/eAdjGeneral' . $prestacion->Id . '.pdf'),
                'estudiosCheck' => storage_path('app/public/temp/estudios' . $prestacion->Id . '.pdf')
            ];
        }
    }

    private function getAuditoriaId(string $accion): int
    {
        return AuditorAcciones::where('Id', $accion)->first(['Id']);
    }
}
