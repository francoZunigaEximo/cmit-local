<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;
use App\Models\ExamenCuenta;
use App\Models\ExamenCuentaIt;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class ExamenACuenta implements ReporteInterface
{
    protected $spreadsheet;
    protected $sheet;

    use ToolsReportes;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    }

    public function columnasYEncabezados($sheet)
    {
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('B4:D4');
        
        $columnas = ['A', 'B', 'C', 'D'];

        foreach($columnas as $columna){
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $sheet->setCellValue('A1', 'DETALLE DE EXAMENES A CUENTA');
        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->getRowDimension('1')->setRowHeight(40);

        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A1:A6')->getFill()
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
        $sheet->getStyle('A1')->applyFromArray($styleArray);

        $sheet->getStyle('A1:A6')->getFont()->setBold(true);
        $sheet->getStyle('A2:A6')->getFont()->setSize(11);

        for ($i = 2; $i <= 6; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(30); 
        }
    }

    public function datos($sheet, $datos)
    {
        $examen = $this->tituloReporte($datos);

        $factura = $examen->Tipo . '-' . sprintf('%04d', $examen->Suc) . '-' . sprintf('%08d', $examen->Nro);

        $sheet->setCellValue('A2', 'Fecha: ');
        $sheet->setCellValue('B2', Carbon::parse($examen->Fecha)->format('d/m/Y'));
        $sheet->setCellValue('A3', 'Factura: ');
        $sheet->setCellValue('B3', $factura);
        $sheet->setCellValue('A4', 'Cliente: ');
        $sheet->setCellValue('B4', sprintf('%05d', $examen->IdEmpresa) . ' - Empresa: ' . $examen->Empresa . ' - ' . $examen->Cuit);
        $sheet->setCellValue('A5', 'Total Ex: ');
        $sheet->setCellValue('B5', $this->totalExamenes($examen->Id));
        $sheet->setCellValue('A6', 'Ex Disponibles: ');
        $sheet->setCellValue('B6', $this->totalDisponibles($examen->Id));

        $examenes = $this->examenesReporte($examen->Id);

                $sheet->setCellValue('A8', 'Prestación');
        $sheet->setCellValue('B8', 'Especialidad Efector');
        $sheet->setCellValue('C8', 'Examen');
        $sheet->setCellValue('D8', 'Paciente');

        $sheet->getStyle('A8:D8')->getFont()->setBold(true)->setSize(11);
        $sheet->getRowDimension('8')->setRowHeight(30);

        $sheet->getStyle('A8:D8')->getFill()
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
        $sheet->getStyle('A8:D8')->applyFromArray($styleArray);

        $sheet->getStyle('A8:D8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $fila = 9;
        foreach($examenes as $reporte){
            $sheet->setCellValue('A'.$fila, $reporte->IdPrestacion === 0 ? '-' : $reporte->IdPrestacion);
            $sheet->setCellValue('B'.$fila, $reporte->NombreEstudio);
            $sheet->setCellValue('C'.$fila, $reporte->NombreExamen);
            $sheet->setCellValue('D'.$fila, $reporte->Apellido . " " . $reporte->Nombre);
            $fila++;
        }

        $filaOcupados = $fila+1;

        $sheet->setCellValue('A'.$filaOcupados, 'TOTAL EXAMENES DISPONIBLES: ');
        $sheet->mergeCells('A'.$filaOcupados.':B'.$filaOcupados);
        $sheet->getStyle('A'.$filaOcupados.':B'.$filaOcupados)->getFont()->setBold(true)->setSize(11);
        $sheet->getRowDimension('8')->setRowHeight(30);
        $sheet->getStyle('A'.$filaOcupados.':B'.$filaOcupados)->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('CCCCCCCC'); 

        $disponibles = $this->examenesDisponibles($examen->Id);

        $datosFinlaes = $filaOcupados + 1;
        foreach($disponibles as $items){
            $sheet->setCellValue('A'.$datosFinlaes, $items->Cantidad);
            $sheet->setCellValue('B'.$datosFinlaes, $items->NombreExamen);
            $datosFinlaes++;
        }
    }

    public function generar($examen)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $examen);
        
        $name = 'examenes_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    private function tituloReporte(?int $id): mixed
    {
        return ExamenCuenta::join('clientes', 'pagosacuenta.IdEmpresa', '=', 'clientes.Id')
            ->join('localidades', 'clientes.IdLocalidad', '=', 'localidades.Id')
            ->select(
                'clientes.RazonSocial as Empresa',
                'clientes.ParaEmpresa as ParaEmpresa',
                'clientes.Direccion as Direccion',
                'clientes.Identificacion as Cuit',
                'clientes.Id as IdEmpresa',
                'clientes.Telefono as Telefono',
                'pagosacuenta.Fecha as Fecha',
                'pagosacuenta.Tipo as Tipo',
                'pagosacuenta.Suc as Suc',
                'pagosacuenta.Nro as Nro',
                'pagosacuenta.Id as Id',
                'localidades.Nombre as NombreLocalidad',
                'localidades.CP as CodigoPostal',
            )
            ->where('pagosacuenta.Id', $id)
            ->first();
    }

    private function examenesReporte(?int $id): mixed
    {
        return ExamenCuentaIt::join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('itemsprestaciones', function (JoinClause $join) {
                $join->on('pagosacuenta_it.IdPrestacion', '=', 'itemsprestaciones.IdPrestacion')
                ->on('pagosacuenta_it.IdExamen', '=', 'itemsprestaciones.IdExamen');
            })
            ->join('profesionales as efector', 'efector.Id', '=', 'itemsprestaciones.IdProfesional')
            ->join('proveedores', 'efector.IdProveedor', '=', 'proveedores.Id')
            ->join('estudios', 'examenes.IdEstudio', '=', 'estudios.Id')
            ->select(
                'prestaciones.Id as IdPrestacion',
                'proveedores.Nombre as NombreEstudio',
                'examenes.Nombre as NombreExamen',
                'pacientes.Nombre as Nombre',
                'pacientes.Apellido as Apellido'  
            )
            ->where('pagosacuenta_it.IdPago', $id)
            ->whereNot('pagosacuenta_it.Obs', 'provisorio')
            ->orderBy('examenes.Nombre')
            ->orderBy('estudios.Nombre')
            ->get();
    }

    private function totalExamenes(?int $id): int
    {
        return ExamenCuentaIt::where('IdPago', $id)->whereNot('Obs', 'provisorio')->count();
    }

    private function totalDisponibles(?int $id): int
    {
        return ExamenCuentaIt::where('IdPago', $id)->where('IdPrestacion', 0)->whereNot('Obs', 'provisorio')->count();
    }

    private function examenesDisponibles(?int $id): mixed
    {
        return ExamenCuentaIt::join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->leftJoin('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->leftJoin('itemsprestaciones', function (JoinClause $join) {
                $join->on('pagosacuenta_it.IdPrestacion', '=', 'itemsprestaciones.IdPrestacion')
                ->on('pagosacuenta_it.IdExamen', '=', 'itemsprestaciones.IdExamen');
            })
            ->leftJoin('profesionales as efector', 'efector.Id', '=', 'itemsprestaciones.IdProfesional')
            ->leftJoin('proveedores', 'efector.IdProveedor', '=', 'proveedores.Id')
            ->join('estudios', 'examenes.IdEstudio', '=', 'estudios.Id')
            ->select(
                'examenes.Nombre as NombreExamen',
                DB::raw('COUNT(pagosacuenta_it.IdExamen) as Cantidad'), 
 
            )
            ->where('pagosacuenta_it.IdPago', $id)
            ->where('pagosacuenta_it.IdPrestacion', 0)
            ->whereNot('pagosacuenta_it.Obs', 'provisorio')
            ->orderBy('examenes.Nombre')
            ->orderBy('estudios.Nombre')
            ->get();
    }
}