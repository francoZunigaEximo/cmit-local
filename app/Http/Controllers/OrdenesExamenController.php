<?php

namespace App\Http\Controllers;

use App\Models\ItemPrestacion;
use App\Models\Prestacion;
use App\Traits\ObserverItemsPrestaciones;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DateTime;
use DateInterval;
use App\Traits\CheckPermission;

use App\Services\Reportes\ReporteService;
use App\Helpers\Tools;
use Illuminate\Support\Facades\Auth;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\File;

use App\Services\Reportes\Titulos\EEstudio;
use App\Services\Reportes\Cuerpos\EvaluacionResumen;
use App\Enum\ListadoReportes;
use App\Helpers\ToolsReportes;
use App\Services\Reportes\Cuerpos\AdjuntosGenerales;
use App\Services\Reportes\Cuerpos\AdjuntosAnexos;
use App\Services\Reportes\Cuerpos\AdjuntosDigitales;

use App\Jobs\ExamenesImpagosJob;
use App\Jobs\ExamenesResultadosJob;
use App\Helpers\ToolsEmails;

class OrdenesExamenController extends Controller
{
    use ObserverItemsPrestaciones, CheckPermission, ToolsReportes, ToolsEmails;

    protected $reporteService;
    protected $outputPath;
    protected $sendPath;
    protected $fileNameExport;
    private $tempFile;

    public function __construct(ReporteService $reporteService)
    {
        $this->reporteService = $reporteService;
        $this->outputPath = storage_path('app/public/fusionar'.Tools::randomCode(15).'.pdf');
        $this->sendPath = storage_path('app/public/cmit-'.Tools::randomCode(15).'-informe.pdf');
        $this->fileNameExport = 'reporte-'.Tools::randomCode(15);
        $this->tempFile = 'app/public/temp/file-';
    }

    public function index()
    {
        if(!$this->hasPermission("etapas_show")) {
            abort(403);
        }

        return view('layouts.ordenesExamen.index');
    }

    public function search(Request $request): mixed
    {   
        if(!$this->hasPermission("etapas_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        if($request->ajax())
        {
            $query = DB::table('itemsprestaciones')
            ->select(
                'itemsprestaciones.Id as IdItem',
                'itemsprestaciones.Fecha as Fecha',
                'itemsprestaciones.IdProfesional as IdProfesional',
                'proveedores.Nombre as Especialidad',
                'proveedores.Id as IdEspecialidad',
                'prestaciones.Id as IdPrestacion',
                'clientes.RazonSocial as Empresa',
                'pacientes.Apellido as pacApellido',
                'pacientes.Nombre as pacNombre',
                'pacientes.Documento as Documento',
                'examenes.Nombre as Examen',
            )
            ->join('prestaciones', function ($join) use ($request) {
                $join->on('itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
                        ->where('prestaciones.Estado', 1);
                if (!empty($request->prestacion)) {
                    $join->where('prestaciones.Estado', $request->prestacion);
                }
            })
            ->join('examenes', function ($join) use ($request) {
                $join->on('itemsprestaciones.IdExamen', '=', 'examenes.Id')
                    ->where('examenes.Inactivo', 0);
                if (!empty($request->examen)) {
                    $join->where('examenes.Id', $request->examen);
                }
            })
            ->join('proveedores', function ($join) use ($request) {
                $join->on('examenes.IdProveedor', '=', 'proveedores.Id')
                        ->where('proveedores.Inactivo', '=', 0);
                        if (!empty($request->especialidad)) {
                            $join->where('proveedores.Id', $request->especialidad);
                        }
            })
            ->join('clientes', function($join) {
                $join->on('prestaciones.IdEmpresa', '=', 'clientes.Id')
                ->where('clientes.Bloqueado', 0);
            })
            ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
            
            ->join('pacientes', function($join) use ($request) {
                $join->on('prestaciones.IdPaciente', '=', 'pacientes.Id')
                        ->where('pacientes.Estado', 1);
                    if(!empty($request->paciente)) {
                        $join->where('pacientes.Id', $request->paciente);
                    }
            })
            ->whereNot('itemsprestaciones.Id', 0)
            ->where('itemsprestaciones.Anulado', 0);

            $query->when(!empty($request->fechaDesde) && !empty($request->fechaHasta), function ($query) use ($request) {
                $query->whereBetween('itemsprestaciones.Fecha', [$request->fechaDesde, $request->fechaHasta]);
            });
    
            $query->when(!empty($request->empresa), function ($query) use ($request) {
                $query->where('clientes.Id', $request->empresa);
            });

            $filtrado = $query->where('itemsprestaciones.IdProfesional', 0);

            $result = $this->condicionesComunes($filtrado);
            
            return Datatables::of($result)->make(true);
        }

        return view('layouts.ordenesExamen.index');
    }

    public function searchA(Request $request): mixed
    {
        if($request->ajax())
        {
            $query = DB::select("CALL getSearchA(?,?,?,?,?,?,?,?,?)",[
                $request->fechaDesde,
                $request->fechaHasta,
                $request->prestacion,
                $request->examen,
                $request->paciente,
                $request->estados,
                $request->efector,
                $request->especialidad,
                $request->empresa
            ]);
        
            return Datatables::of($query)->make(true);
        }
        return view('layouts.ordenesExamen.index');
    }

    public function searchAdj(Request $request): mixed
    {
        if($request->ajax())
        {
            $query = DB::select("CALL getSearchAdj(?,?,?,?,?,?)", [
                $request->fechaDesde,
                $request->fechaHasta,
                $request->efector,
                $request->especialidad,
                $request->empresa,
                $request->art
            ]);

            return Datatables::of($query)->make(true);
        }
        return view('layouts.ordenesExamen.index');
    }

    public function searchInf(Request $request): mixed
    {
        if($request->ajax())
        {
            $query = DB::select("CALL getSearchInf(?,?,?,?,?,?,?,?)", [
                $request->fechaDesde,
                $request->fechaHasta,
                $request->informador,
                $request->especialidad,
                $request->empresa,
                $request->prestacion,
                $request->paciente,
                $request->examen
            ]);

            return Datatables::of($query)->make(true);
        }
        return view('layouts.ordenesExamen.index');
    }

    public function searchInfA(Request $request)
    {
        if($request->ajax())
        {
            $query = DB::select("CALL getSeachInfA(?,?,?,?,?.?,?,?)", [
                $request->fechaDesde,
                $request->fechaHasta,
                $request->informador,
                $request->especialidad,
                $request->examen,
                $request->prestacion,
                $request->empresa,
                $request->paciente
            ]);

            return Datatables::of($query)->make(true);
        }

        return view('layouts.ordenesExamen.index');
    }

    public function searchInfAdj(Request $request)
    {
        if($request->ajax())
        {
            $query = DB::select("CALL getSearchInfAdj(?,?,?,?,?,?)", [
                $request->fechaDesde ?? null,
                $request->fechaHasta ?? null,
                $request->informador ?? null,
                $request->especialidad ?? null,
                $request->art ?? null,
                $request->empresa ?? null
            ]);

        return Datatables::of($query)->make(true);
    }

    return view('layouts.ordenesExamen.index');
}

public function searchPrestacion(Request $request)
{
    if($request->ajax())
    {
        $query = $query = DB::select("CALL getSearchPrestacion(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [
            $request->fechaDesde ?? null,
            $request->fechaHasta ?? null,
            $request->estado ?? null,
            $request->efector ?? null,
            $request->informador ?? null,
            $request->profEfector ?? null,
            $request->profInformador ?? null,
            $request->tipo ?? null,
            $request->adjunto ?? null,
            $request->examen ?? null,
            $request->pendiente ?? null,
            $request->vencido ?? null,
            $request->especialidad ?? null,
            $request->ausente ?? null,
            $request->adjuntoEfector ?? null
        ]);

        return Datatables::of($query)->make(true);   
        }

        return view('layouts.ordenesExamen.index');
    }

    public function searchEenviar(Request $request)
    {
        if($request->ajax()) {
         
            $query = DB::select("CALL getSearchEEnviar(?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $request->fechaDesde ?? null,
                $request->fechaHasta ?? null,
                $request->empresa ?? null,
                $request->paciente ?? null,
                $request->completo ?? null,
                $request->abierto ?? null,
                $request->cerrado ?? null,
                $request->eenviar ?? null,
                $request->impago ?? null
            ]);

            return Datatables::of($query)->make(true);   
        }

        return view('layouts.ordenesExamen.index');
    }

    public function getPagado(Request $request)
    {
        return DB::table('pagosacuenta_it')->join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->where('pagosacuenta_it.IdPrestacion', $request->Id)
            ->first();
    }

    public function vistaPreviaReporte(Request $request)
    {
        $resultados = [];

        foreach($request->Ids as $Id) {
            $listado = [];

            $estudios = $this->AnexosFormulariosPrint($Id); //obtiene los ids en un array
        
            array_push($listado, $this->eEstudio($Id, "si"));
            array_push($listado, $this->adjDigitalFisico($Id, 2));
            array_push($listado, $this->adjAnexos($Id));
            array_push($listado, $this->adjGenerales($Id));

            if(!empty($estudios)) {
                foreach($estudios as $examen) {
                    $estudio = $this->addEstudioExamen($request->Id, $examen);
                    array_push($listado, $estudio);
                }
            }

            $this->reporteService->fusionarPDFs($listado, $this->outputPath);
            File::copy($this->outputPath, FileHelper::getFileUrl('escritura').'/temp/MAPA'.$Id.'.pdf');

            array_push($resultados, FileHelper::getFileUrl('lectura').'/temp/MAPA'.$Id.'.pdf');
        }

        return response()->json($resultados);
    }

    public function envioAviso(Request $request)
    {
        $resultados = [];

        foreach($request->Ids as $Id) {
            $resultado = [];

            $prestacion = Prestacion::with(['paciente', 'empresa','paciente.fichalaboral'])->find($Id);
            $examenes = ItemPrestacion::with('examenes')->where('IdPrestacion', $Id)->where('Anulado', 0)->get();


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
            if ($this->checkExCtaImpago($Id) > 0) {
            
                $asunto = 'Solicitud de pago de exámen de '.$nombreCompleto;
    
                foreach ($emails as $email) {
                    ExamenesImpagosJob::dispatch($email, $asunto, $cuerpo);
                }
    
                $resultado = ['msg' => 'El '.$prestacion->empresa->RazonSocial.' presenta examenes a cuenta impagos en la prestacion '.$Id.'. Se ha enviado el email correspondiente', 'estado' => 'success'];
            
            }
            array_push($resultados, $resultado);
        }

        return response()->json($resultados);
    }

    public function enviarEstudio(Request $request)
    {
        $resultados = [];

        foreach($request->Ids as $Id) {
            $temp_estudio = [];

            $prestacion = Prestacion::with(['paciente', 'empresa','paciente.fichalaboral'])->find($Id);
            $examenes = ItemPrestacion::with('examenes')->where('IdPrestacion', $Id)->where('Anulado', 0)->get();

            $estudios = $this->AnexosFormulariosPrint($Id); //obtiene los ids en un array

            $emails = $this->getEmailsReporte($prestacion->empresa->EMailInformes);
            $nombreCompleto = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;
            
            $cuerpo['tarea'] = $prestacion->paciente->fichaLaboral->first()->Tareas;
            $cuerpo['tipoPrestacion'] = ucwords($prestacion->TipoPrestacion);
            $cuerpo['calificacion'] = substr($prestacion->Calificacion, 2) ?? '';
            $cuerpo['evaluacion'] = substr($prestacion->Evaluacion, 2) ?? '';
            $cuerpo['obsEvaluacion'] = $prestacion->Observaciones ?? '';
            $cuerpo['RazonSocial'] = $prestacion->empresa->RazonSocial ?? '';
            $cuerpo['paciente'] = $nombreCompleto ?? '';
            $cuerpo['TipoDocumento'] = $prestacion->paciente->TipoDocumento ?? '';
            $cuerpo['Documento'] = $prestacion->paciente->Documento ?? '';
            $cuerpo['Fecha'] = Carbon::parse($prestacion->Fecha)->format("d/m/Y") ?? '';
            $cuerpo['examenes'] = $examenes ?? '';

            //path de los archivos a enviar y nombres personalizados cuando se fusionan
            $eEstudioSend = storage_path('app/public/eEstudio'.$prestacion->Id.'.pdf');
            $eAdjuntoSend = storage_path('app/public/temp/eAdjuntos_'.$prestacion->paciente->Apellido.'_'.$prestacion->paciente->Nombre.'_'.$prestacion->paciente->Documento.'_'.Carbon::parse($prestacion->Fecha)->format('d-m-Y').'.pdf');
            $eGeneralSend = storage_path('app/public/eAdjGeneral'.$prestacion->Id.'.pdf');

            //Creando eEnvio para adjuntar
            array_push($temp_estudio, $this->eEstudio($Id, "no")); //construimos el eEstudio (caratula, resumen)
            array_push($temp_estudio, $this->adjDigitalFisico($Id, 2)); // metemos en el eEstudio todos los adj fisicos digitales y fisicos
            $this->reporteService->fusionarPDFs($temp_estudio, $eEstudioSend); //Fusionamos los archivos en uno solo 
            File::copy($this->adjAnexos($Id), $eAdjuntoSend); //adjuntamos individualmente los Anexos
            File::copy($this->adjGenerales($Id), $eGeneralSend);
            
            if(!empty($estudios)) {
                foreach($estudios as $examen) {
                    $estudio = $this->addEstudioExamen($request->Id, $examen);
                    array_push($estudios, $estudio);
                }
            }

            $asunto = 'Estudios '.$nombreCompleto.' - '.$prestacion->paciente->TipoDocumento.' '.$prestacion->paciente->Documento;

            $attachments = [$eEstudioSend, $eAdjuntoSend, $eGeneralSend];
            $estudios !== null ? array_push($attachments, $estudios) : null;

            foreach ($emails as $email) {
                // ExamenesResultadosJob::dispatch("nmaximowicz@eximo.com.ar", $asunto, $cuerpo, $this->sendPath);
                ExamenesResultadosJob::dispatch($email, $asunto, $cuerpo, $attachments);

                // $info = new ExamenesResultadosMail(['subject' => $asunto, 'content' => $cuerpo, 'attachments' => $attachments]);
                //     Mail::to("nmaximowicz@eximo.com.ar")->send($info);

                array_push($resultados, ['msg' => 'Se ha enviado el email correspondiente de la prestación '.$prestacion->Id, 'estado' => 'success']);
            }
            
            $prestacion->EnvioInforme = now()->format('Y-m-d');
            $prestacion->save();
        }

        return response()->json($resultados);
    }

    private function eEstudio(int $idPrestacion, string $opciones): mixed
    {
        return $this->reporteService->generarReporte(
            EEstudio::class,
            EvaluacionResumen::class,
            null,
            null,
            'guardar',
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
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
            storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
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

    public function exportar(Request $request)
    {
        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $sheet->getStyle('A1:M1')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('CCCCCCCC'); 

        // Agregar bordes gruesos a la celda
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:M1')->applyFromArray($styleArray);
        $sheet->getStyle('A1:M1')->getFont()->setBold(true)->setSize(11);

        $sheet->setCellValue('A1', 'Especialidad');
        $sheet->setCellValue('B1', 'Fecha');
        $sheet->setCellValue('C1', 'Prestacion');
        $sheet->setCellValue('D1', 'Empresa');
        $sheet->setCellValue('E1', 'Paciente');
        $sheet->setCellValue('F1', 'Estado');
        $sheet->setCellValue('G1', 'Examen');
        $sheet->setCellValue('H1', 'Efector');
        $sheet->setCellValue('I1', 'Estado Efector');
        $sheet->setCellValue('J1', 'Tipo Adjunto');
        $sheet->setCellValue('K1', 'Informador');
        $sheet->setCellValue('L1', 'Estado Informador');
        $sheet->setCellValue('M1', 'Fecha Vencimiento');

        $fila = 2;
        foreach($examenes as $examen){

            $item = $this->queryPrestacion($examen);

            $estado = $item->PresCerrado === 0 && $item->PresFinalizado === 0
                        ? 'Abierto'
                        : ($item->PresCerrado === 1 && $item->PresFinalizado === 0
                            ? 'Cerrado'
                            : ($item->PresCerrado === 1 && $item->PresFinalizado === 1
                                ? 'Finalizado'
                                : ($item->PresEntregado === 1
                                    ? 'Entregado'
                                    : ($item->PresCerrado === 1 && $item->PresEnviado === 1
                                        ? 'eEnviado'
                                        : '-'))));

            $estadoEfector = in_array($item->Efector, [1,2,4]) 
                                ? "Pendiente"
                                : (in_array($item->Efector, [3,5])
                                    ? 'Cerrado'
                                    : '-');

            $arr = [0 => '', 1 => 'Abierto/Pdte', 2 => 'Abierto/Adjunto', 3 => '', 4 => 'Cerrado/Pdte', 5 => 'Cerrado/Adjunto'];

            $adjunto = $arr[$item->Efector];
            
            $estadoInformador = ($item->Informador === 1) 
                                    ? "Pendiente"
                                    : ($item->Informador === 2
                                        ? 'Borrador'
                                        : ($item->Informador === 3
                                            ? 'Cerrado'
                                            : '-'));

            $itemFecha = new DateTime($item->Fecha);
            $vencimiento = $itemFecha->add(new DateInterval('P' . intval($item->DiasVencimiento) . 'D'));
                                            

            $sheet->setCellValue('A'.$fila, $item->Especialidad);
            $sheet->setCellValue('B'.$fila, Carbon::parse($item->Fecha)->format('d/m/Y'));
            $sheet->setCellValue('C'.$fila, $item->IdPrestacion);
            $sheet->setCellValue('D'.$fila, $item->Empresa);
            $sheet->setCellValue('E'.$fila, $item->NombrePaciente ." ". $item->ApellidoPaciente);
            $sheet->setCellValue('F'.$fila, $estado);
            $sheet->setCellValue('G'.$fila, $item->Examen);
            $sheet->setCellValue('H'.$fila, $item->NombreProfesional . " " . $item->ApellidoProfesional);
            $sheet->setCellValue('I'.$fila, $estadoEfector);
            $sheet->setCellValue('J'.$fila, $adjunto);
            $sheet->setCellValue('K'.$fila, $item->NombreProfesional2 . " " . $item->ApellidoProfesional2);
            $sheet->setCellValue('L'.$fila, $estadoInformador);
            $sheet->setCellValue('M'.$fila, Carbon::parse($vencimiento)->format('d/m/Y'));
            $fila++;
        }

        // Generar un nombre aleatorio para el archivo
        $name = Str::random(10).'.xlsx';

        // Guardar el archivo en la carpeta de almacenamiento
        $filePath = storage_path('app/public/'.$name);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        chmod($filePath, 0777);

        // Devolver la ruta del archivo generado
        return response()->json(['filePath' => $filePath]);  

    }

    private function condicionesComunes($query): mixed
    {
        $query->limit(5000)
        ->orderBy('itemsprestaciones.Id', 'DESC');

        return $query;
    }

    // SELECT 
    //     i.Id,
    //     i.IdPrestacion,
    //     i.CAdj,i.CInfo,
    //     i.IdProfesional2,
    //     i.Incompleto,
    //     i.Ausente,
    //     i.Forma,
    //     i.SinEsc,
    //     i.Devol,
    //     i.Anulado,
    //     ex.Nombre as NombreExamen,
    //     ex.NoImprime,
    //     ex.DiasVencimiento,
    //     e.ParaEmpresa,
    //     pa.Nombre as NPac,
    //     pa.Apellido as APac,
    //     pa.Documento,p.Nombre as NombreProv,
    //     pr.Nombre,pr.Apellido,
    //     f.Fecha as FechaP, 
    //     f.Cerrado,f.Finalizado,
    //     f.Entregado,f.eEnviado,
    //     f.FechaVto,p2.Nombre as Nombre2,
    //     p2.Apellido as Apellido2 
    //     From prestaciones f
    //     ,itemsprestaciones i,
    //     examenes ex,
    //     clientes e,
    //     pacientes pa,
    //     proveedores p,
    //     profesionales pr,
    //     profesionales p2 
    //     where  f.Id=i.IdPrestacion 
    //     and i.IdExamen=ex.Id 
    //     and   e.Id=f.IdEmpresa 
    //     and pa.Id=f.IdPaciente 
    //     and pr.Id=i.IdProfesional 
    //     and p2.Id=i.IdProfesional2 
    //     and i.IdProveedor=p.Id 
    //     and i.Anulado=0  
    //     $wbuscar $wfecha"

    private function queryPrestacion(?int $id): mixed
    {

        return ItemPrestacion::join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
        ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
        ->join('proveedores', 'examenes.IdProveedor2', '=', 'proveedores.Id')
        ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
        ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
        ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('profesionales as prof1', 'itemsprestaciones.IdProfesional', '=', 'prof1.Id')
        ->join('profesionales as prof2', 'itemsprestaciones.IdProfesional2', '=', 'prof2.Id')
        ->select(
            'itemsprestaciones.Id as IdItem',
            'itemsprestaciones.Fecha as Fecha',
            'itemsprestaciones.CAdj as Efector',
            'itemsprestaciones.CInfo as Informador',
            'itemsprestaciones.IdProfesional as IdProfesional',
            'proveedores.Nombre as Especialidad',
            'proveedores.Id as IdEspecialidad',
            'prestaciones.Id as IdPrestacion',
            'prestaciones.Cerrado as PresCerrado',
            'prestaciones.Finalizado as PresFinalizado',
            'prestaciones.Entregado as PresEntregado',
            'prestaciones.eEnviado as PresEnviado',
            'clientes.RazonSocial as Empresa',
            'pacientes.Nombre as NombrePaciente',
            'pacientes.Apellido as ApellidoPaciente',
            'prof1.Nombre as NombreProfesional',
            'prof1.Apellido as ApellidoProfesional',
            'prof2.Nombre as NombreProfesional2',
            'prof2.Apellido as ApellidoProfesional2',
            'examenes.Nombre as Examen',
            'examenes.Id as IdExamen',
            'examenes.DiasVencimiento as DiasVencimiento',
            'examenes.NoImprime as NoImprime'
        )->whereNot('itemsprestaciones.Id', 0)
        ->where('itemsprestaciones.Id', $id)
        ->first();
    }

}