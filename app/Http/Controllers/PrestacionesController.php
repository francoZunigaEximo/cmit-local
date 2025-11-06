<?php

namespace App\Http\Controllers;

use App\Enum\ListadoReportes;
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
use App\Helpers\FileHelper;
use App\Helpers\Tools;
use App\Models\ArchivoEfector;
use App\Models\ArchivoInformador;
use App\Models\ExamenCuentaIt;
use App\Models\Fichalaboral;
use App\Models\ItemPrestacion;
use App\Services\Reportes\Cuerpos\AdjuntosAnexos;
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
use App\Services\Reportes\Cuerpos\PDFREPE1;

use App\Jobs\EnviarReporteJob;
use App\Jobs\ExamenesImpagosJob;
use App\Jobs\ExamenesResultadosJob;

use App\Helpers\ToolsEmails;
use App\Jobs\EnviarAvisoJob;
use App\Jobs\EnvioReporteEspecialJob;
// use Illuminate\Support\Facades\Mail;
// use App\Mail\EnviarReporte;
// use App\Mail\ExamenesResultadosMail;
use App\Models\ArchivoPrestacion;
use App\Models\HistorialPrestacion;
use App\Services\ReportesExcel\ReporteExcel;

use Illuminate\Support\Facades\File;
use App\Services\Facturas\PrestaFichaFactura;

class PrestacionesController extends Controller
{
    use ObserverPrestaciones, ObserverFacturasVenta, CheckPermission, ToolsEmails;

    protected $reporteService;
    protected $outputPath;
    protected $sendPath;
    protected $fileNameExport;
    private $tempFile;

    protected $sendFile1;
    protected $sendFile2;
    protected $sendFile3;

    protected $reporteExcel;
    protected $detalleFactura;

    public $helper = '
        <ul>
            <li>No se podrán <b class="negrita_verde">Cerrar</b> las Prestaciones con exámenes Incompletos o Ausentes</li>
            <li>No se podrán <b class="negrita_verde">Finalizar</b> las Prestaciones con Sin Escanear, Forma o devolución</li>
            <li>Para Borrar Prestaciones con exámenes provenientes de <b class="negrita_verde">exámenes a Cuenta</b> primero deben eliminarse los exámenes</li>
        </ul>
    ';

    public $helperEdit = '
        <ul>
            <li>No se podrán <b class="negrita_verde">Cerrar</b> las Prestaciones con exámenes Incompletos o Ausentes</li>
            <li>No se podrán <b class="negrita_verde">Finalizar</b> las Prestaciones con Preliminar RX, Sin Escanear, Forma o devolución</li>
            <li>Solo se podrán <b class="negrita_verde">Reactivar</b> las Prestaciones sin No asociadas</li>
            <li>El botón <b class="negrita_verde">Todo</b> Cierra, Envia la Calificacion Laboral e Imprime Resumen, o reste y formularios Anexos para completar </li>
            <li>Para poder enviar <b class="negrita_verde">Resultados</b>, el Cliente debe tener Mail Informes, no registrar Sin Envio de Mails y no contar con Examenes a Cuenta impagos asociados</li>
            <li>Solo puede <b class="negrita_verde">e-Enviar</b> si la prestacion esta Cerrada y los examenes estan cerrados, adjuntados e informados (incluidos los anexos)</li>
            <li>Envia los reportes al <b class="negrita_verde">Email Informes</b> de la Empresa o ART, segun el Tipo de Prestación</li>
            <li>Si el <b class="negrita_verde">Tipo</b> es OTRO/CARNET/REC MED/S/C_OCUPACIONAL (Sin Calificacion Laboral), el e-Estudio NO genera hoja de aptitud, solo Adjuntos</li>    
            <li>No es posible cambiar Empresa y ART si la Prestaciones esta Anulada, Facturada o tiene Constancia Entrega</li>
        </ul>
    ';

    public function __construct(
        ReporteService $reporteService, 
        ReporteExcel $reporteExcel,
        PrestaFichaFactura $detalleFactura
        )
    {
        $this->reporteService = $reporteService;
        $this->outputPath = storage_path('app/public/temp/fusionar.pdf');
        $this->sendPath = storage_path('app/public/temp/cmit-'.Tools::randomCode(15).'-informe.pdf');
        $this->fileNameExport = 'reporte-'.Tools::randomCode(15);
        $this->tempFile = 'app/public/temp/file-';
        $this->reporteExcel = $reporteExcel;
        $this->detalleFactura = $detalleFactura;
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

        return view('layouts.prestaciones.index', ['helper' => $this->helper]);
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

        return view('layouts.prestaciones.edit', compact(['tipoPrestacion', 'prestacione', 'financiador', 'auditorias', 'fichalaboral', 'tiposPrestacionOtros']), ['helper'=> $this->helperEdit]);
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

        if ($this->checkPacientePresMapa($request->IdPaciente, $request->IdMapa) > 0 && $request->IdMapa !== 0 && $request->TipoPrestacion === 'ART') {
            return response()->json(['msg' => 'El paciente ya se encuentra incluido en el mapa'], 409);
        }

        $nuevoId = Prestacion::max('Id') + 1;
        $data = $request->only([
            'IdPaciente',
            'TipoPrestacion',
            'Pago',
            'SPago',
            'Observaciones',
            'IdEmpresa',
            'IdART',
            'datos_facturacion_id'
        ]);

        $data['Id'] = $nuevoId;
        $data['Fecha'] = now()->format('Y-m-d');
        $data['IdMapa'] = $request->TipoPrestacion != 'ART' ? 0 : ($request->IdMapa ?? 0);

        Prestacion::create($data);
        Auditor::setAuditoria($nuevoId, 1, 44, Auth::user()->name);

        $empresa = ($request->TipoPrestacion === 'ART' ? $request->IdART : $request->IdEmpresa);

        $request->IdMapa && $this->updateMapeados($request->IdMapa, "quitar");

        if ($request->Tipo && 
            $request->Sucursal && 
            $request->NroFactura && 
            $nuevoId && 
            in_array($request->Pago, ['A', 'B'])
            )
        {
            $this->addFactura($request->Tipo, $request->Sucursal, $request->NroFactura, $empresa, $request->TipoPrestacion, $nuevoId);
            $this->detalleFactura->modificar(['prestacion_id' => intval($nuevoId)], intval($request->datos_facturacion_id));

        } 
    
        return response()->json(['nuevoId' => $nuevoId, 'msg' => 'Se ha generado la prestación del paciente.'], 200);
    }

    public function updatePrestacion(Request $request)
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            abort(403);
        }

        $prestacion = Prestacion::find($request->Id);

        if($prestacion) {
            $this->updateSegundoPlano($prestacion, $request);
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

    public function getPresPaciente(Request $request)
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

    public function exportExcel(Request $request)
    {
        /*if (!$this->hasPermission("prestaciones_report")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }*/
    
        $data = [];
        $filters = $this->procesarFiltros($request->filters);
        
        array_push($data, $request->ids);
        $data['filters'] = $filters;

        switch ($request->tipo) {
            case 'simple':
                $reporte = $this->reporteExcel->crear('simplePrestacionFull');
                return $reporte->generar($data);
            case 'detallado':
                $reporte = $this->reporteExcel->crear('detalladaPrestacionFull');
                return $reporte->generar($data);
            case 'completo':
                $reporte = $this->reporteExcel->crear('completoPrestacionFull');
                return $reporte->generar($data);
        }

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

        if($query)
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

        $verificar = ItemPrestacion::where('IdPrestacion', $request->Id)->count();

        if($verificar === 0) {
            return response()->json(['msg' => 'No se puede generar el reporte porque la prestación no posee exámenes'], 409);
        }

        // Lista de las condiciones y sus respectivas funciones
        $acciones = [
            'adjAnexos' => 'adjAnexos',
            'eEnvio' => ['eEstudio', 'adjAnexos', 'adjGenerales'],
            'adjDigitales' => ['adjDigitalFisico' => 1],
            'adjFisicos' => ['adjDigitalFisico' => 2],
            'adjFisicosDigitales' => ['adjDigitalFisico' => 3],
            'adjGenerales' => 'adjGenerales',
            'infInternos' => 'infInternos',
            'pedProveedores' => 'pedProveedores',
            'conPaciente' => 'conPaciente',
            'resAdmin' => 'resAdmin',
            'consEstDetallado' => 'consEstDetallado',
            'consEstSimple' => 'consEstSimple',
            'caratula' => 'caratula'
        ];

        if ($request->evaluacion == 'true') {
            array_push($listado, $this->caratula($request->Id));
        }

        if ($request->eEstudio == 'true') {
            $eEstudio = [];
            //Generamos un registro en EnviarOpciones
            array_push($listado, $this->eEstudio($request->Id, "no"));
            array_push($listado, $this->adjDigitalFisico($request->Id, 3));

            array_push($eEstudio, $this->eEstudio($request->Id, "si"));
            array_push($eEstudio, $this->adjDigitalFisico($request->Id, 3));

            $this->reporteService->fusionarPDFs($eEstudio, FileHelper::getFileUrl('escritura').'/EnviarOpciones/eEstudio'.$request->Id.'.pdf');
        }

        // Recorrer las acciones y agregarlas a $listado si la condición es true
        foreach ($acciones as $key => $action) {
            if ($request->$key == 'true') {
                // Si la acción está asociada a un array (varios métodos a llamar)
                if (is_array($action)) {
                    foreach ($action as $method => $param) {
                        // Verificar si el $method es un índice numérico o una clave asociativa
                        if (is_numeric($method)) {
                            // Si es un índice numérico, simplemente llamamos el método
                            array_push($listado, $this->$param($request->Id));
                        } else {
                            // Si es una clave asociativa, pasamos el método y el parámetro extra
                            array_push($listado, $this->$method($request->Id, $param));
                        }
                    }
                } else {
                    // Si la acción es una función simple
                   array_push($listado, $this->$action($request->Id));
                }
            }
        }
        
        if (!empty($request->estudios)) {
            foreach($request->estudios as $examen) {
                $estudio = $this->addEstudioExamen($request->Id, $examen);
                array_push($listado, $estudio);
            }
        }

        $this->reporteService->fusionarPDFs($listado, $this->outputPath);

        $name = ($request->buttonEE == 'true' 
            ? 'eEstudio'.$prestacion->Id.'.pdf' && File::copy($this->adjDigitalFisico($request->Id, 3), FileHelper::getFileUrl('escritura').'/EnviarOpciones/eEstudio'.$prestacion->Id)
            : ($request->buttonEA == 'true' 
                ? 'eAdjuntos_'.$prestacion->paciente->Apellido.'_'.$prestacion->paciente->Nombre.'_'.$prestacion->paciente->Documento.'_'.Carbon::parse($prestacion->Fecha)->format('d-m-Y').'.pdf'
                : $this->fileNameExport.'.pdf'));

        if(!empty($listado)) {

            return response()->json([
                'filePath' => $this->outputPath,
                'name' => $name,
                'msg' => 'Reporte generado correctamente',
                'icon' => 'success' 
            ]);
        }else{

            return response()->json(['msg' => 'No hay reportes para imprimir en la selección'], 409);
        }

    }

    public function enviarReporte(Request $request)
    {
        $listado = [];
        $evaluacion = [];

        $prestacion = Prestacion::with('paciente', 'empresa')->find($request->Id);

        $emails = $this->getEmailsReporte($request->EMailInformes);

        if ($request->evaluacion == 'true') {
            array_push($listado, $this->caratula($request->Id));
            array_push($listado, $this->resumenEvaluacion($request->Id));
            // Generamos un array individual para guardar el archivo en ubicacion especial
            array_push($evaluacion, $this->caratula($request->Id));
            array_push($evaluacion, $this->resumenEvaluacion($request->Id));

            $this->reporteService->fusionarPDFs($evaluacion, FileHelper::getFileUrl('escritura').'/EnviarOpciones/eResumen'.$request->Id.'.pdf'); // registramos el archivo de Evaluacion Resumen
        }

        if ($request->adjAnexos == 'true') {
            array_push($listado, $this->adjAnexos($request->Id));
            File::copy($this->adjAnexos($request->Id), FileHelper::getFileUrl('escritura').'/EnviarOpciones/eAnexos'.$request->Id.'_'.$this->getIdArchivoEfector($request->Id).'.pdf');
        }

        if ($request->adjDigitales == 'true') {//
            array_push($listado, $this->consEstDetallado($request->Id));
            File::copy($this->consEstDetallado($request->Id), FileHelper::getFileUrl('escritura').'/EnviarOpciones/eConstanciaD'.$request->Id.'.pdf');
        }

        if ($request->consEstSimple == 'true') {
            array_push($listado, $this->consEstSimple($request->Id));
            File::copy($this->consEstDetallado($request->Id), FileHelper::getFileUrl('escritura').'/EnviarOpciones/eConstanciaS'.$request->Id.'.pdf');
        }
        //Listado de informes internos de Edit Prestaciones
        if ($request->eEstudio == 'true') {
            array_push($listado, $this->eEstudio($request->Id, 'no'));
        }

        if ($request->eEnvio == 'true') {
            array_push($listado, $this->eEstudio($request->Id, "no"));
            array_push($listado, $this->adjDigitalFisico($request->Id, 2));
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
            array_push($listado, $this->conpaciente($request->Id));
        }

        if ($request->caratula == 'true') {
            array_push($listado, $this->caratula($request->Id));
        }

        if($request->estudios) {
            foreach($request->estudios as $examen) {
                $estudio = $this->addEstudioExamen($request->Id, $examen);
                array_push($listado, $estudio);
            }
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
            // dd($this->sendPath);exit;
            EnviarReporteJob::dispatch($email, $asunto, $cuerpo, $this->sendPath)->onQueue('correos'); 
            // $info = new EnviarReporte(['subject' => $asunto, 'content' => $cuerpo, 'attachments' => [$this->sendPath]]);
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

        if (in_array($prestacion->empresa->EMailInformes, [null, ''], true)) {
            return response()->json(['msg' => 'El cliente no posee email de informes'], 409);
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
                ExamenesImpagosJob::dispatch($email, $asunto, $cuerpo)->onQueue('correos');
            }

            return response()->json(['msg' => 'El cliente presenta examenes a cuenta impagos. Se ha enviado el email correspondiente'], 409);
        
        } elseif ($this->checkExCtaImpago($request->Id) === 0) {

            $cuerpo['tarea'] = $prestacion->paciente->fichaLaboral->first()->Tareas;
            $cuerpo['tipoPrestacion'] = ucwords($prestacion->TipoPrestacion);
            $cuerpo['calificacion'] = substr($prestacion->Calificacion, 2) ?? '';
            $cuerpo['evaluacion'] = substr($prestacion->Evaluacion, 2) ?? '';
            $cuerpo['obsEvaluacion'] = $prestacion->Observaciones ?? '';

            $asunto = 'Estudios '.$nombreCompleto.' - '.$prestacion->paciente->TipoDocumento.' '.$prestacion->paciente->Documento;

            foreach ($emails as $email) {
                EnviarAvisoJob::dispatch($email, $asunto, $cuerpo)->onQueue('correos');
            }

            return response()->json(['msg' => 'Se ha enviado el resultado al cliente de manera correcta.'], 200);
        }
    }

    public function enviarReporteEspecial(Request $request)
    {
        $attachments = [];

        $eEstudio = $this->eEstudio($request->Id, "si");
        $adjAnexos = $this->adjAnexos($request->Id);
        $adjGenerales = $this->adjGenerales($request->Id);

        $attachments = [$eEstudio, $adjAnexos, $adjGenerales];

        if (empty($attachments)) {
            return response()->json(['msg' => 'No se han encontrado reportes para empaquetar y enviar. Consulte al administrador'], 409);
        }

        $prestacion = Prestacion::with(['empresa', 'paciente'])->find($request->Id);

        $nombreCompleto = $prestacion->paciente->Apellido. ' '.$prestacion->paciente->Nombre;

        $asunto = 'Examen Laboral ' . $nombreCompleto. ' ' .$prestacion->paciente->Documento . ' ' .$prestacion->TipoPrestacion;

        $cuerpo['empresa'] = $prestacion->empresa->RazonSocial ?? '';
        $cuerpo['nombreCompleto'] = $nombreCompleto ?? '';
        $cuerpo['fechaPrestacion'] = Carbon::parse($prestacion->Fecha)->format('d/m/Y') ?? '';
        $cuerpo['dni'] = $prestacion->paciente->Documento ?? '';
        $cuerpo['paraEmpresa'] = $prestacion->empresa->ParaEmpresa ?? '';
        $cuerpo['prestacion'] = $prestacion->Id ?? '';
        $cuerpo['tipoPrestacion'] = $prestacion->TipoPrestacion ?? '';

        if (empty($prestacion->empresa->EMailInformes)) {
            return response()->json(['msg' => 'El cliente no posee un correo registrado'], 409);
        }

        EnvioReporteEspecialJob::dispatch($prestacion->empresa->EMailInformes, $asunto, $cuerpo, $attachments)->onQueue('correos');

        return response()->json(['msg' => 'Se ha enviado el reporte con exito'], 200);

    }

    public function resumenExcel(Request $request)
    {
        if (!isset($request->Id) || empty($request->Id) || $request->Id === 0) {
            return response()->json(['msg' => 'No se ha podido generar el archivo'], 409);
        }

        $prestacion = Prestacion::find($request->Id);

        if($prestacion) {
            $reporte = $this->reporteExcel->crear('resumenTotal');
            return $reporte->generar($prestacion);
            
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
        $listado = [];
        $temp_estudio = [];

        $prestacion = Prestacion::with(['paciente', 'empresa'])->find($request->Id);
        $examenes = ItemPrestacion::with('examenes')->where('IdPrestacion', $request->Id)->where('Anulado', 0)->get();

        //Cerramos la prestacion
        $prestacion->FechaCierre = now()->format('Y-m-d');
        $prestacion->Cerrado = 1;
        $prestacion->save();
        $prestacion->refresh();

        //Actualizamos la prestacion (grabar)
        if(!empty($request)) {
            $this->updateSegundoPlano($prestacion, $request);
        }

        //Evaluador Exclusivo
        $estudios = $this->AnexosFormulariosPrint($request->Id);

        array_push($listado, $this->eEstudio($request->Id, "no"));
        array_push($listado, $this->adjDigitalFisico($request->Id, 2));
        array_push($listado, $this->adjAnexos($request->Id));
        array_push($listado, $this->adjGenerales($request->Id));

        if($estudios) {
            foreach($estudios as $examen) {
                $estudio = $this->addEstudioExamen($request->Id, $examen);
                array_push($listado, $estudio);
            }
        }
        
        //Verificamos si acepta envio de emails
        if ($prestacion->empresa->SEMail === 1) {
            
            return response()->json([
                'filePath' => $this->outputPath,
                'name' => $this->fileNameExport.'.pdf',
                'msg' => 'El cliente no acepta envio de correos electrónicos. Se imprime todo.',
                'icon' => 'success' 
            ]); 
    
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

        // cualquier valor distinto a cero significa que tiene examenes a cuenta impagos
        if ($this->checkExCtaImpago($request->Id) > 0) {
            
            $asunto = 'Solicitud de pago de exámen de  '.$nombreCompleto;

            foreach ($emails as $email) {
                ExamenesImpagosJob::dispatch($email, $asunto, $cuerpo)->onQueue('correos');
            }

            return response()->json(['msg' => 'El cliente presenta examenes a cuenta impagos. Se ha enviado el email correspondiente'], 409);
        
        } elseif ($this->checkExCtaImpago($request->Id) === 0) {

            //Datos del cuerpo del mensaje
            $cuerpo['tarea'] = $prestacion->paciente->fichaLaboral->first()->Tareas;
            $cuerpo['tipoPrestacion'] = ucwords($prestacion->TipoPrestacion);
            $cuerpo['calificacion'] = substr($prestacion->Calificacion, 2) ?? '';
            $cuerpo['evaluacion'] = substr($prestacion->Evaluacion, 2) ?? '';
            $cuerpo['obsEvaluacion'] = $prestacion->Observaciones ?? '';

            //path de los archivos a enviar y nombres personalizados cuando se fusionan
            $eEstudioSend = storage_path('app/public/eEstudio'.$prestacion->Id.'.pdf');
            $eAdjuntoSend = storage_path('app/public/temp/eAdjuntos_'.$prestacion->paciente->Apellido.'_'.$prestacion->paciente->Nombre.'_'.$prestacion->paciente->Documento.'_'.Carbon::parse($prestacion->Fecha)->format('d-m-Y').'.pdf');
            $eGeneralSend = storage_path('app/public/eAdjGeneral'.$prestacion->Id.'.pdf');

            //Creando eEnvio para adjuntar
            array_push($temp_estudio, $this->eEstudio($request->Id, "no")); //construimos el eEstudio (caratula, resumen)
            array_push($temp_estudio, $this->adjDigitalFisico($request->Id, 2)); // metemos en el eEstudio todos los adj fisicos digitales y fisicos
            $this->reporteService->fusionarPDFs($temp_estudio, $eEstudioSend); //Fusionamos los archivos en uno solo 
            File::copy($this->adjAnexos($request->Id), $eAdjuntoSend); //adjuntamos individualmente los Anexos
            File::copy($this->adjGenerales($request->Id), $eGeneralSend);

            $asunto = 'Estudios '.$nombreCompleto.' - '.$prestacion->paciente->TipoDocumento.' '.$prestacion->paciente->Documento;

            $attachments = [$eEstudioSend, $eAdjuntoSend, $eGeneralSend];

            foreach ($emails as $email) {
                // ExamenesResultadosJob::dispatch("nmaximowicz@eximo.com.ar", $asunto, $cuerpo, $this->sendPath);
                ExamenesResultadosJob::dispatch($email, $asunto, $cuerpo, $attachments)->onQueue('correos');

                // $info = new ExamenesResultadosMail(['subject' => $asunto, 'content' => $cuerpo, 'attachments' => $attachments]);
                //     Mail::to("nmaximowicz@eximo.com.ar")->send($info);
            }

            $prestacion->save();

            return response()->json(['msg' => 'Se ha cerrado la prestación. Se han guardado todos los cambios y se ha enviado el resultado al cliente de manera correcta.'], 200);

        }

    }

    public function pdfPrueba(Request $request){
        
        return $this->addEstudioExamen($request->Id, $request->Examen, $request->vistaPrevia == "true" ? true : false);
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
            'Descripcion' => $request->Descripcion ?? '',
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

    public function getResultados(Request $request)
    {
        $merge = $this->mergePrestaciones($request->IdPaciente);

        return response()->json($merge);
    }

    public function exportResultados(Request $request)
    {
        if ($request->Tipo === 'exportSimple') {

            $reporte = $this->reporteExcel->crear('simplePrestacion');
            return $reporte->generar($this->querySimple($request->IdPaciente));

        }elseif($request->Tipo === 'exportDetallado') {
            
            $reporte = $this->reporteExcel->crear('detalladaPrestacion');
            return $reporte->generar($this->queryDetallado($request->IdPaciente));
        }
        return response()->json(['msg' => 'No se ha podido generar el archivo'], 409);
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
            EvaluacionResumen::class,
            null,
            null,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            ['id' => $idPrestacion, 'firmaeval' => 0, 'opciones' => 'no', 'eEstudio' => 'no'],
            [],
            [],
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

    private function adjDigitalFisico(int $idPrestacion, int $tipo): mixed // 1 es Digital, 2 es Fisico
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
            storage_path('app/public/temp/merge_adjDigitales_'.$idPrestacion.'.pdf')
        );
    }

    private function adjGenerales(int $idPrestacion): mixed
    {
        $prestacion = Prestacion::find($idPrestacion);
        $paciente = $prestacion->paciente;
        $nombreArchivo = $paciente->Apellido.'_'.$paciente->Documento.'_adjPresta_'.$idPrestacion.'.pdf';
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
            storage_path('app/public/temp/'.$nombreArchivo)
        );

    }

    private function adjAnexos(int $idPrestacion): mixed
    {
        $prestacion = Prestacion::find($idPrestacion);
        $paciente = $prestacion->paciente;
        $nombreArchivo = $paciente->Apellido.'_'.$paciente->Documento.'_adjAnexos_'.$idPrestacion.'.pdf';
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
            storage_path('app/public/temp/'.$nombreArchivo)
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

    private function eEstudio(int $idPrestacion, string $opciones): mixed // No - Lleva resumen aptitud
    {
        $prestacion = Prestacion::find($idPrestacion);
        $paciente = $prestacion->paciente;
        $nombreArchivo = $paciente->Apellido.'_'.$paciente->Documento.'_eEstudio_'.$idPrestacion.'.pdf';
        
        return $this->reporteService->generarReporte(
            EEstudio::class,
            EvaluacionResumen::class,
            null,
            null,
            'guardar',
            storage_path($this->tempFile.$nombreArchivo),
            null,
            ['id' => $idPrestacion],
            ['id' => $idPrestacion, 'firmaeval' => 0, 'opciones' => $opciones, 'eEstudio' => 'si'],
            [],
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

            $prestacion->fill($request->all());
            $prestacion->Financiador = ($request->TipoPrestacion == 'ART' ? $request->Art : $request->Empresa) ?? 0;
            $prestacion->FechaAnul = $request->FechaAnul ?? '0000-00-00';
            $prestacion->IdMapa = $mapa;
            $prestacion->save();
            $prestacion->refresh();

            $guardar = [
                'prestacion_id' => $prestacion->Id
            ];
            
            $request->SinEval && $this->setPrestacionAtributo($request->Id, $request->SinEval);
            $request->Obs && $this->setPrestacionComentario($request->Id, $request->Obs);
            $empresa = ($request->tipoPrestacion === 'ART' ? $request->ART : $request->Empresa);
            ItemPrestacion::InsertarVtoPrestacion($request->Id);

            $this->updateFichaLaboral($request->IdPaciente, $request->Art, $request->Empresa);
            $this->addFactura($request->tipo, $request->sucursal, $request->nroFactura, $empresa, $request->tipoPrestacion, $request->Id);
            
            if(!empty($request->datos_facturacion_id)) {
                $this->detalleFactura->modificar($guardar, $request->datos_facturacion_id);
            }

            Auditor::setAuditoria($request->Id, 1, 2, Auth::user()->name);
        }

    }

    private function addEstudioExamen(int $idPrestacion, int $idExamen, bool $vistaPrevia = false): mixed
    {

        return $this->reporteService->generarReporte(
            ListadoReportes::getReporte($idExamen),
            null,
            null,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            ['id' => $idPrestacion, 'idExamen' => $idExamen],
            [],
            [],
            [],
            null,
            $vistaPrevia
        );
    }

    private function mergePrestaciones(int $id)
    {
        $antiguas = HistorialPrestacion::join('clientes', 'hist_prestaciones.IdEmpresa', '=', 'clientes.Id')
            ->select(
                'hist_prestaciones.Id as Id',
                'hist_prestaciones.Fecha as Fecha',
                'clientes.RazonSocial as Empresa',
                'hist_prestaciones.TipoPrestacion as Tipo',
                'hist_prestaciones.Observaciones as Obs',
        )->where('hist_prestaciones.IdPaciente', $id)
        ->orderBy('hist_prestaciones.Id', 'DESC')
        ->get();

        $antiguas = $antiguas->map(function ($item) {
            $item->Evaluacion = 0;
            return $item;
        });
        
        $nuevas = Prestacion::join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
        ->select(
            'prestaciones.Id as Id',
            'prestaciones.Fecha as Fecha',
            'clientes.RazonSocial as Empresa',
            'prestaciones.TipoPrestacion as Tipo',
            'prestaciones.Evaluacion as Evaluacion',
            'prestaciones.Calificacion as Calificacion',
            'prestaciones.Observaciones as Obs',
            'prestaciones.Financiador as Financiador'
        )->where('prestaciones.IdPaciente', $id)
        ->orderBy('prestaciones.Id', 'DESC')
        ->get();
        
        return $nuevas->merge($antiguas);
    }

    private function queryDetallado(int $id)
    {
        $nuevas = DB::table('prestaciones')
        ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('clientes as emp', 'prestaciones.IdEmpresa', '=', 'emp.Id')
        ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
        ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
        ->leftJoin('examenes', 'examenes.Id', '=', 'itemsprestaciones.IdExamen')
        ->leftJoin('prestaciones_comentarios', 'prestaciones.Id', '=', 'prestaciones_comentarios.IdP')
        ->select( 
            'pacientes.Documento as DNI',
            'pacientes.Nombre as Nombre',
            'pacientes.Apellido as Apellido',
            'prestaciones.NroCEE as NroCEE',
            'prestaciones.Anulado as Anulado',
            'prestaciones.ObsAnulado as ObsAnulado', 
            'prestaciones.Observaciones as Observaciones', 
            'prestaciones.Incompleto as Incompleto', 
            'prestaciones.Ausente as Ausente',
            'prestaciones.Forma as Forma',
            'prestaciones.Devol as Devol',
            'prestaciones_comentarios.Obs as ObsEstado',
            'prestaciones.Fecha as FechaAlta',
            'prestaciones.Id as Id',
            'prestaciones.TipoPrestacion as TipoPrestacion',
            'itemsprestaciones.ObsExamen as ObsExamen',
            'itemsprestaciones.Anulado as ExaAnulado',
            'examenes.Nombre as Examen',
            'emp.RazonSocial as EmpresaRazonSocial',
            'emp.ParaEmpresa as EmpresaParaEmp',
            'emp.Identificacion as EmpresaIdentificacion',
            'art.RazonSocial as ArtRazonSocial',
        )
            ->where('prestaciones.Estado', 1)
            ->where('prestaciones.IdPaciente', $id)
            ->orderBy('prestaciones.Id', 'DESC')
            ->get();


        $antiguas = DB::table('hist_prestaciones')
            ->leftJoin('pacientes', 'hist_prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('clientes as emp', 'hist_prestaciones.IdEmpresa', '=', 'emp.Id')
            ->join('clientes as art', 'hist_prestaciones.IdART', '=', 'art.Id')
            ->leftJoin('itemsprestaciones', 'hist_prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->leftJoin('examenes', 'examenes.Id', '=', 'itemsprestaciones.IdExamen')
            ->leftJoin('prestaciones_comentarios', 'hist_prestaciones.Id', '=', 'prestaciones_comentarios.IdP')
            ->select( 
                'pacientes.Documento as DNI',
                'pacientes.Nombre as Nombre',
                'pacientes.Apellido as Apellido',
                'hist_prestaciones.NroCEE as NroCEE',
                'hist_prestaciones.Anulado as Anulado',
                'hist_prestaciones.ObsAnulado as ObsAnulado', 
                'hist_prestaciones.Observaciones as Observaciones', 
                'prestaciones_comentarios.Obs as ObsEstado',
                'hist_prestaciones.Fecha as FechaAlta',
                'hist_prestaciones.Id as Id',
                'hist_prestaciones.TipoPrestacion as TipoPrestacion',
                'itemsprestaciones.ObsExamen as ObsExamen',
                'itemsprestaciones.Anulado as ExaAnulado',
                'examenes.Nombre as Examen',
                'emp.RazonSocial as EmpresaRazonSocial',
                'emp.ParaEmpresa as EmpresaParaEmp',
                'emp.Identificacion as EmpresaIdentificacion',
                'art.RazonSocial as ArtRazonSocial',
            )
                ->where('hist_prestaciones.IdPaciente', $id)
                ->orderBy('hist_prestaciones.Id', 'DESC')
                ->get();

        return $nuevas->merge($antiguas);

    }

    private function querySimple(int $id)
    {
        $nuevas = Prestacion::with(['paciente', 'empresa', 'art', 'prestacionComentario'])->where('IdPaciente', $id)->get();
        $antiguas = HistorialPrestacion::with(['paciente', 'empresa', 'art'])->where('IdPaciente', $id)->get();

        return $nuevas->merge($antiguas);
    }

    private function getIdArchivoEfector(int $id): int
    {
        return ArchivoEfector::where('IdPrestacion', $id)->first(['Id']) ?? 0;
    }

    private function getIdArchivoInformador(int $id): int
    {
        return ArchivoInformador::where('IdPrestacion', $id)->first(['id']) ?? 0;
    }

    private function checkExCtaImpago(int $idPrestacion): mixed
    {
        return ExamenCuentaIt::join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->where('pagosacuenta_it.IdPrestacion', $idPrestacion)->where('pagosacuenta.Pagado', 0)->count();
    }

    private function procesarFiltros(?string $filtro = null): array
    {
        $filters = [];
    
        if (!empty($filtro)) {
            $filtersArray = explode(",", $filtro);
    
            foreach ($filtersArray as $filter) {
                $parts = explode(":", $filter, 2);
    
                // Verificar que haya exactamente dos partes (clave y valor)
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
    
                    // Agregar el filtro solo si el valor no está vacío ni es 'undefined'
                    if (!empty($value) && $value !== 'undefined') {
                        $filters[$key] = $value;
                    }
                }
            }
        }
    
        return $filters;
    }

}
