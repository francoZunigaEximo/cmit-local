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

use App\Services\Reportes\ReporteService;
use App\Services\Reportes\Titulos\Reducido;
use App\Services\Reportes\Cuerpos\EvaluacionResumen;
use App\Services\Reportes\Titulos\CaratulaInterna;
use App\Services\Reportes\Cuerpos\AdjuntosDigitales;
use App\Services\Reportes\Cuerpos\AdjuntosGenerales;

class PrestacionesController extends Controller
{
    use ObserverPrestaciones, ObserverFacturasVenta, CheckPermission;

    protected $reporteService;
    protected $outputPath;
    protected $fileNameExport;
    private $tempFile;

    public function __construct(ReporteService $reporteService)
    {
        $this->reporteService = $reporteService;
        $this->outputPath = storage_path('app/public/fusionar.pdf');
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

        if($prestacion) {

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
            $prestacion->RxPreliminar = $request->RxPreliminar === 'true' ? 1 : 0;
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

        if ($request->evaluacion == 'true') {
            array_push($listado, $this->caratula($request->Id));
            array_push($listado, $this->resumenEvaluacion($request->Id));
        }

        if ($request->adjDigitales == 'true') {
            array_push($listado, $this->adjDigitalFisico($request->Id, 1));
        }

        if ($request->adjFisicosDigitales == 'true') {
            array_push($listado, $this->adjDigitalFisico($request->Id, 2));
        }

        if ($request->adjGenerales == 'true') {
            array_push($listado, $this->adjGenerales($request->Id));
        }

        if ($request->adjAnexos == 'true') {
            array_push($listado, $this->adjAnexos($request->Id));
        }

        $this->reporteService->fusionarPDFs($listado, $this->outputPath);

        return response()->json([
            'filePath' => $this->outputPath,
            'name' => $this->fileNameExport.'.pdf',
            'msg' => 'Reporte generado correctamente',
            'icon' => 'success' 
        ]);
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
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            [],
            [],
            ['id' => $idPrestacion, 'firmaeval' => 0],
            null
        );
    }

    private function caratula(int $idPrestacion): mixed
    {
        return $this->reporteService->generarReporte(
            Reducido::class,
            CaratulaInterna::class,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
            null,
            [],
            ['id' => $idPrestacion],
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
            'guardar',
            null,
            null,
            ['id' => $idPrestacion, 'tipo' => $tipo],
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
            'guardar',
            null,
            null,
            ['id' => $idPrestacion],
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
            'guardar',
            null,
            null,
            ['id' => $idPrestacion],
            [],
            [],
            storage_path('app/public/temp/merge_adjAnexos.pdf')
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

}
