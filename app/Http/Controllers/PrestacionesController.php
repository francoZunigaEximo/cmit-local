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
use App\Helpers\FileHelper;
use App\Helpers\Tools;
use App\Models\ArchivoEfector;
use App\Models\ArchivoInformador;
use App\Models\ExamenCuentaIt;
use App\Models\Fichalaboral;
use App\Models\ItemPrestacion;
use App\Services\Reportes\Cuerpos\AdjuntosAnexos;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;
use Illuminate\Support\Facades\Auth;
use App\Traits\CheckPermission;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

use App\Services\Reportes\ReporteService;
use App\Services\Reportes\Titulos\Reducido;
use App\Services\Reportes\Cuerpos\EvaluacionResumen;
use App\Services\Reportes\Titulos\CaratulaInterna;
use App\Services\Reportes\Cuerpos\AdjuntosDigitales;
use App\Services\Reportes\Cuerpos\AdjuntosGenerales;
use App\Services\Reportes\Cuerpos\ConstEstCompDetallado;
use App\Services\Reportes\Cuerpos\ConstEstCompSimple;
use App\Services\Reportes\Cuerpos\PedidoProveedores;
use App\Services\Reportes\Cuerpos\ResumenAdministrativo;
use App\Services\Reportes\Cuerpos\ControlPaciente;
use App\Services\Reportes\Titulos\NroPrestacion;
use App\Services\Reportes\Cuerpos\EnviarOpciones;
use App\Services\Reportes\Titulos\EEstudio;

use App\Jobs\EnviarReporteJob;
use App\Jobs\ExamenesImpagosJob;
use App\Jobs\ExamenesResultadosJob;

use Illuminate\Support\Facades\Mail;
use App\Mail\EnviarReporte;
use App\Mail\ExamenesResultadosMail;
use App\Models\ArchivoPrestacion;
use App\Models\PrestacionObsFase;
use App\Traits\ReporteExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

use Illuminate\Support\Facades\File;

class PrestacionesController extends Controller
{
    use ObserverPrestaciones, ObserverFacturasVenta, CheckPermission, ReporteExcel;

    protected $reporteService;
    protected $outputPath;
    protected $sendPath;
    protected $fileNameExport;
    private $tempFile;

    protected $sendFile1;
    protected $sendFile2;
    protected $sendFile3;


    public function __construct(ReporteService $reporteService)
    {
        $this->reporteService = $reporteService;
        $this->outputPath = storage_path('app/public/fusionar.pdf');
        $this->sendPath = storage_path('app/public/cmit-'.Tools::randomCode(15).'-informe.pdf');
        $this->fileNameExport = 'reporte-'.Tools::randomCode(15);
        $this->tempFile = 'app/public/temp/file-';
    }

    public function index(Request $request): mixed
    {
        if (!$this->hasPermission("prestaciones_show")) {
            abort(403);
        }

        if ($request->ajax()) {
            $query = $this->buildQuery($request);
            return Datatables::of($query)->make(true);
        }

        return view('layouts.prestaciones.index');
    }
    
    public function create()
    {
        if (!$this->hasPermission("prestaciones_add")) {
            abort(403);
        }

        $tipoPrestacion = PrestacionesTipo::all();
        $paquetes = PaqueteEstudio::all();

        return view('layouts.prestaciones.create', compact(['tipoPrestacion', 'paquetes']));
    }

    public function edit(Prestacion $prestacione)
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            abort(403);
        }
        $tiposPrestacionPrincipales = ['ART', 'INGRESO', 'PERIODICO', 'OCUPACIONAL', 'EGRESO', 'OTRO'];

        $tipoPrestacion = PrestacionesTipo::all();
        $financiador = Cliente::find($prestacione->Financiador, ['RazonSocial', 'Id', 'Identificacion']);
        $auditorias = Auditor::with('auditarAccion')->where('IdTabla', 1)->where('IdRegistro', $prestacione->Id)->orderBy('Id', 'Asc')->get();
        $fichalaboral = Fichalaboral::where('IdPaciente', $prestacione->IdPaciente)->orderBy('Id', 'Desc')->first();
        $tiposPrestacionOtros = PrestacionesTipo::whereNotIn('Nombre', $tiposPrestacionPrincipales)->get();

        return view('layouts.prestaciones.edit', compact(['tipoPrestacion', 'prestacione', 'financiador', 'auditorias', 'fichalaboral', 'tiposPrestacionOtros']));
    }

    public function estados(Request $request)
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        $estado = Prestacion::find($request->Id);

        if($estado){

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
    
            return response()->json($estado, 200);
        }

        return response()->json(['msg' => 'No se encontró la prestación'], 404);

    }

    public function down(Request $request): mixed
    {

        if (!$this->hasPermission("prestaciones_delete")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        $prestaciones = Prestacion::find($request->Id);

        if($this->contadorProfesional($prestaciones) > 0) {
            return response()->json(['msg' => 'La prestacion posee profesionales asignados', 'estado' => 'false']);
        }

        if($this->contadorAdjuntosEfector($prestaciones) > 0) {
            return response()->json(['msg' => 'La prestacion posee adjuntos efector asociados', 'estado' => 'false']);
        }

        if($this->contadorAdjuntosInformador($prestaciones) > 0) {
            return response()->json(['msg' => 'La prestacion posee adjuntos informador asociados', 'estado' => 'false']);
        }

        if($this->contadorEstado($prestaciones) > 0) {
            return response()->json(['msg' => 'La prestacion posee examenes con estados cerrados', 'estado' => 'false']);
        }

        if ($prestaciones) {
            $prestaciones->update(['Estado' => '0']);
            $this->deleteExaCuenta($prestaciones);
            return response()->json(['msg' => 'Se ha dado de baja la prestación', 'estado' => 'true'], 200);
        }

        return response()->json(['msg' => 'No se encontró la prestación'], 404);
    }


    public function blockPrestacion(Request $request)
    {
        if (!$this->hasPermission("prestaciones_block")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        $prestaciones = Prestacion::find($request->Id);

        if($prestaciones)
        {
            $prestaciones->update(['Anulado' => 1, 'FechaAnul' => now()->format('Y-m-d')]); // 0 => Habilitado, 1 => Anulado
            Auditor::setAuditoria($request->Id, 1, 3, Auth::user()->name);
            return response()->json(['msg' => 'Se ha bloqueado la prestación', 'estado' => 'true'], 200);
        }

        return response()->json(['msg' => 'No se encontró la prestación', 'estado' => 'false'], 404);
        
    }

    public function verifyBlock(Request $request)
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        $cliente = Cliente::find($request->cliente);

        if ($cliente) {

            return response()->json(['cliente' => $cliente], 200);
        } 

        return response()->json(['msg' => 'No se puedo llevar adelante la acción'], 409);
    }

    public function savePrestacion(Request $request)
    {
        if (!$this->hasPermission("prestaciones_add")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        if ($this->checkPacientePresMapa($request->paciente, $request->mapas) > 0 && $request->mapas !== 0 && $request->tipoPrestacion === 'ART') {
            return response()->json(['msg' => 'El paciente ya se encuentra incluido en el mapa'], 409);
        }

        $nuevoId = Prestacion::max('Id') + 1;

        Prestacion::create([
            'Id' => $nuevoId,
            'IdPaciente' => $request->paciente,
            'TipoPrestacion' => $request->tipoPrestacion,
            'IdMapa' => $request->tipoPrestacion <> 'ART' ? 0 : ($request->mapas ?? 0),
            'Pago' => $request->pago,
            'SPago' => $request->spago ?? '',
            'Observaciones' => $request->observaciones ??  '',
            'IdEmpresa' => $request->IdEmpresa,
            'IdART' => $request->IdART,
            'Fecha' => now()->format('Y-m-d'),
            'Financiador' => $request->financiador,
            'NroFactProv' => $request->NroFactProv
        ]);

        Auditor::setAuditoria($nuevoId, 1, 44, Auth::user()->name);

        $empresa = ($request->tipoPrestacion === 'ART' ? $request->IdART : $request->IdEmpresa);

        $request->mapas && $this->updateMapeados($request->mapas, "quitar");

        if (!in_array($request->examenCuenta, [0, null, ''])) {
            $examenes = $this->registrarExamenCta($request->examenCuenta, $nuevoId);
        } 
    
        if (isset($examenes) && is_array($examenes) && !in_array($examenes, [0, null, ''])) {
            $this->registrarExamenes($examenes, $nuevoId);
        }

        if($request->tipo && $request->sucursal && $request->nroFactura && $nuevoId)
        {
            $this->addFactura($request->tipo, $request->sucursal, $request->nroFactura, $empresa, $request->tipoPrestacion, $nuevoId);
        }
    
        return response()->json(['nuevoId' => $nuevoId, 'msg' => 'Se ha generado la prestación del paciente.'], 200);
    }

    public function updatePrestacion(Request $request)
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            abort(403);
        }

        $prestacion = Prestacion::find($request->Id);

        if($this->updateSegundoPlano($prestacion, $request)) {
            return response()->json(['msg' => 'Se ha actualizado la prestación'], 200);
        }

        return response()->json(['msg' => 'No se ha actualizado la prestación'], 500);
    }

    public function show(){}

    public function vencimiento(Request $request)
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        $prestacion = Prestacion::find($request->Id);
        if($prestacion){
            $prestacion->update(['Vto' => 1]);
        }  

    }

    public function getParaEmpresas(Request $request): mixed
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

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
        if (!$this->hasPermission("prestaciones_show") || !$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

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

            return response()->json(['resultado' => $prestacion, 'mapa' => $resultados ?? ''], 200);

        } 
    }

    public function verifyWizard(Request $request)
    {
        if (!$this->hasPermission("prestaciones_show") || !$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        $query = Paciente::where('Documento', $request->Documento)->first();
        $existe = $query !== null;

        return response()->json(['existe' => $existe, 'paciente' => $query]);
    }

    public function exportExcel(Request $request)
    {
        /*if (!$this->hasPermission("prestaciones_report")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }*/

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
        if (!$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        $prestacion = Prestacion::where('Id', $request->Id)->first(['Anulado']);
        
        if($prestacion->Anulado === 1)
        {
            return response()->json(['prestacion' => true]);
        }
    }

    public function lstTipoPrestacion()
    {
        if (!$this->hasPermission("prestaciones_show")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        return response()->json(PrestacionesTipo::all());
    }

    public function buscarEx(Request $request)
    {
        $contar = ItemPrestacion::where('IdPrestacion', $request->Id)->count();

        return response()->json($contar);
    }

    public function checkIncompleto(Request $request)
    {
        $result = $this->verificarEstados($request->Id);

        $items = ["inc" => "Incompleto", "aus" => "Ausente", "forma" => "Forma", "sin" => "SinEsc", "devo" => "Devolucion"];

        foreach ($items as $key => $column) {
            $valor = $result->$key === "Completo" ? 0 : 1;
            Prestacion::find($request->Id)->update([$column => $valor]);
        }

        return response()->json($result);
    }

    public function obsNuevaPrestacion(Request $request)
    {
        $query = Prestacion::find($request->IdPrestacion);

        if ($query)
        {
            $query->Observaciones = $request->Observaciones ?? '';
            $query->ObsExamenes = $request->ObsExamenes ?? '';
            $query->save();
            
            $request->Obs && $this->setPrestacionComentario($request->IdPrestacion, $request->Obs);

            return response()->json(['msg' => 'Se ha actualizado la información'], 200);
        } else {
            return response()->json(['msg' => 'No se ha podido actualizar la información.'], 500);
        }
    }

    public function cacheDelete():void
    {
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
    }

    public function pdf(Request $request)
    {
        $listado = [];

        $prestacion = Prestacion::with(['empresa', 'paciente'])->find($request->Id);

        if ($request->evaluacion == 'true') {
            array_push($listado, $this->caratula($request->Id));
            array_push($listado, $this->resumenEvaluacion($request->Id));
        }

        if ($request->eEstudio == 'true') {
            array_push($listado, $this->eEstudio($request->Id));
        }

        if ($request->adjAnexos == 'true') {
            array_push($listado, $this->adjAnexos($request->Id));
        }

        if ($request->eEnvio == 'true') {
            array_push($listado, $this->eEstudio($request->Id));
            array_push($listado, $this->adjAnexos($request->Id));
            array_push($listado, $this->adjGenerales($request->Id));
        }

        if ($request->adjDigitales == 'true') {
            array_push($listado, $this->adjDigitalFisico($request->Id, 1));
        }

        if ($request->adjFisicos == 'true') {
            array_push($listado, $this->adjDigitalFisico($request->Id, 2));
        }

        if ($request->adjGenerales == 'true') {
            array_push($listado, $this->adjGenerales($request->Id));
        }

        if ($request->adjFisicosDigitales == 'true') {
            array_push($listado, $this->adjDigitalFisico($request->Id, 3));
        }

        if ($request->infInternos == 'true') {
            array_push($listado, $this->infInternos($request->Id));
        }

        if ($request->pedProveedores == 'true') {
            array_push($listado, $this->pedProveedores($request->Id));
        }

        if ($request->conPaciente == 'true') {
            array_push($listado, $this->conPaciente($request->Id));
        }

        if ($request->resAdmin == 'true') {
            array_push($listado, $this->resAdmin($request->Id));
        }

        if ($request->consEstDetallado == 'true') {
            array_push($listado, $this->consEstDetallado($request->Id));
        }

        if ($request->consEstSimple == 'true') {
            array_push($listado, $this->consEstSimple($request->Id));
        }

        $this->reporteService->fusionarPDFs($listado, $this->outputPath);

        $name = ($request->buttonEE == 'true' 
            ? 'eEstudio'.$prestacion->Id.'.pdf' 
            : ($request->buttonEA == 'true' 
                ? 'eAdjuntos_'.$prestacion->paciente->Apellido.'_'.$prestacion->paciente->Nombre.'_'.$prestacion->paciente->Documento.'_'.Carbon::parse($prestacion->Fecha)->format('d-m-Y').'.pdf'
                : $this->fileNameExport.'.pdf'));


        return response()->json([
            'filePath' => $this->outputPath,
            'name' => $name,
            'msg' => 'Reporte generado correctamente',
            'icon' => 'success' 
        ]);
    }

    public function enviarReporte(Request $request)
    {
        $listado = [];

        $prestacion = Prestacion::with('paciente', 'empresa')->find($request->Id);

        $emails = $this->getEmailsReporte($request->EMailInformes);

        if ($request->evaluacion == 'true') {
            array_push($listado, $this->caratula($request->Id));
            array_push($listado, $this->resumenEvaluacion($request->Id));
        }

        if ($request->adjAnexos == 'true') {
            array_push($listado, $this->adjAnexos($request->Id));
        }

        if ($request->adjDigitales == 'true') {
            array_push($listado, $this->adjDigitalFisico($request->Id, 1));
        }

        if ($request->adjFisicos == 'true') {
            array_push($listado, $this->adjDigitalFisico($request->Id, 2));
        }

        if ($request->adjGenerales == 'true') {
            array_push($listado, $this->adjGenerales($request->Id));
        }

        if ($request->resAdmin == 'true') {
            array_push($listado, $this->resAdmin($request->Id));
        }

        if ($request->consEstDetallado == 'true') {
            array_push($listado, $this->consEstDetallado($request->Id));
        }

        if ($request->consEstSimple == 'true') {
            array_push($listado, $this->consEstSimple($request->Id));
        }

        $this->reporteService->fusionarPDFs($listado, $this->sendPath);

        $paciente = $prestacion->paciente->Apellido." ".$prestacion->paciente->Nombre;
        $doc = $prestacion->paciente->TipoDocumento. " - ".$prestacion->paciente->Documento; 
        
        $asunto = 'Estudios '.substr($paciente,0,20).' '.$doc;
        $cuerpo = [
            'ParaEmpresa' => $prestacion->empresa->ParaEmpresa,
            'paciente' => $paciente,
            'Fecha' => $prestacion->Fecha,
            'TipoDocumento' => $prestacion->paciente->TipoDocumento,
            'Documento' => $prestacion->paciente->Documento,
            'RazonSocial' => $prestacion->empresa->RazonSocial,
            'idPrestacion' => $prestacion->Id
        ];

        foreach ($emails as $email) {
            EnviarReporteJob::dispatch($email, $asunto, $cuerpo, $this->sendPath);

            // $info = new EnviarReporte(['subject' => $asunto, 'content' => $cuerpo]);
            //         Mail::to($email)->send($info);
        }

        return response()->json(['msg' => 'Se ha enviado el/los reportes de manera correcta'], 200);
    }

    public function visibleButtonEnviar(Request $request)
    {   
        $prestacion = Prestacion::find($request->Id);

        $completos = $this->checkPrestacionesCompletas($request->Id);
        $cerrado = $prestacion->Cerrado === 1;
        $evaluado = $prestacion->IdEvaluador !== 0;
        $pagado = $prestacion->FechaFact !== null;

        return response()->json(['completos' => $completos, 'cerrado' => $cerrado, 'evaluado' => $evaluado, 'pagado' => $pagado], 200);
    }

    public function avisoReporte(Request $request)
    {

        $prestacion = Prestacion::with(['paciente', 'empresa','paciente.fichalaboral'])->find($request->Id);
        $examenes = ItemPrestacion::with('examenes')->where('IdPrestacion', $request->Id)->get();

        if ($prestacion->empresa->SEMail === 1) {
            return response()->json(['msg' => 'El cliente no acepta envio de correos electrónicos'], 409);
        }

        $emails = $this->getEmailsReporte($prestacion->empresa->EMailInformes);

        $nombreCompleto = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;

        $cuerpo = [
            'paciente' => $nombreCompleto,
            'Fecha' => Carbon::parse($prestacion->Fecha)->format("d/m/Y"),
            'TipoDocumento' => $prestacion->paciente->TipoDocumento,
            'Documento' => $prestacion->paciente->Documento,
            'RazonSocial' => $prestacion->empresa->RazonSocial,
            'examenes' => $examenes
        ];

        if ($this->checkExCtaImpago($request->Id) > 0) {
            
            $asunto = 'Solicitud de pago de exámen de  '.$nombreCompleto;

            foreach ($emails as $email) {
                ExamenesImpagosJob::dispatch("nmaximowicz@eximo.com.ar", $asunto, $cuerpo);
            }

            return response()->json(['msg' => 'El cliente presenta examenes a cuenta impagos. Se ha enviado el email correspondiente'], 409);
        
        } elseif ($this->checkExCtaImpago($request->Id) === 0) {

            $cuerpo['tarea'] = $prestacion->paciente->fichaLaboral->first()->Tareas;
            $cuerpo['tipoPrestacion'] = ucwords($prestacion->TipoPrestacion);
            $cuerpo['calificacion'] = substr($prestacion->Calificacion, 2) ?? '';
            $cuerpo['evaluacion'] = substr($prestacion->Evaluacion, 2) ?? '';
            $cuerpo['obsEvaluacion'] = $prestacion->Observaciones ?? '';

            //Creando eEnvio para adjuntar
            $file1 = [];
            $file2 = [];
            $file3 = [];

            array_push($file1, $this->eEstudio($request->Id));
            array_push($file2, $this->adjAnexos($request->Id));
            array_push($file3, $this->adjGenerales($request->Id));

            $eEstudioSend = storage_path('app/public/eEstudio'.$prestacion->Id.'.pdf');
            $eAdjuntoSend = storage_path('app/public/temp/eAdjuntos_'.$prestacion->paciente->Apellido.'_'.$prestacion->paciente->Nombre.'_'.$prestacion->paciente->Documento.'_'.Carbon::parse($prestacion->Fecha)->format('d-m-Y').'.pdf');
            $eGeneralSend = storage_path('app/public/eAdjGeneral'.$prestacion->Id.'.pdf');

            $this->reporteService->fusionarPDFs($file1, $eEstudioSend);
            $this->reporteService->fusionarPDFs($file2, $eAdjuntoSend);
            $this->reporteService->fusionarPDFs($file3, $eGeneralSend);

            $asunto = 'Estudios '.$nombreCompleto.' - '.$prestacion->paciente->TipoDocumento.' '.$prestacion->paciente->Documento;

            $attachments = [$eEstudioSend, $eAdjuntoSend, $eGeneralSend];

            foreach ($emails as $email) {
                // ExamenesResultadosJob::dispatch("nmaximowicz@eximo.com.ar", $asunto, $cuerpo, $this->sendPath);
                ExamenesResultadosJob::dispatch("nmaximowicz@eximo.com.ar", $asunto, $cuerpo, $attachments);

                // $info = new ExamenesResultadosMail(['subject' => $asunto, 'content' => $cuerpo, 'attachments' => $attachments]);
                //     Mail::to("nmaximowicz@eximo.com.ar")->send($info);
            }

            return response()->json(['msg' => 'Se ha enviado el resultado al cliente de manera correcta.'], 200);
        }
    }

    public function resumenExcel(Request $request)
    {
        if (!isset($request->Id) || empty($request->Id) || $request->Id === 0) {
            return response()->json(['msg' => 'No se ha podido generar el archivo'], 409);
        }

        $prestacion = Prestacion::find($request->Id);

        if($prestacion) {
            return $this->resumenPrestacion($prestacion);
        }else{
            return response()->json(['msg' => 'No se ha podido generar el archivo'], 409);
        }
    }

    public function getEstudiosReporte(Request $request): mixed
    {
        // $query="Select e.Id,e.Nombre,e.IdReporte,e.Evaluador From itemsprestaciones ip,examenes e Where e.Id=ip.IdExamen and e.IdReporte <> 0 and ip.Anulado=0 and ip.IdPrestacion=$idprestacion order by e.Nombre"

        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->select(
                    'examenes.Id as IdExamen',
                    'examenes.Nombre as NombreExamen',
                    'examenes.IdReporte as IdReporte',
                    'examenes.Evaluador as Evaluador'
                )->whereNot('examenes.IdReporte', 0)
                ->where('itemsprestaciones.Anulado', 0)
                ->where('itemsprestaciones.IdPrestacion', $request->Id)
                ->orderBy('examenes.Nombre')
                ->get();
    }

    public function cmdTodo(Request $request)
    {   
        $resultados = [];

        $prestacion = Prestacion::with(['paciente', 'empresa'])->find($request->Id);
        $examenes = ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')->select('exanemes.Nombre as Nombre')->where('itemsprestaciones.Anulado', 0)->distinct()->orderBy('examenes.Nombre')->get();

        //Actualizamos la prestacion (grabar)
        $this->updateSegundoPlano($prestacion, $request);

        //Cerramos la prestacion
        $prestacion->FechaCierre = now()->format('Y-m-d');
        $prestacion->Cerrado = 1;
        $prestacion->save();
        $prestacion->refresh();

        //Evaluador Exclusivo
        $estudios = $this->AnexosFormulariosPrint($request->Id);

        //Verificamos si acepta envio de emails
        if ($prestacion->empresa->SEMail === 1) {
            $resultado = ['msg' => 'El cliente no acepta envio de correos electrónicos'];
            $resultados [] = $resultado;
        }

        $emails = $this->getEmailsReporte($prestacion->empresa->EMailInformes);

        $nombreCompleto = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;

        $cuerpo = [
            'paciente' => $nombreCompleto,
            'Fecha' => Carbon::parse($prestacion->Fecha)->format("d/m/Y"),
            'TipoDocumento' => $prestacion->paciente->TipoDocumento,
            'Documento' => $prestacion->paciente->Documento,
            'RazonSocial' => $prestacion->empresa->RazonSocial,
            'examenes' => $examenes
        ];

        if ($this->checkExCtaImpago($request->Id) > 0) {
            
            $asunto = 'Solicitud de pago de exámen de  '.$nombreCompleto;

            foreach ($emails as $email) {
                ExamenesImpagosJob::dispatch("nmaximowicz@eximo.com.ar", $asunto, $cuerpo);
            }

            return response()->json(['msg' => 'El cliente presenta examenes a cuenta impagos. Se ha enviado el email correspondiente'], 409);
        
        } elseif ($this->checkExCtaImpago($request->Id) === 0) {



        }

    }

    public function uploadAdjuntoPrestacion(Request $request)
    {
        $id = ArchivoPrestacion::max('Id') + 1;

        if(!empty($request->hasFile('archivo'))) {
            $fileName = 'APRE'.$id.'_P'.$request->IdEntidad.'.pdf';
            FileHelper::uploadFile(FileHelper::getFileUrl('escritura').'/AdjuntosPrestacion/', $request->archivo, $fileName);
        }
        
        ArchivoPrestacion::create([
            'Id' => $id,
            'IdEntidad' => $request->IdEntidad,
            'Descripcion' => $request->Descripcion,
            'Ruta' => $fileName,

        ]);
    }

    public function deleteAdjPrest(Request $request)
    {
        $query = ArchivoPrestacion::where('Id', $request->Id)->first();
        
 
        if ($query) {
            $ruta = FileHelper::getFileUrl('escritura').'/AdjuntosPrestacion/'.$query->Ruta;
            
            if(File::exists($ruta)) {
                File::delete($ruta);
            }
            
            $query->delete();

            return response()->json(['msg' => 'Se ha eliminado el registro y el archivo correctamente'], 200);
        }

        return response()->json(['msg' => 'No se ha encontrado el registro para eliminar'], 409);
    }

    public function getListadoAdjPres(Request $request)
    {
        return ArchivoPrestacion::where('IdEntidad', $request->Id)->get();
    }

    private function verificarEstados(int $id)
    {
        return Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
        ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
        ->select(
            DB::raw('(SELECT CASE WHEN COUNT(*) = SUM(CASE WHEN items.Incompleto = 0 THEN 1 ELSE 0 END) THEN "Completo" ELSE "Incompleto" END FROM itemsprestaciones AS items WHERE items.IdPrestacion = prestaciones.Id) AS inc'),
            DB::raw('(SELECT CASE WHEN COUNT(*) = SUM(CASE WHEN items.Ausente = 0 THEN 1 ELSE 0 END) THEN "Completo" ELSE "Incompleto" END FROM itemsprestaciones AS items WHERE items.IdPrestacion = prestaciones.Id) AS aus'),
            DB::raw('(SELECT CASE WHEN COUNT(*) = SUM(CASE WHEN items.Forma = 0 THEN 1 ELSE 0 END) THEN "Completo" ELSE "Incompleto" END FROM itemsprestaciones AS items WHERE items.IdPrestacion = prestaciones.Id) AS forma'),
            DB::raw('(SELECT CASE WHEN COUNT(*) = SUM(CASE WHEN items.SinEsc = 0 THEN 1 ELSE 0 END) THEN "Completo" ELSE "Incompleto" END FROM itemsprestaciones AS items WHERE items.IdPrestacion = prestaciones.Id) AS sin'),
            DB::raw('(SELECT CASE WHEN COUNT(*) = SUM(CASE WHEN items.Devol = 0 THEN 1 ELSE 0 END) THEN "Completo" ELSE "Incompleto" END FROM itemsprestaciones AS items WHERE items.IdPrestacion = prestaciones.Id) AS devo'),
            )
            ->where('prestaciones.Id', $id)
            ->first();
    }

    private function contadorProfesional($prestacion)
    {
        return $prestacion->itemsPrestacion()->where('IdProfesional','!=', 0)->count();
    }

    private function contadorAdjuntosEfector($prestacion)
    {
        return ArchivoEfector::join('itemsprestaciones', 'archivosefector.IdEntidad', '=', 'itemsprestaciones.Id')->where('IdEntidad', $prestacion->Id)->count();
    }

    private function contadorAdjuntosInformador($prestacion)
    {
        return ArchivoInformador::join('itemsprestaciones', 'archivosinformador.IdEntidad', '=', 'itemsprestaciones.Id')->where('IdEntidad', $prestacion->Id)->count();
    }

    private function contadorEstado($prestacion)
    {
        return $prestacion->itemsPrestacion()->whereIn('CAdj', [3,5])->count();
    }

    private function deleteExaCuenta($prestacion)
    {
        $exaCuenta = ExamenCuentaIt::where('IdPrestacion', $prestacion->Id)->get();

        if($exaCuenta->isNotEmpty()) {
            foreach($exaCuenta as $row) {

                $row->IdPrestacion = 0;
                $row->save();
            } 
        } 
    }

    //Checkea si el paciente y la prestación ya existen en ese mapa para no volver a incluilos
    private function checkPacientePresMapa(int $paciente, int $mapa)
    {
        return Prestacion::where('IdPaciente', $paciente)->where('IdMapa', $mapa)->count();
    }

    private function resumenEvaluacion(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            Reducido::class,
            null,
            EvaluacionResumen::class,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            [],
            [],
            ['id' => $idPrestacion, 'firmaeval' => 0, 'opciones' => 'no', 'eEstudio' => 'no'],
            [],
            null
        );
    }

    private function caratula(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            Reducido::class,
            CaratulaInterna::class,
            null,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            [],
            ['id' => $idPrestacion],
            [],
            [],
            null
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
            storage_path('app/public/temp/merge_adjDigitales.pdf')
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
            storage_path('app/public/temp/merge_adjGenerales.pdf')
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
            storage_path('app/public/temp/merge_adjAnexos.pdf')
        );
    }

    private function infInternos(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            PedidoProveedores::class,
            ControlPaciente::class,
            ResumenAdministrativo::class,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            ['id' => $idPrestacion],
            ['id' => $idPrestacion, 'controlCorte' => 1],
            ['id' => $idPrestacion, 'controlCorte' => 1],
            [],
            null
        );
    }

    private function pedProveedores(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            PedidoProveedores::class,
            null,
            null,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            ['id' => $idPrestacion],
            [],
            [],
            [],
            null
        );
    }

    private function conPaciente(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            ControlPaciente::class,
            null,
            null,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            ['id' => $idPrestacion, 'controlCorte' => 1],
            [],
            [],
            [],
            null
        );
    }

    private function resAdmin(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            ResumenAdministrativo::class,
            null,
            null,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            ['id' => $idPrestacion, 'controlCorte' => 1],
            [],
            [],
            [],
            null
        );
    }

    private function consEstDetallado(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            Reducido::class,
            NroPrestacion::class,
            ConstEstCompDetallado::class,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            [],
            ['id' => $idPrestacion],
            ['id' => $idPrestacion],
            [],
            null
        );
    }

    private function consEstSimple(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            Reducido::class,
            NroPrestacion::class,
            ConstEstCompSimple::class,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            [],
            ['id' => $idPrestacion],
            ['id' => $idPrestacion],
            [],
            null
        );
    }

    private function enviarOpciones(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            EnviarOpciones::class,
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
            storage_path('app/public/temp/merge_eAdjuntos.pdf')
        );
    }

    private function eEstudio(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            EEstudio::class,
            EvaluacionResumen::class,
            AdjuntosDigitales::class,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            ['id' => $idPrestacion],
            ['id' => $idPrestacion, 'firmaeval' => 0, 'opciones' => 'no', 'eEstudio' => 'si'],
            ['id' => $idPrestacion, 'tipo' => 3],
            [],
            null
        );
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
                DB::raw('COALESCE(COUNT(CASE WHEN (itemsprestaciones.CAdj = 5 OR itemsprestaciones.CAdj = 3) AND (itemsprestaciones.CInfo = 3 OR itemsprestaciones.CInfo = 0) THEN itemsprestaciones.IdPrestacion END), 0) as CerradoAdjunto'),
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
            //->orderBy('prestaciones.Fecha', 'ASC');
    
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
                $query->where('pacientes.Id', $request->pacienteSelect2);
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

    private function checkExCtaImpago(int $idPrestacion)
    {
        return ExamenCuentaIt::join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->where('pagosacuenta_it.IdPrestacion', $idPrestacion)->count();
    }

    private function getEmailsReporte(string $correos): array
    {
        $emails = explode(",", $correos);
        $emails = array_map('trim', $emails);

        return $emails;
    }

    private function checkPrestacionesCompletas(int $id): mixed
    {
        return ItemPrestacion::whereIn('CAdj', [3,5])
            ->whereIn('CInfo', [3,0])
            ->where('IdPrestacion', $id)
            ->count() == ItemPrestacion::where('IdPrestacion', $id)->count();
    }

    private function updateSegundoPlano($prestacion, $request): void
    {

        if($prestacion && !empty($request)) {

            $mapa = $request->Mapas ?? 0;
            
            // nuevo registro de mapa
            if (($mapa != "0" || $mapa != 'null' || $mapa != '') && $prestacion->IdMapa == "0") {
                $this->updateMapeados($mapa, "quitar");
            }

            // agrego un nuevo cupo al registro de mapa
            if (($mapa == '0' || $mapa == '') && ($prestacion->IdMapa != 'null' || $prestacion->IdMapa != '0' || $prestacion->IdMapa != '' )) {

                $this->updateMapeados($prestacion->IdMapa, "agregar");
            } 
        
            $prestacion->IdEmpresa = $request->Empresa ?? 0;
            $prestacion->IdART = $request->Art ?? 0;
            $prestacion->Fecha = $request->Fecha ?? '';
            $prestacion->TipoPrestacion = $request->TipoPrestacion ?? '';
            $prestacion->IdMapa = $mapa;
            $prestacion->Pago = $request->Pago ?? '';
            $prestacion->SPago = $request->SPago ?? '';
            $prestacion->Financiador = ($request->TipoPrestacion == 'ART' ? $request->Art : $request->Empresa) ?? 0;
            $prestacion->Observaciones = $request->Observaciones ?? '';
            $prestacion->NumeroFacturaVta = $request->NumeroFacturaVta ?? 0;
            $prestacion->IdEvaluador = $request->IdEvaluador ?? 0;
            $prestacion->Evaluacion = $request->Evaluacion ?? 0;
            $prestacion->Calificacion = $request->Calificacion ?? 0;
            $prestacion->ObsExamenes = $request->ObsExamenes ?? '';
            $prestacion->FechaAnul = $request->FechaAnul ?? '0000-00-00';
            $prestacion->NroFactProv = $request->NroFactProv ?? '';
            $prestacion->save();
            
            $request->SinEval && $this->setPrestacionAtributo($request->Id, $request->SinEval);
            $request->Obs && $this->setPrestacionComentario($request->Id, $request->Obs);
            $empresa = ($request->tipoPrestacion === 'ART' ? $request->ART : $request->Empresa);
            ItemPrestacion::InsertarVtoPrestacion($request->Id);

            $this->updateFichaLaboral($request->IdPaciente, $request->Art, $request->Empresa);
            $this->addFactura($request->tipo, $request->sucursal, $request->nroFactura, $empresa, $request->tipoPrestacion, $request->Id);
            Auditor::setAuditoria($request->Id, 1, 2, Auth::user()->name);
        }

    }

    
    private function AnexosFormulariosPrint(int $id)
    {
        //verifico si hay anexos con formularios a imprimir
	    // $query="Select e.Id From itemsprestaciones ip,examenes e Where e.Id=ip.IdExamen and e.IdReporte <> 0 and ip.Anulado=0 and e.Evaluador=1 and  ip.IdPrestacion=$idprest LIMIT 1";	$rs=mysql_query($query,$conn);

        return ItemPrestacion::join('examenes', 'itemsprestacones.IdExamen', '=', 'examenes.Id')
                ->select('examenes.Id as Id')
                ->whereNot('examenes.IdReporte', 0)
                ->where('itemsprestaciones.Anulado', 0)
                ->where('examenes.Evaluador', 1)
                ->where('itemsprestaciones.IdPrestacion', $id)
                ->first();
    }

}
