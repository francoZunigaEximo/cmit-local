<?php

namespace App\Http\Controllers;

use App\Models\ItemPrestacion;
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

class OrdenesExamenController extends Controller
{
    use ObserverItemsPrestaciones, CheckPermission;

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
            $query = DB::select("CALL getSearchA(".
                ($request->fechaDesde ? "'".$request->fechaDesde."'" : "NULL").", ".
                ($request->fechaHasta ? "'".$request->fechaHasta."'" : "NULL").", ".
                ($request->prestacion ? $request->prestacion : "NULL").", ".
                ($request->examen ? $request->examen : "NULL").", ".
                ($request->paciente ? $request->paciente : "NULL").", ".
                ($request->estados ? "'".$request->estados."'" : "NULL").", ".
                ($request->efector ? $request->efector : "NULL").", ".
                ($request->especialidad ? $request->especialidad : "NULL").", ".
                ($request->empresa ? $request->empresa : "NULL").")");
        
            return Datatables::of($query)->make(true);
        }
        return view('layouts.ordenesExamen.index');
    }

    public function searchAdj(Request $request): mixed
    {
        if($request->ajax())
        {
            $query = DB::select("CALL getSearchAdj(".
                ($request->fechaDesde ? "'".$request->fechaDesde."'" : "NULL").", ".
                ($request->fechaHasta ? "'".$request->fechaHasta."'" : "NULL").", ".
                ($request->efector ? $request->efector : "NULL").", ".
                ($request->especialidad ? $request->especialidad : "NULL").", ".
                ($request->empresa ? $request->empresa : "NULL").", ".
                ($request->art ? $request->art : "NULL").")");

            return Datatables::of($query)->make(true);
        }
        return view('layouts.ordenesExamen.index');
    }

    public function searchInf(Request $request): mixed
    {
        if($request->ajax())
        {
            $query = DB::select("CALL getSearchInf(".
            ($request->fechaDesde ? "'".$request->fechaDesde."'" : "NULL").", ".
            ($request->fechaHasta ? "'".$request->fechaHasta."'" : "NULL").", ".
            ($request->informador ? $request->informador : "NULL").", ".
            ($request->especialidad ? $request->especialidad : "NULL").", ".
            ($request->empresa ? $request->empresa : "NULL").", ".
            ($request->prestacion ? $request->prestacion : "NULL").", ".
            ($request->paciente ? $request->paciente : "NULL").", ".
            ($request->examen ? $request->examen : "NULL").")");

            return Datatables::of($query)->make(true);
        }
        return view('layouts.ordenesExamen.index');
    }

    public function searchInfA(Request $request)
    {
        if($request->ajax())
        {
            $query = DB::select("CALL getSearchInfA(".
            ($request->fechaDesde ? "'".$request->fechaDesde."'" : "NULL").", ".
            ($request->fechaHasta ? "'".$request->fechaHasta."'" : "NULL").", ".
            ($request->informador ? $request->informador : "NULL").", ".
            ($request->especialidad ? $request->especialidad : "NULL").", ".
            ($request->examen ? $request->examen : "NULL").", ".
            ($request->prestacion ? $request->prestacion : "NULL").", ".
            ($request->empresa ? $request->empresa : "NULL").", ".
            ($request->paciente ? $request->paciente : "NULL").")");

            return Datatables::of($query)->make(true);
        }

        return view('layouts.ordenesExamen.index');
    }

    public function searchInfAdj(Request $request)
    {
        if($request->ajax())
        {
            $query = DB::select("CALL getSearchInfAdj(".
            ($request->fechaDesde ? "'".$request->fechaDesde."'" : "NULL").", ".
            ($request->fechaHasta ? "'".$request->fechaHasta."'" : "NULL").", ".
            ($request->informador ? $request->informador : "NULL").", ".
            ($request->especialidad ? $request->especialidad : "NULL").", ".
            ($request->art ? $request->art : "NULL").", ".
            ($request->empresa ? $request->empresa : "NULL").")");

        return Datatables::of($query)->make(true);
    }

    return view('layouts.ordenesExamen.index');
}

public function searchPrestacion(Request $request)
{
    if($request->ajax())
    {
        $query = $query = DB::select("CALL getSearchPrestacion(".
        ($request->fechaDesde ? "'".$request->fechaDesde."'" : "NULL").", ".
        ($request->fechaHasta ? "'".$request->fechaHasta."'" : "NULL").", ".
        ($request->estado ? "'".$request->estado."'" : "NULL").", ".
        ($request->efector ? "'".$request->efector."'" : "NULL").", ".
        ($request->informador ? "'".$request->informador."'" : "NULL").", ".
        ($request->profEfector ? $request->profEfector : "NULL").", ".
        ($request->profInformador ? $request->profInformador : "NULL").", ".
        ($request->tipo ? "'".$request->tipo."'" : "NULL").", ".
        ($request->adjunto ? "'".$request->adjunto."'" : "NULL").", ".
        ($request->examen ? $request->examen : "NULL").", ".
        ($request->pendiente ? $request->pendiente : "NULL").", ".
        ($request->vencido ? $request->vencido : "NULL").", ".
        ($request->especialidad ? $request->especialidad : "NULL").", ".
        ($request->ausente ? "'".$request->ausente."'" : "NULL").", ".
        ($request->adjuntoEfector ? $request->adjuntoEfector : "NULL").")");

        return Datatables::of($query)->make(true);   
        }

        return view('layouts.ordenesExamen.index');
    }

    public function searchEenviar(Request $request)
    {
        if($request->ajax()) {

            // $query = $query = DB::select("CALL getSearchEEnviar(".
            // ($request->fechaDesde ? "'".$request->fechaDesde."'" : "NULL").", ".
            // ($request->fechaHasta ? "'".$request->fechaHasta."'" : "NULL").", ".
            // ($request->empresa ? $request->empresa : "NULL").", ".
            // ($request->paciente ? $request->paciente : "NULL").", ".
            // ($request->completo ? "'".$request->completo."'" : "NULL").", ".
            // ($request->abierto ? "'".$request->abierto."'" : "NULL").", ".
            // ($request->cerrado ? "'".$request->cerrado."'" : "NULL").", ".
            // ($request->eenviar ? "'".$request->eenviar."'" : "NULL").")");

            $query = DB::table('itemsprestaciones as i')
                ->join('prestaciones as pre', function($join) use ($request){
                        $join->on('i.IdPrestacion', '=', 'pre.Id')
                            ->where('pre.Anulado', 0);
                            if (!empty($request->eenviar)) {
                                $join->when($request->eenviar === 'eenviado', function($query) {
                                    return $query->where('pre.eEnviado', 1);
                                })
                                ->when($request->eenviar === 'noeenviado', function($query) {
                                    return $query->where('pre.eEnviado', 0);
                                })
                                ->when($request->eenviar === 'todos', function($query) {
                                    return $query->whereIn('pre.eEnviado', [0, 1]);
                                });
                            }
                })
                ->join('clientes as cli', function ($join) use ($request) {
                    $join->on('pre.IdEmpresa', '=', 'cli.Id');
                    if (!empty($request->empresa)) {
                        $join->where('cli.Id', $request->empresa);
                    }
                })
                ->join('pacientes as pa', function ($join) use ($request) {
                    $join->on('pre.IdPaciente', '=', 'pa.Id');
                    if (!empty($request->paciente)) {
                        $join->where('pa.Id', $request->paciente);
                    }
                })
                ->join('examenes as exa', function($join) {
                    $join->on('i.IdExamen', '=', 'exa.Id')
                        ->where('exa.Informe', 1);
                })
                ->join('pagosacuenta as pc', 'cli.Id', '=', 'pc.IdEmpresa')
                ->leftJoin('pagosacuenta_it as pc2', 'pre.Id', '=', 'pc2.IdPrestacion')
                ->select(
                    'i.Fecha AS Fecha', 
                    'pre.Id AS IdPrestacion', 
                    'pre.FechaEnviado AS FechaEnviado',
                    'cli.EMailInformes AS Correo',
                    'cli.RazonSocial AS Empresa',
                    DB::raw("CONCAT(pa.Apellido, ' ', pa.Nombre) AS NombreCompleto"),
                    'pa.Documento AS Documento', 
                    'pa.Id AS IdPaciente', 
                    'exa.Nombre AS Examen', 
                    'pc.Pagado AS Pagado', 
                    'i.Id AS IdExa'
                );

                $query->when(!empty($request->fechaDesde) && !empty($request->fechaHasta), function ($query) use ($request) {
                    $query->whereBetween('i.Fecha', [$request->fechaDesde, $request->fechaHasta]);
                });

                $query->when(!empty($request->completo) && $request->completo === 'activo', function ($query) {
                    $query->whereIn('i.CAdj', [3, 5])
                        ->where('i.CInfo', 3)
                        ->where('pc.Pagado', 1);
                });
    
                $query->when(!empty($request->abierto) && $request->abierto === 'activo', function ($query) {
                    $query->whereIn('i.CAdj', [0, 1, 2])
                        ->where('i.CInfo', 1)
                        ->where('pc.Pagado', 0);
                });

                $query->when(!empty($request->cerrado) && $request->cerrado === 'activo', function ($query) {
                    $query->whereIn('i.CAdj', [3, 4, 5])
                        ->where('i.CInfo', 3)
                        ->whereIn('pc.Pagado', [0, 1]);
                });

                $query->whereNot('i.Id', 0)
                    ->whereNot('i.Fecha', '0000-00-00')
                    ->whereNot('i.Fecha', null)
                    ->groupBy('pre.Id')
                    ->orderBy('i.Id', 'DESC')
                    ->limit(1000);

            return Datatables::of($query)->make(true);   
        }

        return view('layouts.ordenesExamen.index');
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